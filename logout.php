<?php
session_start();

session_unset();
session_destroy();

header("Location: static/login.php");
exit;
?>
