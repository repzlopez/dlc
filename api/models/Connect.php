<?php
class Connect {
     protected $conn;

     public function __construct($db) {
          $this->conn = $db;
     }

     public function login($un, $pw) {
          $qry = "SELECT dsdid FROM accounts WHERE dsdid = '$un' AND password = '$pw'";

          $rs = $this->conn->prepare($qry);
          $rs->execute();

          if($rs->rowCount() > 0 ) {
               $this->user = $un;

               $ret = "dsdid,CONCAT(dslnam,', ',dsfnam,' ',SUBSTRING(dsmnam,1,1)) name,dssid,(SELECT CONCAT(dslnam,', ',dsfnam,' ',SUBSTRING(dsmnam,1,1)) FROM distributors WHERE dsdid=d.dssid) sponsor,dsbrth,dssetd ";

               $qry = "SELECT $ret
               FROM distributors d
               WHERE dsdid='$un'";

               $rs = $this->conn->prepare($qry);
               $rs->execute();
               return $rs;
          }
     }

     public function distributors($id, $ret, $wer = '', $join = '', $order = '') {
          $qry = "SELECT $ret
               FROM distributors d
               $join
               $wer
               $order";

          $rs = $this->conn->prepare($qry);
          $rs->execute();
          return $rs;
     }

     public function warehouse($id, $ret, $wer = '', $join = '', $order = '') {
          $qry = "SELECT $ret
               FROM tblwarehouse w
               $join
               $wer
               $order";
          // echo "\n<br>$qry"."\n\n";

          $rs = $this->conn->prepare($qry);
          $rs->execute();
          return $rs;
     }

     public function products($ret, $cat) {
          $packages = ($cat == 'packages' ? "AND p.cat LIKE 'others' AND subcat='Product Packages'" : "AND p.cat LIKE '%$cat%'");

          $qry = "SELECT $ret
               FROM tbllist l
               LEFT JOIN tblproducts p ON p.id=l.id
               LEFT JOIN tblfda f ON f.id=l.id
               WHERE p.cat!=''
               AND p.cat!='productaids'
               $packages
               AND l.status=1
               AND p.status=1
               ORDER BY CASE WHEN cat='others' THEN 1 ELSE 0 END,sort_order,l.id";

          $rs = $this->conn->prepare($qry);
          $rs->execute();
          return $rs;
     }

     public function productsSingle($id, $ret, $wer) {
          $qry = "SELECT $ret
               FROM tbllist l
               LEFT JOIN tblproducts p
                    ON p.id=l.id
               LEFT JOIN tblfda f
                    ON f.id=l.id
               WHERE l.status=1 AND p.status=1
               $wer
               ORDER BY l.id";

          $rs = $this->conn->prepare($qry);
          $rs->execute();
          return $rs;
     }

     public function addData($tbl,$reqs,$wer) {
          $idata= $udata= '';
          $ids = 'transactions_id|delivers_id|payments_id';
          $fix = "/$ids/i";
          $int = "/pid|wsp|pov|srp|pv|qty|amount|fees|paid|delivered|status/i";

          foreach($reqs as $k=>$v) {
               $v = trim(strip_tags($v));
     		if(preg_match($int, $k)) {
                    $udata .= $k."=".$v.",";
     			$idata .= $v.",";

     		} else {
     			if( !preg_match($fix, $k) ) $udata .= $k."='".$v."',";
     			$idata .= "'".$v."',";
     		}
          }

          $udata = substr($udata, 0, -1);
          $idata = substr($idata, 0, -1);

          $qry="
               INSERT INTO $tbl
               VALUES ($idata) ON DUPLICATE KEY
               UPDATE $udata
               $wer";

          $rs = $this->conn->prepare($qry);
          $rs->execute();
          return $this->conn->lastInsertId();
     }

     public function listData($tbl,$wer) {
          $ret = trim($wer)=='' ? ($tbl=='orders'?'transactions':$tbl).'_id' :'*';
          $qry = "SELECT $ret
               FROM $tbl
               $wer
               ORDER BY ".$ret;

          $rs = $this->conn->prepare($qry);
          $rs->execute();
          return $rs;
     }

}
?>
