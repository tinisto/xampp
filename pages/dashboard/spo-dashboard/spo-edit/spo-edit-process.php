<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_admin.php";

include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/email_functions.php";

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"], $_POST["id_spo"])) {
    $action = $_POST["action"];
    $idCollege = $_POST["id_spo"];  // Using id_spo as unique identifier

    // Include the corresponding email template based on the action
    if ($action === "accept") {
        include $_SERVER["DOCUMENT_ROOT"] . "/includes/email-templates/email-template-update-request-approve.php";
    } elseif ($action === "reject") {
        include $_SERVER["DOCUMENT_ROOT"] . "/includes/email-templates/email-template-update-request-refuse.php";
    } else {
        echo "Invalid action.";
        exit();
    }

    // Fetch data from the database
    if ($action === "accept") {
        // Fetch the new data from the verification table
        $queryFetchVerification = "SELECT * FROM spo_verification WHERE id_spo = ?";
        $stmtVerification = $connection->prepare($queryFetchVerification);
        $stmtVerification->bind_param("i", $idCollege);
        $stmtVerification->execute();
        $resultVerification = $stmtVerification->get_result();

        if ($rowVerification = $resultVerification->fetch_assoc()) {
            // Get editor email from the verification table
            $editorEmail = $rowVerification['user_id'];

            // Dynamically build the update query for the spo table
            $updateFields = [];
            $updateValues = [];

            // Loop through the verification data and prepare fields for update
            foreach ($rowVerification as $key => $value) {
                if ($key !== "id_spo" && $key !== "user_id") { // Exclude id_spo and user_id
                    $updateFields[] = "$key = ?";
                    $updateValues[] = $value;
                }
            }

            // Add the id_spo for the WHERE clause
            $updateValues[] = $idCollege;

            $updateFields[] = "approved = 1"; // Add this line to set 'approved' to 1

            // Prepare the dynamic SQL query
            $updateQuery = "UPDATE spo SET " . implode(", ", $updateFields) . " WHERE id_spo = ?";
            $stmtUpdate = $connection->prepare($updateQuery);

            // Dynamically bind the parameters
            $types = str_repeat("s", count($updateValues) - 1) . "i"; // Assume all fields are strings except for id_spo
            $stmtUpdate->bind_param($types, ...$updateValues);

            // Execute the update query
            if ($stmtUpdate->execute()) {
                // Delete the verification record
                $deleteQuery = "DELETE FROM spo_verification WHERE id_spo = ?";
                $stmtDelete = $connection->prepare($deleteQuery);
                $stmtDelete->bind_param("i", $idCollege);
                $stmtDelete->execute();

                if ($stmtDelete->affected_rows > 0) {
                    // Send acceptance email
                    sendToUser($editorEmail, $subject, $body);
                    sendToAdmin($subject, $body);

                    // Redirect to the updated college page
                    header("Location: /spo/" . urlencode($rowVerification["spo_url"]));
                    exit();
                } else {
                    echo "Error deleting verification record.";
                }
            } else {
                echo "Error updating college data: " . $stmtUpdate->error;
            }

            // Close statements
            $stmtUpdate->close();
            $stmtDelete->close();
        } else {
            echo "Verification record not found.";
        }

        $stmtVerification->close();
    } elseif ($action === "reject") {
        // Fetch editor email
        $queryFetchEmail = "SELECT user_id FROM spo_verification WHERE id_spo = ?";
        $stmtEmail = $connection->prepare($queryFetchEmail);
        $stmtEmail->bind_param("i", $idCollege);
        $stmtEmail->execute();
        $resultEmail = $stmtEmail->get_result();

        if ($rowEmail = $resultEmail->fetch_assoc()) {
            $editorEmail = $rowEmail['user_id'];

            // Delete the verification record
            $deleteQuery = "DELETE FROM spo_verification WHERE id_spo = ?";
            $stmtDelete = $connection->prepare($deleteQuery);
            $stmtDelete->bind_param("i", $idCollege);
            $stmtDelete->execute();

            if ($stmtDelete->affected_rows > 0) {
                // Send rejection email
                sendToUser($editorEmail, $subject, $body);
                sendToAdmin($subject, $body);

                // Redirect to a thank-you page
                header("Location: /thank-you?message=Verification rejected successfully.");
                exit();
            } else {
                echo "Error deleting verification record.";
            }

            $stmtDelete->close();
        } else {
            echo "Error fetching editor email.";
        }

        $stmtEmail->close();
    } else {
        echo "Invalid action.";
    }
} else {
    echo "Invalid form submission.";
}
