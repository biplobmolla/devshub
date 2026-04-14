<?php
    include "config.php";

    session_start();

    $id = $_GET['id'];
    $row = null;
    $posts_count = 0;

    if($id){
      $sql = "SELECT * FROM users WHERE id='$id'";
      $query = mysqli_query($con, $sql);
      $row = mysqli_fetch_assoc($query);

      $sql2 = "SELECT * FROM posts WHERE author_id='$id' ORDER BY created_at DESC";
      $query2 = mysqli_query($con, $sql2);
      $posts_count = mysqli_num_rows($query2);

    }else if(isset($_SESSION['username'])) {
      $row = $_SESSION['user'];
            $sql2 = "SELECT * FROM posts WHERE author_id='" . $_SESSION['user_id'] . "' ORDER BY created_at DESC";
      $query2 = mysqli_query($con, $sql2);
      $posts_count = mysqli_num_rows($query2);
    }else{
      header("Location: index.php");
    }

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Profile</title>
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
            <li class="header-auth">
              <span class="header-username"><?php echo $_SESSION['fullname']; ?></span>
              <a class="header-logout" href="logout.php">Log out</a>
              <a href="#" class="profile-icon" aria-label="Your profile">
                <img src="./images/profile-icon.png" alt="" />
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </header>
    <div class="upper-section">
      <div class="profile-user-icon">
        <img src="./images/profile-icon.png" alt="Profile Icon" />
      </div>
      <div>
        <h1 class="profile-name"><?php echo $row['fullname']; ?></h1>
        <p class="profile-bio">Web developer and tech enthusiast.</p>
      </div>
      <div class="summary">
        <div class="friends-summary">3 friends</div>
        <div class="posts-summary"><?php echo $posts_count; ?> posts</div>
      </div>
      <?php if($id != $_SESSION['user_id'] && $id != null) { ?>
      <div class="add-friend">
        <button>Add Friend
          <i class="fa-solid fa-plus"></i>
        </button>
      </div>
      <?php } else { ?>
      <div class="edit-profile">
        <button>Edit Profile <i class="fa-solid fa-pencil"></i></button>
      </div>
      <?php } ?>

    </div>
    <div class="my-posts" id="my-posts">
      <h4 class="my-posts-title"><?php if($id != $_SESSION['user_id'] && $id != null) { echo $row['fullname'] . "'s Posts"; }else { echo "My posts"; } ?></h4>
      <div class="posts-list" id="posts-list">
        <ul id="post">
          <?php if($posts_count > 0) { 
            while($posts_row = mysqli_fetch_assoc($query2)) { ?>  
          <li>
            <div class="post-item">
              <div class="post-header">
                <div class="post-header-left">
                  <div class="profile-icon">
                    <img src="./images/profile-icon.png" alt="Profile Icon" />
                  </div>
                  <div class="post-author"><?php echo $posts_row['fullname']; ?></div>
                </div>
                <div class="post-header-right">
                  <span class="post-time">3 minutes ago</span>
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
              <p class="post-content"><?php echo $posts_row['description']; ?></p>
            </div>
          </li>
          <?php } } else { ?>
            <li>No posts found.</li>
          <?php }?>
        </ul>
      </div>
    </div>

    <script src="script.js"></script>
  </body>
</html>