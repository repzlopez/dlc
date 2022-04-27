jQuery(document).ready(function() {

     jQuery(document).on('click','.link',function() {
          window.location = jQuery(this).attr('rel');
     });

     jQuery(document).on('click','.reflink',function(e) {
		var $tmp = jQuery('<input>');
		jQuery('body').append($tmp);
		$tmp.val(jQuery(this).attr('rel')).select();
		document.execCommand('copy');
		$tmp.remove();
          e.preventDefault();
	});

     jQuery(document).on('click','.addtocart',function(e) {
          if( jQuery("#product_list").length>0 ) {
               var loc = jQuery(this).siblings('a').attr('href');

          } else {
               var loc = window.location.href;
          }

          var pid = jQuery(this).attr('data-id');
          var qty = 1;

          jQuery.ajax({
               type: 'POST',
               url: '/distrilog/cart/shop.php',
               data: { 'pid':pid, 'qty':qty, 'loc':loc },
               beforeSend: function() { jQuery("#shopping_cart").show(); },
               success: function(data) {
                    loadCart();
               },error: function(XMLHttpRequest,textStatus,errorThrown) {
                    alert("Error: "+errorThrown);
               }
          });

          e.preventDefault();
     });

     jQuery(document).on('click','#clearcart',function(e) {
          if( confirm("Clear Orders?") ) {
               jQuery.ajax({
                    type: 'POST',
                    url: '/distrilog/cart/shop.php',
                    data: {'clear':true},
                    success: function(data) {
                         unloadCart();
                    }
               });

               if( jQuery("#shop_list").length>0 ) {
                    location.reload();
               }
          }

          return false;
     });

     if( jQuery("#product_list").length>0 ) {
          var cat = jQuery("#product_list").attr('class');
          loadProductList(cat);

          jQuery("#product_search").on('keyup',function(e) {
               var ret = jQuery(this).val();
               loadProductList(cat,ret);
          });
     }

     if( jQuery("#product_list,#product_main").length>0 ) {
          jQuery('#secondary').css('display','block');
          loadCart();
     }

     if( jQuery("#product_list,#product_main,#shop_list").length>0 ) {
          if( jQuery("#shop_list").length>0 ) {
               var parent = "#shop_list ul";
               var span   = "span:nth-child(5)";
               var lastcount = 4;

          } else {
               var parent = "#shopping_cart";
               var span   = "span:nth-child(2)";
               var lastcount = 3;
          }

          var cartli = parent + " li:not(:first-child):not(:last-child) " + span;
          jQuery(document).on('click',cartli,function(e) {
               if( jQuery( parent + " li" ).length>lastcount ) {

                    if( confirm('Remove item?') ) {

                         if( jQuery("#shop_list").length>0 ) {
                              var pid = jQuery(this).siblings('span:first-child').text();

                         } else {
                              var pid = jQuery(this).parent('li').attr('data-pid');
                         }

                         var qty = jQuery(this).text();

                         shopUpdate(pid,-qty);
                    }

               } else {
                    jQuery('#clearcart').click();
               }
          });

          jQuery(document).on('click','#qtydn',function(e) {
               var pid = jQuery("#plusminus").attr('data-pid');
               shopUpdate(pid,-1);
          });

          jQuery(document).on('click','#qtyup',function(e) {
               var pid = jQuery("#plusminus").attr('data-pid');
               shopUpdate(pid,+1);
          });

          jQuery(document).on('mouseover',parent + " li",function(e) {
               jQuery("#plusminus").remove();
          });

          jQuery(document).on('mouseover',parent + " li" + ":not(:first-child):not(:last-child)",function(e) {
               var cart_side = jQuery(this).attr('data-pid');
               var cart_main = jQuery(this).children('span:first-child').text();

               jQuery(parent).before('<div id="plusminus" data-pid=""><div><span id="qtydn"></span><label class="s0"></label><span id="qtyup"></span></div></div>');
               if( jQuery("#shop_list").length>0 ) {
                    var pr = jQuery('#shop_list').find("li:contains('"+ cart_main +"')");
                    var sc = pr.children('span:nth-child(5)');

                    jQuery("#plusminus").attr('data-pid',cart_main)
                         .css('top',( jQuery(this).offset().top - jQuery("#shop_list").offset().top -5 ) +'px')
                         .css('left',( jQuery(this).offset().left - jQuery("#shop_list").offset().left +430 ) +'px')

               } else {
                    jQuery("#plusminus").attr('data-pid',cart_side)
                         .css('top',(jQuery(this).offset().top - jQuery(parent).offset().top -4) + 'px')
                         .css('left',(jQuery(this).offset().left - jQuery(parent).offset().left +212) + 'px');
               }
          });

          jQuery(document).on('mouseover',"#shop_list li:nth-last-child(2)",function(e) {
               jQuery("#plusminus").remove();
          });
     }

     function loadProductList(cat='',find='') {
          jQuery.ajax({
     		type: 'POST',
               url: '/updateproducts.php',
               data: { 'wp':true,'p':cat,'find':find },
               beforeSend: function() {
                    jQuery("#product_list").html('<li>Loading... Please wait...</li>');
               },
     		success: function(data) {
                    if( data=='' ) {
                         data = '<li style="color:red">Item not found</li>';
                    }

                    jQuery("#product_list").html(data);
     		}
     	});

     }

     function loadCart() {
          unloadCart();
          jQuery.ajax({
               type: 'POST',
               url: dlcAjax.cart_url,
               success: function(data) {
                    if( data!='' ) {
                         jQuery("#shopping_cart").html(data).show();
                         jQuery(".widget_custom_html").slideDown(1);
                         jQuery("#cart_menu").show();
                         jQuery(".no_cart").removeClass('no_cart');
                    }
               },error: function(XMLHttpRequest,textStatus,errorThrown) {
                    alert("Error: " + errorThrown);
               }
          });
     }

     function unloadCart() {
          jQuery("#shopping_cart").hide();
          jQuery(".widget_custom_html").slideUp(100);
          jQuery("#cart_menu").addClass('no_cart').hide();
     }

     function shopUpdate(pid,qty) {
          var sc = jQuery('#shop_list').find("li:contains('"+ pid +"')");
          var qt = sc.children('span:nth-child(5)').text();
          var pv = parseFloat(sc.children('span:nth-child(3)').text());
          var pc = fmtCurr(sc.children('span:nth-child(4)').text());
               pc=pc.toString().replace(/\$|\,/g,'');

          var tq = 0;
          var tv = 0;
          var tc = 0;

     	jQuery.ajax({
     		type: 'POST',
               url: '/distrilog/cart/shop.php',
     		data: { 'pid':pid, 'qty':qty, 'loc':1 },
     		success: function(data) {
                    if( jQuery("#product_list,#product_main").length>0 ) {
                         loadCart();
                    }

                    var q = parseInt(qt)+parseInt(qty);
                    sc.children('span:nth-child(5)').text(q);
                    sc.children('span:nth-child(6)').text(fmtCurr(q*pv));
                    sc.children('span:nth-child(7)').text(fmtCurr(q*pc));

                    jQuery('#shop_list li:not(:first-child):not(:last-child):not(:nth-last-child(2))').each(function() {
                         tq += parseInt(jQuery(this).children('span:nth-child(5)').text());
                         tv += parseFloat(jQuery(this).children('span:nth-child(6)').text());
                         tc += parseFloat(jQuery(this).children('span:nth-child(7)').text().toString().replace(/\$|\,/g,''));
                    });

                    var tt = jQuery('#shop_list li:last-child');
                    tt.children('strong:nth-child(5)').text(tq);
                    tt.children('strong:nth-child(6)').text(fmtCurr(tv),2);
                    tt.children('strong:nth-child(7)').text(fmtCurr(tc));
               },error: function(XMLHttpRequest,textStatus,errorThrown) {
                    alert("Error: "+errorThrown);
     		}
     	});
     }
});

function fmtCurr(num) {
	num=num.toString().replace(/\$|\,/g,'');
	if(isNaN(num)) num="0";
	sign=(num==(num=Math.abs(num)));
	num=Math.floor(num*100+0.50000000001);
	cents=num%100;
	num=Math.floor(num/100).toString();
	if(cents<10)
	cents="0"+cents;
	for(var i=0;i<Math.floor((num.length-(1+i))/3);i++)
	num=num.substring(0,num.length-(4*i+3))+','+
	num.substring(num.length-(4*i+3));
	return (((sign)?'':'-')+num+'.'+cents);
}
