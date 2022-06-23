<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
include '../setup.php';
$file=$_FILES['file'];
unset($_POST);
unset($_FILES);
$temp=$file['tmp_name'];
$msg='';$old='';$new='';$d=0;$qty=1;

if(isValid($file['type'])) {
	if(!isset($_SESSION['transfers'])) $_SESSION['transfers']=array();
	$data=fopen($temp,'r');
	$arr=array();
	while($rw=fgets($data)) {
		$n=explode(',',$rw);
		$new=$n[0];
		if($d>0&&$old!='') {
			if(array_key_exists($new,$arr)) {$arr[$new]+=(float)$n[1];}
			else{$arr[$new]=$qty=(float)$n[1];}
		}$old=$new;$d++;
	}unset($_SESSION['csverror']);

	foreach($arr as $x=>$y) {validate($x,getPName($x),$y);}
}else $_SESSION['csverror']=1;
reloadTo('index.php?p=transfer');

function validate($tm,$de,$qy) {
	$i=0;
	while($i<count($_SESSION['transfers'])&&$_SESSION['transfers'][$i][0]!=$tm) $i++;
	if($i<count($_SESSION['transfers'])) {
		if($_SESSION['transfers'][$i][2]!=$qy) {
			$_SESSION['transfers'][$i][1]=$de;
			$_SESSION['transfers'][$i][2]=$qy;
		}
	}else $_SESSION['transfers'][]=array($tm,$de,$qy);
}

function isValid($ft) {
	if($ft=='text/csv'||$ft=='application/csv'||$ft=='application/vnd.ms-excel') return true;
}
?>
