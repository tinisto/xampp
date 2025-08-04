<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
?>

<div class="col-md-8">
  <div id="here">
    <h2>Комментарии</h2>

    <?php
    $id_entity = isset($_GET['id_entity']) ? $_GET['id_entity'] : $id_entity;
    $entity_type = isset($_GET['entity_type']) ? $_GET['entity_type'] : $entity_type;
    $page = isset($_GET['page']) ? $_GET['page'] : 1;

    include_once 'comment_functions.php';

    // Check if the user is logged in
    $isUserLoggedIn = isset($_SESSION['email']);

    // Assuming 5 comments per page
    $commentsPerPage = 5;
    $offset = ($page - 1) * $commentsPerPage;

    // Query to count total comments
    $queryCount = "SELECT COUNT(*) AS total FROM comments WHERE id_entity=? AND entity_type=? AND parent_id=0";
    $stmtCount = mysqli_prepare($connection, $queryCount);
    mysqli_stmt_bind_param($stmtCount, "is", $id_entity, $entity_type);
    mysqli_stmt_execute($stmtCount);
    $resultCount = mysqli_stmt_get_result($stmtCount);
    $totalComments = mysqli_fetch_assoc($resultCount)['total'];

    // Query to fetch comments
    $queryComments = "SELECT
        comments.id,
        comments.id_entity,
        comments.user_id,
        COALESCE(users.avatar_url, 'default_avatar.jpg') AS avatar,
        comments.comment_text,
        comments.date,
        COALESCE(users.timezone, 'UTC') AS user_timezone,
        comments.author_of_comment
    FROM
        comments
    LEFT JOIN users ON comments.user_id = users.id
    WHERE
        comments.id_entity=? AND comments.entity_type=? AND parent_id=0
    ORDER BY
        comments.date DESC LIMIT ?, ?;
    ";

    $stmtComments = mysqli_prepare($connection, $queryComments);

    if (!$stmtComments) {
      header("Location: /error");
      exit();    }

    // Perform type casting
    $id_entity = (int)$id_entity;
    $entity_type = (string)$entity_type;
    $offset = (int)$offset;
    $commentsPerPage = (int)$commentsPerPage;

    // Bind parameters based on entity type and ID
    mysqli_stmt_bind_param($stmtComments, "isii", $id_entity, $entity_type, $offset, $commentsPerPage);

    mysqli_stmt_execute($stmtComments);

    if ($stmtComments->errno) {
      header("Location: /error");
      exit();    }

    $resultComments = mysqli_stmt_get_result($stmtComments);

    if (!$resultComments) {
      header("Location: /error");
      exit();    }

    // Output additional comments with the dynamically determined container ID
    while ($comment = mysqli_fetch_assoc($resultComments)) {
      if ($comment !== null) {
        // Dynamically generate a unique container ID for each comment
        $containerID = 'commentContainer_page' . $page . '_comment' . $comment['id'];
    ?>

        <div class="card mb-2 vasya" id="<?php echo $containerID; ?>">
          <div class="d-flex flex-row align-items-center">
            <div class="hiddenContent d-none flex-grow-1 text-center custom-background">
              <span class="fs-6 fw-lighter fst-italic"><small>Комментарии скрыты</small></span>
            </div>
            <div class="shownContent m-2 d-flex flex-grow-1">
              <!-- Display avatar if available -->
              <?php if (isset($comment['avatar']) && $comment['avatar']): ?>
                <img src="../images/avatars/<?php echo $comment['avatar']; ?>" alt="Avatar" class="rounded-circle me-2"
                  width="30" height="30">
              <?php else: ?>
                <!-- Default placeholder avatar if no avatar is available -->
                <img src="../images/avatars/default_avatar.jpg" alt="Default Avatar" class="rounded-circle me-2" width="30"
                  height="30">
              <?php endif; ?>
              <div class="flex-grow-1">

                <?php
                echo '<div class="custom-comment-author">';
                echo '<strong>';

                // Check if the user_id is 0
                if ($comment['user_id'] == 0) {
                  $userNames['name'] = $comment['author_of_comment'];
                  echo $userNames['name'];
                } else {
                  // Fetch user's first and last names based on user_id
                  $userNames = getUserNames($comment['user_id'], $connection);
                  echo $userNames['first_name'] . ' ' . $userNames['last_name'];
                }

                echo '</strong><br>';

                $commentText = $comment['comment_text'];
                if ($commentText !== null) {
                  // Apply same XSS protection as posts/news
                  $allowed_tags = '<p><br><strong><b><em><i><u><a><ul><ol><li><blockquote><h1><h2><h3><h4><h5><h6><span><div>';
                  $safeText = strip_tags($commentText, $allowed_tags);
                  echo '<span class="text-break">' . nl2br($safeText) . '</span>';
                } else {
                  echo 'Comment text is empty.';
                }
                echo '</div>';

                $timestamp = isset($comment['date']) ? strtotime($comment['date']) : null;
                ?>

                <?php include 'comment-reply-block-file.php'; ?>

                <!-- Reply Form (Initially Hidden) -->
                <div id="replyForm_<?php echo $comment['id']; ?>" class="mt-3" style="display: none;">
                  <?php include 'comment_form_reply.php'; ?>
                </div>
                <?php
                include 'load_child_comments.php';
                ?>
              </div>
            </div>

            <i class="toggle-icon fas fa-eye-slash toggleCard p-1" style="font-size: 12px;"></i>

          </div>
        </div>
    <?php
      }
    }
    ?>
  </div>

  <?php if ($totalComments > $commentsPerPage): ?>
    <button class="custom-button" id="loadMoreButton" data-id-entity="<?= $id_entity; ?>" data-entity-type="<?= $entity_type; ?>">
      Показать еще комментарии
    </button>
  <?php endif; ?>
</div>

<script src="/comments/JS/toggleReplyForm.js"></script>
<script src="/comments/JS/loadMoreComments.js"></script>
<script src="/comments/JS/tooltipsTime.js"></script>
<script src="/comments/JS/toggleComments.js"></script>