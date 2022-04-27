<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
$arrJs=array('/js/jquery/jquery-1.7.1.min.js','/js/jquery/zebra_datepicker.js','/js/center.js');
ob_start();
echo (ISIN_MIG)?'<div class="clear"><a href="'.test_input($_SERVER['HTTP_REFERER']).'" class="back" id="download">BACK</a></div>':'';
echo loadFoot('',ISIN_MIG?'<a href="'.LOGO_LINK.'">DLC MIG</a>':'<a href="'.LOGO_LINK.'">DLC Home</a>',$arrJs,1);
ob_end_flush();?>
