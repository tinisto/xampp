<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Жалоба на комментарий</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body">
                <!-- Use a placeholder element to display the comment ID -->
                <p id="commentIdPlaceholder"></p>

                <!-- Add a textarea for the report description -->
                <div class="mb-3">
                    <textarea class="form-control" id="reportDescription" rows="6"
                        placeholder="Введите описание вашей жалобы здесь"></textarea>
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="close-button" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" class="custom-button" onclick="sendReport()">Отправить</button>

            </div>
        </div>
    </div>
</div>




<script>
    document.addEventListener('DOMContentLoaded', function () {
        var reportDescriptionInput = document.getElementById('reportDescription');

        if (reportDescriptionInput) {
            reportDescriptionInput.addEventListener('input', function () {
                // ... (your modal-specific logic)
            });
        }
        function sendReport() {
            // Assuming you have the necessary data
            var commentId = document.getElementById('commentIdPlaceholder').innerText;
            var userId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;

            var idSchool = window.location.pathname.split('/').pop();
            var reportReason = "Some reason"; // Replace with your logic to get the report reason
            var reportDescription = document.getElementById('reportDescription').value;

            // Send data to the server using AJAX
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "submit_report.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Display confirmation message
                    alert(xhr.responseText);

                    // Close the modal
                    $('#reportModal').modal('hide');
                }
            };

            // Prepare data to send
            var data = "commentId=" + commentId + "&userId=" + userId + "&idSchool=" + idSchool + "&reportReason=" + reportReason + "&reportDescription=" + reportDescription;

            // Send the request
            xhr.send(data);
        }

        // Add an event listener to the Send button
        document.getElementById('sendReportButton').addEventListener('click', sendReport);
    });

</script>

<div class="report-icon" data-bs-toggle="modal" data-bs-target="#reportModal"
    data-comment-id="<?php echo $comment['id']; ?>">
    <i class="fas fa-exclamation-triangle"></i>
</div>


<script>
    // Use this script to handle the comment ID and update the modal content
    var reportModal = new bootstrap.Modal(document.getElementById('reportModal'));

    function openReportModal(commentId) {
        // Update the content in the modal body
        document.getElementById('commentIdPlaceholder').innerText = 'Comment ID: ' + commentId;

        // Show the modal using Bootstrap's method
        reportModal.show();
    }
</script>