<?php
if( !isset($_SESSION) ) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK', 1);
require_once('../setup.php');

if( !ISIN_ADMIN && !isset($_SESSION['login_id']) && !isset($_POST['submit']) ) {
	reloadTo(DLC_ADMIN);
	exit;
}

$con = SQLi('beta');
foreach( $_POST as $key=>$val ) {
	$$key = trim_escape($val);
}

foreach( $_SESSION as $k=>$v ) {
	$_SESSION[$k] = trim_escape($v);
}

if( isset($submit) && $submit=='Login' ) {
	$pw = md5($un.$pw.'@)!)');
//echo "SELECT * FROM tbladmin WHERE un='$un' AND status=1";
	$check = mysqli_query($con,"SELECT * FROM tbladmin WHERE un='$un' AND status=1") or die(mysqli_error($con));
	$rs = mysqli_num_rows($check);

	if( $rs==0 ) {						/*query if un exist*/
		mysqli_close($con);
		$_SESSION['bad_admin'] = true;
		reloadTo('../');
	}
	while($info=mysqli_fetch_array($check)) {
		$pwql = stripslashes($info['pw']);
		$name = stripslashes($info['nn']);
		mysqli_close($con);

		if( $pw!=$pwql ) {					/*pwword check*/
			$_SESSION['bad_admin'] = true;
			reloadTo('../');

		} else {
			$un = stripslashes($un);
			$_SESSION['login_id']   = $un;
			$_SESSION['login_name'] = $name;
			$_SESSION['login_type'] = 'admin';
			$_SESSION['a_logged']   = true;
			$_SESSION['bad_admin']  = false;

			if( testScope("global") ) {
				$_SESSION['maintenance'] = true;

			} elseif( testScope("apc") ) {
				$_SESSION['apc_id'] = $un;
			}

			reloadTo(DLC_ADMIN);
		}
	}
}
?>
