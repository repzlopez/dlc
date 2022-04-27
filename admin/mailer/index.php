<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../setup.php');
ob_start();

if(!isset($sentto)) $sentto=array();
$i=0;
$subj = 'Special Invitation from our Founder, Eddy Chai';

//CC COPY
$namecc = 'DLC COPY';
$addrcc = 'repzlopezdlc@yahoo.com';
sendMassMail($namecc,$addrcc,$subj);
$sentto[]=array($namecc,$addrcc);

//LIVE COPY
$con=SQLi('distributor');
$rs=mysqli_query($con,"SELECT * FROM accounts WHERE email!=''") or die(mysqli_error($con));
while($rw=mysqli_fetch_assoc($rs)){
	$name = getName($rw['dsdid'],'lfm');
	$addr = $rw['email'];
	if(trim($rw['email'])!=''){
		$sentto[]=array($name,$addr);
		sendMassMail($name,$addr,$subj);
		$i++;
	}
}mysqli_close($con);

echo '<html><style>body{font-family:tahoma} ul{margin-left:20px;}</style><body>';
echo '<div><a href="../"><img src="'.DLC_ROOT.'/src/dlc_logo_min.png" alt="'.DLC_FULL.'" /></a></div><br />';
echo '<p>Successfully sent '.$i.' e-mail'.(($i>1)?'s':'').' to the following:</p><ul>';
foreach($sentto as $value){
	echo '<li>'.$value[0].' ('.$value[1].')</li>';
}
echo '</ul></body></html>';
unset($sentto);
ob_end_flush();

function sendMassMail($name,$addr,$subj){
	$headers ='MIME-Version: 1.0'."\r\n";
	$headers.='Content-type: text/html; charset=iso-8859-1'."\r\n";
	$headers.='To: '.$addr."\r\n";
	$headers.='From: DLC MAILER <DONOTREPLY@diamondlifestylecorporation.com>'."\r\n";

	$msg ='<html><body>';
//	$msg = '<div><img src="http://www.diamondlifestylecorporation.com/src/dlc_logo_min.png" alt="Diamond Lifestyle Corporation" /></div><br />';
//	$msg.='<p>Dear <b>'.$name.'</b>:<p><br />';
	$msg.='<p><img src="http://www.diamondlifestylecorporation.com/images/mails/invitation.jpg" alt="Elevator Plan" /></p>';
	$msg.='</body></html>';

	mail($addr,$subj,$msg,$headers);
}

function getName($id){
	$con=SQLi('distributor');
	$rs = mysqli_query($con,"SELECT dsfnam,dsmnam,dslnam FROM distributors WHERE dsdid = '$id'") or die(mysqli_error($con));
	$rw = mysqli_fetch_array($rs);
	return $rw['dslnam'].', '.$rw['dsfnam'].' '.(($rw['dsmnam']!='')?substr($rw['dsmnam'],0,1).'.':'');
}
?>
