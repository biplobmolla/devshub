<?php
    session_start();

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
    <title>My Profile</title>
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
          <a href="/">DevsHub</a>
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
        <h1 class="profile-name">Biplob Molla</h1>
        <p class="profile-bio">Web developer and tech enthusiast.</p>
      </div>
      <div class="summary">
        <div class="friends-summary">3 friends</div>
        <div class="posts-summary">5 posts</div>
      </div>
      <div class="add-friend">
        <button>Add Friend</button>
      </div>
    </div>
    <div class="my-posts" id="my-posts">
      <h4 class="my-posts-title">My posts</h4>
      <div class="posts-list" id="posts-list">
        <ul id="post">
          <li>
            <div class="post-item">
              <div class="post-header">
                <div class="post-header-left">
                  <div class="profile-icon">
                    <img src="./images/profile-icon.png" alt="Profile Icon" />
                  </div>
                  <div class="post-author">Biplob Molla</div>
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
              <p class="post-content">This is my first post</p>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <script src="script.js"></script>
  </body>
</html>
