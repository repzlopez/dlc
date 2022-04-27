<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if( !isset($_SESSION) ) session_start();

$x    = '';
$tbl  = 'tbl'. $content;
$goup = ($adminpage=='distriserve') ? '../' :'';
$showall = isset($_GET['showalldistri']) ? $_GET['showalldistri'] : ( isset($_SESSION['showalldistri']) ? $_SESSION['showalldistri'] :0 );
$_SESSION['showalldistri'] = $showall;
$encoding_path = $_SESSION['a_page']=='encoding' ? 'encoding' : 'distriserve';

$qry = "	SELECT d.*,n.contact,
		CONCAT(d.dslnam,', ',d.dsfnam,' ',d.dsmnam) nam,
		CONCAT(SUBSTR(d.dssetd,1,4),'-',SUBSTR(d.dssetd,5,2),'-',SUBSTR(d.dssetd,7,2)) setup,
		(SELECT CONCAT(s.dslnam,', ',s.dsfnam,' ',s.dsmnam) FROM diamondl_distributor.distributors s WHERE s.dsdid=d.dssid) sponsor,
		(SELECT r.dssid FROM diamondl_beta.tblresponsor r WHERE r.id=d.dsdid LIMIT 1) responsor
	FROM ".DB."distributor.distributors d
	LEFT JOIN ".DB."beta.tblnewdistri n ON n.dsdid=d.dsdid
";

$con = SQLi('distributor');
switch( $do ) {
	case 0:
		$n = 1;
		$x .= '<div><input type="checkbox" id="showalldistri" value='. $showall .' '. ($showall? C_K :'') .' /> <label for="showalldistri">Show Contacted Distributors</label></div><br />';
		$x .= '<ul id="'.$content.'" class="list">';
		$x .= '<li class="hdr"><span class="s0"></span>
			<span class="s3">ID</span>
			<span class="s5">Name</span>
			<span class="s5">Sponsor</span>
			<span class="s1">TIN</span>
			<span class="s1">Contact</span>
			<span class="s1">Respon</span>
			<span class="s1 rt">Encoded</span></li>';

		// $add=$showall?"AND d.dsdid NOT LIKE 'DLC0%'":"HAVING setup>=NOW()-INTERVAL ".newDistriDays." DAY";
		$not  = $showall ? "": "NOT";
		$qry .= "WHERE $not EXISTS (SELECT * FROM ".DB."beta.tblnewdistri w WHERE w.dsdid=d.dsdid AND w.status=1)
			HAVING setup>=NOW()-INTERVAL ".newDistriDays." DAY ORDER BY setup DESC";

		$rs = mysqli_query($con,$qry) or die(mysqli_error($con));
		while( $r=mysqli_fetch_assoc($rs) ) {
			foreach($r as $k=>$v) $$k=$v;

			$tin_1 = trim($dstin)!='';
			$con_0 = (trim($dsoph)==''&&trim($dshph)==''&&trim($dsmph)=='');

			$x .= '<li><span class="s0">'. $n++ .'</span>
				<a href="'.DLC_ADMIN.'/'. $encoding_path .'/?p=newdistri&do=2&i='.$dsdid.'" class="s3">'.$dsdid.'</a>
				<span class="s5" title="'.utf8_encode($nam).'">'.utf8_encode($nam).'</span>
				<span class="s5" title="'.utf8_encode($sponsor).'">'.utf8_encode($sponsor).'</span>
				<span class="s1 '.($tin_1?'':'b bad').'">'.($tin_1?'':'x').'</span>
				<span class="s1 '.($con_0?'b bad':'').'">'.($con_0?'x':'').'</span>
				<span class="s1 '.($responsor?'b bad':'').'">'.($responsor?'x':'').'</span>
				<span class="s2 rt">'.date('m.d.Y',strtotime($setup)).'</span></li>';
		}

		mysqli_close($con);
		break;

	case 2:
		$qry .= "WHERE d.dsdid='$item'";

		$rs = mysqli_query($con,$qry) or die(mysqli_error($con));
		$r  = mysqli_fetch_array($rs);mysqli_close($con);
		foreach($r as $k=>$v) $$k=$v;

		$ins  = ($responsor?'Your responsorship in DLC Philippines has been approved.':'You are now a registered member of DLC Philippines.');
		$txt  = "Congratulations $dsfnam!"."\n";
		$txt .= "$ins "."\n";
		$txt .= "You can now log-in to our official website www.dlcph.com"."\n\n";
		$txt .= "Distributor ID: $dsdid"."\n";
		$txt .= "Default Password: ".sprintf("%08d",$dsbrth)."\n\n";
		$txt .= $dstin!=''?'':"Don't forget to send us your TIN#"."\n\n";
		$txt .= "Welcome to DLC Philippines!"."\n";
		$txt .= "Aim Higher!";

		$x .= '<ul id="'.$content.'" data-dsdid="'.$item.'">';
		$x .= '<li><strong>'.$item.'</strong> <span class="s6 rt">encoded '.date('m.d.Y',strtotime($setup)).'</span> '.($responsor?'<span class="s4 ct b bad">RESPONSORED</span>':'').(trim($dstin)==''?'<span class="s3 ct b bad">NO TIN#</span>':'').'</li>';
		$x .= '<li><strong class="blue">'.(strtoupper($nam)).'</strong></li>';
		$x .= '<li><textarea id="copyThis">'.$txt.'</textarea> <ul>';
		$x .= ($dsoph?'<li><input type="text" class="s5" value="'.$dsoph.'" /> <input type="button" class="copyLine" value="Copy" /></li>':'');
		$x .= ($dshph?'<li><input type="text" class="s5" value="'.$dshph.'" /> <input type="button" class="copyLine" value="Copy" /></li>':'');
		$x .= ($dsmph?'<li><input type="text" class="s5" value="'.$dsmph.'" /> <input type="button" class="copyLine" value="Copy" /></li>':'');
		$x .= '<li><span>'.$dsstrt.' '.$dsbarn.' '.$dscity.' '.$dsprov.'</span></li>';
		$x .= '<li><span>'.$dseadd.'</span></li></ul>';
		$x .= '<br /><input type="button" id="noContact" value=" No Contact " /> <input type="button" id="doneDistri" value=" DONE " /><span class="s5"></span><input type="button" id="copyMsg" value="Copy Message" /></li>';
		break;
}

$x .= '</ul>';
echo $x;
?>
