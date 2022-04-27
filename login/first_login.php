<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
date_default_timezone_set('Asia/Manila');
define('INCLUDE_CHECK',1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/Exception.php';	//Exception class
require 'PHPMailer/src/PHPMailer.php';	//The main PHPMailer class
require 'PHPMailer/src/SMTP.php';		//SMTP class

require('../admin/setup.php');

if(isset($_SESSION['first_login'])&&!$_SESSION['first_login']){ reloadTo('../'); }
else{
	if(isset($_POST['submit'])&&$_POST['submit']=='Change Password'){
		if($_POST['new_pass']==''||$_POST['con_pass']==''||$_POST['email']==''){
			$_SESSION['err_msg']='** Required field missing!';
		}elseif(strlen($_POST['new_pass'])<8){
			$_SESSION['err_msg']='** Passwords must be at least eight (8) characters long!';
		}elseif($_POST['new_pass']!=$_POST['con_pass']){
			$_SESSION['err_msg']='** Passwords does not match!';
		}elseif(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)){
			$_SESSION['err_msg']='** Email address <strong>'.$_POST['email'].'</strong> is not valid!';
		}else{
			$con=SQLi('distributor');
			// $name =ucwords(strtolower(getName($_SESSION['u_site'],'fml')));
			$user =trim_escape($_SESSION['u_site']);
			$mail =trim_escape($_POST['email']);
			$pass =trim_escape($_POST['new_pass']);
			$pass =sha1($pass);
			$rset =sha1(rand());
			$now  =date(TMDSET,time());
			$data ="'$user','$now','$pass','$mail','','$rset',0,0,0";
			mysqli_query($con,"INSERT INTO accounts VALUES ($data);") or die(mysqli_error($con));
			mysqli_close($con);
			sendMail($mail,$rset);

			$_SESSION['isLogged']=1;
			unset($_SESSION['first_login']);
			unset($_SESSION['err_msg']);

			reloadTo(DLC_MYPAGE);
		}reloadTo($_SERVER['PHP_SELF']);
	}
	$_SESSION['lastURI']='first_login';
	ob_start();
	echo loadHead('| First Login');
	echo loadLogo('');
?>
<div id="container">
	<div id="first_login">
		<h3 class="blue">This is your First Login</h3>
		<p><strong>We require you to change your password right now for your security.</strong></p>
		<p>Your password <strong>MUST</strong>:</p>
		<p><span>* be at least 8 characters long</span><br />
			<span>* contain a COMBINATION of letters, numbers and special characters</span>
		</p>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>"><ul id="changePass">
			<li><label>New Password</label><input type="password" name="new_pass" class="txt" /></li>
			<li><label>Confirm New Password</label><input type="password" name="con_pass" class="txt" /></li>
			<li><label>Email Address</label><input type="text" id="email" name="email" class="txt" /></li>
			<li><input type="submit" name="submit" value="Change Password" class="btn" /><div class="more rt">** all fields are required!</div></li>
				<?php if(isset($_SESSION['err_msg'])) echo '<div class="bad">'.$_SESSION['err_msg'].'</div>'?>
		</ul></form>
	</div>
</div>
<?php echo loadFoot();
} ob_end_flush();

function sendMail($email,$reset){
	$style='<style>
	ul,div { -moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;border-radius:5px;border:#ccc solid 1px;list-style:none;margin:20px; }
	ul,li,p { font:normal 14px "lucida sans unicode","Lucida Grande",sans-serif;padding:10px; }
	div { background:#eee;padding:20px; }
	p { font-size:10px; margin:0 20px; }
	</style>';

	$head='<head><meta http-equiv="Content-Language" content="en" />
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	'.$style.'</head>';

	$x ='<html>'.$head.'<body><img src="http://dlcph.com/src/dlc_logo_min.png" alt="'.DLC_FULL.'" />';
	$x.='<ul>';
	$x.='<li>Congratulations and welcome to <b>'.DLC_FULL.'</b>!</li>';
	$x.='<li>This confirms your first login and password change. You can now fully access our website <a href="http://www.dlcph.com">www.dlcph.com</a>.</li>';
	$x.='<li>In case you need to reset your password, click <a href="http://www.dlcph.com/support.php?p=forgotpass" target="_blank">HERE</a> and use this Reset Key:<br>';
	$x.='<div><em>'.$reset.'</em></div></li>';
	$x.='<li>If you have questions, feel free to get in touch with us. Click <a href="http://www.dlcph.com/support.php?p=contact" target="_blank">HERE</a> for contact info, or thru the Messenger API in the website.</li>';
	$x.='<li>You can also contact us thru our Facebook Page <a href="http://fb.com/dlcphilippines" target="_blank">DLC Philippines</a> and our Official Messenger <a href="http://m.me/dlcphilippines" target="_blank">DLC Philippines</a></li>';
	$x.='</ul>';
	$x.='<p><em>This is a system generated message. Please do not respond to this email.<br>To ensure reliable delivery of all emails from our system, please ensure you add donotreply@dlcph.com to your address book.</em></p>';
	$x.='<p>'.DLC_FULL.' &copy;2009-'.date('Y',time()).' All rights reserved.</p>';
	$x.='</body></html>';

	$mail = new PHPMailer(TRUE);
	try {//Open the try/catch block
		$mail->setFrom('donotreply@dlcph.com',DLC_FULL);//Set the mail sender
		$mail->addAddress($email,DIST_NAME);//Add a recipient
		$mail->Subject = 'Welcome to '.DLC_FULL;//Set the subject
		// $mail->MsgHTML($x);

		/* Set the mail message body. */
		$mail->isHTML(TRUE);
		$mail->Body = $x;
		// $mail->AltBody = 'There is a great disturbance in the Force.';

		/* SMTP parameters. */
		$mail->isSMTP();//Tells PHPMailer to use SMTP
		$mail->Host = 'mail.dlcph.com';//SMTP server address
		// $mail->SMTPDebug = 2;//SMTP debug /* REMOVE */
		$mail->SMTPAuth = true;//Use SMTP authentication
		$mail->SMTPSecure = 'tls';//Set the encryption system
		$mail->Username = 'donotreply@dlcph.com';//SMTP authentication username
		$mail->Password = 'a[qCE,7+(&qP';//SMTP authentication password
		$mail->Port = 25;//Set the SMTP port
		/* SMTP parameters. */

		$mail->send();
	}

	catch (Exception $e){/* PHPMailer exception. */
		echo $e->errorMessage();
	}

	catch (\Exception $e){/* PHP exception (note the backslash to select the global namespace Exception class). */
		echo $e->getMessage();
	}
}

function sendVerifMail($mail,$rset){
	$hdr ='MIME-Version: 1.0'."\r\n";
	$hdr.='Content-type: text/html; charset=iso-8859-1'."\r\n";
	$hdr.='To: '.$mail."\r\n";
	$hdr.='From: DLC MAILER <DONOTREPLY@dlcph.com>'."\r\n";

	$msg ='<html><body><div><img src="'.DLC_ROOT.'/src/dlc_logo_min.png" alt="'.DLC_FULL.'" /></div><br />';
	$msg.='<p>Dear <b>'.DIST_NAME.'</b>:<p><br />';
	$msg.='<p>Welcome to <b>'.DLC_FULL.'</b>!<p><br />';
	$msg.='<p>You may now use your log-in information when you visit our website.<p><br />';
	$msg.='<p>In case you need to reset your password, use this code: <p><br />';
	$msg.='<p><b>'.$rset.'</b></p>';
	$msg.='</body></html>';

	mail($mail,"Welcome to DLC",$msg,$hdr);
}
?>
