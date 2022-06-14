<?php

$con = new mysqli(Host_Location, Database_Username, Database_Password, Host_Database);
$con->set_charset("utf8");

$functions = new functions();

// Check if the user is already logged in, if yes then redirect him/her to index
if (!isset($_SESSION["logged_in"])) {
    // Redirect user to index
    header("location: /");
    exit;
}

// Link Section
$id = $_SESSION["id"];
$username = $_SESSION["username"];
$query = "SELECT * FROM `post` WHERE `post_title` = '$username' AND `post_status` = 'owner';";
$data = $functions->FetchAssoc($query, $con);
$post_id = $data['post_id'];

if (isset($_POST['logout'])) {
    // Initialize the session
    // session_start();

    // Unset all of the session variables
    session_unset();

    // Destroy the session.
    session_destroy();

    // Redirect to login page
    header("location: /");
    exit;
}

if (isset($_POST['delete_link'])) {
    $query = "DELETE FROM `post` WHERE `post`.`post_id` = $post_id;";

    $con->query($query);

    $delete_query = "SELECT * FROM `post` WHERE `post_parent` = $post_id;";

    $query_data = $functions->FetchArray($delete_query, $con);

    foreach ($query_data as $row) {
        $row_id = $row['post_id'];

        $query = "DELETE FROM `post` WHERE `post`.`post_id` = $row_id;";

        $con->query($query);
    }

    // Generating a url for challenges
    $random_id = $functions->GenerateRandomLink();
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/challenge/' . $random_id; //  . $_SERVER['REQUEST_URI']

    // $query = "INSERT INTO `post` (`post_id`, `author`, `post_date`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `modify_date`, `guid`, `post_type`, `mime_type`, `post_parent`) VALUES (NULL, $id, 'CURRENT_TIMESTAMP', '$url', '$username', 'New Generated Link', 'owner', 'CURRENT_TIMESTAMP', '$random_id', 'link', NULL, '0')";

    // $con->query($query);
    
    $stmt = $con->prepare("INSERT INTO `post` (`author`, `post_date`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `modify_date`, `guid`, `post_type`, `post_parent`) VALUES (?, CURRENT_TIMESTAMP, ?, ?, ?, ?, CURRENT_TIMESTAMP, ?, ?, ?)"); 
    // ($id, 'CURRENT_TIMESTAMP', '$url', '$username', 'New Generated Link', 'owner', 'CURRENT_TIMESTAMP', '$random_id', 'link', '0')
            
    $link = 'New Generated Link';
    $post_status = 'owner';
    $post_type = 'link';
    $post_parent = '0';

    $stmt->bind_param('isssssss', $id, $url, $username, $link, $post_status, $random_id, $post_type, $post_parent);

    $stmt->execute();

    header("Refresh: 0");
    // header("Refresh: 0; url=name.php");
}

if (isset($_POST['delete_account'])) {
    $query = "DELETE FROM `post` WHERE `post`.`post_id` = $post_id;";

    $con->query($query);

    $delete_query = "SELECT * FROM `post` WHERE `post_parent` = $post_id;";

    $query_data = $functions->FetchArray($delete_query, $con);

    foreach ($query_data as $row) {
        $row_id = $row['post_id'];

        $query = "DELETE FROM `post` WHERE `post`.`post_id` = $row_id;";

        $con->query($query);
    }

    $delete_query = "DELETE FROM `users` WHERE `users`.`user_id` = $id;";

    $con->query($delete_query);

    $delete_query = "SELECT * FROM `user_meta` WHERE `user_id` = $id;";

    $query_data = $functions->FetchArray($delete_query, $con);

    foreach ($query_data as $row) {
        $row_id = $row['umeta_id'];

        $query = "DELETE FROM `user_meta` WHERE `user_meta`.`umeta_id` = $row_id;";

        $con->query($query);
    }

    // Initialize the session
    // session_start();

    // Unset all of the session variables
    session_unset();

    // Destroy the session.
    session_destroy();

    // Redirect to login page
    header("location: /");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once('header.php'); ?>

    <title>Anonymous Challenge | View</title>
</head>

<body class="<?php if (isset($_SESSION['theme'])) echo $_SESSION['theme'];?>">
    <header>
        <div class="theme">
            <div class="switch-container switch-ios">
                <!-- <label for="theme">Theme</label> -->
                <input type="checkbox" name="ios" id="ios" onclick="ChangeTheme()" accesskey="T" <?php if ($_SESSION['theme'] == 'dark-theme') echo 'checked'; ?> />
                <label for="ios"></label>
            </div>
        </div>
    </header>
    <form class="form" method="POST">
        <div class="title">Anonymous Challenge</div>
        <div class="subtitle">Your challenge is ready, share the following link with your friends so that your friends can chat with you anonymously.</div>
        <div class="input-container ic1">
            <input id="link" class="input" type="text" placeholder=" " value="<?php echo $data['post_content']; ?>">
            <div class="cut cut-short"></div>
            <label for="link" class="placeholder">Link</label>
        </div>
        <!-- <div class="input-container ic2">
            <input id="email" class="input" type="text" placeholder=" ">
            <div class="cut cut-short"></div>
            <label for="email" class="placeholder">Email</label>
        </div> -->
        <!-- <div class="input-container ic2">
            <input id="password" class="input" type="password" placeholder=" ">
            <div class="cut"></div>
            <label for="password" class="placeholder">Password</label>
        </div> -->
        <button id="share" type="text" class="submit" name="share">Share Link</button>
        <button type="text" class="submit" name="logout">Logout</button>
        <button type="text" class="submit" name="delete_link">Delete Link</button>
        <button type="text" class="submit" name="delete_account">Delete Account</button>
        <div class="subtitle ic1">Replies:</div>
        <?php
        $replies_query = "SELECT * FROM `post` WHERE `post_parent` = $post_id;";

        $replies_data = $functions->FetchArray($replies_query, $con);
        if ($replies_data) :
        ?>
            <?php foreach ($replies_data as $row) : ?>
                <div class="message-container ic2">
                    <div class="message-name">
                        <?php echo $row['post_title']; ?>
                    </div>
                    <div class="message">
                        <?php echo $row['post_content']; ?>
                    </div>
                    <div class="message-date">
                        <?php echo $row['post_date']; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="message-container ic2">
                <div class="message">
                    No message yet!
                </div>
            </div>
        <?php endif; ?>
        <div class="footer ic2">
            <p>Website created by
                <a href="https://github.com/itzreqle" class="links">itzreqle</a>
            </p>
        </div>
    </form>

    <script>
        var copyInputbtn = document.getElementById('share');

        copyInputbtn.addEventListener('click', function(event) {
            var copyInput = document.getElementById('link');
            copyInput.focus();
            copyInput.select();

            try {
                var successful = document.execCommand('copy');
                var msg = successful ? 'successful' : 'unsuccessful';
                console.log('Copying text command was ' + msg);
            } catch (err) {
                console.log('Oops, unable to copy');
            }
        });
    </script>

    <script>
        $('#ios').click(function() {
            $.ajax({
                url: 'theme',
                // type: 'POST',
                // data: {
                //     email: 'email@example.com',
                //     message: 'hello world!'
                // },
                // success: function(msg) {
                //     alert('Email Sent');
                // }
            });
        });
    </script>

    <script type="text/javascript" src="/assets/js/scripts.js"></script>
</body>

</html>
