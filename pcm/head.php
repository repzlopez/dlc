<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
if(!defined('INCLUDE_CHECK')) die('Invalid Operation');

$errmsg=(isset($_SESSION['pcm_bad'])&&$_SESSION['pcm_bad'])?'** Invalid ID or Password':'';
$adclas=(isset($content))?'class="'.$content.'"':'';

$_SESSION['lastURI'] = 'pcm';

$pcm_dsdid = isset($_SESSION['pcm_dsdid'])?$_SESSION['pcm_dsdid']:null;
$frm = '';

if(ISIN_PCM) {
	$frm .= '<div id="user" rel="'.$pcm_dsdid.'" data-login="'.LOGIN_TYPE.'">'.(UPDATE_ON?'<span class="smaller">Update in progress...</span> ':'').'<span class="blue">PCM '.LOGIN_BRANCH.' | '.LOGIN_NAME.' |</span> <a href="/logout" class="lite">Logout</a></div>';
}

ob_start();
echo loadHead($title,'<link rel="stylesheet" type="text/css" href="/css/zebra_datepicker.css"></link>',1);
echo loadLogo($frm);
echo '<div id="container" '.$adclas.'>';

if(ISIN_PCM) echo '<div class="nav2"><h2>'.$title.'</h2></div>';

ob_end_flush();
?>
