<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();

testScope("global|distri", DLC_ADMIN);

$tbl  = $content;
$goup = ($adminpage=='distriserve')?'../':'';
$con  = SQLi('distributor');

$x = '';
$qry = "SELECT *,
		(SELECT CONCAT(dslnam,', ',dsfnam,' ',dsmnam) FROM distributors WHERE dsdid=r.dsdid) mysname,
		(SELECT CONCAT(dslnam,', ',dsfnam,' ',dsmnam) FROM distributors WHERE dsdid=r.dssid) oldsname,
		(SELECT CONCAT(dslnam,', ',dsfnam,' ',dsmnam) FROM distributors WHERE dsdid=r.newsponsor) newsname
	FROM responsor r
";
$order = "ORDER BY date DESC,r.status DESC";

if ( $do==0 ) {
	$x .= '<ul id="'.$content.'" class="list">';
	$x .= '<li class="hdr">';
		$x .= '<span class="s4 ct">ID</span>';
		$x .= '<span class="s5">Name</span>';
		$x .= '<span class="s5">Old Sponsor</span>';
		$x .= '<span class="s5">New Sponsor</span>';
		$x .= '<span class="s1 rt">Date</span>';
		$x .= '<span class="s1 rt">Status</span></li>';

	$rs = mysqli_query($con,"$qry $order") or die(mysqli_error($con));
	while( $rw=mysqli_fetch_assoc($rs) ) {
		foreach($rw as $k=>$v) $$k=$v;

		$linkisglobal = testScope("global") ? '<a href="?p='.$content.'&do=2&i='.$dsdid.'">'.$dsdid.'</a>' : $dsdid;

		$x .= '<li rel="'.$dsdid.'"><span class="s4">'.$linkisglobal.'</span>';
		$x .= '<span class="s5">'.$mysname.'</span>';
		$x .= '<span class="s5" title="'.$dssid.'">'.$oldsname.'</span>';
		$x .= '<span class="s5" title="'.$newsponsor.'">'.$newsname.'</span>';
		$x .= '<span class="s1 rt">'.date('m.d.Y',strtotime($rw['date'])).'</span>';
		$x .= '<span class="s1 rt">' . $status . '</span></li>';
	}
	
	mysqli_close($con);
	$x .= '</ul>';

}else{
	$dsdid= $dssid= $dsdid= $mysname= $oldsname= $newsname= $newsname= $newsname= $newsponsor= $date= '';
	$rs = mysqli_query($con,"$qry WHERE dsdid='$item' $order") or die(mysqli_error($con));
	$rw = mysqli_fetch_array($rs);
	if($rw) { foreach($rw as $k=>$v) $$k=$v; }
	else reloadTo('?p=responsor&do=0',1);

	$x .= '<li><label>Distributor:</label> <strong>'.$dsdid.'</strong> '.$mysname.' </li>';
	$x .= '<li><label>Old Sponsor:</label> <strong>'.$dssid.'</strong> '.$oldsname.' </li>';
	$x .= '<li><label>New Sponsor:</label> <strong>'.$newsponsor.'</strong> '.$newsname.' </li>';
	$x .= '<li><label>Requested:</label> '.date('m.d.Y',strtotime($date)).'</li>';
	$x .= '<li><label>Whole Line:</label> <input type="hidden" name="wholeline" value=0 /><input type="checkbox" name="wholeline" value=1 /></li>';
	$x .= '<li><label>Status:</label> <strong>'. ( $status ? 'PENDING' : 'PROCESSED' ) .'</strong></li>';
	$x .= '<li><br><h3>Direct Downlines <span class="smaller">will be moved up to the Old Sponsor</span></h3></li>';

	$qry = "SELECT dsdid,CONCAT(dslnam,', ',dsfnam,' ',dsmnam) nam FROM distributors WHERE dssid=$dsdid";
	$rs  = mysqli_query($con,"$qry") or die(mysqli_error($con));

	while( $r=mysqli_fetch_assoc($rs) ) {
		foreach($rw as $k=>$v) $$k=$v;
		$x .= '<li><label></label> <strong>'.$r['dsdid'].'</strong> '.$r['nam'].'</li>';
	}

	mysqli_close($con);

	$x  = '<form method="post" id="responsor_form" action="/admin/update.php?t='.$content.'"><ul>'.$x;
	$x .= '<input type="hidden" name="dssid" value="'.$newsponsor.'" />';
	$x .= '<input type="hidden" name="oldsp" value="'.$dssid.'" />';
	$x .= '<input type="hidden" name="do" value="'.$do.','.$dsdid.'" />';
	if( $status ) $x .= '<input type="submit" name="submit" class="btn" value="Submit" />';
	$x .= '</ul></form>';
}

echo $x;
?>
