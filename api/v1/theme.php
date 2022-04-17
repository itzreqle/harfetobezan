<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SESSION['theme'] == 'dark-theme') {
    $_SESSION['theme'] = '';
    $data = array('Theme' => 'Dark Theme Disable ');
} else {
    $_SESSION['theme'] = 'dark-theme';
    $data = array('Theme' => 'Dark Theme Enable');
}

echo json_encode($data);
