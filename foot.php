<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
ob_start();
echo '</div>';

$arrJs=array('/js/jquery/jquery-1.7.1.min.js','/js/jquery/jquery.easing.min.js','/js/jquery/jquery.lavalamp.min.js');
if($page=='index'){ $arrJs[]='/js/jquery/jquery-ui.min.js';}
if($page=='news'){ $arrJs[]='/js/jquery/lightbox-2.6.min.js';}
$arrJs[]='/js/common.js';
$arrJs[]='/js/cart.js';
ob_start();
echo loadFoot('','<a href="http://www.dlctw.com" target="_blank">DLC Taiwan</a>',$arrJs);
?>
<!-- Load Facebook SDK for JavaScript -->
<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
    FB.init({
      xfbml            : true,
      version          : 'v3.3'
    });
  };

  (function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<!-- Your customer chat code -->
<?php
echo ISIN_DISTRI||SPECTATOR||OVERRIDE?'':'<div class="fb-customerchat"
  attribution=setup_tool
  page_id="209982732380914"
  theme_color="#00539b">
</div>';
ob_end_flush();?>
