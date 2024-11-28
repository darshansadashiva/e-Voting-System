<?php
session_start();
session_destroy();

// Redirect to the desired page
header('Location: http://localhost/smartballot/index.php');
exit();
?>