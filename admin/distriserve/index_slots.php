<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();

testScope("global|distri", DLC_ADMIN);

$x   = '';
$tbl = 'tbl' . $content;
$con = SQLi('orders');

if( $do==0 ) {
	$u_ok= $u= 0;

	$h  = '<li class="hdr"><span class="s1">Unused</span><span class="s5">Slot from ID</span><span class="s5">Sponsor ID</span><span class="s5">Downline ID</span><span class="s2 lt">Received</span><span class="s2 lt">Used</span></li>';
	$x .= '<ul id="'.$content.'" class="list">'.$h;

	$qry = "
		SELECT slotid,used,ref,
			COUNT(CASE WHEN (used IS NOT NULL) THEN 0 ELSE used END) isused,
			COUNT(CASE WHEN (used='' OR used IS NULL) THEN 0 ELSE used END) unused,
			s.dsdid,CONCAT(d1.dslnam,', ',d1.dsfnam,' ',SUBSTR(d1.dsmnam,1,1),'.') did,
			s.dslid,CONCAT(d2.dslnam,', ',d2.dsfnam,' ',SUBSTR(d2.dsmnam,1,1),'.') dfr,
			s.dsfor,CONCAT(d3.dslnam,', ',d3.dsfnam,' ',SUBSTR(d3.dsmnam,1,1),'.') lid
		FROM ".DB."orders.tblslots s
		LEFT JOIN ".DB."distributor.distributors d1 ON d1.dsdid = s.dsdid
		LEFT JOIN ".DB."distributor.distributors d2 ON d2.dsdid = s.dsfor
		LEFT JOIN ".DB."distributor.distributors d3 ON d3.dsdid = s.dslid
		WHERE s.dsdid<>'void'
		GROUP BY (CASE WHEN (used IS NOT NULL) THEN s.dslid ELSE s.dsdid END)
		ORDER BY (CASE WHEN (used IS NOT NULL) THEN 0 ELSE used END),s.dsdid,used DESC,ref
	";

	$rs = mysqli_query($con,$qry) or die(mysqli_error($con));
	while( $rw=mysqli_fetch_assoc($rs) ) {
		foreach( $rw as $k=>$v ) $$k=$v;

		$u_ok = ( $used && !$u ? 1 : 0 );

		if( $used && $u_ok ) {
			$x .= '<li><br><h3>Used</h3></li>'.$h;
			$u  = 1;
		}

		$x .= '<li rel="'.$slotid.'">';
		$x .= '<span class="s1">'.(!$used?$unused:' ').'</span>';
		$x .= '<span class="s5"><a href="?p='.$content.'&do=2&i='.$slotid.'">'.strtoupper($dsdid).'<br>'.ucwords(strtolower($did)).'</a></span>';
		$x .= '<span class="s5">'.$dsfor.'<br>'.ucwords(strtolower($dfr)).'</span>';
		$x .= '<span class="s5">'.$dslid.'<br>'.ucwords(strtolower($lid)).'</span>';
		$x .= '<span class="s2 lt">'.substr($ref,0,4).' '.substr($ref,4,2).'</span>';
		$x .= '<span class="s2 lt">'.(formatDate($used,TMDSET,1)?date('m.d.Y',strtotime($used)):'').'</span></li>';
	}

	mysqli_close($con);
	$x .= '</ul>';

} else {

	if( $do==1 ) {
		$x .= '<form method="post" id="slots" rel="'.WKYR.WEEK.'" action="/admin/update.php?t='.$content.'"><ul><input type="hidden" name="slotid" value="" />';
		$x .= '<li><label>Slot from ID:</label><input type="text" name="dsdid" class="txt ex" /></li>';
		$x .= '<li><label>Online Reg ID:</label><input type="hidden" name="olregid" /></li>';
		$x .= '<li><label>Sponsor ID:</label><input type="hidden" name="dsfor" /></li>';
		$x .= '<li><label>Downline ID:</label><input type="hidden" name="dslid" /></li>';
		$x .= '<li><label>Slot Used:</label><input type="hidden" name="used" /></li>';
		$x .= '<li><label>Slot Reference:</label><input type="text" name="ref" class="txt ex" value="'.WKYR.WEEK.'" '.READ_ONLY.' /></li>';

	} else if( $do==2 ) {
		$rs = mysqli_query($con,"SELECT * FROM $tbl WHERE slotid='$item'") or die(mysqli_error($con));
		$rw = mysqli_fetch_assoc($rs);

		foreach($rw as $i=>$v) $$i=$v;

		$used = ( $used!='' ? $used : date(TMDSET) );

		mysqli_close($con);
		$x .= '<ul class="print"><li><a href="javascript:window.print()"></a></li></ul>';

		$x .= '<form method="post" id="slots" rel="'.WKYR.WEEK.'" action="/admin/update.php?t='.$content.'"><ul><input type="hidden" name="slotid" value="'.$slotid.'" />';
		$x .= '<li><label>Slot from ID:</label><input type="text" name="dsdid" class="txt ex" value="'.$dsdid.'" maxlength=32 /></li>';
		$x .= '<li><label>Online Reg ID:</label><input type="text" name="olregid" class="txt ex" value="'.$olregid.'" maxlength=16 /></li>';
		$x .= '<li><label>Sponsor ID:</label><input type="text" name="dsfor" class="txt ex" value="'.$dsfor.'" maxlength=32 /></li>';
		$x .= '<li><label>Downline ID:</label><input type="text" name="dslid" class="txt ex" value="'.$dslid.'" maxlength=32 /></li>';
		$x .= '<li><label>Slot Used:</label><input type="text" name="used" class="txt ex" value="'.$used.'" /></li>';
		$x .= '<li><label>Slot Reference:</label><input type="text" name="ref" class="txt ex" value="'.$ref.'" maxlength=24 /></li>';
	}

	$x .= '<input type="hidden" name="do" value="'.$do.','.(isset($slotid)?$slotid:'').'" /><input type="submit" name="submit" class="btn" value="Submit" /></ul></form>';
}

echo $x;
?>
