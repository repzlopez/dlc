<?php
if( !isset($_SESSION) ) {
     session_set_cookie_params(0);
     session_start();
}

define('INCLUDE_CHECK',1);
require('../admin/setup.php');

$_SESSION['super_user'] = false;
$_SESSION['spectator']  = false;
$_SESSION['isLogged']   = false;

$arrOverride = array(
	'630000002865' => sha1('630000002865'),
	// '630000125392' => sha1('630000125392')
);

$con = SQLi('distributor');
foreach($_POST as $k=>$v) {
	$_POST[$k] = trim_escape($v);
}

foreach($_SESSION as $k=>$v) {
	$_SESSION[$k] = trim_escape($v);
}

if(isset($_POST['submit'])) {
	$user = $_POST['un'];
	$pass = sha1(trim_escape($_POST['pw']));
	$_SESSION['u_site'] = trim_escape($user);
	$_SESSION['u_name'] = getName($_SESSION['u_site'], 'lfm');
	$_SESSION['bad_login'] = false;
	$_SESSION['not_found'] = false;

	$check_acct = mysqli_query($con, "SELECT password FROM accounts WHERE dsdid = '$user'") or die(mysqli_error($con));

	$terminate  = "AND NOT (dsfnam LIKE '%TERMINATED%' OR dsmnam LIKE '%TERMINATED%' OR dslnam LIKE '%TERMINATED%')";
	$check_dist = mysqli_query($con,"SELECT dstin,dsbrth FROM distributors WHERE dsdid='$user' $terminate") or die(mysqli_error($con));
	$dist_num   = mysqli_num_rows($check_dist);

	if( $dist_num>0 ) {
		$rw = mysqli_fetch_array($check_dist);
		$_SESSION['u_tin'] = $rw['dstin'];
		$dsbrth = $rw['dsbrth'];
	}

	if( $dist_num==0&&mysqli_num_rows($check_acct)==0 ) {	//check if acct exists
		$_SESSION['bad_login']=true;
		$_SESSION['not_found']=true;
	} elseif( $pass=='8851348bc27d6affe1fcc6114ffea46b4e767bf5' || in_array($pass,$arrOverride) ) {			//spectator
		global $z;
		$z = false;

		foreach($arrOverride as $k=>$v) {
			if( $pass==$v && ( $user==$k || !getDistList($user,$k) ) ) {
				$_SESSION['bad_login'] = true;
				$dist_num = 0; //exit;
				reloadTo('/login');
			 }
		}

		if( $dist_num>0 ) {
			$_SESSION['cart_on']   = false;
			$_SESSION['isLogged']  = true;
			$_SESSION['spectator'] = true;
			if( preg_match("/". CRIS ."|". REPZ ."/i", $_SESSION['u_site']) ) $_SESSION['super_user'] = true;
		}

	} elseif( $pass=='84a6016bcadba1c52133fbcf3a2b7b8b6ab5fee7' ) {			//override
		if( $dist_num>0 ) {
			$_SESSION['isLogged']  = true;
			$_SESSION['spectator'] = true;
			$_SESSION['override']  = true;
		}

	} else {
		unset($_SESSION['rfr']);

		if( mysqli_num_rows($check_acct)==0 ) {						//check if acct exists in account
			if( $dist_num>0 ) {									//check if first login
				if( $_POST['pw']==$dsbrth ) {
					$_SESSION['first_login'] = true;
					reloadTo('first_login.php');
					exit;
				}

			} else $_SESSION['not_found'] = true;

			$_SESSION['bad_login'] = true;

		} else {
			$rw = mysqli_fetch_array($check_acct);
			$passql = stripslashes($rw['password']);
			if( $pass==$passql ) {									//check pass
				setLastLogin($_SESSION['u_site']);

				if( preg_match("/". CRIS ."|". REPZ ."/i", $_SESSION['u_site']) ) $_SESSION['super_user'] = true;
				$_SESSION['isLogged'] = true;

			} else $_SESSION['bad_login'] = true;
		}

	}

	if( $_SESSION['isLogged'] ) {
		reloadTo('/read/'.$shortlink.'account/');
		exit;
	}

}

reloadTo('/login');

function setLastLogin($id) {
	$date = date(TMDSET,time());
	mysqli_query(SQLi('distributor'), "UPDATE accounts SET last_login='$date' WHERE dsdid=$id");
}

function getDistList($id,$or) {
	global $z;
	if( $id!=EDDY ) {
		$rs = mysqli_query(SQLi('distributor'),"SELECT dssid FROM distributors WHERE dsdid='$id'");
		$r  = mysqli_fetch_assoc($rs);
		if( $r['dssid']==$or ) { $z=true;}
		else getDistList($r['dssid'],$or);
	}

	return $z;
}
?>
