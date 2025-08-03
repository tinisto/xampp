<?php
function renderButtonBlock($submitText = "Создать страницу", $cancelText = "Отмена", $link = '/', $showDelete = false, $deleteId = null, $deleteType = 'news') {
    // Escape the link to ensure it doesn't break the JavaScript
    $link = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');  // Escape special characters

    $buttons = "
    <div class='d-flex justify-content-center my-3 gap-3 flex-wrap'>
        <button type='submit' class='btn btn-success'>$submitText</button>
        <button type='button' class='btn btn-secondary' onclick=\"window.location.href = '$link';\">$cancelText</button>";
    
    if ($showDelete && $deleteId) {
        $buttons .= "
        <button type='button' class='btn btn-danger' onclick='confirmDelete($deleteId, \"$deleteType\")'>
            <i class='fas fa-trash me-1'></i>Удалить
        </button>";
    }
    
    $buttons .= "
    </div>";

    if ($showDelete) {
        $buttons .= "
        <script>
        function confirmDelete(id, type) {
            if (confirm('Вы уверены, что хотите удалить эту запись? Это действие нельзя отменить.')) {
                // Create a form and submit it
                const deleteForm = document.createElement('form');
                deleteForm.method = 'POST';
                deleteForm.action = '/pages/common/' + type + '/' + type + '-delete.php';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id_' + type;
                idInput.value = id;
                
                deleteForm.appendChild(idInput);
                document.body.appendChild(deleteForm);
                deleteForm.submit();
            }
        }
        </script>";
    }
    
    return $buttons;
}
?>