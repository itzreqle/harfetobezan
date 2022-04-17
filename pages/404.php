<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once('header.php'); ?>

    <title>Anonymous Challenge | 404</title>
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
    <div class="form">
        <div class="title">404,</div>
        <div class="subtitle">Server could not find the requested website</div>
        <button type="text" class="submit" onclick="history.back()">Go Back</button>
        <button type="text" class="submit" onclick="window.location.href = '/'">Go Home</button>
    </div>

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

    <?php include_once('footer.php'); ?>
</body>

</html>