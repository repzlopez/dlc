<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
$imp='!important';
$w='width';
$m='margin';
$style= '
<style type="text/css">
body { background:#fff;border:#333 solid 1px;font:normal 14px courier;'.$m.':0;padding:10px;overflow:hidden;height:960px;'.$w.':720px; }
ul { padding:0;'.$m.':0; }
li { list-style:none; }
span { display:inline-block; }
.rt { text-align:right; }
.s2 { '.$w.':70px '.$imp.'; }
.s3 { '.$w.':96px '.$imp.'; }
.s4 { '.$w.':150px '.$imp.'; }
.s5 { '.$w.':244px '.$imp.'; }
.s6 { '.$w.':182px '.$imp.'; }
.s7 { '.$w.':500px '.$imp.'; }
.s8 { '.$w.':320px '.$imp.'; }
.hdr { '.$m.':10px 0;'.$w.':450px; }
.recaphead { '.$m.':0 0 10px;text-align:center; }
.recaptotals { '.$w.':490px;'.$m.':10px 0 10px 222px; }
.recap .btm,.print,#download { display:none '.$imp.'; }
.recappage { page-break-after:always; }
@page{
	size:8.5in 11in;margin:2cm;
	orphans:0;widows:0;
    '.$m.':0mm;
}
</style>
';
ob_start();
require_once("dompdf/dompdf_config.inc.php");
require('updaterecap.php');
$dompdf=new DOMPDF();
$filename='recap.pdf';
if(isset($_SESSION['multirecap'])&&$_SESSION['multirecap']){
	$d=0;$html=null;
	$recapdistri=$_SESSION['recapdistri'];
	ini_set('memory_limit', '-1');
	set_time_limit(600);
	foreach($recapdistri as $val){
		$_SESSION['u_site']=trim($val);
		$html.='<div class="recappage">'.getRecap().'</div>';
	}

	$dompdf->load_html('<html>'.$style.'<body>'.$html.'</body></html>');
	$dompdf->render();
	$pdf=$dompdf->output();
	$file_location='../downloads/recap/'.$filename;
	file_put_contents($file_location,$pdf);
	$_SESSION['recapcreated']=1;
	echo '<META HTTP-EQUIV=Refresh CONTENT="0; URL=../admin/index.php?p=recap">';

}else{
	$dompdf->load_html($style.$_SESSION['prints']['table']);
	$dompdf->render();
	$dompdf->stream($filename,array("Attachment" => 0));
}
?>
