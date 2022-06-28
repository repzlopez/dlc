<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if( !isset($_SESSION) ) session_start();
if( !IS_GLOB ) {
	reloadTo(DLC_ADMIN);
	exit;
}
$x = '';
$tbl = 'tbl'. $content;
$con = SQLi('beta');
if( $do==0 ) {
	$x .= '<ul id="'.$content.'" class="list">';
	$x .= '<li class="hdr"><span class="s3">Admin ID</span><span class="s4">Name</span><span class="s2">Status</span></li>';

	$rs = mysqli_query($con,"SELECT * FROM $tbl ORDER BY un") or die(mysqli_error($con));
	while($r=$rs->fetch_assoc()) {
		$styleis =! $r['status'] ? 'bad' :'';
		if( $r['un']!='repz' ) $x .= '<li rel="'.$r['id'].'"><span class="s3"><a href="?p='.$content.'&do=2&i='.$r['id'].'">'.$r['un'].'</a></span><span class="s4">'.$r['nn'].'</span><span class="s2 stat '.$styleis.'">'.$r['status'].'</span></li>';
	}

	mysqli_close($con);

	$x .= '</ul>';

} else {
	$id = null;
	switch( $do ) {
		case 1:
			$rs = mysqli_query($con, "DESC $tbl");
			while ($r = $rs->fetch_assoc()) $arr[$r['Field']] = '';
			foreach ($arr as $k => $v) $$k = $v;

		case 2:
			$rs = mysqli_query($con, "SELECT * FROM $tbl WHERE id='$item'") or die(mysqli_error($con));
			while($r = $rs->fetch_assoc()) {
				foreach ($r as $k => $v) $$k = $v;
			}
			break;
	}

	mysqli_close($con);
	$h = ($item>900) ? ' class="hide"' :'';

	$x .= '<li><label>ID#:</label><input type="text" name="id" value="'.$id.'" maxlength=3 required /></li>';
	$x .= '<li><label>Admin ID:</label><input type="text" name="un" class="txt" value="'.$un.'" required /></li>';
	$x .= '<li><label>Name:</label><input type="text" name="nn" class="txt" value="'.$nn.'" required /></li>';
	$x .= '<li'. $h .'><label>Password:</label><input type="password" name="pw" class="txt" value="" /></li>';
	$x .= '<li'. $h .'><label>Global:</label><input type="hidden" name="global" value=0 /><input type="checkbox" name="global" value=1 '.($global?C_K:'').' /></li>';
	$x .= '<li'. $h .'><label>Distri:</label><input type="hidden" name="distri" value=0 /><input type="checkbox" name="distri" value=1 '.($distri?C_K:'').' /></li>';
	$x .= '<li'. $h .'><label>Data:</label><input type="hidden" name="data" value=0 /><input type="checkbox" name="data" value=1 ' . ($data ? C_K : '') . ' /></li>';
	$x .= '<li'. $h .'><label>Encoding:</label><input type="hidden" name="encoding" value=0 /><input type="checkbox" name="encoding" value=1 ' . ($encoding ? C_K : '') . ' /></li>';
	$x .= '<li'. $h .'><label>Registration:</label><input type="hidden" name="registration" value=0 /><input type="checkbox" name="registration" value=1 ' . ($registration ? C_K : '') . ' /></li>';
	$x .= '<li'. $h .'><label>Lookup:</label><input type="hidden" name="lookup" value=0 /><input type="checkbox" name="lookup" value=1 '.($lookup?C_K:'').' /></li>';
	$x .= '<li'. $h .'><label>Orders:</label><input type="hidden" name="orders" value=0 /><input type="checkbox" name="orders" value=1 '.($orders?C_K:'').' /></li>';
	$x .= '<li'. $h .'><label>Logistics:</label><input type="hidden" name="logis" value=0 /><input type="checkbox" name="logis" value=1 '.($logis?C_K:'').' /></li>';
	$x .= '<li'. $h .'><label>BizDev:</label><input type="hidden" name="bizdev" value=0 /><input type="checkbox" name="bizdev" value=1 '.($bizdev?C_K:'').' /></li>';
	$x .= '<li'. $h .'><label>ProdDev:</label><input type="hidden" name="proddev" value=0 /><input type="checkbox" name="proddev" value=1 '.($proddev?C_K:'').' /></li>';
	$x .= '<li'. $h .'><label>Accounting:</label><input type="hidden" name="accounting" value=0 /><input type="checkbox" name="accounting" value=1 '.($accounting?C_K:'').' /></li>';
	$x .= '<li'. $h .'><label>GOS:</label><input type="hidden" name="gos" value=0 /><input type="checkbox" name="gos" value=1 '.($gos?C_K:'').' /></li>';
	$x .= '<li'. $h .'><label>PCM:</label><input type="hidden" name="pcm" value=0 /><input type="checkbox" name="pcm" value=1 '.($pcm?C_K:'').' /></li>';
	$x .= '<li'. $h .'><label>MIG:</label><input type="hidden" name="mig" value=0 /><input type="checkbox" name="mig" value=1 '.($mig?C_K:'').' /></li>';
	$x .= '<li'. $h .'><label>APC:</label><input type="hidden" name="apc" value=0 /><input type="checkbox" name="apc" value=1 '.($apc?C_K:'').' /></li>';
	$x .= '<li><label>Status:</label><input type="hidden" name="status" value=0 /><input type="checkbox" name="status" value=1 '.($status?C_K:'').' /></li>';
	$x .= '<li><input type="hidden" name="do" value="'.$do.','.$id.'" /></li>';

	$x  = '<form method="post" action="update.php?t='.$content.'"><ul>'.$x;
	$x .= '<input type="submit" name="submit" class="btn" value="Submit" /></ul></form>';
}

echo $x;
?>
