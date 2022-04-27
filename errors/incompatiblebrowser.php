<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../admin/setup.php');
$_SESSION['lastURI']='error';
ob_start();
?><html>
<head>
	<title>Oops..</title>
	<link rel="shortcut icon" href="/src/favicon.ico"></link>
	<link rel="stylesheet" type="text/css" href="/css/styles.php"></link>
</head>
<body><?php echo loadLogo('')?>
<div id="container"><br /><br />
<div class="msg"><br /><br /><br /><br />
	<h2>WE APOLOGIZE FOR THE INCONVENIENCE</h2><br /><br />
	<p>YOU SEEM TO BE USING AN OLD BROWSER<br />
	PLEASE UPDATE YOUR BROWSER OR USE ANOTHER BROWSER</p>
</div>
<?php include('../contact.php');?>
<div class="clear"></div>
</div></body></html>
<?php ob_end_flush();?>
