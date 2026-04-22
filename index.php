<?php
    include 'config.php';
    include './utils/generateUID.php';
    include './utils/timeAgo.php';

    session_start();

    $post_errors = [];
    $post_description = '';

    $search_query = trim($_GET['search'] ?? '');

    if($search_query !== '') {
        $safe_search = mysqli_real_escape_string($con, $search_query);
        $sql = "SELECT posts.*, users.profile_image FROM posts LEFT JOIN users ON posts.author_id = users.id WHERE posts.description LIKE '%$safe_search%' OR posts.fullname LIKE '%$safe_search%' OR posts.username LIKE '%$safe_search%' ORDER BY posts.created_at DESC";
    } else {
        $sql = "SELECT posts.*, users.profile_image FROM posts LEFT JOIN users ON posts.author_id = users.id ORDER BY posts.created_at DESC";
    }
    $query = mysqli_query($con, $sql);
    $results_count = $query ? mysqli_num_rows($query) : 0;

    if(isset($_SESSION['username']) && isset($_POST['post'])) {
        $post_description = trim($_POST['description'] ?? '');

        if($post_description === '') {
            $post_errors[] = 'Post content cannot be empty.';
        } elseif(strlen($post_description) < 2) {
            $post_errors[] = 'Post content must be at least 2 characters long.';
        } elseif(strlen($post_description) > 5000) {
            $post_errors[] = 'Post content cannot exceed 5000 characters.';
        }

        if(empty($post_errors)) {
            $description = mysqli_real_escape_string($con, $post_description);
            $author_id = $_SESSION['user_id'];
            $fullname = mysqli_real_escape_string($con, $_SESSION['fullname']);
            $username = mysqli_real_escape_string($con, $_SESSION['username']);
            $id = generateUniqueInt();

            $sql = "INSERT INTO posts (id, description, author_id, fullname, username) VALUES ($id, '$description', '$author_id', '$fullname', '$username')";

            if(mysqli_query($con, $sql)) {
                $sql2 = "SELECT * FROM friends WHERE fr_sender_id=" . $_SESSION['user_id'] . " OR fr_receiver_id=" . $_SESSION['user_id'];
                $query2 = mysqli_query($con, $sql2);

                if($query2 && mysqli_num_rows($query2) > 0){
                  while($row2 = mysqli_fetch_assoc($query2)){
                    $friend_id = $row2['fr_sender_id'] == $_SESSION['user_id'] ? $row2['fr_receiver_id'] : $row2['fr_sender_id'];
                    $sql3 = "INSERT INTO notifications (notification_type, notification_receiver_id, notification_sender_id, notification_message, post_id) VALUES ('post', '$friend_id', '$author_id', 'posted something new.', $id)";
                    mysqli_query($con, $sql3);
                  }
                }
                header("Location: index.php");
                exit();
            } else {
                $post_errors[] = 'Could not publish the post. Please try again.';
            }
        }
    }

    $unread_count = 0;

    $sql2 = "SELECT * FROM notifications WHERE notification_receiver_id='" . $_SESSION['user_id'] . "' AND is_read=0";
    $query2 = mysqli_query($con, $sql2);

    if(mysqli_num_rows($query2) > 0) {
      $unread_count = mysqli_num_rows($query2);
    }
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>DevsHub</title>
    <link rel="stylesheet" href="style.css" />
  </head>

  <body>
    <header>
      <ul>
        <li>
          <a href="index.php">DevsHub</a>
        </li>
        <li>
          <ul>
            <li>
              <form class="search-bar" action="index.php" method="get" role="search">
                <input
                  type="text"
                  name="search"
                  id="search-input"
                  placeholder="Search posts..."
                  value="<?php echo htmlspecialchars($search_query); ?>"
                  autocomplete="off"
                />
                <button type="submit" class="search-submit" aria-label="Search">
                  <img src="./images/search-icon.png" alt="" />
                </button>
              </form>
            </li>
            <?php if(isset($_SESSION['username'])) { ?>
            <li class="header-auth">
              <span class="header-username"><?php echo $_SESSION['fullname']; ?></span>
              <a class="header-logout" href="logout.php">Log out</a>
              <a
                href="./profile.php?id=<?php echo $_SESSION['user_id']; ?>"
                class="profile-icon"
                aria-label="Your profile"
              >
                <img src="<?php echo profile_image_url($_SESSION['user']['profile_image'] ?? ''); ?>" alt="" />
              </a>
            </li>
            <?php } else { ?>
            <li class="header-auth">
              <a class="header-login" href="login.php">Log in</a>
              <a class="header-register" href="register.php">Register</a>
            </li>
            <?php } ?>
          </ul>
        </li>
      </ul>
    </header>
    <aside class="left-sidebar">
      <nav>
        <ul>
          <li><a href="/" class="sidebar-link-active">Home</a></li>
          <?php
            if(isset($_SESSION['username'])) { ?>
              <li><a href="./profile.php?id=<?php echo $_SESSION['user_id']; ?>#my-posts">My posts</a></li>
              <li><a href="./profile.php?id=<?php echo $_SESSION['user_id']; ?>">Profile</a></li>
              <li><a href="./friends.php?id=<?php echo $_SESSION['user_id']; ?>">Friends</a></li>
              <?php } ?>
              <li><a href="./notifications.php">Notifications <?php if($unread_count > 0) { echo "<span class='unread-count'>{$unread_count}</span>"; } ?></a></li>
        </ul>
      </nav>
    </aside>
    <aside class="right-sidebar">
      <section class="messages">
        <h2>Messages</h2>
        <ul>
          <li class="message-item active">
            <div class="profile-icon">
              <img src="./images/profile-icon.png" alt="Profile Icon" />
            </div>
            <div class="message-content">
              <h4>John Doe</h4>
              <p>Hey, how are you?</p>
            </div>
          </li>
          <li class="message-item">
            <div class="profile-icon">
              <img src="./images/profile-icon.png" alt="Profile Icon" />
            </div>
            <div class="message-content">
              <h4>John Doe</h4>
              <p>Hey, how are you?</p>
            </div>
          </li>
          <li class="message-item">
            <div class="profile-icon">
              <img src="./images/profile-icon.png" alt="Profile Icon" />
            </div>
            <div class="message-content">
              <h4>John Doe</h4>
              <p>Hey, how are you?</p>
            </div>
          </li>
          <li class="message-item">
            <div class="profile-icon">
              <img src="./images/profile-icon.png" alt="Profile Icon" />
            </div>
            <div class="message-content">
              <h4>John Doe</h4>
              <p>Hey, how are you?</p>
            </div>
          </li>
        </ul>
      </section>
    </aside>
    <section class="posts">
      <?php if(isset($_SESSION['username'])) { ?>
      <?php if(!empty($post_errors)): ?>
        <div class="form-errors <?php echo count($post_errors) === 1 ? 'form-errors-single' : ''; ?>">
          <?php if(count($post_errors) > 1): ?>
            <span class="form-errors-title">Please fix the following:</span>
            <ul>
              <?php foreach($post_errors as $err): ?>
                <li><?php echo htmlspecialchars($err); ?></li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <?php echo htmlspecialchars($post_errors[0]); ?>
          <?php endif; ?>
        </div>
      <?php endif; ?>
      <form method="post" class="quick-post-form" novalidate>
        <div class="create-post">
          <div class="profile-icon">
            <img src="<?php echo profile_image_url($_SESSION['user']['profile_image'] ?? ''); ?>" alt="Profile Icon" />
          </div>
          <div class="post-input">
            <input
              type="text"
              name="description"
              id="post-description"
              maxlength="5000"
              value="<?php echo htmlspecialchars($post_description); ?>"
              placeholder="Discuss about dev problems and solutions"
              class="<?php echo !empty($post_errors) ? 'field-invalid' : ''; ?>"
            />
            <button name="post" type="submit" id="post-button">Post</button>
          </div>
        </div>
      </form>
      <?php } ?>
      <?php if($search_query !== ''): ?>
        <div class="search-indicator">
          <div>
            Showing results for <strong>&ldquo;<?php echo htmlspecialchars($search_query); ?>&rdquo;</strong>
            <span class="search-count">(<?php echo $results_count; ?> <?php echo $results_count === 1 ? 'post' : 'posts'; ?>)</span>
          </div>
          <a href="index.php" class="search-clear">Clear</a>
        </div>
      <?php endif; ?>
      <div class="posts-list" id="posts-list">
        <ul id="post">
          <?php
            if($query && mysqli_num_rows($query) > 0) {
                while($row = mysqli_fetch_assoc($query)) {
          ?>
          <li id="<?php echo $row['id'] ?>">
            <div class="post-item">
              <div class="post-header">
                <div class="post-header-left">
                  <div class="profile-icon">
                    <img src="<?php echo profile_image_url($row['profile_image'] ?? ''); ?>" alt="Profile Icon" />
                  </div>
                  <a href="profile.php?id=<?php echo $row['author_id']; ?>" class="post-author"><?php echo $row['fullname']; ?></a>
                </div>
                <div class="post-header-right">
                  <span class="post-time" title="<?php echo htmlspecialchars($row['created_at']); ?>"><?php echo timeAgo($row['created_at'], $con); ?></span>
                  <details class="post-menu">
                    <summary
                      class="post-menu-summary"
                      aria-label="Post options"
                      id="post-option"
                    ></summary>
                    <div class="post-menu-panel" id="post-menu-panel">
                      <button
                        type="button"
                        class="post-edit"
                        id="edit-post-btn"
                        onClick="openModalForEdit(<?php echo $row['id']; ?>, '<?php echo addslashes($row['description']); ?>')"
                      >
                        Edit
                      </button>
                      <form
                        class="post-delete-form"
                        action="delete_post.php?id=<?php echo $row['id']; ?>"
                        method="post"
                        onsubmit="return confirm('Are you sure you want to delete this post?');"
                      >
                        <button type="submit" class="post-delete">
                          Delete
                        </button>
                      </form>
                    </div>
                  </details>
                </div>
              </div>
              <p class="post-content"><?php echo $row['description']; ?></p>
            </div>
            <?php if($_SESSION['user_id'] == $row['author_id']) { ?>
            <div class="container" id="post-modal">
              <div id="close-button">x</div>
              <h2 id="post-modal-title">Edit Post</h2>
              <form id="postForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <?php
                  if(isset($_SESSION['username']) && isset($_POST['edit_post'])) {
                    $edit_description = trim($_POST['description'] ?? '');
                    $post_id = $row['id'];

                    if($edit_description !== '' && strlen($edit_description) <= 5000) {
                      $safe_description = mysqli_real_escape_string($con, $edit_description);
                      $sql = "UPDATE posts SET description='$safe_description' WHERE id=$post_id";

                      if(mysqli_query($con, $sql)) {
                        header("Location: index.php");
                        exit();
                      }
                    }
                  }
                ?>
                <div class="form-group">
                  <label for="post-modal-description">Post *</label>
                  <textarea
                    name="description"
                    id="post-modal-description"
                    maxlength="5000"
                    placeholder="Write something..."
                  ><?php echo $row['description']; ?></textarea>
                  <div class="error" id="descError"></div>
                </div>

                <button type="submit" name="edit_post" id="create-post-button">Save</button>
              </form>
            </div>
            <?php } ?>
          </li>
          <?php
                }
            } else {
                if($search_query !== '') {
                    echo "<li class='no-posts'>No posts match your search.</li>";
                } else {
                    echo "<li class='no-posts'>No posts found.</li>";
                }
            }
          ?>
        </ul>
      </div>
    </section>

    <script src="script.js"></script>
  </body>
</html>
