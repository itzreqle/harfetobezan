<?php

$con = new mysqli(Host_Location, Database_Username, Database_Password, Host_Database);
$con->set_charset("utf8");

$functions = new functions();

$message_error;

// Link Section
$query = "SELECT * FROM `post` WHERE `guid` = '$challenge_id';";
$data = $functions->FetchAssoc($query, $con);

// Check if the link is correct
if (!$data) {
    // Redirect user to 404
    header("location: /404");
    exit;
} else {
    $name = $data['post_title'];
    $guid = $data['guid'];
    $link = $data['post_content'];
    $post_id = $data['post_id'];
}

if (isset($_POST['send'])) { 
    $message_name = htmlspecialchars(trim($_POST["name"]), ENT_QUOTES , 'UTF-8');
    $message = htmlspecialchars(trim($_POST["message"]), ENT_QUOTES , 'UTF-8');

    // Validate message
    if (empty($message_name)) {
        $message_name = 'Anonymous';
    }

    // Validate message
    if (empty($message)) {
        $message_error = "Please enter a message.";
    }

    // Check input errors before inserting in database
    if (empty($message_error)) {
        // $insert_query = "INSERT INTO `post` (`post_id`, `author`, `post_date`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `modify_date`, `guid`, `post_type`, `mime_type`, `post_parent`) VALUES (NULL, NULL, 'CURRENT_TIMESTAMP', '$message', '$message_name', 'Reply', 'visiter', 'CURRENT_TIMESTAMP', '$guid', 'reply', NULL, '$post_id');";

        // $con->query($insert_query);
        $stmt = $con->prepare("INSERT INTO `post` (`post_date`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `modify_date`, `guid`, `post_type`, `post_parent`) VALUES (CURRENT_TIMESTAMP, ?, ?, ?, ?, CURRENT_TIMESTAMP, ?, ?, ?);");

        $post_excerpt = 'Reply';
        $post_status = 'visiter';
        $post_type = 'reply';


        $stmt->bind_param('ssssssi', $message, $message_name, $post_excerpt, $post_status, $guid, $post_type, $post_id);

        $stmt->execute();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once('header.php'); ?>

    <title>Anonymous Challenge | Challenge</title>
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
        <div class="subtitle">Write whatever you like anonymously to <?php echo $name; ?></div>
        <?php if (!empty($message_error)) : ?>
            <div class="subtitle message error ic1"><?php echo $message_error; ?></div>
        <?php endif; ?>
        <div class="input-container ic1">
            <input id="link" class="input" type="text" placeholder=" " value="<?php echo $link; ?>">
            <div class="cut cut-short"></div>
            <label for="link" class="placeholder">Link</label>
        </div>
        <div class="input-container ic2">
            <input id="name" name="name" class="input" type="text" placeholder=" ">
            <div class="cut cut-short"></div>
            <label for="name" class="placeholder">Name</label>
        </div>
        <!-- <div class="input-container ic2">
            <input id="email" class="input" type="text" placeholder=" ">
            <div class="cut cut-short"></div>
            <label for="email" class="placeholder">Email</label>
        </div> -->
        <div class="input-container ic2">
            <input id="message" name="message" class="input" type="text" placeholder=" ">
            <div class="cut"></div>
            <label for="message" class="placeholder">Message</label>
            <!-- <div class="cut"></div>
            <textarea name="message" id="message" class="input" cols="30" rows="10" placeholder=""></textarea> -->
        </div>
        <button type="text" class="submit" name="send">Send</button>
        <button id="share" type="text" class="submit" name="share">Share Link</button>
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

    <script type="text/javascript" src="/assets/js/scripts.js"></script>
</body>

</html>
