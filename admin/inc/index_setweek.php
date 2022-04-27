<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
if(!IS_GLOB){reloadTo(DLC_ADMIN);exit;}
$x ='<form method="post" action="update.php?t='.$content.'"><ul>';
$x.='<input type="hidden" name="id" value="995" />';
$x.='<li><label>Current Week:</label> Week '.WEEKDESC.'</li>';
$x.='<li><label>Override Week:</label><input type="text" name="status" value='.WEEK.' /></li>';
$x.='<li><input type="hidden" name="do" value="2,995" /></li>';
$x.='<input type="submit" name="submit" class="btn" value="Submit" /></ul></form>';
echo $x;
?>