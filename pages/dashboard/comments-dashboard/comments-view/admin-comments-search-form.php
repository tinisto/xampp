<?php
function displaySearchForm($searchEntityType, $searchEntityID)
{
    ?>
  <!-- Display search form -->
  <div class="col-md-6 mx-auto mb-3">
    <form class="form-inline" method="get" action="">
      <div class="d-flex flex-row justify-content-between align-items-center gap-3">
        <div class="input-group input-group-sm">
          <label class="input-group-text" for="entity_type">EntityType</label>
          <select class="form-select" name="entity_type" id="entity_type">
            <option value="" <?= $searchEntityType === ""
                ? "selected"
                : "" ?>>Choose</option>
            <option value="post" <?= $searchEntityType === "post"
                ? "selected"
                : "" ?>>Post</option>
            <option value="vpo" <?= $searchEntityType === "vpo"
                ? "selected"
                : "" ?>>High Edu</option>
            <option value="spo" <?= $searchEntityType === "spo"
                ? "selected"
                : "" ?>>Middle Edu</option>
            <!-- Add more options as needed -->
          </select>
        </div>

        <div class="input-group input-group-sm">
          <span class="input-group-text" id="inputGroup-sizing-sm">EntityID</span>
          <input type="text" class="form-control" name="entity_id" id="entity_id"
            value="<?= htmlspecialchars(
                $searchEntityID
            ) ?>" placeholder="EntityType">
        </div>
        <button type="submit" class="btn btn-primary btn-sm" name="search" value="true">Search</button>
        <a href="/dashboard/admin-comments.php" class="btn btn-secondary btn-sm">Reset</a>
      </div>
    </form>
  </div>
  <?php
}
?>
