<?php
session_start();
if (!isset($_SESSION['user_id'])) die('Not logged in');

foreach ($_POST as $key => $value) {
    if ($key !== 'step') {
        $_SESSION['business_form'][$key] = $value;
    }
}
echo "Saved " . htmlspecialchars(json_encode($_POST));