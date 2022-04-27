$(document).ready(function(){
	var prodofchoicemin=$('#tpv').attr('data-olpv');
	var shop_url = location.protocol + '//' + window.location.host + '/distrilog/cart/shop.php';

	var cartnote=($('.cartnote').length==0)?-230:-250;
	if($('#cart ul li').length==0) $('#totals a').slideUp();
	$('#freight').slideUp();

	$('#shopbag').toggle(
		function(){$('#cart').stop().animate({bottom:0},{duration:2000,easing:'easeOutElastic'});sumTotal();},
		function(){$('#cart').stop().animate({bottom:cartnote},{duration:2000,easing:'easeOutElastic'})}
	);

	$('#addtocart').click(function(){
		var loc=window.location.href;
		var pid=$(this).attr('rel');
		var qty=$('#kart input').val();
		shopUpdate(pid,qty,loc);
		$('#cart').stop().animate({bottom:0},{duration:2000,easing:'easeOutElastic'});
		return false;
	});

	$('#freightinfo').toggle(
		function(){
			$('#freight').slideDown();
			return false;
		},function(){
			$('#freight').slideUp();
	});

	$('#checkout').click(function(){
		var bal=prodofchoicemin-parseFloat($('#tpv').text());
		if($('#cart').attr('rel')=='olreg'&&bal>0){
			alert("Selected products are below "+prodofchoicemin+"pv. You need "+fmtCurr(bal)+"pv more.");
			return false;
		}
	});

	$('#clearcart').click(function(){
		$confirm = confirm("Clear Orders?");
		if($confirm){
			$.ajax({
				type: 'POST',
				url: shop_url,
				data: {'clear':true},
				success: function(data){
					$('#cart ul').empty().append(data);
				}
			});
			$('#shop input[type=text]').val(0);
			$('#cart').stop().animate({bottom:cartnote},{duration:2000,easing:'easeOutElastic'});
			$('#tam').text('0.00');
			$('#tpv').text('0.00');
			$('#totals a').slideUp();
		}return false;
	});

	$('.qtyup').live('click',function(){
		$('#cart').stop().animate({bottom:0},{duration:2000,easing:'easeOutElastic'});
		var loc=$(this).parent().next().attr('href');
		var pid=$(this).prev().attr('rel');
		var shp=$('input[rel='+pid+']');
		shopUpdate(pid,1,loc);
	});

	$('.qtydn').live('click',function(){
		$('#cart').stop().animate({bottom:0},{duration:2000,easing:'easeOutElastic'});
		var loc=window.location.href;
		var pid=$(this).next().attr('rel');
		var shp=$('input[rel='+pid+']');
		if(parseInt($('span[rel='+pid+']').text())>0) shopUpdate(pid,-1,loc);
	});

	$('#kart input').click(function(){
		$(this).select();
	});

	$('#useDist').click(function(){
 		if($(this).attr('checked')){
			$('#deliName').val($('#distName').text());
			$('#deliCont').val($('#distCont').text());
			$('#deliAddy').val($('#distAddy').text());
		}else{
			$('#deliName').val('');
			$('#deliCont').val('');
			$('#deliAddy').val('');
		}
	});

	$('#payStat').click(function(){
		if($(this).attr('checked')){
			$('input[name=payStat]').val(1);
		}else{
			$('input[name=payStat]').val(0);
		}
	});

	$('input[name=deliBox]').change(function(){
		var boxamt=$(this).attr('data-boxfee');
		var payamt=$('input[name=payAmt]').attr('rel')
		payamt=parseFloat(payamt.replace(/\,/g,''),10);

		$('.delfee').text(fmtCurr(boxamt));
		$('input[name=payAmt]').val(fmtCurr(payamt+parseFloat(boxamt)));
	});

	$('input[name=deliAddto]').click(function(){
		$('input[name=deliBox]:eq(3)').click();
	});

	$('select[name=payOp]').change(function(){
		var p;
		var s=$(this).val();
		var v=$('select[name=payOp] :selected').text();
		var i=v.indexOf('c/o ');
		rec=v.substring(i);
		var boxamt=$('input[name=deliBox]:eq(0)').attr('data-boxfee');
		$('.ifremit').remove();

		switch(s){
			case '7':
			case '8':
				var boxamt=0;break;
			case '11':
			case '12':p='Name of Remittance Center (LBC, Palawan, Western Union, M Lhuillier, 7-11, etc.)'+"\n"+'Name of Sender'+"\n"+'Contact #';break;
			default:p='Enter note here';break;
		}

		var payamt=$('input[name=payAmt]').attr('rel');
		payamt=parseFloat(payamt.replace(/\,/g,''),10);

		$('.delfee').text(fmtCurr(boxamt));
		$('input[name=payAmt]').val(fmtCurr(payamt+parseFloat(boxamt)));

		if(s<=6||(s>10&&s<13)) $(this).parent('li').after('<li class="ifremit"><span class="s4 lbl">Pay To</span> <p class="s7">DLC Philippines Shareconomy, Inc.<br>'+rec+'<br>0917 854 3837 / 0922 835 7068</p></li>');
		$('textarea[name=payNote]').attr('placeholder',p).css('height','60px');
//		document.getElementsByName('payNote')[0].placeholder=i;
	});

	$('.disabledbutton').live('click',function(){
		alert('waaa');
		return false;
	});

	$('#totals input[type=submit]').click(function(){
		$('input[type=text],textarea').css({'border':'#ccc solid 1px'});
		var $bad_deli='<span class="bad" id="download">PLEASE ENTER DELIVERY DETAILS</span>';
		$('.bad').remove();

		if($(this).attr('data-order')<1){
			window.scrollTo(0,0);
			$('#package').append('<span class="bad" id="download">PLEASE SELECT YOUR PACKAGE</span>');
			return false;
		}if($('input[name=deliName]').val()==''){
			$('input[name=deliName]').css({'border':'gold solid 1px'}).focus();
			$('#delivery').append($bad_deli);
			return false;
		}if($('input[name=deliCont]').val()==''){
			$('input[name=deliCont]').css({'border':'gold solid 1px'}).focus();
			$('#delivery').append($bad_deli);
			return false;
		}if($('textarea[name=deliAddy]').val()==''){
			$('textarea[name=deliAddy]').css({'border':'gold solid 1px'}).focus();
			$('#delivery').append($bad_deli);
			return false;
		}if($('input[name=deliBox]:eq(3)').is(':checked')&&$('input[name=deliAddto]').val()==''){
			$('input[name=deliAddto]').css({'border':'gold solid 1px'}).focus();
			$('#delivery').append($bad_deli);
			return false;
		}else{
			if(!confirm("Checkout and Post Orders?")) return false;
		}
	});

	$('.updateOrder').click(function(){
		if($('#validatepw').length==0){
			var refNo = $('#refNo').text();
			var payAmt = $('input[name=payAmt]').val();
			var payNote = $('textarea[name=payNote]').val();
			var payOp = $('select[name=payOp]').val();
			var payDate = $('input[name=payDate]').val();
			var deliName = $('input[name=deliName]').val();
			var deliCont = $('input[name=deliCont]').val();
			var deliAddy = $('textarea[name=deliAddy]').val();
			var deliNote = $('textarea[name=deliNote]').val();
			var deliAddto = $('input[name=deliAddto]').val();
			$confirm = confirm("Update Order with Ref# "+refNo+"?");
			if($confirm){
				$('.updateOrder').text('UPDATING...');
				$.ajax({
					type: 'POST',
					url: 'post.php',
					data: {	'payOp':payOp,
							'payAmt':parseFloat(payAmt.replace(/\,/g,''),10),
							'payNote':payNote,
							'payDate':payDate.replace(/\./g,''),
							'deliName':deliName,
							'deliCont':deliCont,
							'deliAddy':deliAddy,
							'deliNote':deliNote,
							'deliAddto':deliAddto,
							'refNo':refNo,
							'update':true,
							'submit':'|||'},
					success: function(data){
						if(document.referrer.indexOf(window.location.hostname)!=-1){
							parent.history.back();
						}
					}
				});
			}
		}return false;
	});

	$('.cancelorder').click(function(){
		if($('#validatepw').length==0){
			var refNo = $('#refNo').text();
			var $confirm = confirm("Cancel Order with Ref# "+refNo+"?");
			if($confirm){
				var html='<div id="validate"><span>Enter Password</span> <input type="password" id="validatepw" /> <input type="button" id="validatebtn" value="CONFIRM" /></div>';
				$('.vieworders ul').append(html);
				$('#validate').slideDown(600).show();
				$('#validatepw').focus();
				$('.updateOrder').attr('disabled','disabled');
				$('.cancelorder').attr('disabled','disabled');
			}
		}
		return false;
	});

	$('#validatebtn').live('click',function(){
		var validpw=$('#validatepw').val();
		$.ajax({
			type: 'POST',
			url: 'validate.php',
			data: {'pw':validpw,'cancel':true},
			beforeSend: function(){
				$('#validatebtn').val('CANCELLING...')},
			success: function(isvalid){
				if(isvalid){
 					var refNo = $('#refNo').text();
					$.ajax({
						type: 'POST',
						url: 'post.php',
						data: {'refNo':refNo,'submit':'1','cancel':true},
						success: function(data){
							location.reload();
						}
					});
					$('#validatebtn').val('CANCELLED');
				}else{
					alert('Invalid password');
					$('#validatepw').focus();
					$('#validatebtn').val('TRY AGAIN');
				}
			}
		});
	});

	if($('#package').length>0){
		$('#package li').click(function(){

			$.post(shop_url,{'clear':true});
			$('input[name=deliBox]:eq(0)').click();
			var pid=$(this).attr('data-code');
			var payamt=$(this).attr('data-price');
			var paynot='';

			switch(pid){
				case '18143':
				case '18144':paynot='purchased Resellers Package';break;
				case '18155':paynot='purchased 500PV Package. Shipping Fee to be confirmed.';break;
				case '18159':
				case '18164':paynot='purchased PCM Package. Shipping Fee to be confirmed.';
					$('input[name=deliBox]:eq(3)').click();
					$('input[name=deliAddto]').val(paynot);
					break;
				default:break;
			}

			$('#package li').children('p').css({'background':'#000'});
			$(this).children('p').css({'background':'blue'});
			$('#totals input').attr('data-order',1);
			$('#details').hide();
			$('.procfee').hide();
			$('.bad').remove();

			$('.payamt').text(fmtCurr(payamt));
			$('textarea[name=payNote]').text(paynot);
			$('input[name=payAmt]').attr('rel',payamt);
			$('input[name=payAmt]').val(fmtCurr(parseFloat(payamt)+parseFloat($('.delfee').text())));

			$.post(shop_url,{'pid':pid,'qty':1,'loc':'','olg':pid});
		});
	}

	if($('#datePicker').length>0){
		$('#datePicker').Zebra_DatePicker({format:'m.d.Y',show_icon:true,offset:[-190,40]});
	}
});

function shopUpdate(pid,qty,loc){
	var shp=$('input[rel='+pid+']');
	$.ajax({
		type: 'POST',
		url: shop_url,
		data: {'pid':pid,'qty':qty,'loc':loc},
		success: function(data){
			$('#cart ul').empty().append(data);
			sumTotal();
			shp.val(parseFloat(shp.val())+(qty));
			if($('#cart ul li').length==0){
				$('#totals a').slideUp();
			}else{
				$('#totals a').slideDown();
			}
		}
	});
}

function sumTotal(){
	var tam=0;
	var tpv=0;
	$('.shopqty').each(function(){
		var sqty=$(this).text();
		sqty=parseFloat(sqty.replace(/\,/g,''),10);
		tam+=parseFloat($(this).next().attr('rel'))*sqty;
		tpv+=parseFloat($(this).attr('rel'))*sqty;
	});
	$('#tam').text(fmtCurr(tam));
	$('#tpv').text(fmtCurr(tpv));
}

function fmtCurr(num){
	num=num.toString().replace(/\$|\,/g,'');
	if(isNaN(num))
	num="0";
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
