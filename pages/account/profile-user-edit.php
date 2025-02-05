<div class="d-flex justify-content-center">
    <!-- Button to toggle profile details -->
    <button class="btn btn-secondary btn-sm mb-3" type="button" id="toggleDetailsBtnProfile">
        Изменить данные профиля
    </button>
</div>

<div id="profileDetails" style="display: none;" class="mb-5">
    <div class="row">
        <!-- Vertical Tabs -->
        <div class="col-md-3">
            <ul class="nav flex-column nav-pills mb-3" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-dark active d-inline-block" id="profile-tab" data-bs-toggle="pill" href="#profile-section" role="tab" aria-controls="profile-section" aria-selected="true">Редактировать данные</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-dark d-inline-block" id="settings-tab" data-bs-toggle="pill" href="#settings-section" role="tab" aria-controls="settings-section" aria-selected="false">Сменить пароль</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-dark d-inline-block" id="avatar-tab" data-bs-toggle="pill" href="#avatar-section" role="tab" aria-controls="avatar-section" aria-selected="false">Аватар</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-dark d-inline-block" id="delete-tab" data-bs-toggle="pill" href="#delete-section" role="tab" aria-controls="delete-section" aria-selected="false">Удалить аккаунт</a>
                </li>
            </ul>
        </div>

        <!-- Tab Content -->
        <div class="col-md-6">
            <div class="tab-content" id="profileTabsContent">
                <div class="tab-pane fade show active" id="profile-section" role="tabpanel" aria-labelledby="profile-tab">
                    <?php include '../personal-data/personal-data-change.php'; ?>
                </div>
                <div class="tab-pane fade" id="settings-section" role="tabpanel" aria-labelledby="settings-tab">
                    <?php include '../password-change/password-change.php'; ?>
                </div>
                <div class="tab-pane fade" id="avatar-section" role="tabpanel" aria-labelledby="avatar-tab">
                    <?php include '../avatar/avatar.php'; ?>
                </div>
                <div class="tab-pane fade" id="delete-section" role="tabpanel" aria-labelledby="delete-tab">
                    <?php include '../delete-account/delete-account.php'; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Script to toggle visibility and close on click outside -->
<script>
    document.getElementById('toggleDetailsBtnProfile').addEventListener('click', function(event) {
        var detailsDiv = document.getElementById('profileDetails');
        // Toggle the visibility of the div
        if (detailsDiv.style.display === 'none' || detailsDiv.style.display === '') {
            detailsDiv.style.display = 'block'; // Show the details
        } else {
            detailsDiv.style.display = 'none'; // Hide the details
        }
        event.stopPropagation(); // Prevent the click from propagating to the document
    });

    // Close the div when clicking outside
    document.addEventListener('click', function(event) {
        var detailsDiv = document.getElementById('profileDetails');
        var toggleButton = document.getElementById('toggleDetailsBtnProfile');

        // Check if the clicked area is outside the button and the div
        if (!toggleButton.contains(event.target) && !detailsDiv.contains(event.target)) {
            detailsDiv.style.display = 'none'; // Close the details
        }
    });
</script>