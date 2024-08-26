<?php
// start  session
session_start();

$_SESSION = array();

// end  session
session_destroy();

// redirect
header("location: index.php");
exit;
?>
