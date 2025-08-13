<?php
include $_SERVER["DOCUMENT_ROOT"] . "/includes/functions/get_avatar.php";

$linkBootstrapHover = "text-white link-offset-2 link-offset-3-hover link-underline-light link-underline-opacity-0 link-underline-opacity-75-hover";

// Function to fetch categories from the database
function fetchCategories($connection, $selectedCategories)
{
  // Check if connection is valid
  if (!$connection || $connection->connect_error) {
    return [];
  }
  
  $whereClause = implode(',', $selectedCategories);
  $query = "SELECT id_category, url_category, title_category FROM categories WHERE id_category IN ($whereClause) ORDER BY title_category ASC";

  $result = mysqli_query($connection, $query);

  if ($result) {
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
  } else {
    return [];
  }
}

// Function to generate navigation items
function generateNavItems($categories, $isDropdown = false)
{
  $navItems = '';

  foreach ($categories as $category) {
    $categoryId = $category['id_category'];
    $categoryUrl = $category['url_category'];
    $categoryTitle = $category['title_category'];

    $liClass = ($categoryId == 1) ? 'selected' : '';
    $itemClass = $isDropdown ? 'dropdown-item' : 'text-white link-offset-2 link-offset-3-hover link-underline-light link-underline-opacity-0 link-underline-opacity-75-hover';

    if ($isDropdown) {
      $navItems .= "<li><small><a class='dropdown-item' href='/category/$categoryUrl'>$categoryTitle</a></small></li>";
    } else {
      $navItems .= "<li class='nav-item mx-auto py-1 px-3 $liClass'><small><a class='$itemClass' href='/category/$categoryUrl'>$categoryTitle</a></small></li>";
    }
  }

  return $navItems;
}

$selectedCategoriesNav = [1, 6];
$selectedCategoriesDropdown = [2, 3, 5, 7, 8, 9, 10, 11, 12, 13, 14, 15, 18];

$categoriesNav = fetchCategories($connection, $selectedCategoriesNav);
$categoriesDropdown = fetchCategories($connection, $selectedCategoriesDropdown);

// Function to fetch categories from the database
function fetchCategoriesNews($connection)
{
  // Check if connection is valid
  if (!$connection || $connection->connect_error) {
    return [];
  }
  
  $queryNews = "SELECT id_category_news, url_category_news, title_category_news FROM news_categories ORDER BY title_category_news ASC";
  $resultNews = mysqli_query($connection, $queryNews);

  if ($resultNews) {
    return mysqli_fetch_all($resultNews, MYSQLI_ASSOC);
  } else {
    return [];
  }
}

function generateNewsItems($categories_news)
{

  $newsItems = '';
  foreach ($categories_news as $category_news) {
    $categoryNewsId = $category_news['id_category_news'];
    $categoryNewsUrl = $category_news['url_category_news'];
    $categoryNewsTitle = $category_news['title_category_news'];
    $newsItems .= "<li><small><a class='dropdown-item' href='/category-news/$categoryNewsUrl'>$categoryNewsTitle</a></small></li>";
  }
  return $newsItems;
}
?>

<nav class="navbar navbar-dark navbar-expand-lg bg-dark border-bottom border-white">
  <div class="container-fluid">
    <a class="navbar-brand" href="/">
      <img src="/images/logo.png" alt="11-классники" width="35" style="margin-left: 20px;">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" data-bs-theme="dark">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav mx-auto align-items-center">
        <?php echo generateNavItems($categoriesNav); ?>

        <li class="nav-item mx-auto py-1 px-3">
          <a class="<?php echo $linkBootstrapHover; ?>" href="/vpo-all-regions"><small>ВУЗы</small></a>
        </li>
        <li class="nav-item mx-auto py-1 px-3">
          <a class="<?php echo $linkBootstrapHover; ?>" href="/spo-all-regions"><small>ССУЗы</small></a>
        </li>
        <li class="nav-item mx-auto py-1 px-3">
          <a class="<?php echo $linkBootstrapHover; ?>" href="/schools-all-regions"><small>Школы</a></small>
        </li>

        <li class="nav-item mx-auto py-1 px-3 dropdown">
          <?php echo '<a class="' . $linkBootstrapHover . ' dropdown-toggle" href="#" role="button" id="categoryDropdownNews" data-bs-toggle="dropdown" aria-expanded="false">'; ?>
          <small>Новости</small>
          </a>

          <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="categoryDropdownNews">
            <?php
            $categories_news = fetchCategoriesNews($connection);
            echo generateNewsItems($categories_news);
            ?>
          </ul>
        </li>

        <li class="nav-item mx-auto py-1 px-3 dropdown">
          <?php echo '<a class="' . $linkBootstrapHover . ' dropdown-toggle" href="#" role="button" id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">'; ?>
          <small>Рубрики</small>
          </a>
          <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="categoryDropdown">
            <?php echo generateNavItems($categoriesDropdown, true); ?>
          </ul>
        </li>
        <li class="nav-item mx-auto py-1 px-3">
          <a class="<?php echo $linkBootstrapHover; ?>" href="/search">
            <i class="fas fa-search"></i>
          </a>
        </li>


        <?php
        // Define test configurations directly in the header file
        $testsConfig = [
          'iq-test' => [
            'title' => 'Тест на IQ',
            'url' => '/iq-test',
          ],
          'aptitude-test' => [
            'title' => 'Тест на профпригодность',
            'url' => '/aptitude-test',
          ],
          // Add more tests here as needed
        ];
        ?>

        <li class="nav-item mx-auto py-1 px-3 dropdown">
          <?php echo '<a class="' . $linkBootstrapHover . ' dropdown-toggle" href="#" role="button" id="Tests" data-bs-toggle="dropdown" aria-expanded="false">'; ?>
          <small>Тесты</small>
          </a>

          <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="Tests">
            <?php foreach ($testsConfig as $test): ?>
              <li class="nav-item mx-auto py-1 px-3">
                <a class="<?php echo $linkBootstrapHover; ?>" href="<?php echo htmlspecialchars($test['url']); ?>">
                  <small><?php echo htmlspecialchars($test['title']); ?></small>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </li>

      </ul>





      <ul class="navbar-nav align-items-center">
        <?php
        if (isset($_SESSION['email'])) {
          $email = $_SESSION['email'];
          $checkUserQuery = "SELECT * FROM users WHERE email=?";
          $stmtCheckUser = mysqli_prepare($connection, $checkUserQuery);

          if ($stmtCheckUser) {
            mysqli_stmt_bind_param($stmtCheckUser, "s", $email);
            mysqli_stmt_execute($stmtCheckUser);
            $resultCheckUser = mysqli_stmt_get_result($stmtCheckUser);


            if ($resultCheckUser && mysqli_num_rows($resultCheckUser) > 0) {
              if ($_SESSION['role'] == 'admin') {
                echo '<li class="nav-item mx-auto py-1 px-3"><a href="/dashboard" class="<?php ' . $linkBootstrapHover . ' ?>"><small>Dashboard</small></a></li>';
              }
              $user = mysqli_fetch_assoc($resultCheckUser);

              $occupation = $user['occupation'];
              echo '<li class="nav-item dropdown mx-auto py-1 px-3">';
              echo '<div class="btn-group dropstart">';
              echo '<button type="button" class="btn btn-dark" data-bs-toggle="dropdown" aria-expanded="false">';


              // Get the user's avatar URL, fetch the full avatar path, and display the image with styling
              $avatarUrl = $user['avatar'];
              $avatarPath = getAvatar($avatarUrl);
              echo '<img src="' . htmlspecialchars($avatarPath) . '" alt="Avatar" style="width: 30px; height: 30px; border-radius: 50%;">';

              echo '</button>';
              echo '<ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="userDropdown">';
              echo '<li><small><a class="dropdown-item" href="/account">Аккаунт</a></small></li>';
              echo '<li><small><a class="dropdown-item" href="/pages/logout/logout.php"><i class="fas fa-sign-out-alt"></i> Выход</a></small></li>';
              echo '</ul>';
              echo '</div>';
              echo '</li>';
            } else {
              session_destroy();
              header('Location: /login');
              exit();
            }
          }

          mysqli_stmt_close($stmtCheckUser);
        } else {
          echo '<li class="nav-item mx-auto py-1 px-3">
          <a class=" ' . $linkBootstrapHover . ' ?>" href="/login">
          <i class="fas fa-sign-in-alt"></i><small class="ms-2">Вход</small>
          </a>
      </li>';
        }
        ?>
      </ul>
    </div>
  </div>
</nav>