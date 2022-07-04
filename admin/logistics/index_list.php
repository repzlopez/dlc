<?php
if (!isset($_SESSION)) {
	session_set_cookie_params(0);
	session_start();
}
if (!defined('INCLUDE_CHECK')) die('Invalid Operation');

$con = SQLi('products');

if( isset($_POST['submit']) && $_POST['submit']=='UPLOAD' ) {
	$file = $_FILES['file'];
	$temp = $file['tmp_name'];											//live

	//	$temp_file='c://dlcwebtemp/prodstemp_'.date('Ymd',time()).'.csv';		//local
	//	$usefile=(strpos($_SERVER['SERVER_NAME'],'dlc')!==false) ? $temp_file:$temp;

	unset($_POST);
	unset($_FILES);

	$msg = 'List upload failed';

	if( isValid($file['type']) ) {
		$tbl = 'tbllist';
		$sqlcreate = "CREATE TABLE IF NOT EXISTS $tbl (
			id INT(5) UNSIGNED ZEROFILL,
			name LONGTEXT NOT NULL,
			pv FLOAT NOT NULL default '0',
			wsp FLOAT NOT NULL default '0',
			pov FLOAT NOT NULL default '0',
			srp FLOAT NOT NULL default '0',
			pmp FLOAT NOT NULL default '0',
			pmbonus FLOAT NOT NULL default '0',
			wt FLOAT NOT NULL default '0',
			status TINYINT(1) NULL default '0'
		)";
// echo "1 $sqlcreate<br>";

		// $sqluniq="ALTER TABLE $tbl ADD UNIQUE(id)";
		// $con->query($sqlcreate) or die(mysqli_error($con));
		// $con->query($sqluniq) or die(mysqli_error($con));
		postData($temp,$tbl);

/*
		move_uploaded_file($temp,$temp_file);
		$con->query(getQuery($tbl,'(@x,id,name,@x,pv,wsp,pov,srp,wt,@stat) SET status = IF(@stat="",1,0)',$usefile)) or die(mysqli_error($con));
*/

		$tbl = 'tblstocks';
		$sqlcreate = "CREATE TABLE IF NOT EXISTS $tbl (
			id INT(5) UNSIGNED ZEROFILL,
			safeqty INT(5) NOT NULL default '0'
		)";
// echo "2 $sqlcreate<br>";

		// $sqluniq="ALTER TABLE $tbl ADD UNIQUE(id)";
		// $con->query($sqlcreate) or die(mysqli_error($con));
		// $con->query($sqluniq) or die(mysqli_error($con));

		$idata = ",0". countCols();
		updateTable($tbl, $idata);

		$tbl = 'tblproducts';
		$sqlcreate = "CREATE TABLE IF NOT EXISTS $tbl (
			id INT(5) UNSIGNED ZEROFILL,
			cat VARCHAR(32) NOT NULL,
			subcat VARCHAR(64) NOT NULL,
			pfda VARCHAR(16) NOT NULL,
			fdalink TEXT NOT NULL,
			size VARCHAR(128) NOT NULL,
			contains LONGTEXT NOT NULL,
			description LONGTEXT NOT NULL,
			sort_order INT(3) UNSIGNED ZEROFILL default '999',
			img VARCHAR(64) NOT NULL,
			stock TINYINT(1) NULL default '0',
			promo TINYINT(1) NULL default '0',
			slp TINYINT(1) NULL default '0',
			feat TINYINT(1) NULL default '0',
			status TINYINT(1) NULL default '0'
		)";
// echo "3 $sqlcreate<br>";

		// $sqluniq="ALTER TABLE $tbl ADD UNIQUE(id)";
		// $con->query($sqlcreate) or die(mysqli_error($con));
		// $con->query($sqluniq) or die(mysqli_error($con));

		$idata = ",'','','','','','','','999','',0,0,0,0,0";
		updateTable($tbl, $idata);

		$msg = 'List successfully uploaded';
		// echo '<META HTTP-EQUIV=Refresh CONTENT="0;URL=index.php?p=list">';
	}

	echo $msg;
}

$data = getList();
$lic  = isset($_SESSION['listcount']) ? $_SESSION['listcount']:0;
$num  = ($lic>0) ? $lic:0;
$msg  = 'Found '.$num.' products';

ob_start();

echo '<form method="post" id="productlist" enctype="multipart/form-data" action="index.php?p=list"><ul>';
echo '<li><span class="blue">Upload Product List</span>';
echo '<li><label>File:</label><input type="file" name="file" id="prodlist" /><span class="bad"></span></li>';
echo '<li><span class="note">'.$msg.'</span></li>';
echo '<input type="submit" name="submit" class="btn" value="UPLOAD" /></ul></form>';
echo '<div id="'.$adminpage.'" class="clear"><a href="../logistics" class="back" id="download">BACK</a></div>';

echo $data;

ob_end_flush();

unset($_SESSION['uprecap']);
unset($_SESSION['lastrecap']);
unset($_SESSION['listcount']);

/*
function getQuery($tbl,$cols,$file) {
	$qry='LOAD DATA LOCAL INFILE \''.$file.'\'
		REPLACE INTO TABLE '.$tbl.'
		FIELDS TERMINATED BY \',\'
		ENCLOSED BY \'"\'
		ESCAPED BY \'\\\\\'
		LINES TERMINATED BY \'\r\n\'
		IGNORE 4 LINES
		'.$cols.'
	';
	return $qry;
}
*/
function updateTable($tbl,$idata) {
	$con = SQLi('products');
	$rs = $con->query("SELECT id FROM tbllist") or die(mysqli_error($con));
	while( $r= $rs->fetch_array() ) {
		$id  = $r['id'];
		$qry = "INSERT INTO $tbl VALUES ($id$idata) ON DUPLICATE KEY UPDATE id=id";
		$con->query($qry) or die(mysqli_error($con));
	}
}

function getList() {
	$msg  = '<ul class="list clear">';
	$msg .= '<li class="hdr">';
	$msg .= '<span class="s1">Item</span>';
	$msg .= '<span class="s6">Description</span>';
	$msg .= '<span class="s1 rt">WSP</span>';
	$msg .= '<span class="s1 rt">PMP</span>';
	$msg .= '<span class="s1 rt">SRP</span>';
	$msg .= '<span class="s2 rt">Bonus</span>';
	$msg .= '<span class="s2 rt">PV</span>';
	$msg .= '<span class="s1">Active</span>';
	$msg .= '</li>';

	$con = SQLi('products');
	$rs  = $con->query("SELECT * FROM tbllist ORDER BY id") or die(mysqli_error($con));
	$num = $rs->num_rows;

	$_SESSION['listcount'] = $num;

	if( $num>0 ) {
		while( $rw= $rs->fetch_array() ) {
			$msg .= '<li>';
			$msg .= '<span class="s1">' . $rw['id'] . '</span>';
			$msg .= '<span class="s6">' . utf8_encode($rw['name']) . '</span>';
			$msg .= '<span class="s1 rt">' . sprintf('%05d', $rw['wsp']) . '</span>';
			$msg .= '<span class="s1 rt">' . sprintf('%05d', $rw['pmp']) . '</span>';
			$msg .= '<span class="s1 rt">' . sprintf('%05d', $rw['srp']) . '</span>';
			$msg .= '<span class="s2 rt">' . number_format($rw['pmbonus'], 2, '.', '') . '</span>';
			$msg .= '<span class="s2 rt">' . number_format($rw['pv'], 2, '.', '') . '</span>';
			$msg .= '<span class="s1">' . $rw['status'] . '</span>';
			$msg .= '</li>';
		}
	}

	$msg .= '</ul>';
	return $msg;
}

function countCols() {
	$con = SQLi('products');
	$rs  = $con->query("SELECT * FROM tblwarehouse WHERE status=1") or die(mysqli_error($con));
	$num = $rs->num_rows;

	while( $rw= $rs->fetch_array() ) {
		$id   = $rw['id'];
		$test = $con->query("SHOW COLUMNS FROM tblstocks LIKE 'w$id'");

		if( trim($id)!='' && !mysqli_num_rows($test) ) {
			$con->query("ALTER TABLE tblstocks ADD w$id INT") or die(mysqli_error($con));
		}
	}

	return str_pad("",$num*2,",0");
}

function isValid($ft) {
	if(
		$ft == 'text/csv' ||
		$ft == 'application/csv' ||
		$ft == 'application/octet-stream' ||
		$ft == 'application/vnd.ms-excel'
	) return true;
}

function postData($file,$tbl) {
	$hdr  = 0;
	$data = fopen($file,'r');

	while( $r=fgets($data) ) {
		$line = str_getcsv($r);

		$x1 = $line[1];
		$x2 = utf8_encode(str_replace("'",'',$line[2]));
		$x3 = ( is_numeric($line[4]) ? $line[4] : 0 );
		$x4 = ( is_numeric($line[5]) ? $line[5] : 0 );
		$x5 = ( is_numeric($line[6]) ? $line[6] : 0 );
		$x6 = ( is_numeric($line[7]) ? $line[7] : 0 );
		$x7 = ( is_numeric($line[8]) ? $line[8] : 0 );
		$x8 = ( is_numeric($line[9]) ? $line[9] : 0 );
		$x9 = ( is_numeric($line[10]) ? $line[10] : 0 );
		$x10= ( $line[11]=='' ? 1 : 0 );

		$idat = "$x1,'$x2',$x3,$x4,$x5,$x6,$x7,$x8,$x9,$x10";
		$udat = "name='$x2',pv=$x3,wsp=$x4,pov=$x5,srp=$x6,pmp=$x7,pmbonus=$x8,wt=$x9,status=$x10";

		if( $hdr>=5 && trim($x1)!='' ) {
// echo $udat . '<br>';
			getData($idat, $udat, $tbl);
		}
		$hdr++;
	}
}

function getData($idata,$udata,$tbl) {
	$con = SQLi('products');
	// if($tbl=='tbllist') echo "$qry<br><br>";
	$con->query("INSERT INTO $tbl VALUES ($idata) ON DUPLICATE KEY UPDATE $udata") or die(mysqli_error($con));
}
?>
