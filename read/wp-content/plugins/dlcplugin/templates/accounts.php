<?php
if( !isset($_SESSION) ) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK', 1);
if( !isset($_SESSION['isLogged']) ) exit;

require_once( '../admin/setup.php' );
require_once( '../distrilog/updatelist.php' );

global $dlcuser;
$id    = DIST_ID;
$minpv = 100;
$dash  = '';
$over  = 88102; //MIG override

$qryAllowSponsorD=1; //getAllowQry($id,'distributor','d',$minpv);
$qryAllowSponsorO=1; //getAllowQry($id,'orders','o',$minpv);

$ppv= $npv= $gpv= $lev= $ndpv=0;
$baseid = strpos(DIST_ID,'-')!==false ? substr(DIST_ID,0,strpos(DIST_ID,'-')) :DIST_ID;
list($ppv,$npv,$gpv,$lev,$ndpv) = getCurrentPV($id);

for($i=0;$i<=4;$i++) {
     if( $i>0 ) $dash = "-$i";
     $x .= "(SELECT dsdid FROM distributors WHERE dsdid='$baseid$dash') id$i,";
}

// $x=substr($x,0,-1);
$reflink = DLC_ROOT .'/?rfr='. $id;
$hlv = getHighLev($id);
$pct = getPercent($lev>$hlv?$lev:$hlv,date('Y'),WEEK);
$qry = "SELECT
          CONCAT(dslnam,', ',dsfnam,' ',dsmnam) nam,dssid,dstin,$x $qryAllowSponsorD,$qryAllowSponsorO,
          (SELECT CONCAT(dslnam,', ',dsfnam,' ',dsmnam) FROM distributors WHERE dsdid=d.dssid) sname,
          (SELECT referrer FROM ". DB ."beta.tblolreg WHERE dsdid='$id' AND referrer='$over') refer
     FROM distributors d
     WHERE dsdid='$id'";
// echo $qry;
$con = SQLi('distributor');
$rs  = mysqli_query($con,$qry) or die(mysqli_error($con));
$rw  = mysqli_fetch_array($rs);

foreach($rw as $k=>$v) { $$k=$v; }
$allid = "$id0|$id1|$id2|$id3|$id4";
mysqli_close($con);

if ( preg_match("/630000038352|" . REPZ . "/i", $baseid) ) {
     // echo "$hlv<br><br>";
     // print_r($_SESSION);
}

$x  = '<ul id="distributor">';
$x .= '<li><h2>'.$nam.'</h2></li>';

if(isset($id1)) {
     $x .= '<li><label>Other Accounts:</label><div>';

     foreach( explode('|',$allid) as $k ) {
          if($k!=$id) $x .= '<span class="s4"><a href="?page_id=384&otherid='.$k.'">'.$k.'</a></span>';
     }

     $x .= '</div></li>';
}

$x .= '<li><hr></li>';
$x .= '<li><label>Sponsor:</label><br>';
$x .= '<div><span>'.$dssid.'</span> <span>'.ucwords(strtolower($sname)). '</span></div>';
$x .= '</li>';

$x .= '<li><hr></li>';
$x .= '<li class="pv">
     <div><label>Personal:</label> <span>'.number_format($ppv,2).' PV</span></div>
     <div><label>Group:</label> <span>'.number_format($npv,2).' PV</span></div>
     <div><label>Total PV:</label> <span>'.number_format($gpv,2).' PV</span></div>
     <div><label>Level:</label> <span>'.$pct.'</span></div>';
$x .= '</li>';

$x .= '<li><hr></li>';
$x .= '<li><label>Referral Link:</label><br>';
$x .= '<div><code>'.$reflink.'</code> <a href="#" class="reflink" rel="'.$reflink. '">Copy Link</a></div>';
$x .= '</li>';

if( $hlv < 1 ) {
     $x .= '<li><hr></li>';
     $x .= '<li><h4 class="good">PREFERRED DISTRIBUTOR</h4><br>';
     // $x .= '<div>You are qualified for 20% discount on Supplements, and 10% discount on all other products</div>';
     $x .= '<div>You need to accumulate <strong class="bad">'. MINPV_6pct . 'PV</strong> in one cut-off to become a FULL DISTRIBUTOR</div>';
     $x .= '</li>';
}

// if( $d_allow || $o_allow || isset($id1) || $refer==$over ) {}
// else {
//      $x .= '<li><hr></li>';
//      $x .= '<li><h4 class="good">RESELLER</h4><br>';
//      $x .= '<div>You need to accumulate '.$minpv.'PV in one cut-off to become a distributor</div>';
//      $x .= '</li>';
// }

if( $dstin=='' ) {
     $x .= '<li><hr></li>';
     $x .= '<li><h4 class="bad">NO TIN</h4><br>';
     $x .= '<div>Kindly update your TIN, as required by BIR. <strong class="bad">NO TIN, NO VOLUME BONUS</strong>.</div>';
     $x .= '</li>';
}

$profile  = get_page_by_title( 'Profile', '', 'page' );
$cart     = get_page_by_title( 'Cart', '', 'page' );

$cartbtn  = (isset($_SESSION['shoplist']))?'<input type="button" rel="'.get_permalink($cart->ID).'" class="link" value="Cart">':'';

$x .= '<li><hr></li>';
$x .= '<li><input type="button" rel="/distrilog/mypage.php" class="link" value="Dashboard"> ';
$x .= '<input type="button" rel="'.get_permalink($profile->ID).'?i='.$dlcuser['id']. '" class="link" value="Profile"> ';
$x .= '<input type="button" rel="/distrilog/myorders.php" class="link" value="Orders"> ';
$x .= $cartbtn.'</li>';

$x .= '</ul>';

echo $x;

function getHighLev($id) {
	$con = SQLi('beta');$n=0;
	$rs  = mysqli_query($con,"SELECT level FROM tblmanagers WHERE id='$id' AND status=1");

	if( mysqli_num_rows($rs)>0 ) {
		$rw = mysqli_fetch_array($rs);
		$n  = ( $rw['level']>2 ? 5 : $rw['level'] );

	} else {
		$con = SQLi('distributor');
		$rs  = mysqli_query($con,"SELECT bhelev FROM bohstp WHERE bhdid LIKE '$id' AND bhelev<>'' ORDER BY bhelev DESC LIMIT 1") or die(mysqli_error($con));

          if( mysqli_num_rows($rs)>0 ) {
			$rw = mysqli_fetch_array($rs);
			$n  = ( $rw['bhelev']>2 ? 2 : $rw['bhelev'] );
		}
	}

     mysqli_close($con);
	return $n;
}

function getAllowQry($id,$db,$d,$pv) {
     $history = ($d=='d') ? "OR EXISTS (SELECT bhppv FROM ".DB."$db.bohstp WHERE bhdid='".$id."' GROUP BY bhpyr,bhpmo HAVING bhppv>=$pv)" :'';
     return "
          (SELECT EXISTS (SELECT bmppv FROM ".DB."$db.bomstp WHERE bmdid='".$id."' HAVING bmppv>=$pv)
               OR EXISTS (SELECT SUM(ompv) pv FROM ormstp WHERE omdid='".$id."' GROUP BY ompyr,ompmo HAVING pv>=$pv)
               $history
          ) ".$d."_allow
     ";
}
?>
