<?php
function displayCommentsTable($connection, $comments)
{
  if (!empty($comments)) {
    echo '<div class="table-responsive">';
    echo '<table class="table table-hover table-sm table-bordered align-middle" style="font-size: 13px;">';
    echo '<thead class="table-dark">';
    echo "<tr>";
    echo '<th class="text-center">ID</th>';
    echo '<th class="text-center">Comment</th>';
    echo '<th class="text-center">Date</th>';
    echo '<th class="text-center">UserID</th>';
    echo '<th class="text-center">EntityType</th>';
    echo '<th class="text-center">EntityID</th>';
    echo '<th class="text-center">ParentID</th>';
    echo '<th class="text-center">Edit</th>';
    echo '<th class="text-center">Delete</th>';
    echo '<th class="text-center">Remove Parent</th>';
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach ($comments as $comment) {
      // Check if the comment has a parent ID not equal to 0
      $isParentComment = $comment["parent_id"] !== 0;

      // Add a conditional class for rows with parent ID not equal to 0
      $rowClass = $isParentComment ? "table-warning" : "";

      $url = getEntityNameById(
        $connection,
        $comment["entity_type"],
        $comment["id_entity"]
      );
      echo '<tr class="' . $rowClass . '">';
      if ($comment["entity_type"] === "school") {
        echo '<td class="text-center"><a href="/school/' .
          $comment["id_entity"] .
          '" target="_blank">' .
          $comment["id"] .
          "</a></td>";
      } else {
        echo '<td class="text-center"><a href="' .
          $url .
          '" target="_blank">' .
          $comment["id"] .
          "</a></td>";
      }
      echo "<td>" . nl2br($comment["comment_text"]) . "</td>";
      echo '<td class="text-center">' .
        date("d/m/y g:ia", strtotime($comment["date"])) .
        "</td>";
      if ($comment["user_id"] != 0) {
        echo '<td class="text-center"><a href="/pages/dashboard/users-dashboard/user.php?id=' . $comment["user_id"] . '" target="_blank">' . $comment["user_id"] . '</a></td>';
      } else {
        echo '<td class="text-center">' . $comment["user_id"] . '</td>';
      }
      echo '<td class="text-center">' . $comment["entity_type"] . "</td>";
      echo '<td class="text-center">' . $comment["id_entity"] . "</td>";
      echo '<td class="text-center">' . $comment["parent_id"] . "</td>";

      echo '<td class="text-center">
            <a href="/pages/dashboard/comments-dashboard/comments-view/edit-comment/admin-comments-edit.php?action=edit&comment_id=' .
        $comment["id"] .
        '">
                <i class="fas fa-pencil-alt icon" data-action="edit"></i>
            </a>
          </td>';

      echo '<td class="text-center">
          <a href="?action=delete&comment_id=' . $comment["id"] . '" onclick="return confirmDelete(' . $comment["id"] . ', \'' . addslashes($comment["comment_text"]) . '\');">
              <i class="fas fa-trash icon" data-action="delete" style="color: red;"></i>
          </a>
        </td>';

      echo '<td class="text-center">';
      if ($comment["parent_id"] > 0) {
        echo '<a href="?action=removeParent&comment_id=' . $comment["id"] . '" onclick="return confirmRemoveParent(\'' . addslashes($comment["comment_text"]) . '\');">
            <i class="fas fa-arrow-up icon" data-action="removeParent"></i>
          </a>';
      }
      echo "</td>";

      echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";
  } else {
    echo "No comments found";
  }
} ?>

<script>
  function confirmDelete(commentId, commentText) {
    return confirm("Are you sure you want to delete this comment? ID: " + commentId + "\n\nComment: " + commentText);
  }

  function confirmRemoveParent(commentText) {
    return confirm("Are you sure you want to remove the parent of this comment?\n\nComment: " + commentText);
  }
</script>