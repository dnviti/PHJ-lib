<?php
session_start();
ob_start();
include 'php/phj.php';
$site=new PHJ("phjtest", "en", "/phjtest", "main.html");
return $site->fill();
?>