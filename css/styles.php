<?php
ini_set('max_execution_time',300);
if(!isset($_SESSION)) session_start();
$lasturi		=isset($_SESSION['lastURI'])?$_SESSION['lastURI']:null;
$req			=isset($_GET['get'])?$_GET['get']:$lasturi;
$addcss		=isset($_GET['css'])?$_GET['css']:null;
$browser		=isset($_SESSION['browser'])?$_SESSION['browser']:null;
$cartnote		=isset($_SESSION['cartNoteON'])?$_SESSION['cartNoteON']:null;
$offset		=72000;
$ExpStr		='Expires: '.gmdate('D, d M Y H:i:s',time()+$offset).' GMT';

header($ExpStr);
header('Content-type:text/css');

$blue		= '#00539b';		//official blue
$gold		= '#fdb913';		//official gold
$bg			= '#fff';			//container bg
$bodybg		= '#fff';		//body bg #efebd6
$btn_bg		= '#fff';			//button bg
$btnhov		= '#eee';			//button hover
$d_tab		= $bodybg;		//tab
$d_tab_curr	= $btnhov;		//current tab
$place_color   = '#ccc';           //placeholder color
$bor1		= 'border:1px solid';
$bord_bot		= 'border-bottom:#eee solid 1px';
$bord_all		= $bor1.' #ccc';
$bk			= 'background';
$bg_non		= $bk.':none';
$bg_pos		= $bk.'-position';
$src			= $bk.':url(../src';
$norep		= 'no-repeat';
$nav			= $src.'/nav.png)';
$nav_bg		= $src.'/nav_bg.png) 0 bottom '.$norep;
$prod_bg		= $src.'/categ_bg.png) '.$norep;
$diamond		= $src.'/diamond.png) '.$norep;
$commit_dmd	= $src.'/diamonds.jpg) '.$norep;
$left_nav		= $src.'/left.png) '.$norep;
$load_ani		= $src.'/loading.gif) '.$norep;
$link		= $src.'/link.gif) '.$norep;
$pdf			= $src.'/pdf.png) '.$norep;
$pdf_min		= $src.'/pdf_min.png) 0 3px '.$norep;
$dlctxt		= $src.'/dlc_text.png) '.$norep;
$nostock		= $src.'/nostock.png) '.$norep;
$print		= $src.'/print.gif) 0 '.$norep;
$fblike		= $src.'/dlcfb.gif) '.$norep;
$shopbag		= $src.'/shopbag.gif) '.$norep;
$plusminus	= $src.'/plusminus.png) '.$norep;
$checkout		= $src.'/checkout.png) '.$norep;
$order		= $src.'/bgshop.jpg) '.$norep;
$totop		= $src.'/totop.png) '.$norep;
$sort		= $src.'/sort.gif) '.$norep;
$list_dec		= 'list-style-type:decimal';
$list_disc	= 'list-style-type:disc';
$list_circ	= 'list-style-type:circle';
$list_none	= 'list-style:none';
$in_sub		= 'input[type=submit]';
$in_but		= 'input[type=button]';
$in_txt		= 'input[type=text]';
$in_rad		= 'input[type=radio]';
$in_cbo		= 'input[type=checkbox]';
$fc		= 'first-child';
$flft	= 'float:left';
$frgt	= 'float:right';
$fnon	= 'float:none';
$fbold	= 'font-weight:bold';
$fital	= 'font-style:italic';
$dinlin	= 'display:inline';
$dblok	= 'display:block';
$diblok	= $dinlin.'-block';
$dtcell 	= 'display:table-cell';
$dnon	= 'display:none';
$bor_0	= 'border:0';
$bcc		= 'border-collapse:collapse';
$pad_0	= 'padding:0';
$pad_5	= 'padding:5px';
$pad_10	= 'padding:10px';
$mar_0	= 'margin:0';
$mar_5	= 'margin:5px';
$mar_10	= 'margin:10px';
$m_auto	= $mar_0.' auto';
$m_lft	= 'margin-left';
$m_rgt	= 'margin-right';
$nowrap	= 'white-space:nowrap';
$txlf	= 'text-align:left';
$txrt	= 'text-align:right';
$txct	= 'text-align:center';
$txjs	= 'text-align:justify';
$posabs	= 'position:absolute';
$posrel	= 'position:relative';
$posfix	= 'position:fixed';
$ov_hid	= 'overflow:hidden';
$ov_aut	= 'overflow:auto';
$h_aut	= 'height:auto';
$h100	= 'height:100%';
$w_aut	= 'width:auto';
$w100	= 'width:100%';
$maxwid	= 'width:960px';
$cur_pt	= 'cursor:pointer';
$vat		= 'vertical-align:top';
$vam		= 'vertical-align:middle';
$st		= '1px 1px 5px #fff';
$shafff	= "text-shadow:$st,$st,$st,$st";
$sha777	= "text-shadow:1px 1px 3px #777";
$shablu	= "text-shadow:2px 2px 3px ".$blue;
$cart_ht	= $cartnote?'230px':'210px';
$cart_bot	= $cartnote?'-250px':'-230px';
$imp		= '!important';
$fonts	= '"lucida sans unicode",arial,sans-serif';
$fon_cus	= 'font-family:'.$fonts;
$fon_sz	= 'font-size';
$impfont	= '';
$center		= '';

echo <<<CSS
@charset "utf-8";

html,body,div,span,label,ul,ol,li,form,select,input,textarea,a,p,img,hr,h1,h2,h3,h4,h5,h6 { $mar_0;$pad_0;outline:none; }
html,body,div,span,label,ul,ol,li,form,select,input,textarea,a,p { font:normal 12px $fonts; }
img,hr { $bor_0; }
lh { $fbold; }
ol { width:500px; }
ol,ul,li { $list_none; }
p { $txjs;margin-bottom:5px; }
a { color:$blue;text-decoration:none; }
a:active,a:focus,a:selected { $bor_0;outline:none; }
textarea,select { $bord_all; }
input { $bord_all;padding:2px; }
input[type=hidden],input[type=file],$in_rad { $bor_0; }
input[type=file] { $w_aut; }
$in_cbo { $posrel;top:2px;}
$in_rad,$in_cbo { $pad_0; }
$in_sub,$in_but,#download,.dobatch { $bk:$btn_bg;$cur_pt; }
$in_sub:hover,$in_but:hover,#download:hover,.dobatch:hover { $bk:$btnhov; }
form label:not(:$fc) { $txlf;$w_aut; }

html,body { $h100; }
html { overflow-y:scroll; }
body { $bk:$bodybg;$fon_sz:62.5%;$m_auto;$posrel;$txct;$maxwid; }
#container { $bk:$bg;$m_auto;$pad_10;$txlf;$maxwid; }
#container div.orders .bad { $frgt;padding:2px 10px; }
#backtotop { $totop;height:46px;width:108px;$posfix;bottom:0;$m_lft:-10px; }
#backtotop a { padding:14px 52px;$posabs;top:14px;left:0; }
#player { $mar_0 20px; }
#main_logo { margin:20px auto 0;$dnon; }
#main_logo img { $w100; }
#download,.dobatch { $bor1 #eee;$frgt;$fon_sz:11px;height:18px;line-height:18px;$m_lft:2px;padding:2px; }
#fblike { $frgt;margin-top:-42px;$ov_hid;$pad_0;width:155px; }
#fblike a { $flft; }
#moredl { $bg_non;$frgt;height:18px;padding:3px;$posrel;top:-30px; }
#sig { $dnon; }

/*	header		*/
#head { $bk:$bg;height:90px;$mar_0;$pad_10;$maxwid; }
#head a { $m_lft:5px; }
#head a,#head label { color:#555; }
#head a img { $flft; }
#head a:hover { color:$blue;$sha777; }
#head form { $frgt;$pad_0 $imp;width:620px; }
#head form div { height:20px;$frgt; }
#head label { $m_rgt:5px;$w_aut $imp; }
#head input { $fon_sz:10px;width:100px; }
#head $in_sub { $fnon $imp;width:60px; }
#topnav { $frgt;$pad_5; }

#user { $frgt;color:#999;$diblok;$pad_0 5px; }
#user .blue { $fon_cus;$fon_sz:12px; }
#user a { color:#555;margin:0 5px; }
#user label { $fital; }
/*	header		*/

/*	navigation	*/
/*	lava*/
.lava { height:40px;$m_auto;$ov_hid;$posrel;$txlf;width:800px;z-index:1; }
.lava li { $flft; }
.lava li.back { border-bottom:$gold solid 2px;height:30px;$posabs;z-index:-1; }
.lava a { color:#333;$dblok;$flft;$fon_cus;$fon_sz:15px;$fbold;height:36px;line-height:36px;$ov_hid;$pad_0 40px;$posrel;$txct;text-shadow:1px 1px 3px #aaa;z-index:0; }
.lava a:hover { color:$blue }
/*	lava*/

.nav2 { border-bottom:#ccc solid 1px;height:40px;$ov_hid;$mar_0 0 10px 0;$pad_0 20px;$w_aut; }
.nav2 li { $flft;height:36px;$pad_0 10px; }
.nav2 li.rt { $frgt;$txrt; }
.nav2 h2 { color:$blue;$flft;$fon_cus;height:40px;line-height:40px;$mar_0;$pad_0 10px;text-transform:capitalize; }
.nav2 h3 { color:#666;$dinlin;$flft;line-height:42px;$mar_0; }
.nav2 a { color:#777;$dinlin;$flft;line-height:40px;$mar_0 10px;$w_aut; }
.nav2 a:hover { color:$blue; }
/*	navigation	*/

/*	sides		*/
.left_side { $flft;$h_aut $imp;min-height:320px;height:320px;$mar_0;$pad_0;width:200px; }
.left_side strong { $bk:url($nav) repeat-x;color:#eee;$dblok;$fon_cus;$fon_sz:16px;height:36px;line-height:34px;$pad_0 10px; }
.left_side li { $bord_bot;height:36px;line-height:36px;$pad_0;width:200px; }
.left_side li:$fc { $nav; }
.left_side a { $dblok;$fon_sz:14px;height:36px;line-height:36px;$pad_0 15px;$sha777;$ov_hid; }
.left_side a:hover { $left_nav;height:50px;width:230px; }
/*	sides		*/

/*	footer		*/
#foot { $bk:#444;$pad_10 10px 20px;$maxwid;$ov_hid; }
#foot .foot { $pad_5 0; }
#foot .footnav a { color:#fff;$flft;$m_rgt:5px; }
#foot .footnav a:hover { color:$gold; }
#foot ul li { $flft;$mar_5;width:136px; }
#foot ul li li { padding-left:5px;$txlf; }
#foot .hdr { $bg_non;$fon_cus;color:$gold;$fbold;$pad_0; }
#foot .footexplore li { width:200px; }

#foot .foot_sig { $fnon;padding:40px 0 20px; }
#foot div a { color:#bbb;$flft;$fon_cus; }
#foot div a:hover { color:$gold;$sha777; }
#foot div span { color:#bbb;$frgt; }
#foot hr { $bord_bot;height:1px; }
#foot img { $flft; }
/*	footer		*/

/*	content		*/
.content { $h_aut $imp;min-height:300px;height:300px;$pad_0; }
.content_main { $flft;$pad_0 0 0 20px;$mar_0 0 20px 0;width:736px; }
.content_main ul { $pad_0;$w100; }
.content_main p,
.content_main li { $fon_sz:13px;width:710px;$mar_0 0 10px 0; }
.content_main li li { $m_lft:20px; }
.content_main ol li { $list_dec;$m_lft:36px; }
.content_main ul:not(.faq) img { $mar_0; }
.content_main .blue { $fon_cus;$fon_sz:18px; }
.guest li { $flft;$pad_5; }
.guest p { $mar_5;width:296px; }
.guest a { $bk:$blue;color:$gold;$diblok;$fon_sz:18px;$pad_10;width:288px; }
.guest a:hover { $bk:$gold;color:$blue; }
.faq li { $bord_bot; }
.faq span { $pad_10; }
.legal li { $flft;width:220px; }
/*	content		*/

/*	index 	*/
.ui-tabs-nav { $flft;height:460px;$mar_0;$ov_hid;width:360px; }
.ui-tabs-nav li { color:#666;padding:2px;padding-left:13px; }
.ui-tabs-nav .ui-tabs-selected a { $bk:$d_tab; }
.ui-tabs-nav-item { $dblok;height:110px;$ov_hid; }
.ui-tabs-nav-item>a { $bk:$bg;color:#333;$dblok;height:110px;$ov_hid;width:100%; }
.ui-tabs-nav-item>a:hover { $bk:#e2e2e2; }
.ui-tabs-nav-item img { $bk:$bg;$flft;width:360px; }
.ui-tabs-selected { $bk:url('../src/selected-item.gif') left 25px $norep; }
.ui-tabs-panel { $flft;height:460px;width:600px;$mar_0;$ov_hid; }
.ui-tabs-panel img { height:460px;width:600px; }
.ui-tabs-hide { $dnon; }
/*	index 	*/

/*	pricelist 	*/
#pricelist li { $pad_5;width:920px;$bor1 $btnhov; }
#pricelist li.hdr { $bk:$btnhov; }
#pricelist li * { $vat; }
/*	pricelist 	*/

/*	shopping cart	*/
#cart { border:$blue solid 5px;$bk:$bg;height:$cart_ht;width:510px;$pad_5;$posfix;bottom:$cart_bot;right:0;z-index:999; }
#cart .blue { $diblok;height:16px;$txlf; }
#cart ul { $bord_all;height:115px;$pad_5 0;$ov_aut;$txlf; }
#cart li { $pad_5 0; }
#cart li:hover { $bk:#eee;$cur_pt; }
#cart span { $diblok;$fon_sz:11px;$ov_hid;$pad_0 5px; }
#cart .rt { $txrt; }
#cart .link { color:$blue;padding-left:20px;width:280px; }
#cart .link:hover { $link 5px 0; }
#shopbag { $shopbag;$dblok;$frgt;margin-top:-90px;height:100px;width:86px; }
#totals { height:38px;width:500px; }
#totals a { $checkout left top;$flft;height:32px;margin-top:5px;width:100px;$ov_hid;$m_rgt:5px; }
#totals span { color:$blue;$fon_sz:11px;height:16px;$pad_0;$txrt; }
#totals div { $frgt;width:260px; }
#clearcart { $checkout left bottom $imp; }

.shopqty span { $txct;width:40px; }
.shopqty input { $plusminus;$bor_0;height:14px;width:14px;$mar_0;$pad_0; }
.shopqty .qtyup { $bg_pos:left top; }
.shopqty .qtydn { $bg_pos:right top; }
.shopqty input:hover { $plusminus; }
.shopqty .qtyup:hover { $bg_pos:left bottom; }
.shopqty .qtydn:hover { $bg_pos:right bottom; }
/*	shopping cart	*/

/*	rounded corner	*/
#shop,.pbut,
textarea,select,input,.btn,
#download,.ui-tabs-nav-item a,.left_side strong,
.news li img,.newsletters img,.newsletters div,
.calendar_page li,.calendar_page li span,.othermonths,.calinfo,
.orders .blue,.cancelorder,.dobatch,#newdistri li ul
{ -moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;border-radius:5px; }

.nav2,.upload,									/* admin */
#first_login form,#first_login h3,
.ui-tabs-panel img,.ui-tabs-panel .info,
#prod_list li,#prod_list img,#cart,.prod_main img,.feat_prod li,.feat_prod .feat,
.newsletters li,.fullnews img,
#prev,#prev img,.prev img,
.calendar_page,.legend,.sampleimg,
#cart ul,.orders,.vieworders ul,#validate,#recap ul,#getrec ul,#getsm ul,#getbonussum ul,#olreg,#package,#package li,#package_data,#responsor,
#gos_cart,#gos_cart div,#gos_cart ul,.gos_totals,
#pcm_cart,#pcm_cart div,#pcm_cart ul,.pcm_totals,
.remit ul div,#distrilist,#shortnav,.home li a,.guest a,.guest #player
{ -moz-border-radius:10px;-webkit-border-radius:10px;-khtml-border-radius:10px;border-radius:10px; }

.home,.rel_prod,.rel_prod li,.rel_prod img
{ -moz-border-radius:4em 1em;-webkit-border-radius:4em 1em;-khtml-border-radius:4em 1em;border-radius:4em 1em; }

#tab1 a
{ -moz-border-radius:1em 1em 1px 1px;-webkit-border-radius:1em 1em 1px 1px;-khtml-border-radius:1em 1em 1px 1px;border-radius:1em 1em 1px 1px; }
/*	rounded corner	*/

/*	tabs		*/
#tab { $pad_10 0 0; }
#tab a { $bk:$d_tab;$bord_all;border-bottom:none;$diblok;$fon_sz:14px;height:30px;line-height:30px;$txct;width:112px; }
#tab a:hover,.current { $fbold; }
/*	tabs		*/

/*	popup		*/
#overlay { width:200px;opacity:0.5;filter:alpha(opacity=50); }
#modal { $posfix;bottom:120px;left:-220px; }
#popup { $bk:#eee;$dblok;width:200px;$ov_hid;$posrel;top:20px;left:0;z-index:999; }
#popup li:$fc { $bk:$blue; }
#popup li:$fc a { color:#fff; }
#popup li a:not(#close) { $dblok;font-size:20px;$pad_10; }
#popup li:not(:$fc) a:hover { color:$gold; }
#popup li { border-bottom:#ccc solid 1px;$txlf;$w100; }
#popup #close { color:#fff;font-size:16px;$pad_0 5px;$posabs;top:10px;right:10px; }
#overlay,#popup
{ -moz-border-radius:0 1em 1em 0;-webkit-border-radius:0 1em 1em 0;-khtml-border-radius:0 1em 1em 0;border-radius:0 1em 1em 0; }
/*	popup		*/

/*	placeholder		*/
::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
  color: $place_color;
  opacity: 1; /* Firefox */
}

:-ms-input-placeholder { /* Internet Explorer 10-11 */
  color: $place_color;
}

::-ms-input-placeholder { /* Microsoft Edge */
  color: $place_color;
}
/*	placeholder		*/

/*	classes		*/
.dark { color:#333 $imp; }
.good { color:darkgreen; }
.bad { color:red $imp; }
.blue { color:$blue $imp;$fbold; }
.gold { color:$gold;$fbold; }
.lite { color:#999 $imp; }
.clear { clear:both; }
.ct { $txct; }
.lt { $txlf $imp; }
.rt { $txrt $imp; }
.b { $fbold; }
.u { border-bottom:#000 dotted 1px; }
.fnon { $fnon $imp; }
.breakaway { $bk:#ffd $imp; }
.breakoff { $bk:#ddd $imp; }
.breakfda { $bk:red $imp;color:#fff $imp; }
.print { $frgt;$posfix;top:20px;right:20px; }
.print li { $pad_5 0;clear:both; }
.print a { $print;$frgt;$dblok;height:40px;width:120px; }
.printonly,.hide { $dnon; }
.pbut { $bord_all $imp;$dnon $imp;height:24px $imp;width:50px $imp; }
.monyr { $pad_10; }
input.monyr { $pad_5; }
.more { color:$blue;$cur_pt;$fon_sz:11px;$fital; }
.note { $diamond;color:$blue;padding-left:20px; }
.loading,.responsor { $dblok;height:25px;$pad_0 10px;$posabs;top:75px;$maxwid; }
.responsor { height:20px;top:90px;$pad_0 50px 0 300px;width:620px; }
.smaller { color:#777;$fon_sz:11px;$fital; }
.nobg,.nobor { $bg_non $imp; }
.nobor { $bor_0 $imp; }
.nowrap { $ov_hid $imp;$nowrap; }
.nodata { padding:20px; }
.nostock { height:75px $imp;width:75px $imp;$posabs;top:0;left:-22px;z-index:10; }
.nostockprev { height:50px $imp;width:50px $imp;$pad_0;top:-8px;left:16px; }
.getminpv { height:100px;line-height:100px;$txct; }
a.back { $frgt $imp;$mar_5 10px;$pad_5 20px $imp; }

.s1,.s2,.s3,.s4,.s5,.s6,.s7,.s8 { $diblok;$mar_0 $imp; }
.s0 { width:20px $imp;$txrt; }
.s1 { width:50px $imp;$txct; }
.s2 { width:80px $imp;$txct; }
.s3 { width:100px $imp; }
.s4 { width:150px $imp; }
.s5 { width:200px $imp; }
.s6 { width:300px $imp;}
.s7 { width:500px $imp;$ov_aut; }
/*	classes		*/

CSS;

if($addcss==1) {
echo <<<CSS
#bg { $bk:$bg;$h100;$pad_0 10px;$posfix;bottom:0;$maxwid;z-index:-99; }

.home { $bor1 #ccc;$mar_10 auto;$pad_10 20px;width:800px; }
.home li { border-bottom:0 $imp;$flft;$pad_10 ; }
.home li a { $bk:$blue;color:#fff;$dtcell;$fon_sz:14px;height:60px;width:100px;$pad_10;$txct;$vam; }
.home li a:hover { $bk:$gold;color:$blue; }

.list { $mar_0 auto; }
.list li { $cur_pt;$ov_hid;$mar_0 auto;$pad_5 10px; }
.list li:hover { $bk-color:#dcedf5; }
.list span,
.list strong { $diblok;min-height:16px;$h_aut;$pad_0 5px;$vat;width:140px; }
.list img { $frgt;height:100px;$mar_0 auto;$txct; }
.list div { $diblok;$pad_0 5px;$mar_0 10px;width:130px; }

form { $dblok;$mar_10;$pad_10; }
form ul { $mar_0 auto;width:650px; }
form ul li { $bord_bot;$pad_10; }
form li img { border:#eee dotted 1px;height:36px;width:36px;margin-bottom:-12px; }
form .blue { $fon_sz:14px; }
form label:$fc { $diblok;width:100px $imp; }
select { padding:2px; }
input { $pad_5; }
$in_cbo,$in_rad { $pad_5;width:16px; }
input.txt,textarea { width:500px; }
input.btn { $bk:$bg;$frgt;height:24px;line-height:12px; }
textarea { height:100px; }
.editor { $m_lft:100px; }

ul#filter { $pad_5 0; }
ul#filter li { $bor_0;$flft;$pad_5; }
ul#filter li input { $bor_0;$mar_0;$pad_0;width:20px; }
#buttons li { $bor_0 $imp;$frgt;$mar_0;$pad_0 5px; }
#buttons li,#buttons a { height:32px;width:32px; }
#buttons a { $src/buttons.png) $norep; }
#buttons .home a { $bg_pos:0 0; }
#buttons .save a { $bg_pos:-32px 0; }
#buttons .add a { $bg_pos:-64px 0; }
#buttons .edit a { $bg_pos:-192px 0; }
#buttons .del a { $bg_pos:-96px 0; }
#buttons .cancel a { $bg_pos:-128px 0; }
#buttons .back a { $bg_pos:-160px 0; }
#nicEditButtons { $posrel;right:0; }
#uploadrecapsched { $frgt;margin:6px; }
#getavailrecap { $frgt; }
#transferstocks div { $mar_0 auto;width:650px; }
#recap ul,#getrec ul,#getsm>ul,#getbonussum>ul { $bor1 #ccc;padding:20px 20px 40px; }
.dlrecap { $fnon $imp; }

#transarc li ,#addline li { width:600px; }
#addline span { $vam; }
#transarcdetails ul { width:800px; }
#transarcdetails span { $pad_0 5px; }
#realtime input:not(.btn) { $bor_0;border-bottom:#888 solid 1px; }
#realtime li span { $pad_0 5px; }
#realtime #errmsg { color:darkred; }
#bomstp li span { $nowrap $imp; }
#bomstp li span.s3 { width:106px $imp; }
#dllog li { width:880px; }
#dllog li span { $pad_0 5px;$ov_hid; }
#box { overflow-x:scroll;$nowrap; }
#stocks { $diblok; }
#stocks .s5 { $ov_hid;text-overflow:ellipsis; }
#stocksactual li { width:650px; }
#newdistri li { $pad_5 10px; }
#newdistri li * { $vam;}
#newdistri.list li * { $nowrap;$ov_hid; }
#newdistri li strong { $fon_sz:16px; }
#newdistri ul { $diblok;$vat;width:420px; $bor1 #ccc }
#newdistri ul $in_but { $vat; }
#newdistri a { $pad_0 5px; }
#newdistri li textarea { height:210px;$mar_0 5px 5px 0;$pad_10;width:420px; }

.load_calendar { $mar_0 auto;width:350px $imp; }
.load_calendar li { $flft;font:bold 18px arial;height:40px;line-height:40px;$txct;width:50px; }
.load_calendar a { $dblok;font:normal 18px arial;height:40px;line-height:40px; }
.load_calendar a.today { $fbold;text-decoration:underline; }
.monyear { height:50px;$txct; }
.monyear a { $dinlin;$mar_0 20px;width:20px; }
.monyear span { $diblok;font:bold 20px arial;width:200px; }
.assembly  ul li:$fc { $bk:#ddd; }
.fdalist span { $ov_hid;$nowrap; }

#search { $frgt; }
#orderhdr { $pad_10 50px;width:860px; }
#orderhdr li { $bor_0 $imp;$flft;$txct;width:166px; }
#orderhdr a { $fon_sz:14px;$fbold;$pad_5; }
.orders li { $bk:none; }
.orders li:hover { $bk:#eee; }
.orders li * { font-family:tahoma $imp; }
.delistat { $cur_pt; }

#overlay { $bk:#000;$pad_10;$posabs;z-index:99; }
.hdr { $bk-color:#fff;height:20px $imp;line-height:20px; }
.hdr span { $fbold; }
.upload { $bor1 #ddd;$mar_10; }
.upload div { $diblok; }
.upload li { $pad_5; }
.removepic { color:#00539b;$cur_pt; }
.blk { color:#999 $imp; }

CSS;
}

if($req=='mypage') {
echo <<<CSS

/*	distributors 	*/
#foot { height:50px; }
#download { $flft;$fon_sz:12px;$pad_5; }
#container { min-height:100%; }
#distdata span { $diblok;width:90px;$vam; }
#distdata div { line-height:22px;margin:2px 0;$bor_0; }
#distdata img { $frgt;margin:-80px 100px 0 0; }
#distdata label { color:#777;$diblok; }
#spon_id { $cur_pt; }
#data_list { $bord_all;$mar_0; }
#data_list li { $bord_bot;$pad_5 0; }
#data_list li:hover { $bk:$bodybg; }
#data_list li ul { $bk:#fff;$pad_0; }
#data_list .hdr { $bg_non $imp; }
#data_list input { $txct;width:40px; }
#data_list span { color:#1b3f8b;$diblok;$pad_0;$vat; }
#recaplookup { $posabs;top:10px;right:10px; }
#recaplookup a { $frgt; }
#dsmstp li { $pad_5 0; }
#dsmstp span { color:#1b3f8b;$diblok;$pad_0 10px;$vam; }
#dsmstp $in_txt { $pad_5;width:400px; }
.recap { $bor_0 $imp; }
.recap div,.recap li,.recap label,.recap span,.recaphead { font:normal 14px 'courier new' $imp; }
.recap .s8 { width:450px $imp; }
.recaphead { $fon_sz:16px $imp;$txct; }
.recaptotals { $m_lft:430px; }
.blue { color:$blue;$fon_cus;$fon_sz:14px; }
.hdr { $pad_10 5px; }
.hdr span { $fon_sz:14px;$fbold; }
.lookup_data { $dnon;$bk:$bg;$bor1 $blue;$w100;$pad_10;$posabs;top:329px;left:-1px; }
#searchresults { $bcc; }
#searchresults td,
#searchresults th { $bor1 $btnhov;$pad_5;$txlf; }
#searchresults tr:nth-child(even) { $bk:$btnhov; }
#searchresults tr:hover { $bk:$gold; }
#tab a:hover,.current { $bk:$d_tab_curr $imp; }

#totals  { $pad_0 50px;width:860px; }
#totals a { $frgt; }
.orders { $bord_all;$fon_sz:14px;$mar_10 50px;$pad_10 20px; }
.orders ul { $pad_5 0; }
.orders li { $bord_bot;$fon_sz:14px;$pad_5 0; }
.orders li span { $ov_hid;$vat; }
.orders li .s6 { overflow:inherit; }
.orders li .lbl { color:$blue; }
.orders .lt { $txlf; }
.orders .rt { $txrt; }
.orders .blue { $left_nav;padding:2px 5px; }
.orders div.s7 { line-height:22px; }
.orders select { $m_lft:4px;padding:5px; width:510px $imp; }
.orders select[name=payOp] { width:282px $imp; }
.vieworders { $pad_5;$ov_hid; }
.vieworders ul { $bord_all;$diblok;$mar_5;$pad_10;$vat;width:390px; }
.vieworders .s8 { $diblok;width:270px; }
.cancelorder { $bord_all;$cur_pt;$frgt;padding:2px; }
textarea { $pad_5; }
input:not(type=radio) { $pad_5;width:500px; }
$in_sub { $frgt;$m_rgt:2px;width:100px;$ov_hid; }
#totals $in_sub { $checkout;height:32px; }
#totals $in_sub:hover { $checkout; }
#validate { $bk:darkred;$bord_all;$dnon;$m_auto;padding:20px;$posabs;$mar_0 0 0 510px;z-index:1000; }
#validate input { margin:2px;width:100px; }
#validate span { color:#fff; }
#cover { $posabs; }
input[name=payDate],.updn { $cur_pt; }

.w0 { width:50px; }
.w1 { width:120px; }
.w2 { width:250px; }
.w3 { width:350px; }
li.gen1 { $bk:$btnhov; }
span.gen1 { color:#c82536 $imp; }
.fff { $bk:#fff $imp;$bor_0 $imp;$pad_0 $imp; }
.pad { color:#fff $imp; }
.print select { font-family:'courier new';$txrt; }
.updated { $bor_0 $imp;color:#999;$txrt;$fon_sz:11px;$fital;margin:2px 10px;$pad_0 $imp; }
.updated:hover { $bk:$bg $imp; }
.btn { $bk:$btn_bg;$bor1 $btnhov;$cur_pt;$pad_5; }
.btn:hover { $bor1 #ccc; }
/*	distributors 	*/

CSS;
}else if($req=='onreg') {
echo <<<CSS

/*	online reg 	*/
#olreg,#responsor,#package,#package_data .orders { $bord_all;$m_auto;$pad_10;width:700px; }
#olreg img { width:400px; }
#olreg #download { height:50px $imp;padding:6px; }
#responsor p { $mar_5 $imp; }
li { padding:2px 5px; }
label { $diblok;$pad_5;$txrt;width:200px; }
label.s2 { $txlf; }
$in_txt { $pad_5;width:300px; }
$in_sub,$in_but { $pad_10;$w_aut; }
textarea { $pad_5; }
#package ul { $pad_5;$w_aut; }
#package li { $bord_all;$cur_pt;$diblok;$flft;$mar_5;$pad_0;$posrel;$ov_hid; }
#package img { $diblok;height:160px;width:160px; }
#package p { $bk:#000;color:#fff;height:32px;$mar_0;$pad_5 10px;$txrt;width:140px;$posabs;bottom:0; }
#package_data #shop_list .s6 { $vat;width:180px $imp }
#package_data .orders div:$fc { $pad_10 0; }
#package_data #totals { $txct;$w_aut; }
#package_data $in_sub { $checkout left top;height:32px;width:100px; }
#package_data .lbl { $vat; }
#overlay.blue { $bk:blue;color:#fff $imp; }
.btn { $bk:$btn_bg;$bor1 $btnhov;$cur_pt;$pad_5; }
.btn:hover { $bor1 #ccc; }

#olreg input:invalid { border-color: red; }

/*	online reg 	*/

CSS;
}else if($req=='first_login') {
echo <<<CSS

/*	first login 	*/
#container { padding:50px 10px; }
#foot { height:80px; }
#first_login { $m_auto;width:500px; }
form { $bord_all;$pad_10; }
h3 { $bord_bot; }
h3,p,li { margin:20px 0;$pad_0 10px; }
li,span { padding-left:20px; }
label { $diblok;width:160px; }
input { $pad_5;width:270px; }
$in_sub { $w_aut; }
.blue { $fon_sz:16px;$pad_5 10px; }
/*	first login 	*/

CSS;
}else if($req=='error') {
echo <<<CSS

/*		error			*/
body { $h100;$ov_hid; }
#head { padding:20px 10px; }
#container { $bk:$bg;$h100;$pad_0; }
.bad_browser { margin:50px 0; }
.bad_browser p { $txct; }

.msg { $flft;$mar_0 10px;width:420px; }
.msg h2 { color:$blue;$txct; }
.msg p { $fon_sz:14px;$fbold;$txct; }
.msg span { color:green;font:bold 18px verdana; }
.msg img { $dblok;height:200px;width:300px;$m_auto; }
.msg img,.contact { -moz-border-radius:10px;-webkit-border-radius:10px;-khtml-border-radius:10px;border-radius:10px; }

.contact { $bor1 $blue;border-width:1px 0 1px 0;$flft;$pad_0 20px;width:400px; }
.contact ul { $mar_10 0; }
/*		error			*/

CSS;
}else if($req=='gos'||$req=='pcm') {
echo <<<CSS

/*	gos 	*/
select { padding:4px $imp; }
#filter li { $w_aut $imp; }
#filter label { $pad_0 8px;$w_aut; }
#center_orders,
#center_products { $pad_10; }
#msg { $fon_sz:14px; }
#shortnav { $bk:#eee;$bor1 #aaa;width:42px;$posfix;left:-10px;top:20px;$pad_10; }
#shortnav li { margin:0 0 5px 10px; }
#shortnav a { $bk:green;$dblok;height:32px;width:32px; }
#shortnav li:$fc a { $src/buttons.png) 0 0 $norep; }

.gos_totals,
.pcm_totals { $bk:$bg;$bor1 #777;$diblok;$fon_sz:20px;height:20px;padding:15px 10px;$txrt;vertical-align:bottom;width:160px; }
#gos_cart,
#pcm_cart { $bk:$d_tab_curr;$bor1 #777;height:0;$maxwid;$m_lft:-20px;$pad_0 30px 10px;$posfix;bottom:-10px; }
#gos_cart ul,
#pcm_cart ul { border-bottom:#777 solid 1px;$mar_0;padding:15px 0; }
#gos_cart li,
#pcm_cart li { $flft;height:52px;margin-bottom:10px; }
#gos_cart input[type=file],
#pcm_cart input[type=file] { $bor1 #777;$fon_sz:12px; }
#gos_cart .txt,
#pcm_cart .txt { $mar_0;width:284px; }
#gos_cart label,
#pcm_cart label { $flft;$w_aut; }
#gos_cart label.rt,
#pcm_cart label.rt { $frgt; }

#ulkart,#ulsubm { height:52px; }
#uldist { height:176px; }
#ulpaym { height:238px; }
#uldist li,#ulpaym li { width:476px; }
#uldist span,#ulpaym span { $flft;$mar_0 5px;width:140px; }
#ulsubm li,#ulkart li { width:952px; }
#datePicker { $cur_pt; }
#gos_tab,#pcm_tab,
#ulsubm span { $cur_pt;$frgt $imp;$m_lft:10px; }
#gos_tab:hover,#pcm_tab:hover,
#ulsubm span:hover { $bk:$d_tab; }
#tab { padding-bottom:0 $imp; }
#tab a { width:112px; }
#tab a:hover,.current { $bk:$d_tab_curr $imp; }

.distri li,.setup li { $pad_10; }
.distri .dobatch { $diblok;$fnon $imp;$pad_5 $imp;$mar_5 0 $imp; }

.orders $in_txt { margin:-2px 0 0 $imp; }
.orders li { width:820px; }
.orders .s6 { $h_aut; }
.orders .hdr span { width:820px;$pad_0 5px;$mar_0; }
.list li,.remit .txt,.oqty { padding:3px $imp; }
#upfile { $diblok;$mar_0 $imp;$pad_0 $imp;width:100px; }
#ulpaym #upfile { $flft; }
#batpaym li,#batconf li,#batrepl li,#batclos li { $diblok; }
#distrilist { $bk:$bg;height:476px;$maxwid;$pad_5 10px;$posfix;bottom:0;z-index:9999; $bor1 #777;$ov_aut; }
#distrilist li { $bord_bot;$cur_pt;$dblok;$txlf;$pad_5; }
#distrilist span { $fon_sz:20px; }
#distrilist p { $cur_pt;$posabs;top:0;right:0;$mar_10; }

.remit ul,.remit .hdr { border-bottom:#aaa solid 1px;$mar_0 auto; }
.remit ul div { $bor1 #eee;$dnon;$mar_10 0 5px;$pad_5;width:610px; }
.remit li { $pad_5 $imp; }
.remit .lt { $txlf $imp; }
.chkbox { width:14px $imp;$mar_0 2px; }
.smaller:hover { $bg_non $imp;color:darkred; }
.ital { $fital; }
.remit #upfile { $w_aut; }
.multi span { $plusminus top right;$cur_pt;$diblok;height:14px;width:14px; }
.multi select { $mar_0 0 0 5px $imp; }
.multi input { $mar_0 3px $imp; }
.remit table { $bcc; }
.remit th,.remit td  { $bor1 #777 $imp;padding:2px 5px $imp; }
.remit td { max-width:100px $imp;$ov_hid;text-overflow:ellipsis;$nowrap; }

.setup form label { $txrt;$mar_0 5px;width:120px; }
.setup form .txt { width:480px; }

.remit ul,.remit .hdr,.remit li { width:720px; }
.remit span,.remit .hdr  { $h_aut $imp;$w_aut; }

.hdr { $bg_non $imp; }
iframe,input[name=gc_batch],
#batpaym,#batconf,#batrepl,#batclos,
.multi form input,.list .chkbox { $dnon; }
#addnew { $plusminus 0 2px;$diblok;height:14px;width:14px;$mar_0 4px;$pad_0; }
/*	gos/pcm 	*/

CSS;
}
?>
<?php
if($req=='calendar') {
	$dbsrc='beta';
	require('../admin/infoconfig.php');
	$tbl='tbllocations';
	$rs = mysqli_query($con,"SHOW TABLES LIKE '$tbl'") or die(mysqli_error($con));
	if(mysqli_num_rows($rs)==1) {
		$rs=mysqli_query($con,"SELECT * FROM $tbl WHERE status=1") or die(mysqli_error($con));
		while($rw = mysqli_fetch_assoc($rs)) {
			echo '.'.$rw['id'].' { color:'.$rw['color'].' '.$imp.'; }'."\n";
		}
	}
	/* mysqli_close($con); */
	echo '/*	calendar 	*/';
}?>
<?php
if($addcss==2) {
$mgronly='';$astocks='';$calprint='';
$willshow=(isset($_SESSION['is_recap'])&&$_SESSION['is_recap'])?'':$dnon;
if(isset($_SESSION['is_mgrlist'])&&$_SESSION['is_mgrlist']) {
	$mgronly="#head,#distdata,#tab,.updated{ $dnon }
#data_list{ $posabs;top:0; }";
}
if(isset($_SESSION['isActualStocks'])&&$_SESSION['isActualStocks']) {
	$astocks="#head,.nav2,input { $dnon $imp; } #sig,.pbut { $diblok $imp; } #stocksactual li { $pad_0; } #stocksactual input { $pad_0;height:18px; }
";
}
if($req=='calendar') {
	$calprint="#cart,.lava,.nav2,.caltitle,input { $dnon $imp; }
	.lastmonth,.nextmonth { color:#fff $imp; }
	.calendar_page li { $bor1 #999;border-width:0 1px 1px 0; }";
}
if($req=='gos'||$req=='pcm') {
	$center="
	#head,#shortnav,.nav2 { $dnon; }
	.remit ul { $mar_0 $imp;$bor_0; }
	.remit ul,.remit li { width:420px $imp; }
	.paysummary li { $bord_bot $imp;width:800px $imp; }
	table { page-break-inside:avoid; page-break-after:auto }
  ";
}
$usemargin='0mm 0mm 0mm 0mm';
echo <<<CSS

/*		print		*/
body { $bk:$bg;$mar_0; }
#user,.btn,.transrec { $dnon $imp }
#olreg li { $pad_5 0; }
#olreg input { $bor_0; }
#olreg label { $diblok; }
.toplinks { $willshow; }
.recaphead,.recap div,.recap span,.recap label,.recap li,.recap p { font:normal 20px 'courier new' $imp;$ov_hid; }
.recaphead { margin:-100px 0 50px;padding-left:200px; }
.recap .s2 { width:70px $imp; }
.recap .s3 { width:140px $imp; }
.recap .s4 { width:200px $imp; }
.recap .s5 { width:250px $imp; }
.recap .s6 { width:260px $imp; }
.recap .s7 { width:800px $imp; }
.recap .hdr .s6 { width:450px $imp; }
.recaptotals { $m_lft:450px; }

.toplinks,#head form,#foot,#tab,#dist_level,#download,.print,#totals,#notice,
#backtotop,.totop,.noprint,#search { $dnon $imp; }

#sig,.printonly { $dblok; }
#data_list,#data_list li,.nav2 { $bor_0; }
span,label { $fon_sz:12px;color:#333 $imp; }
.updated { $fon_sz:10px;$fital;color:#555; }
$mgronly $astocks $calprint $center
@page{
    size: auto;
    margin:$usemargin;
}
/*		print		*/
CSS;
}
?>
