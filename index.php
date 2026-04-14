<?php
    include 'config.php';

    session_start();

    $sql = "SELECT * FROM posts ORDER BY created_at DESC";
    $query = mysqli_query($con, $sql);

    if(isset($_SESSION['username']) && isset($_POST['post'])) {
        $description = $_POST['description'];
        $author_id = $_SESSION['user_id'];
        $fullname = $_SESSION['fullname'];
        $username = $_SESSION['username'];

        $sql = "INSERT INTO posts (description, author_id, fullname, username) VALUES ('$description', '$author_id', '$fullname', '$username')";
        if(mysqli_query($con, $sql)) {
            header("Location: index.php");
        }
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
    <div class="container" id="post-modal">
      <div id="close-button">x</div>
      <h2 id="post-modal-title">Edit Post</h2>
      <form id="postForm">
        <div class="form-group">
          <label for="post-modal-description">Post *</label>
          <textarea
            name="description"
            id="post-modal-description"
            maxlength="5000"
            placeholder="Write something..."
          ></textarea>
          <div class="error" id="descError"></div>
        </div>

        <button type="submit" id="create-post-button">Save</button>
      </form>
    </div>

    <header>
      <ul>
        <li>
          <a href="index.php">DevsHub</a>
        </li>
        <li>
          <ul>
            <li>
              <div class="search-bar">
                <input type="text" placeholder="Search..." />
                <img src="./images/search-icon.png" alt="Search Icon" />
              </div>
            </li>
            <?php if(isset($_SESSION['username'])) { ?>
            <li class="header-auth">
              <span class="header-username"><?php echo $_SESSION['fullname']; ?></span>
              <a class="header-logout" href="logout.php">Log out</a>
              <a
                href="./profile.php"
                class="profile-icon"
                aria-label="Your profile"
              >
                <img src="./images/profile-icon.png" alt="" />
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
              <li><a href="./profile.php#my-posts">My posts</a></li>
              <li><a href="./profile.php">Profile</a></li>
          <?php } ?>
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
      <form method="post" class="quick-post-form">
        <div class="create-post">
          <div class="profile-icon">
            <img src="./images/profile-icon.png" alt="Profile Icon" />
          </div>
          <div class="post-input">
            <input
              type="text"
              name="description"
              id="post-description"
              maxlength="5000"
              required
              placeholder="Discuss about dev problems and solutions"
            />
            <button name="post" type="submit" id="post-button">Post</button>
          </div>
        </div>
      </form>
      <?php } ?>
      <div class="posts-list" id="posts-list">
        <ul id="post">
          <?php
            if(mysqli_num_rows($query) > 0) {
                while($row = mysqli_fetch_assoc($query)) {
          ?>
          <li>
            <div class="post-item">
              <div class="post-header">
                <div class="post-header-left">
                  <div class="profile-icon">
                    <img src="./images/profile-icon.png" alt="Profile Icon" />
                  </div>
                  <div class="post-author"><?php echo $row['fullname']; ?></div>
                </div>
                <div class="post-header-right">
                  <span class="post-time"><?php echo $row['created_at']; ?></span>
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
                      >
                        Edit
                      </button>
                      <form
                        class="post-delete-form"
                        onsubmit="return confirm('Delete this post?');"
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
          </li>
          <?php
                }
            } else {
                echo "<li>No posts found.</li>";
            }
          ?>
        </ul>
      </div>
    </section>

    <script src="script.js"></script>
  </body>
</html>
