<?php
if (!isset($_SESSION)) {
	session_set_cookie_params(0);
	session_start();
}
if (!defined('INCLUDE_CHECK')) die('Invalid Operation');

$x   = '';
$tbl = 'tbl'. $content;
$con = SQLi('orders');

if( $do==0 ) {
	$old = $nam1 = '';
	$rel_ok= $rel= $t= 0;

	$h  = '<li class="hdr"><span class="s4 lt">ID</span><span class="s5">Name</span><span class="s5">From</span><span class="s2 rt">BB Bonus</span><span class="s2 rt">Qualified</span><span class="s2 rt">Released</span></li>';
	$x .= '<ul id="'.$content.'" class="list">'.$h;

	$qry = "
		SELECT n.*,
			CONCAT(d.dsfnam,' ',LEFT(d.dsmnam,1),' ',d.dslnam) nam1,
			CONCAT(l.dsfnam,' ',LEFT(l.dsmnam,1),' ',l.dslnam) nam2
		FROM $tbl n
		LEFT JOIN ".DB."distributor.distributors d ON d.dsdid=rsdid
		LEFT JOIN ".DB."distributor.distributors l ON l.dsdid=rslid
		ORDER BY released,rsdid,rsref DESC
	";

	$rs = $con->query($qry) or die(mysqli_error($con));
	while( $rw = $rs->fetch_assoc() ) {
		$nam = $nam1;
		foreach($rw as $k=>$v) { $$k=$v; }

		$rel_ok = ( $released && !$rel ? 1 : 0 );

		$link = ( testScope("global|accounting") ) ? '?p='.$content.'&do=2&i='.$id :'';

		if( ($old!=$rsdid&&$old!=''&&!$released) || ($released&&$rel_ok) ) {
			$x  .=  '<li><strong class="s4 u">'.$old.' </strong>';
			$x .= '<strong class="s5 u">'.$nam.' </strong>';
			$x .= '<strong class="s5 u">Unreleased Bonus</strong>';
			$x .= '<strong class="s2 rt u">'.number_format($t,2).'</strong>';
			$x .= '<strong class="s2 u">&nbsp;</strong><br><br></li>';
			$t  = 0;
		}

		if( $released && $rel_ok ) {
			$rel = 1;
			$x .= '<li><br><h3>Released</h3></li>'.$h;
		}

		$x .= '<li rel="'.$id.'"><span class="s4"><a href="'.$link.'">'.$rsdid.'</a></span>';
		$x .= '<span class="s5">'.$nam1.'</span>';
		$x .= '<span class="s5">'.$nam2.'</span>';
		$x .= '<span class="s2 rt">'.number_format($rsamt,2).'</span>';
		$x .= '<span class="s2 rt">'.( $rsref!=null ? formatDate(substr($rsref,2,8)) : '-' ).'</span>';
		$x .= '<span class="s2 rt">'.( $released!=null ? formatDate($released) : '-' ).'</span></li>';

		$old = $rsdid;
		$t  += $rsamt;
	}

	mysqli_close($con);
	$x .= '</ul>';

} else {
	$datenow  = date(TMDSET,time());
	$readonly = (testScope("global|accounting")) ? '': READ_ONLY;

	if( $do==2 ) {
		$con = SQLi('orders');
		$rs  = $con->query("SELECT * FROM $tbl WHERE id='$item'") or die(mysqli_error($con));
		while($rw = $rs->fetch_assoc()) foreach($rw as $k=>$v) { $$k=$v; }

		$x .= '<li><label>Distributor:</label><span>'.$rsdid.'</span> <span>'.getName($rsdid,'fml').'</span></li>';
		$x .= '<li><label>From:</label><span>'.$rslid.'</span> <span>'.getName($rslid,'fml').'</span></li>';
		$x .= '<li><label>Referral:</label><span>'.number_format($rsamt,2).'</span></li>';
		$x .= '<li><label>Qualified:</label><span>'.($rsref!=null?formatDate(substr($rsref,2,8)):'-').'</span></li>';
		$x .= '<li><label>Released:</label><input type="text" name="released" class="txt s4" value="'.($released!=''?$released:date('Ymd')).'" maxlength=10 /> <span class="more">format: yyyymmdd</span>';
		$x .= '<input type="hidden" name="do" value="'.$do.','.$id.'" /></li>';

		mysqli_close($con);
	}

	$x  = '<form method="post" id="referral" action="/admin/update.php?t='.$content.'"><ul>'.$x;
	$x .= '<input type="submit" name="submit" class="btn" value="Submit" /></ul></form>';
}

echo $x;
?>
