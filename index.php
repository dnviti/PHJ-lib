<?php
session_start();
ob_start();
include 'php/phj.php';
$site=new PHJ("phjtest", "en", "/PHJ-lib", "main.html");
return $site->fill();
?>
