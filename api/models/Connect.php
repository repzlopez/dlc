<?php
class Connect {
     protected $conn;

     public function __construct($db) {
          $this->conn = $db;
     }

     public function login($un, $pw, $tbl) {
          $qry = "SELECT $ret
               FROM $tbl
               $wer";

echo "\n<br>$qry"."\n\n";
          // $rs = $this->conn->prepare($qry);
          // $rs->execute();
          // return $rs;
     }

     public function distributors($id, $ret, $wer = '', $join = '', $order = '') {
          $qry = "SELECT $ret
               FROM distributors d
               $join
               $wer
               $order";
          // echo "\n<br>$qry"."\n\n";

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

     public function products($ret) {
          $qry = "SELECT $ret
               FROM tbllist l
               WHERE l.status=1
               ORDER BY l.id";

          $rs = $this->conn->prepare($qry);
          $rs->execute();
          return $rs;
     }

     public function productsSingle($id, $ret, $wer) {
          $qry = "SELECT $ret
               FROM tbllist l
               LEFT JOIN tblproducts p
                    ON p.id=l.id
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
