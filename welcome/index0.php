<?php if(!isset($_SESSION)) session_start();
define('INCLUDE_CHECK',1);
require('../func.php');
if(!ISIN_DISTRI&&!GUEST){ reloadTo(DLC_ROOT);exit; }
$_SESSION['lastURI']='welcome';
$page='welcome';
$title=' | Welcome';
$reflink=REF_PROD!==null?REF_PROD:'../products.php';

$x='
<div class="ct"><iframe id="player" src="http://www.youtube.com/embed/videoseries?list=PLLY9xStJZXeWvwzxbUX8OOL29P10w-fTz&showinfo=1"
width="640" height="390" frameborder="0"></iframe></div>
<div class="content guest"><h1 class="blue ct">Welcome to DLC Philippines</h1><br>
<h2 class="ct">Would you like to:</h2><br>
<h2 class="ct"><a href="'.$reflink.'">Buy products?</a></h2>
<h2 class="ct">or</h2>
<h2 class="ct"><a href="../reg/package.php">Sign Up?</a></h2>
<br><br><br><br><br><br><br><br><br>
</div>';

$msg=loadHead($title).loadLogo('');
$msg.='<div id="container">'.$x;

ob_start();
echo $msg;
echo loadFoot('','','',1);
ob_end_flush();
?>
