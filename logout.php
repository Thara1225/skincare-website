<?php
session_start(); // Start the session

// Destroy all session data
session_unset();
session_destroy();

// Redirect to the homepage after logging out
header("Location: homepage.php");
exit();
?>
