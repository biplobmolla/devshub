<?php
    include "config.php";
    include "./utils/timeAgo.php";

    session_start();

    $id = $_GET['id'] ?? null;
    $row = null;
    $posts_count = 0;
    $isFRSent = false;
    $isFRGot = false;
    $isFriend = false;

    $profile_errors = [];
    $profile_edit_values = null;
    $open_edit_modal = false;

    $has_bio_col = false;
    $col_check = mysqli_query($con, "SHOW COLUMNS FROM users LIKE 'bio'");
    if($col_check && mysqli_num_rows($col_check) > 0) {
        $has_bio_col = true;
    }

    $sql = "SELECT * FROM friends WHERE fr_receiver_id='" . $id . "' OR fr_sender_id='" . $id . "'";
    $query = mysqli_query($con, $sql);
    $friends_count = $query ? mysqli_num_rows($query) : 0;

    if($id){
      $sql = "SELECT * FROM users WHERE id='$id'";
      $query = mysqli_query($con, $sql);
      $row = mysqli_fetch_assoc($query);

      $sql2 = "SELECT * FROM posts WHERE author_id='$id' ORDER BY created_at DESC";
      $query2 = mysqli_query($con, $sql2);
      $posts_count = mysqli_num_rows($query2);

      if(isset($_POST['edit_profile']) && isset($_SESSION['user_id']) && $id == $_SESSION['user_id']) {
        $new_fullname = trim($_POST['fullname'] ?? '');
        $new_username = trim($_POST['username'] ?? '');
        $new_bio = trim($_POST['bio'] ?? '');
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_new_password = $_POST['confirm_new_password'] ?? '';

        $profile_edit_values = [
          'fullname' => $new_fullname,
          'username' => $new_username,
          'bio' => $new_bio,
        ];

        if($new_fullname === '') {
          $profile_errors[] = 'Full name is required.';
        } elseif(strlen($new_fullname) > 255) {
          $profile_errors[] = 'Full name must be 255 characters or fewer.';
        }

        if($new_username === '') {
          $profile_errors[] = 'Username is required.';
        } elseif(strlen($new_username) < 3) {
          $profile_errors[] = 'Username must be at least 3 characters long.';
        } elseif(strlen($new_username) > 64) {
          $profile_errors[] = 'Username must be 64 characters or fewer.';
        } elseif(!preg_match('/^[A-Za-z0-9_]+$/', $new_username)) {
          $profile_errors[] = 'Username can only contain letters, numbers, and underscores.';
        }

        if(strlen($new_bio) > 255) {
          $profile_errors[] = 'Bio cannot exceed 255 characters.';
        }

        if(empty($profile_errors) && $new_username !== $_SESSION['username']) {
          $safe_username = mysqli_real_escape_string($con, $new_username);
          $my_id = (int) $_SESSION['user_id'];
          $check_sql = "SELECT id FROM users WHERE username='$safe_username' AND id != $my_id";
          $check_query = mysqli_query($con, $check_sql);
          if($check_query && mysqli_num_rows($check_query) > 0) {
            $profile_errors[] = 'This username is already taken.';
          }
        }

        $change_password = ($current_password !== '' || $new_password !== '' || $confirm_new_password !== '');
        if($change_password) {
          if($current_password === '') {
            $profile_errors[] = 'Current password is required to change your password.';
          } else {
            $my_id = (int) $_SESSION['user_id'];
            $safe_curr = mysqli_real_escape_string($con, md5($current_password));
            $pw_check = mysqli_query($con, "SELECT id FROM users WHERE id=$my_id AND password='$safe_curr'");
            if(!$pw_check || mysqli_num_rows($pw_check) === 0) {
              $profile_errors[] = 'Current password is incorrect.';
            }
          }

          if($new_password === '') {
            $profile_errors[] = 'New password is required.';
          } elseif(strlen($new_password) < 6) {
            $profile_errors[] = 'New password must be at least 6 characters long.';
          } elseif(strlen($new_password) > 72) {
            $profile_errors[] = 'New password must be 72 characters or fewer.';
          }

          if($confirm_new_password === '') {
            $profile_errors[] = 'Please confirm your new password.';
          } elseif($new_password !== $confirm_new_password) {
            $profile_errors[] = 'New passwords do not match.';
          }
        }

        $new_profile_image_filename = null;

        if(empty($profile_errors) && !empty($_FILES['profile_image']['name'])) {
          $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
          $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

          if(!in_array($ext, $allowed)) {
            $profile_errors[] = 'Please upload a valid image (JPG, PNG, GIF, or WEBP).';
          } else {
            $new_name = time() . '_' . (int) $_SESSION['user_id'] . '.' . $ext;
            if(move_uploaded_file($_FILES['profile_image']['tmp_name'], 'uploads/profiles/' . $new_name)) {
              $new_profile_image_filename = $new_name;
            } else {
              $profile_errors[] = 'Failed to upload image.';
            }
          }
        }

        if(empty($profile_errors)) {
          $safe_fullname = mysqli_real_escape_string($con, $new_fullname);
          $safe_username = mysqli_real_escape_string($con, $new_username);
          $safe_bio = mysqli_real_escape_string($con, $new_bio);
          $my_id = (int) $_SESSION['user_id'];

          $set_parts = ["fullname='$safe_fullname'", "username='$safe_username'"];
          if($has_bio_col) {
            $set_parts[] = "bio='$safe_bio'";
          }
          if($change_password) {
            $hashed_new = md5($new_password);
            $safe_pw = mysqli_real_escape_string($con, $hashed_new);
            $set_parts[] = "password='$safe_pw'";
          }
          if($new_profile_image_filename !== null) {
            $safe_img = mysqli_real_escape_string($con, $new_profile_image_filename);
            $set_parts[] = "profile_image='$safe_img'";
          }
          $update_sql = "UPDATE users SET " . implode(', ', $set_parts) . " WHERE id=$my_id";

          if(mysqli_query($con, $update_sql)) {
            mysqli_query($con, "UPDATE posts SET fullname='$safe_fullname', username='$safe_username' WHERE author_id=$my_id");

            $_SESSION['fullname'] = $new_fullname;
            $_SESSION['username'] = $new_username;
            $refresh = mysqli_query($con, "SELECT * FROM users WHERE id=$my_id");
            if($refresh) {
              $_SESSION['user'] = mysqli_fetch_assoc($refresh);
            }
            header("Location: profile.php?id=$my_id");
            exit();
          } else {
            $profile_errors[] = 'Failed to update profile. Please try again.';
          }
        }

        if(!empty($profile_errors)) {
          $open_edit_modal = true;
        }
      }

      if(isset($_POST['add_friend'])) {
        $my_id = $_SESSION["user_id"];
          $sql = "INSERT INTO friend_requests (fr_receiver_id, fr_sender_id) VALUES ('$id', '$my_id')";
          $sql2 = "INSERT INTO notifications (notification_type, notification_receiver_id, notification_sender_id, notification_message) VALUES ('friend_request', '$id', '$my_id', 'sent you a friend request')";
          if(mysqli_query($con, $sql) && mysqli_query($con, $sql2)) {
            header("Location: profile.php?id=$id");
          }
      }

      $sql3 = "SELECT * FROM friend_requests WHERE 	fr_receiver_id='$id' AND fr_sender_id='" . ($_SESSION['user_id'] ?? 0) . "'";
      $query3 = mysqli_query($con, $sql3);
      $isFRSent = ($query3 && mysqli_num_rows($query3) > 0);

      $sql5 = "SELECT * FROM friend_requests WHERE 	fr_receiver_id='" . ($_SESSION['user_id'] ?? 0) . "' AND fr_sender_id='$id'";
      $query5 = mysqli_query($con, $sql5);
      $isFRGot = ($query5 && mysqli_num_rows($query5) > 0);

      if(isset($_POST['cancel_friend_request'])) {
        $my_id = $_SESSION["user_id"];
        $sql = "DELETE FROM friend_requests WHERE fr_receiver_id='$id' AND fr_sender_id='$my_id'";
        if(mysqli_query($con, $sql)) {
          header("Location: profile.php?id=$id");
        }
      }

      $sql4 = "SELECT * FROM friends WHERE 	fr_receiver_id='" . ($_SESSION['user_id'] ?? 0) . "' AND fr_sender_id='$id' OR fr_receiver_id='$id' AND fr_sender_id='" . ($_SESSION['user_id'] ?? 0) . "'";
      $query4 = mysqli_query($con, $sql4);
      $isFriend = ($query4 && mysqli_num_rows($query4) > 0);

      if(isset($_POST['accept_friend_request'])) {
        $my_id = $_SESSION["user_id"];
        $sql = "INSERT INTO friends (fr_receiver_id, fr_sender_id) VALUES ('$my_id', '$id')";
        $sql2 = "INSERT INTO notifications (notification_type, notification_receiver_id, notification_sender_id, notification_message) VALUES ('friend_request', '$id', '$my_id', 'accepted your friend request')";
        if(mysqli_query($con, $sql) && mysqli_query($con, $sql2)) {
          $sql = "DELETE FROM friend_requests WHERE fr_receiver_id='$my_id' AND fr_sender_id='$id'";
          mysqli_query($con, $sql);
          header("Location: profile.php?id=$id");
        }
      }

      if(isset($_POST['reject_friend_request'])) {
        $my_id = $_SESSION["user_id"];
        $sql = "DELETE FROM friend_requests WHERE fr_receiver_id='$my_id' AND fr_sender_id='$id'";
        if(mysqli_query($con, $sql)) {
          header("Location: profile.php?id=$id");
        }
      }

      if(isset($_POST['unfriend'])) {
        $my_id = $_SESSION["user_id"];
        $sql = "DELETE FROM friends WHERE fr_receiver_id='$id' AND fr_sender_id='$my_id' OR fr_receiver_id='$my_id' AND fr_sender_id='$id'";
        if(mysqli_query($con, $sql)) {
          header("Location: profile.php?id=$id");
        }
      }

    }else if(isset($_SESSION['username'])) {
      $row = $_SESSION['user'];
      $sql2 = "SELECT * FROM posts WHERE author_id='" . $_SESSION['user_id'] . "' ORDER BY created_at DESC";
      $query2 = mysqli_query($con, $sql2);
      $posts_count = mysqli_num_rows($query2);
    }else{
      header("Location: index.php");
      exit();
    }

    $display_fullname = $profile_edit_values['fullname'] ?? ($row['fullname'] ?? '');
    $display_username = $profile_edit_values['username'] ?? ($row['username'] ?? '');
    $display_bio = $profile_edit_values['bio'] ?? ($row['bio'] ?? '');
    $is_own_profile = isset($_SESSION['user_id']) && $id == $_SESSION['user_id'];
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
  <body<?php echo $open_edit_modal ? ' class="modal-open"' : ''; ?>>
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

    <?php if($is_own_profile): ?>
    <div class="profile-edit-modal<?php echo $open_edit_modal ? ' is-open' : ''; ?>" id="edit-profile-modal">
      <button type="button" class="profile-modal-close" id="close-edit-profile-btn" aria-label="Close">×</button>
      <h2 id="profile-modal-title">Edit Profile</h2>

      <?php if(!empty($profile_errors)): ?>
        <div class="form-errors <?php echo count($profile_errors) === 1 ? 'form-errors-single' : ''; ?>">
          <?php if(count($profile_errors) > 1): ?>
            <span class="form-errors-title">Please fix the following:</span>
            <ul>
              <?php foreach($profile_errors as $err): ?>
                <li><?php echo htmlspecialchars($err); ?></li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <?php echo htmlspecialchars($profile_errors[0]); ?>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <form method="post" action="profile.php?id=<?php echo htmlspecialchars($id); ?>" enctype="multipart/form-data" novalidate>
        <div class="form-group profile-image-group">
          <label>Profile image</label>
          <div class="profile-image-picker">
            <img id="profile-image-preview" src="<?php echo profile_image_url($row['profile_image'] ?? ''); ?>" alt="Current profile image" />
            <div class="profile-image-actions">
              <label for="edit-profile-image" class="profile-image-upload-btn">
                <i class="fa-solid fa-camera"></i> Choose image
              </label>
              <small class="field-hint">JPG, PNG, GIF, or WEBP.</small>
            </div>
            <input
              type="file"
              name="profile_image"
              id="edit-profile-image"
              accept="image/png,image/jpeg,image/gif,image/webp"
            />
          </div>
        </div>

        <div class="form-group">
          <label for="edit-fullname">Full name</label>
          <input
            type="text"
            name="fullname"
            id="edit-fullname"
            maxlength="255"
            value="<?php echo htmlspecialchars($display_fullname); ?>"
          />
        </div>

        <div class="form-group">
          <label for="edit-username">Username</label>
          <input
            type="text"
            name="username"
            id="edit-username"
            maxlength="64"
            value="<?php echo htmlspecialchars($display_username); ?>"
          />
        </div>

        <div class="form-group">
          <label for="edit-bio">Bio</label>
          <textarea
            name="bio"
            id="edit-bio"
            maxlength="255"
            placeholder="Tell the community about yourself..."
          ><?php echo htmlspecialchars($display_bio); ?></textarea>
          <?php if(!$has_bio_col): ?>
            <small class="field-hint">Bio changes will be saved once the <code>bio</code> column is added to the <code>users</code> table.</small>
          <?php endif; ?>
        </div>

        <div class="password-section">
          <h3>Change password <span class="optional-label">(optional)</span></h3>
          <div class="form-group">
            <label for="edit-current-password">Current password</label>
            <input type="password" name="current_password" id="edit-current-password" autocomplete="current-password" />
          </div>
          <div class="form-group">
            <label for="edit-new-password">New password</label>
            <input type="password" name="new_password" id="edit-new-password" autocomplete="new-password" />
          </div>
          <div class="form-group">
            <label for="edit-confirm-password">Confirm new password</label>
            <input type="password" name="confirm_new_password" id="edit-confirm-password" autocomplete="new-password" />
          </div>
        </div>

        <button type="submit" name="edit_profile" class="profile-edit-submit">Save changes</button>
      </form>
    </div>
    <?php endif; ?>

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
                  autocomplete="off"
                />
                <button type="submit" class="search-submit" aria-label="Search">
                  <img src="./images/search-icon.png" alt="" />
                </button>
              </form>
            </li>
            <li class="header-auth">
              <span class="header-username"><?php echo htmlspecialchars($_SESSION['fullname'] ?? ''); ?></span>
              <a class="header-logout" href="logout.php">Log out</a>
              <a href="./profile.php?id=<?php echo $_SESSION['user_id'] ?? ''; ?>" class="profile-icon" aria-label="Your profile">
                <img src="<?php echo profile_image_url($_SESSION['user']['profile_image'] ?? ''); ?>" alt="" />
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </header>
    <div class="upper-section">
      <div class="profile-user-icon">
        <img src="<?php echo profile_image_url($row['profile_image'] ?? ''); ?>" alt="Profile Icon" />
      </div>
      <div>
        <h1 class="profile-name"><?php echo htmlspecialchars($row['fullname'] ?? ''); ?></h1>
        <p class="profile-bio"><?php
          $shown_bio = $row['bio'] ?? '';
          echo htmlspecialchars($shown_bio !== '' ? $shown_bio : 'Web developer and tech enthusiast.');
        ?></p>
      </div>
      <div class="summary">
        <a href="./friends.php?id=<?php echo $id; ?>" class="friends-summary"><?php echo $friends_count; ?> friends</a>
        <div class="posts-summary"><?php echo $posts_count; ?> posts</div>
      </div>
      <?php if($id != ($_SESSION['user_id'] ?? null) && $id != null) {
        if($isFRSent) { ?>
          <div class="friend-request-sent">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "?id=$id"; ?>">
              <button name="cancel_friend_request" type="submit" class="fr_cancel_btn">Cancel Friend Request <i class="fa-solid fa-x"></i></button>
            </form>
          </div>
<?php } else if($isFriend) { ?>
      <div class="friend">
        <button class="friend_btn">Friend <i class="fa-solid fa-check"></i></button>
        <form action="<?php echo $_SERVER['PHP_SELF'] . "?id=$id"; ?>" method="post">
          <button onClick="return confirm('Are you sure you want to unfriend this user?');" class="unfriend_btn" name="unfriend" type="submit">Unfriend <i class="fa-solid fa-x"></i></button>
        </form>
      </div>
<?php } else if($isFRGot){ ?>
      <div class="respond-friend-request">
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "?id=$id"; ?>">
          <button name="accept_friend_request" type="submit" class="fr_accept_btn">Accept <i class="fa-solid fa-check"></i></button>
          <button name="reject_friend_request" type="submit" class="fr_reject_btn">Reject <i class="fa-solid fa-x"></i></button>
        </form>
      </div>
<?php } else if(isset($_SESSION['user_id'])) { ?>
      <div class="add-friend">
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "?id=$id"; ?>">
          <button name="add_friend" type="submit">Add Friend
            <i class="fa-solid fa-plus"></i>
          </button>
        </form>
      </div>

      <?php } } else if($is_own_profile) { ?>
      <div class="edit-profile">
        <button type="button" id="open-edit-profile-btn">Edit Profile <i class="fa-solid fa-pencil"></i></button>
      </div>
      <?php } ?>

    </div>
    <div class="my-posts" id="my-posts">
      <h4 class="my-posts-title"><?php if($id != ($_SESSION['user_id'] ?? null) && $id != null) { echo htmlspecialchars(($row['fullname'] ?? '') . "'s Posts"); }else { echo "My posts"; } ?></h4>
      <div class="posts-list" id="posts-list">
        <ul id="post">
          <?php if($posts_count > 0) { 
            while($posts_row = mysqli_fetch_assoc($query2)) { ?>  
          <li>
            <div class="post-item">
              <div class="post-header">
                <div class="post-header-left">
                  <div class="profile-icon">
                    <img src="<?php echo profile_image_url($row['profile_image'] ?? ''); ?>" alt="Profile Icon" />
                  </div>
                  <a href="profile.php?id=<?php echo $posts_row['author_id']; ?>" class="post-author"><?php echo htmlspecialchars($posts_row['fullname']); ?></a>
                </div>
                <div class="post-header-right">
                  <span class="post-time" title="<?php echo htmlspecialchars($posts_row['created_at'] ?? ''); ?>"><?php echo timeAgo($posts_row['created_at'] ?? '', $con); ?></span>
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
              <p class="post-content"><?php echo htmlspecialchars($posts_row['description']); ?></p>
            </div>
          </li>
          <?php } } else { ?>
            <li>No posts found.</li>
          <?php }?>
        </ul>
      </div>
    </div>

    <script src="script.js"></script>
    <?php if($is_own_profile): ?>
    <script>
      (function () {
        const openBtn = document.getElementById("open-edit-profile-btn");
        const closeBtn = document.getElementById("close-edit-profile-btn");
        const modal = document.getElementById("edit-profile-modal");

        function openModal() {
          modal.classList.add("is-open");
          document.body.classList.add("modal-open");
        }

        function closeModal() {
          modal.classList.remove("is-open");
          document.body.classList.remove("modal-open");
        }

        if (openBtn) openBtn.addEventListener("click", openModal);
        if (closeBtn) closeBtn.addEventListener("click", closeModal);

        document.addEventListener("keydown", function (e) {
          if (e.key === "Escape" && modal.classList.contains("is-open")) {
            closeModal();
          }
        });

        const imgInput = document.getElementById("edit-profile-image");
        const imgPreview = document.getElementById("profile-image-preview");
        if (imgInput && imgPreview) {
          imgInput.addEventListener("change", function () {
            const file = this.files && this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (ev) { imgPreview.src = ev.target.result; };
            reader.readAsDataURL(file);
          });
        }
      })();
    </script>
    <?php endif; ?>
  </body>
</html>
