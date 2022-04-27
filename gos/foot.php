<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
$editorder=(isset($_SESSION['editorder'])&&$content=='remit')?$_SESSION['editorder']:'';
$arrJs=array('/js/jquery/jquery-1.7.1.min.js','/js/jquery/zebra_datepicker.js','/js/center.js');
ob_start();
echo (ISIN_GOS)?'<div class="clear"><a href="'.test_input($_SERVER['HTTP_REFERER']).'" class="back" id="download">BACK</a>'.$editorder.'</div>':'';
echo loadFoot('',ISIN_GOS?'<a href="'.LOGO_LINK.'">DLC GOS</a>':'<a href="'.LOGO_LINK.'">DLC Home</a>',$arrJs,1);
ob_end_flush();?>
