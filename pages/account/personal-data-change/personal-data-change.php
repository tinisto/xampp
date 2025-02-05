<?php include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/buttons/form-buttons.php";?>
<div>

<form action="/pages/account/personal-data-change/personal-data-change-process.php" method="post">
    <input type="hidden" name="updatePersonalData" value="1">
    
    <div class="mb-3">
        <div class="form-floating">
            <input type="text" id="firstname" name="firstname" class="form-control" value="<?php echo htmlspecialchars($userData['firstname']); ?>" required>
            <label for="firstname" class="form-label">Имя:</label>
        </div>
    </div>

    <div class="mb-3">
        <div class="form-floating">
            <input type="text" id="lastname" name="lastname" class="form-control" value="<?php echo htmlspecialchars($userData['lastname']); ?>" required>
            <label for="lastname" class="form-label">Фамилия:</label>
        </div>
    </div>

    <div class="mb-3">
        <select id="occupation" name="occupation" class="form-select form-select-sm" aria-label="Small select example" required>
            <option value="">Выберите род деятельности</option>
            <option value="Представитель ВУЗа" <?= $userData['occupation'] === 'Представитель ВУЗа' ? 'selected' : '' ?>>Представитель ВУЗа</option>
            <option value="Представитель ССУЗа" <?= $userData['occupation'] === 'Представитель ССУЗа' ? 'selected' : '' ?>>Представитель ССУЗа</option>
            <option value="Представитель школы" <?= $userData['occupation'] === 'Представитель школы' ? 'selected' : '' ?>>Представитель школы</option>
            <option value="Родитель" <?= $userData['occupation'] === 'Родитель' ? 'selected' : '' ?>>Родитель</option>
            <option value="Учащийся/учащаяся" <?= $userData['occupation'] === 'Учащийся/учащаяся' ? 'selected' : '' ?>>Учащийся</option>
            <option value="Другое" <?= $userData['occupation'] === 'Другое' ? 'selected' : '' ?>>Другое</option>
        </select>
</div>


    <?= renderButtonBlock("Сохранить изменения", "Отмена", "/user/profile.php"); ?>

</form>
</div>
