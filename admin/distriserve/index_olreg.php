<?php if ( !defined('INCLUDE_CHECK') ) die('Invalid Operation');
if ( !isset($_SESSION) ) session_start();
$x    = '';
$tbl  = 'tbl'. $content;
$type = ( isset($_GET['type']) ?$_GET['type'] :0 );
$arr  = array('PENDING', 'CONFIRMED', 'VOID');
$con  = SQLi('beta');

if ($do==0) {

	$x .= '<div><label class="s3"><input type="radio" name="olreg_type" value=0 '.($type==0?C_K:'').' />'.$arr[0].'</label> <label class="s3"><input type="radio" name="olreg_type" value=1 '.($type==1?C_K:'').' />'.$arr[1].'</label> <label class="s3"><input type="radio" name="olreg_type" value=2 '.($type==2?C_K:'').' />'.$arr[2].'</label></div><br />';

	$x .= '<ul id="'.$content.'" class="list">';
	$x .= '<li class="hdr"><span class="s4">Distributor ID</span><span class="s5">Name</span><span class="s6">Sponsor</span><span class="s2">Submitted</span><span class="s1">Status</span></li>';

	$qry = "SELECT o.*,
		CONCAT(o.dslnam,', ',o.dsfnam,' ',o.dsmnam) onam,
		(CASE WHEN o.dssid<>'' THEN CONCAT(d.dslnam,', ',d.dsfnam,' ',d.dsmnam) END) dnam
	FROM $tbl o
	LEFT JOIN ".DB."distributor.distributors d
		ON d.dsdid=o.dssid
	WHERE o.status=$type";

	$rs = mysqli_query($con,$qry) or die(mysqli_error($con));
	while ( $rw=mysqli_fetch_assoc($rs) ) {
		$styleis =! $rw['status']?'bad':'';
		$x .= '<li rel="'.$rw['id'].'">';
		$x .= '<span class="s4">'.$rw['dsdid'].'</span>';
		$x .= '<span class="s5"><a href="?p='.$content.'&do=2&i='.$rw['id'].'">'.ucwords(utf8_decode(strtolower(($rw['onam'])))).'</a></span>';
		$x .= '<span class="s6">'.($rw['dssid']!=''?$rw['dssid'].' ':'-').($rw['dnam']!=''?ucwords(strtolower(utf8_decode($rw['dnam']))):'Waiting for ENCODE Request').'</span>';
		$x .= '<span class="s2">'.date('Y.m.d',strtotime($rw['date'])).'</span>';
		$x .= '<span class="s1 '.$styleis.'">'.$arr[$rw['status']].'</span></li>';
	}

	mysqli_close($con);
	$x .= '</ul>';

} else {

	if ($do==2) {

		$rs = mysqli_query($con,"SELECT * FROM $tbl WHERE id='$item'") or die(mysqli_error($con));
		if ( mysqli_num_rows($rs)>0 ) {
			$rw = mysqli_fetch_assoc($rs);
			foreach ($rw as $i=>$v) $$i = "$v";
			mysqli_close($con);

			$test    = testAllow($dssid);
			$allowed = ( ISIN_ADMIN ? '<span class="'. ( $test ? 'good"> [ ' : 'bad"> [NOT' ) . 'ALLOWED TO SPONSOR ]</span> ' :'');

			$scn     = testImg("../../reg/scan/$item");
			$arrSex  = array('Male','Female');
			$arrStat = array('Single','Married','Separated','Widow/er');

			$x .= '<ul class="print"><li><a href="javascript:window.print()"></a></li></ul>';

			$x .= '<form method="post" id="olreg" action="/admin/update.php?t='.$content.'"><ul><input type="hidden" name="id" value="'.$id.'" />';
			$x .= '<li><label>Distributor ID:</label><input type="text" name="dsdid" class="txt ex" value="'.$dsdid.'" maxlength=16 /></li>';
			$x .= '<li><label>Last Name:</label><input type="text" name="dslnam" class="txt" value="'.strtoupper($dslnam).'" /></li>';
			$x .= '<li><label>First Name:</label><input type="text" name="dsfnam" class="txt" value="'.strtoupper($dsfnam).'" /></li>';
			$x .= '<li><label>Middle Name:</label><input type="text" name="dsmnam" class="txt" value="'.strtoupper($dsmnam).'" /></li>';
			$x .= '<li><label>Birth Date:</label><input type="text" name="dsbday" class="txt" value="'.$dsbday.'" /></li>';
			$x .= '<li><label>Spouse:</label><input type="text" name="spouse" class="txt" value="'.strtoupper($spouse).'" /></li>';
			$x .= '<li><label>Gender:</label><label class="s3"><input type="radio" name="dssex" value="0" '.(!$dssex?C_K:'').' />'.$arrSex[0].'</label> &nbsp; <label class="s3"><input type="radio" name="dssex" value="1" '.($dssex?C_K:'').' />'.$arrSex[1].'</label></li>';
			$x .= '<li><label>Marital Status:</label><input type="radio" name="dsstat" value="0" '.(!$dsstat?C_K:'').' />'.$arrStat[0].'</label> &nbsp;';
				$x .= '<label class="s3"><input type="radio" name="dsstat" value="1" '.($dsstat==1?C_K:'').' />'.$arrStat[1].'</label> &nbsp; ';
				$x .= '<label class="s3"><input type="radio" name="dsstat" value="2" '.($dsstat==2?C_K:'').' />'.$arrStat[2].'</label> &nbsp; ';
				$x .= '<label class="s3"><input type="radio" name="dsstat" value="3" '.($dsstat==3?C_K:'').' />'.$arrStat[3].'</label></li>';
			$x .= '<li><label>Mobile / Phone:</label><input type="text" name="dscont" class="txt" value='.$dscont.' /></li>';
			$x .= '<li><label>Unit / Street:</label><input type="text" name="dsstrt" class="txt" value="'.$dsstrt.'" /></li>';
			$x .= '<li><label>Subd / Brgy:</label><input type="text" name="dsbrgy" class="txt" value="'.$dsbrgy.'" /></li>';
			$x .= '<li><label>Town / City:</label><input type="text" name="dscity" class="txt" value="'.$dscity.'" /></li>';
			$x .= '<li><label>Province / ZIP:</label><input type="text" name="dsprov" class="txt" value="'.$dsprov.'" /></li>';
			$x .= '<li><label>Country:</label><input type="text" name="dscoun" class="txt" value='.$dscoun.' /></li>';
			$x .= '<li><label>TIN:</label><input type="text" name="dstin" class="txt" value='.$dstin.' /></li>';
			$x .= '<li><label>BPI Acct Name:</label><input type="text" name="dsbankact" class="txt" value="'.$dsbankact.'" /></li>';
			$x .= '<li><label>BPI Acct #:</label><input type="text" name="dsbankno" class="txt" value="'.$dsbankno.'" /></li>';
			$x .= '<li><label>E-mail:</label><input type="text" name="dsemail" class="txt" value='.$dsemail.' /></li>';
			$x .= '<li><label>Sponsor ID:</label><input type="text" name="dssid" class="txt s4" value="'.$dssid.'" /> '.( $dssid!='' ? strtoupper(getName($dssid,'fml')) . $allowed : 'Waiting for ENCODE Request' ).'</li>';
			$x .= '<li><label>Contact:</label><input type="text" name="dsscont" class="txt" value="'.$dsscont.'" /></li>';
			$x .= '<li><label>Submitted:</label><input type="text" name="date" class="txt" value="'.$date.'" '.READONLY.' /></li>';
			$x .= '<li><label>Referrer:</label><input type="text" name="referrer" class="txt s4" value="'.$referrer.'" '.READONLY.' /> <span>'.strtoupper(getName($referrer,'fml')).'</span><input type="hidden" name="noslot" value="'.$noslot.'" /></li>';
			$x .= $noslot ? '<li><label>Package:</label><span>Kindly check purchased package. No slot required.</span><a href="'.DLC_ADMIN.'/orders/?get=1" id="download" target="_blank">CHECK ORDER</a></li>' :'';
			$x .= '<li><label>Status:</label><label class="s3"><input type="radio" name="status" value="1" '.($status==1?C_K:'').' />'.$arr[1].'</label> &nbsp; <label class="s3"><input type="radio" name="status" value="0" '.($status==0?C_K:'').' />'.$arr[0].'</label> &nbsp; <label class="s3"><input type="radio" name="status" value="2" '.($status==2?C_K:'').' />'.$arr[2].'</label></li>';
			$x .= '<li><label>Copy of ID:</label><span class="s5">'.($scn!=''?'<a href="'.$scn.'" target="_blank">CLICK TO VIEW SCANNED COPY</a>':'').'</span></li>';
		}
	}

	$x .= '<input type="hidden" name="do" value="'.$do.','.(isset($id)?$id:'').'" /><input type="submit" name="submit" class="btn" value="Submit" /></ul></form>';

}

echo $x;
?>
