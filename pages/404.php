<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once('header.php'); ?>

    <title>Anonymous Challenge | 404</title>
</head>

<body class="dark-theme">
    <header>
        <div class="theme">
            <div class="switch-container switch-ios">
                <!-- <label for="theme">Theme</label> -->
                <input type="checkbox" name="ios" id="ios" onclick="ChangeTheme()" accesskey="T" checked />
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

    <?php include_once('footer.php'); ?>
</body>

</html>