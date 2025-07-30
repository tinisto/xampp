<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/check_user.php";

include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/buttons/form-buttons.php";
include 'write-functions.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-md-8">

        <h5 class="text-center fw-bold mb-3">Если у вас возникли какие-либо вопросы<br>или вы хотели бы обсудить что-то с нами, свяжитесь с нами.</h5>

        <form id="messageForm" action="write-process-form" method="post">
    <?php echo csrf_field(); ?>

            <div class="form-floating mb-3">
                <textarea class="form-control" placeholder="Ваше сообщение" id="message" name="message" style="height: 100px"
                    required></textarea>
                <label for="message">Ваше сообщение</label>
            </div>
            <input type="hidden" name="userEmail" value="<?php echo $_SESSION['email']; ?>">
            <?php echo renderButtonBlock("Отправить"); ?>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the form and input elements
        var form = document.getElementById('messageForm');
        var submitButton = document.getElementById('submitButton');

        // Add event listeners to form inputs
        form.addEventListener('input', function() {
            // Check if all required fields are filled
            var areFieldsFilled = Array.from(form.elements).every(function(element) {
                return !element.required || (element.type !== 'checkbox' && element.value.trim() !== '');
            });
            // Enable or disable the submit button based on field status
            submitButton.disabled = !areFieldsFilled;
        });
    });
</script>