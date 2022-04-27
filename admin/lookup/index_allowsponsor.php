<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();

$id=$name=$msg=$c=$x='';
if(isset($_POST['submit'])&&isset($_POST['id'])){
	$id   = trim_escape($_POST['id']);
	$name = getName($id,'fml');
	$test = testAllow($id);

	$msg='ALLOWED';
	if($test>0){ $c='good';}
	else{ $msg="NOT $msg";$c='bad'; }
	unset($_POST);
}

$x.='<form method="post" action="'.$_SERVER['REQUEST_URI'].'" id="testsponsoring"><ul>';
$x.='<li><span class="blue">Allowed to Sponsor</span>';
$x.='<li><label>Distributor ID:</label><input type="text" name="id" class="txt" id="distid" /></li>';
$x.='<li><h3 class="b ct '.$c.'">'.$id.'<br>'.strtoupper($name).'</h3></li>';
$x.='<li><h1 class="b ct '.$c.'">'.$msg.'</h1></li>';
$x.='<input type="submit" name="submit" class="btn" value="Check" /></ul></form>';

ob_start();
echo $x;
ob_end_flush();
?>
