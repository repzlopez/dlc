<?php
class Auth extends API {

     public function __construct($request, $origin) {
          parent::__construct($request);

// echo "$origin==".$_SERVER['SERVER_NAME']."\n\n";
//           if ($origin!=$_SERVER['SERVER_NAME'])
//                throw new Exception('Unauthorized access');
          if( !isset( $_SERVER['PHP_AUTH_USER'] ) ) {
               header('WWW-Authenticate: Basic realm="DLC Shareconomy"');
               header('HTTP/1.0 401 Unauthorized');
               echo 'Authentication FAILED';
               exit;

          } else {

               list ($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] ) = explode(':' , base64_decode( substr( $_SERVER['HTTP_AUTHORIZATION'], 6) ) );
               $this->user = $_SERVER['PHP_AUTH_USER'];
               $this->pass = $_SERVER['PHP_AUTH_PW'];

               $arrCoid = array(
                    '923660b5-db83-led8-c4ef'=>'',
                    '59f05ba3-d825-l9b1-c9f6'=>'LM');

               if( array_key_exists( $_SERVER['PHP_AUTH_PW'], $arrCoid ) ) {
                    $this->coid = $arrCoid[ $_SERVER['PHP_AUTH_PW'] ];
 
               } else
                    throw new Exception('Unauthorized access');

          }

     }

     protected function login($un, $pw, $db_) {
          include_once 'config/DB.php';
          include_once 'models/Connect.php';

          $DB   = new DB();
          $db   = $DB->connect($db_);
          $post = new Connect($db_);
          $rs   = $post->distributors($this->user, $ret);

          return $this->send($rs);
     }

     protected function mig() {
          // /profile
          // /ormstp/[yr]/[mo]
          // /bomstp
          // /bohstp/[yr]/[mo]

          if($this->method == 'GET') {
               $arg = $join = $wer = $and = $yr = $wk = $order = '';
               $ret = '*';

               if(isset($this->verb)) {
                    // $ret  = "dsdid,CONCAT(dslnam,', ',dsfnam,' ',SUBSTRING(dsmnam,1,1)) name " . ($this->verb != 'profile' && $this->verb != '' ? ',stp.*' : '');
                    // $join = preg_match("/bomstp|bohstp|ormstp/i", $this->verb) ? 'LEFT JOIN ' . $this->verb . ' stp ON ' : '';
                    $ret = '*';

                    // if (!empty($this->args)) {
                    //      $yr = (!empty($this->args[0])) ? $this->args[0] : '';
                    //      $wk = (!empty($this->args[1])) ? $this->args[1] : '';
                    // }

                    switch($this->verb) {
                         // case 'bomstp': // no args
                         //      $join .= 'bmdid=dsdid';
                         //      break;
                         // case 'bohstp': // args /[yr]/[mo]
                         //      $join .= 'bhdid=dsdid';
                         //      $order = 'ORDER BY bhpyr DESC,bhpmo DESC ';
                         //      $and   = ($yr != '' ? "AND bhpyr=$yr" : '') . ($wk != '' ? " AND bhpmo=$wk" : '');
                         //      break;
                         // case 'ormstp': // args /[yr]/[mo]
                         //      $join .= 'omdid=dsdid';
                         //      $order = 'ORDER BY ompyr DESC,ompmo DESC ';
                         //      $and   = ($yr != '' ? "AND ompyr=$yr" : '') . ($wk != '' ? " AND ompmo=$wk" : '');
                         //      break;
                         // case 'profile':
                              // $ret  .= ',w.*';
                              // break;
                         case '':
                              break;
                         default:
                              return 'Invalid argument: ' . $this->verb;
                              exit;
                    }
               }

               $wer = "WHERE dsdid='" . $this->user . "' $and";

               include_once 'config/DB.php';
               include_once 'models/Connect.php';

               $DB   = new DB();
               $db   = $DB->connect('distributor');
               $post = new Connect($db);
               $rs   = $post->distributors($this->user, $ret, $wer, $join, $order);

               return $this->send($rs);

          } else {
               http_response_code(405);
          }
     }

     protected function me() {
          // /profile
          // /ormstp/[yr]/[mo]
          // /bomstp
          // /bohstp/[yr]/[mo]

          if($this->method == 'GET') {
               $arg = $join = $wer = $and = $yr = $wk = $order = '';
               $ret ='*';

               if(isset($this->verb)) {
                    $ret  = "dsdid,CONCAT(dslnam,', ',dsfnam,' ',SUBSTRING(dsmnam,1,1)) name,dssid sid,(SELECT CONCAT(dslnam,', ',dsfnam,' ',SUBSTRING(dsmnam,1,1)) FROM distributors WHERE dsdid=sid) sponsor,dsbrth,dssetd " . ($this->verb != 'profile' && $this->verb != '' ? ',stp.*' : '');
                    $join = preg_match("/bomstp|bohstp|ormstp/i",$this->verb)?'LEFT JOIN '.$this->verb.' stp ON ':'';

                    if(!empty($this->args)) {
                         $yr = (!empty($this->args[0]))?$this->args[0]:'';
                         $wk = (!empty($this->args[1]))?$this->args[1]:'';
                    }

                    switch($this->verb) {
                         case 'bomstp': // no args
                              $join .= 'bmdid=dsdid';
                              break;
                         case 'bohstp': // args /[yr]/[mo]
                              $join .= 'bhdid=dsdid';
                              $order = 'ORDER BY bhpyr DESC,bhpmo DESC ';
                              $and   = ($yr!=''?"AND bhpyr=$yr":'').($wk!=''?" AND bhpmo=$wk":'');
                              break;
                         case 'ormstp': // args /[yr]/[mo]
                              $join .= 'omdid=dsdid';
                              $order = 'ORDER BY ompyr DESC,ompmo DESC ';
                              $and   = ($yr!=''?"AND ompyr=$yr":'').($wk!=''?" AND ompmo=$wk":'');
                              break;
                         case 'profile':
                              $ret  .= ',d.*';
                              break;
                         case '':
                              break;
                         default:
                              return 'Invalid argument: '.$this->verb;
                              exit;
                    }
               }

               $wer = "WHERE dsdid='".$this->user."' $and";

               include_once 'config/DB.php';
               include_once 'models/Connect.php';

               $DB   = new DB();
               $db   = $DB->connect('distributor');
               $post = new Connect($db);
               $rs   = $post->distributors($this->user,$ret,$wer,$join,$order);

               return $this->send($rs);

          } else {
               http_response_code(405);
          }
     }

     protected function distributors() {
          // /find/[id|name]/[args]
          // /list
          // /list/[id]
          // /list/[id]/ormstp/[yr]/[mo]
          // /list/[id]/bomstp
          // /list/[id]/bohstp/[yr]/[mo]

          if($this->method == 'GET') {
               $id  = $arg = $join = $wer = $order = $and = $yr = $wk = '';
               $ret = '*';

               if(!empty($this->args)) {
                    $id = $this->args[0];
                    if(array_key_exists(1,$this->args) && preg_match("/find|list/i",$this->verb)) {
                         $yr   = (!empty($this->args[2]))?$this->args[2]:'';
                         $wk   = (!empty($this->args[3]))?$this->args[3]:'';
                         $join = preg_match("/bomstp|bohstp|ormstp/i",$this->args[1])?'LEFT JOIN '.$this->args[1].' stp ON ':'';

                         switch($this->args[1]) {
                              case 'bomstp':
                                   $join .= 'bmdid=dsdid';
                                   $order = '';
                                   break;
                              case 'bohstp':
                                   $join .= 'bhdid=dsdid';
                                   $order = 'ORDER BY bhpyr DESC,bhpmo DESC '.($yr!=''?'':'LIMIT 1');
                                   $and   = ($yr!=''?"AND bhpyr=$yr":'').($wk!=''?" AND bhpmo=$wk":'');
                                   break;
                              case 'ormstp':
                                   $join .= 'omdid=dsdid';
                                   $order = 'ORDER BY ompyr DESC,ompmo DESC '.($yr!=''?'':'LIMIT 1');
                                   $and   = ( $yr!='' ? "AND ompyr=$yr" : '' ) . ( $wk!='' ? " AND ompmo=$wk" : '' );
                                   break;
                              case '':
                                   break;
                              default:
                                   return 'Invalid argument: '.$this->args[1];
                                   exit;
                         }
                    }
               }

               $arg = implode(',',array_slice($this->args,1));
               switch($this->verb) {
                    case 'find':
                         $ret = "dsdid,CONCAT(dslnam,', ',dsfnam,' ',SUBSTRING(dsmnam,1,1)) name ".($arg!=''?',stp.*':'');
                         $wer = "WHERE (dsdid='$id' OR LOWER(dsfnam) LIKE '%".strtolower($id)."%' OR LOWER(dslnam) LIKE '%".strtolower($id)."%' OR LOWER(dsmnam) LIKE '%".strtolower($id)."%') AND dsdid LIKE '".$this->coid."%' $and";
                         if( empty($this->args) ) {
                              return 'Nothing to find';
                              exit;
                         }
                         break;
                    case 'list':
                         $ret = $id!='' ? $ret : 'dsdid';
                         $wer = "WHERE dsdid LIKE '".$this->coid."%' ".($id!=''?" AND dsdid='$id'":'')." $and";
                         break;
                    default:
                         $id=$id!=''?$id:null;
                         $ret=$ret!=''?$ret:'*';
                         $wer="WHERE dsdid='$id'";
                         break;
               }

               include_once 'config/DB.php';
               include_once 'models/Connect.php';

               $DB = new DB();
               $db = $DB->connect('distributor');
               $post = new Connect($db);
               $rs = $post->distributors($id,$ret,$wer,$join,$order);

               return $this->send($rs);

          } else {
               http_response_code(405);
          }
     }

     protected function products() {
          // /list
          // /find/[id/name(full/partial)]/[args]
          // /[id]/[args]

          if($this->method == 'GET') {
               $id = $arg = $wer = $ret = '';
               // $ret='l.id';

               if(!empty($this->args)) {
                    $id = $this->args[0];
                    // $ret='*';
                    if(array_key_exists(1,$this->args)) {
                         foreach($this->args as $key => $value) {
                              if( $key==0 || preg_match("/id/i",$value) ) {}
                              else $arg.="$value,";
                              $ret = substr("l.id,$arg",0,-1);
                         }
                    }
               }

               switch($this->verb) {
                    case 'find':
                         if ($id=='') { return array('message'=>'Nothing to find');exit; }
                         $ret = substr("l.id,name,$arg",0,-1);
                         $wer = "AND (l.id='$id' OR LOWER(l.name) LIKE '%".strtolower($id)."%')";
                         break;
                    case 'list':
                         // $wer="AND (l.id='$id' OR LOWER(l.name) LIKE '%".strtolower($id)."%')";
                         $ret = 'l.id,l.name';
                         break;
                    default:
                         $id  = $id!='' ? $id : null;
                         $ret = $ret!='' ? $ret : '*';
                         $wer = "AND l.id='$id'";
                         break;
               }

               if($this->verb==''&&empty($this->args)) {
                    return array('message'=>'Nothing to do');
               } else {
                    include_once 'config/DB.php';
                    include_once 'models/Connect.php';

                    $DB = new DB();
                    $db = $DB->connect('products');
                    $post = new Connect($db);

                    if($this->verb == 'list') {
                         $rs = $post->products($ret);
                    } else {
                         $rs = $post->productsSingle($id, $ret, $wer);
                    }

                    return $this->send($rs);
               }

          } else {
               http_response_code(405);
          }
     }

     protected function orders() {
          // verbs [ add | edit ]
          // types [ transactions | delivers | payments | orders ]
          // /[verb]/[type]/id

          $id = $tbl = $wer = '';

          if(!empty($this->args)) {
               $tbl = $this->args[0];
               if(array_key_exists(1,$this->args)) {
                    $id = $this->args[1];
               }
          }

          if($this->method == 'POST') {

               if( $this->verb=='' && empty($this->args) ) {
                    return array('message'=>'Nothing to do');
               } else {
                    include_once 'config/DB.php';
                    include_once 'models/Connect.php';

                    $DB   = new DB();
                    $db   = $DB->connect('api');
                    $post = new Connect($db);

                    switch($this->verb) {
                         case 'add':
                         case 'edit':
                              return $post->addData($tbl,$this->request,$wer);
                              break;
                         default:
                              return 'Nothing to do';
                              break;
                    }

                    // return $this->send($rs);
// return $this->verb;
               }
          } else if($this->method == 'GET') {
               // types [ transactions | delivers | payments | orders ]
               // /list/[type]/id

               if( $this->verb=='' && empty($this->args) ) {
                    return array('message'=>'Nothing to do');

               } else {
                    include_once 'config/DB.php';
                    include_once 'models/Connect.php';

                    $DB   = new DB();
                    $db   = $DB->connect('api');
                    $post = new Connect($db);

                    switch($this->verb) {
                         case 'list':
                              $wer = ($id!='' ? 'WHERE '.$tbl.'_id='.$id : '');
                              $rs = $post->listData($tbl,$wer);
                              break;
                         default:
                              return 'Nothing to do';
                              break;
                    }

                    return $this->send($rs);
               }

          } else {
               http_response_code(405);
          }
     }

     protected function send($rs,$ret='') {
          if( $rs->rowCount()>0 ) {
               $arr['data']=array();
               if($ret!='') $arr['lastid'] = $ret;

               while( $rw=$rs->fetch(PDO::FETCH_ASSOC) ) {
                    foreach($rw as $k=>$v) $item[$k] = $v;
                    array_push($arr['data'],$item);
               }
               return $arr;
          } else {
               return array('message'=>null);
          }
     }
}
?>
