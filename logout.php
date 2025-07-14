<?php
require_once 'config/session.php';

logout();
$_SESSION['success_message'] = 'You have been logged out successfully.';
header("Location: login.php");
exit();
?>
