<?php
session_start();
session_unset();
session_destroy();

// Clear the "Remember Me" cookies
setcookie("user_id", "", time() - 3600, "/");
setcookie("first_name", "", time() - 3600, "/");

exit();
?>