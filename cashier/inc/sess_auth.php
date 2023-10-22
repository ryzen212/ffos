<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
    $link = "https"; 
else
    $link = "http"; 
$link .= "://"; 
$link .= $_SERVER['HTTP_HOST']; 
$link .= $_SERVER['REQUEST_URI'];
if(!isset($_SESSION['userdata']) && !strpos($link, 'login.php')){
	redirect('index.php');
}
if(isset($_SESSION['userdata']) && strpos($link, 'login.php')){
	redirect('cashier/index.php');
}
$module = array('','cashier','tutor');
if(isset($_SESSION['userdata']) && (strpos($link, 'index.php') || strpos($link, 'cashier/')) && $_SESSION['userdata']['login_type'] !=  2){
	// echo "<script>alert('Access Denied!');location.replace('".base_url.$module[$_SESSION['userdata']['login_type']]."');</script>";
    redirect('index.php');
    exit;
}
