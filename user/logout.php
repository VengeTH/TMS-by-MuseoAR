<?php
session_start();
session_unset();
session_destroy();

//! Clear the "Remember Me" cookies.
// * recommended to use JWT tokens for this purpose. insecure.
setcookie("user_id", "", time() - 3600, "/");
setcookie("first_name", "", time() - 3600, "/");

header("Location: /");
exit();
?>
