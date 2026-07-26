<?php
session_start();
$_SESSION['test'] = "Session Working!";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>
