<?php
if( !isset($_SESSION) ) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK', 1);

foreach( $_POST as $k=>$v ) $$k=$v;
$dbpro = (isset($_SESSION['dbprod']) && $_SESSION['dbprod']) ? true : false;

if( isset($submit) ) {
	switch($submit) {
		case 'Submit':
			$dbsrc = $dbpro ? 'products' : 'beta';
			include('infoconfig.php');

			$qry = "UPDATE tbl$tbl SET $editvar=$status WHERE id='$id'";
			mysqli_query($con,$qry) or die(mysqli_error($con));
			mysqli_close($con);
			echo $status;
			break;

		case 'online':
			$ret   = '';
			$dbsrc = 'orders';
			include('infoconfig.php');
			include('setup.php');

			$is_encoding = isset($_SESSION['a_page']) && $_SESSION['a_page'] == 'encoding';

			if( ISIN_ADMIN && NOTIF_ON && !$is_encoding ) {
				$qry = "
				SELECT
					(SELECT COUNT(*) ord FROM ". DB ."orders.tblorders WHERE deliStat=1) ord,
					(SELECT COUNT(*) ord FROM ". DB ."beta.tblolreg WHERE status=0) reg,
					(SELECT COUNT(*) ord FROM ". DB ."distributor.distributors d
						WHERE CONCAT(SUBSTR(dssetd,1,4),'-',SUBSTR(dssetd,5,2),'-',SUBSTR(dssetd,7,2))>=NOW()-INTERVAL ".newDistriDays." DAY
						AND NOT EXISTS (SELECT 1 FROM ". DB ."beta.tblnewdistri n WHERE n.dsdid = d.dsdid AND n.status=1)) encode,
					(SELECT COUNT(*) ord FROM ". DB ."distributor.responsor WHERE status=1) responsor,
					(SELECT COUNT(*) ord FROM ". DB ."products.tblfda WHERE expiry < DATE_ADD(NOW(), INTERVAL 6 MONTH)) fda
				";

				$rs = mysqli_query($con,$qry) or die(mysqli_error($con));
				$r  = mysqli_fetch_assoc($rs);

				foreach($r as $k=>$v) $$k=$v;

				if( $ord>0 ) $ret .= '<li><a href="/admin/orders/?get=1">('.$ord.') Order'.($ord>1?'s':'').'</a></li>';
				if( $reg>0 ) $ret .= '<li><a href="/admin/distriserve/?p=olreg&do=0">('.$reg.') Registrant'.($reg>1?'s':'').'</a></li>';
				if( $encode>0 ) $ret .= '<li><a href="/admin/distriserve/?p=newdistri&do=0">('.$encode.') Encoded</a></li>';
				if( $responsor>0 ) $ret .= '<li><a href="/admin/distriserve/?p=responsor&do=0">('.$responsor.') Responsor</a></li>';
				if( $fda>0 ) $ret .= '<li><a href="/admin/logistics/?p=fda">('.$fda.') FDA Renewal</a></li>';
				if( $ret!='' ) $ret='<li><a>Online</a><a href="#" id="close">x</a></li>'.$ret;
				mysqli_close($con);
			}

			echo $ret;
			break;
	}
}
unset($_POST);
?>
