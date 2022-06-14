<?php

$con = new mysqli(Host_Location, Database_Username, Database_Password, Host_Database);
$con->set_charset("utf8");

$functions = new functions();

$last_username = "";
$last_password;
$message_error;

// Check if the user is already logged in, if yes then redirect him/her to profile
if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
    // Redirect user to profile
    header("location: /profile");
    exit;
}

if (isset($_POST['login'])) {
    $last_username = htmlspecialchars(trim($_POST["username"]), ENT_QUOTES , 'UTF-8');
    // $last_password = trim($_POST["password"]);

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $message_error = "Please enter a username.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $message_error = "Username can only contain letters, numbers, and underscores.";
    } else {
        // Prepare a select statement
        $username = htmlspecialchars(trim($_POST["username"]), ENT_QUOTES , 'UTF-8');

        $query = "SELECT * FROM `users` WHERE `login` = '$username';";
        $user_exists = $functions->FetchAssoc($query, $con);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $message_error = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 8) {
        $message_error = "Password must have atleast 8 characters.";
    } else {
        $password = trim($_POST["password"]);
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
    }

    // Check input errors before inserting in database
    if (empty($message_error)) {
        if ($user_exists) {
            // $message_error = "This username is already taken.";
            if (password_verify($password, $user_exists['password'])) {
                // Store data in session variables
                $_SESSION["id"] = $user_exists['user_id'];
                $_SESSION["username"] = $username;
                $_SESSION["logged_in"] = true;

                // Redirect user to profile
                header("location: /profile");
            } else {
                $message_error = "Invalid username or password.";;
            }
        } else {
            // if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            //     // Email is valid
            //     $email = $username;
            //     $query = "INSERT INTO `users` (`user_id`, `login`, `password`, `nikname`, `email`, `url`, `registered`, `status`, `display_name`) VALUES (NULL, '$username', '$password_hash', '$username', '$email', NULL, 'CURRENT_TIMESTAMP', '1', '$username');";
            // }

            // $query = "INSERT INTO `users` (`user_id`, `login`, `password`, `nikname`, `email`, `url`, `registered`, `status`, `display_name`) VALUES (NULL, '$username', '$password_hash', '$username', NULL, NULL, 'CURRENT_TIMESTAMP', '1', '$username');";

            // $con->query($query);
            
            $stmt = $con->prepare("INSERT INTO `users` (`login`, `password`, `nikname`, `email`, `registered`, `status`, `display_name`) VALUES (?, ?, ?, '',CURRENT_TIMESTAMP, '1', ?);");
            // ('$username', '$password_hash', '$username', 'CURRENT_TIMESTAMP', '1', '$username')
            $nikname = $username;
            $displayname = $username;

            $stmt->bind_param('ssss', $username, $password_hash, $nikname, $displayname);

            $stmt->execute();
            
            // $new_user_id = $con->insert_id;
            $new_user_id = $stmt->insert_id;
            

            // $query = "INSERT INTO `user_meta` (`umeta_id`, `user_id`, `key`, `value`) VALUES (NULL, '$new_user_id', 'role', 'user');";

            // $con->query($query);
            
            $stmt = $con->prepare("INSERT INTO `user_meta` (`user_id`, `key`, `value`) VALUES (?, ?, ?);");

            $role = 'role';
            $user = 'user';

            $stmt->bind_param('iss', $new_user_id, $role, $user);

            $stmt->execute();

            // Store data in session variables
            $_SESSION["id"] = $new_user_id;
            $_SESSION["username"] = $username;
            $_SESSION["logged_in"] = true;

            // Generating a url for challenges
            $random_id = $functions->GenerateRandomLink();
            $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/challenge/' . $random_id; //  . $_SERVER['REQUEST_URI']

            // $query = "INSERT INTO `post` (`post_id`, `author`, `post_date`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `modify_date`, `guid`, `post_type`, `mime_type`, `post_parent`) VALUES (NULL, NULL, 'CURRENT_TIMESTAMP', '$url', '$username', 'Generated Link', 'owner', 'CURRENT_TIMESTAMP', '$random_id', 'link', NULL, '0')";

            // $con->query($query);
            
            $stmt = $con->prepare("INSERT INTO `post` (`author`, `post_date`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `modify_date`, `guid`, `post_type`, `post_parent`) VALUES (?, CURRENT_TIMESTAMP, ?, ?, ?, ?, CURRENT_TIMESTAMP, ?, ?, ?)"); 
            // ($id, 'CURRENT_TIMESTAMP', '$url', '$username', 'Generated Link', 'owner', 'CURRENT_TIMESTAMP', '$random_id', 'link', '0')
            
            $link = 'Generated Link';
            $post_status = 'owner';
            $post_type = 'link';
            $post_parent = '0';

            $stmt->bind_param('isssssss', $id, $url, $username, $link, $post_status, $random_id, $post_type, $post_parent);

            $stmt->execute();

            // Redirect user to profile
            header("location: /profile");
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once('header.php'); ?>

    <title>Anonymous Challenge | Login</title>
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
        <div class="title">Welcome 😊</div>
        <div class="subtitle">Ask your friends to send you an anonymous message without knowing them. 🤪😆😁🥳</div>
        <div class="subtitle">If this is not your first time visiting this page you should be signing in! </div>
        <div class="subtitle">Let's create your account!</div>
        <?php if (!empty($message_error)) : ?>
            <div class="subtitle message error ic1"><?php echo $message_error; ?></div>
        <?php endif; ?>
        <div class="input-container ic1">
            <input id="username" name="username" class="input" type="text" placeholder=" " value="<?php echo $last_username; ?>">
            <div class="cut"></div>
            <label for="username" class="placeholder">Username</label>
        </div>
        <!-- <div class="input-container ic2">
            <input id="email" class="input" type="text" placeholder=" ">
            <div class="cut cut-short"></div>
            <label for="email" class="placeholder">Email</label>
        </div> -->
        <div class="input-container ic2">
            <input id="password" name="password" class="input" type="password" placeholder=" " value="<?php // echo $last_password; 
                                                                                                        ?>">
            <div class="cut"></div>
            <label for="password" class="placeholder">Password</label>
        </div>
        <button type="text" class="submit" name="login">Submit</button>
        <div class="footer ic2">
            <p>Website created by
                <a href="https://github.com/itzreqle" class="links">itzreqle</a>
            </p>
        </div>
    </form>

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
