<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}

function getBrowser() {
	$str='';
	$ua=$_SERVER['HTTP_USER_AGENT'];
	/* ==== Detect the OS ==== */

	// ---- Mobile ----
	$android=strpos($ua,'Android')?true:false;			// Android
	$blackberry=strpos($ua,'BlackBerry')?true:false;	// BlackBerry
	$iphone=strpos($ua,'iPhone')?true:false;			// iPhone
	$palm=strpos($ua,'Palm')?true:false;				// Palm

	// ---- Desktop ----
	$linux=strpos($ua,'Linux')?true:false;				// Linux
	$mac=strpos($ua,'Macintosh')?true:false;			// Macintosh
	$win=strpos($ua,'Windows')?true:false;				// Windows
	/* ============================ */

	/* ==== Detect the UA ==== */
	$chrome=strpos($ua,'Chrome')?true:false;			// Google Chrome

	$firefox=strpos($ua,'Firefox')?true:false;			// All Firefox
	$firefox_2=strpos($ua,'Firefox/2.0')?true:false;	// Firefox 2
	$firefox_3=strpos($ua,'Firefox/3.0')?true:false;	// Firefox 3
	$firefox_3_6=strpos($ua,'Firefox/3.6')?true:false;	// Firefox 3.6

	$msie=strpos($ua,'MSIE')?true:false; 				// All IE
	$msie_6=strpos($ua,'MSIE 6.0')?true:false;			// IE6
	$msie_7=strpos($ua,'MSIE 7.0')?true:false;			// IE7
	$msie_8=strpos($ua,'MSIE 8.0')?true:false;			// IE8

	$opera=preg_match("/\bOpera\b/i",$ua);				// All Opera
	$opera_mini=strpos($ua,'Opera Mini')?true:false;	// Opera Mini
	$nokiabrowser=strpos($ua,'NokiaBrowser')?true:false;// Nokia Browser

	$safari=strpos($ua,'Safari')?true:false;			// All Safari
	$safari_2=strpos($ua,'Safari/419')?true:false;		// Safari 2
	$safari_3=strpos($ua,'Safari/525')?true:false;		// Safari 3
	$safari_3_1=strpos($ua,'Safari/528')?true:false;	// Safari 3.1
	$safari_4=strpos($ua,'Safari/531')?true:false;		// Safari 4
	/* ============================ */

	//Tests for browsers and operating systems
	if($ua) {											// ---- Test if Handheld ----
		if($android) $str='Android';					// Android
		if($blackberry) $str='Blackberry';				// Blackberry
		if($iphone) $str='iPhone';						// iPhone
		if($palm) $str='Palm';							// Palm
		if($linux) $str='Linux';						// Linux Desktop

		if($firefox) { $str='Firefox';					// ---- Test if Firefox ----
			if($firefox_2) { $str.='v2';					// Firefox 2
			}elseif($firefox_3) { $str.='v3';			// Firefox 3
			}elseif($firefox_3_6) { $str.='v3.6';		// Firefox 3.6
			}else{ $str.='What Version do you use?';	// A version not listed
			}
		}elseif($nokiabrowser) { $str='Nokia Browser';	// ---- Test if Nokia Browser ----
		}elseif($chrome) { $str='Chrome';				// Test if Chrome
		}elseif(($safari||$chrome)&&!$iphone) {			// ---- Test if Safari or Chrome ----
			if($safari&&!$chrome) $str='Safari';		// Test if Safari and not Chrome
			if($mac&&$safari) $str.='Safari on Mac';	// Safari Mac
			if($win&&$safari) $str.='Safari on Win';	// Safari Windows
			if($safari_2) { $str.='v2';					// Safari 2
			}elseif($safari_3) { $str.='v3';				// Safari 3
			}elseif($safari_4) { $str.='v4';				// Safari 4
			}else{ $str.='What version are you using?';
			}
		}elseif($iphone&&$safari_3_1) { $str='Safari 3.1';// ---- Test if iPhone with Safari 3.1 ----
		}elseif($msie) {									// ---- Test if IE ----
			if($msie_6) { $str='IE6';					// IE 6
			}elseif($msie_7) { $str='IE7';				// IE 7
			}elseif($msie_8) { $str='IE8';				// IE 8
			}else{ $str='IE<6';
			}
		}elseif($opera_mini) { $str='Opera Mini';		// ---- Test if Opera ----
		}elseif($opera) { $str='Opera';					// ---- Test if Opera ----
		}else { $str.='What browser are you using?';	// ---- If none of the above ----
		}
	}
	return $str;
}
?>
