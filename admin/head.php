<?php
if( !isset($_SESSION) ) {
     session_set_cookie_params(0);
     session_start();
}

if( !defined('INCLUDE_CHECK') ) die('Invalid Operation');

$errmsg = (isset($_SESSION['bad_admin'])&&$_SESSION['bad_admin'])?'** invalid ID or Password':'';
$item   = isset($_GET['i'])?$_GET['i']:'';
$do     = isset($_GET['do'])?$_GET['do']:'';
$ap     = isset($adminpage)?$adminpage:'';
$co     = isset($content)?$content:'';
$rcnt   = str_repeat('../',substr_count($_SERVER['REQUEST_URI'],'/')-2);
$root   = ($ap!='')?$rcnt:'';
$frm    = '';

if( !ISIN_ADMIN && !ISIN_GOS && !ISIN_PCM && !ISIN_MIG ) {
	reloadTo('/login');

	// $frm.='<form method="post" action="/admin/login/login.php"><div>';
	// $frm.='<label for="un">User <input type="text" name="un" value="" /></label>';
	// $frm.='<label for="pw">Pass <input type="password" name="pw" value="" /></label>';
	// $frm.='<input type="submit" name="submit" class="btn" value="Login" /></div>';
	// $frm.='<div class="bad">'.$errmsg.'</div></form>';

}else{
	$frm.='<div id="user" data-title="DLC '.$title.'" '.(LOGIN_TYPE?'data-login="'.LOGIN_TYPE.'"':'').' data-notif="'.NOTIF_ON.'">'.(UPDATE_ON?'<label>UPDATE IN PROGRESS...</label> ':'').' <span>Week '.WEEK.'</span> | <span class="blue">Admin | '.LOGIN_NAME.' |</span> <a href="/logout" class="lite">Logout</a></div>';
}

ob_start();
echo loadHead($title,'',1);
echo loadLogo($frm);
echo '<div id="container">';

if( ISIN_ADMIN ) {
	echo '<div class="nav2">';
	echo '<h2>DLC '.(isset($page)?$page:'Administrator Page').'</h2>'.(isset($page)?getButtons($co,$item,$do):'');
	echo '</div>';
}

ob_end_flush();

?>
