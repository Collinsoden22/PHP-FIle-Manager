<?php
session_start();
// Destroy user session and logout
session_destroy();
$msg = 'You have successfully logged out of your account.';
header("Location: login.php?msg=$msg");
exit;
