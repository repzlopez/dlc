<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../admin/setup.php');
$img='gjd.jpg';
$msg='<p>SORRY FOR THE INCONVENIENCE</p><p>We\'re applying <span class="product">Golden Jubilee Diamond</span><br />to keep our pages from aging</p>';
ob_start();
?><html>
<head>
	<title>Oops..</title>
	<link rel="shortcut icon" href="/src/favicon.ico"></link>
	<link rel="stylesheet" type="text/css" href="/css/styles.php?get=error"></link>
</head>
<body><?php echo loadLogo('')?>
<div id="container"><br /><br />
<div class="msg">
	<img src="/src/<?php echo $img;?>" id="prod" alt="<?php echo DLC_FULL?>" /><br />
	<?php echo $msg?><br />
	<p><a href="<?php echo DLC_ROOT?>">Take me home</a></p>
</div>
<?php include('../contact.php');?>
<div class="clear"></div>
</div></body></html>
<?php ob_end_flush();?>
