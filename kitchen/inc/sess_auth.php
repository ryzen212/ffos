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
	redirect('kitchen/index.php');
}
$module = array('','kitchen','tutor');
if(isset($_SESSION['userdata']) && (strpos($link, 'index.php') || strpos($link, 'kitchen/')) && $_SESSION['userdata']['login_type'] !=  3){
    redirect('index.php');
    exit;
}
