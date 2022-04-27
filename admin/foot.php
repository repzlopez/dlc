<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
$arrJs=array('/js/jquery/jquery-1.7.1.min.js','/admin/editor/nicEdit.js','/admin/common.js');
if(ISIN_ADMIN) echo '<div id="modal"><div id="overlay"></div><ul id="popup"></ul></div>';
ob_start();
if(substr(getcwd(),-5)!='admin') echo '<div class="clear"><a href="'.LOGO_LINK.'" class="back" id="download">HOME</a></div>';
echo loadFoot('',ISIN_ADMIN?'<a href="'.LOGO_LINK.'">DLC Admin</a>':'<a href="'.LOGO_LINK.'">DLC Home</a>',$arrJs,1);
ob_end_flush();?>
