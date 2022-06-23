$(document).ready(function() {
	var url=window.location.pathname;
	var tbl=$('#buttons').attr('rel');
	var amps=url.replace(/apc|orders|data|logis|distriserve/gi,'sub');
	var root=(~amps.indexOf('sub'))?'../':'';
	var apc=url.replace(/apc|orders/gi,'sub');
	var apcroot=(~apc.indexOf('sub'))?'../logistics/':'';
	var ttl=$('#user').attr('data-title');
	if( $('#user').length>0 && $('#user').attr('data-login')=='admin' && $('#user').attr('data-notif')>0 ) initOLNotif();

	$('.removepic').click(function() {
		if(confirm("Delete Image?")) {
			var fl=$(this).siblings('img').attr('src');
			$.post('/admin/update.php','do=&del_file='+fl+'&submit=del_img',function(n) {
				alert("Image deleted");
			});
		}
	});

	$('#filter input').click(function() {
		var filter='';
		var filterloc=getUrlVars()['p'];
		$("input[type=checkbox]").each(function() {
			filter+=$(this).attr('checked')?'1':'0';
		});
		document.location=url+'?p='+filterloc+'&do=0&filter='+filter;
	});

	$('.list li').click(function() {
		if( $(this).hasClass('hdr') ) {
		}else{
			$('.list li').css('background-image','none');
			$(this).css('background','url("../src/o.png") left 5px no-repeat');
		}
	});

	$('#recap,#getrec').submit(function() {
		if(($(this).attr('id')=='recap'&&$('input[id=uprecap]').val()=='')||
		($(this).attr('id')=='getrec'&&$('input[id=printrecap]').val()=='')) {
			alert('Unable to continue. Please select a valid file.');
			return false;
		}
		if($(this).attr('id')=='getrec') {
			$('.recapstat').text('PREPARING RECAP FILE');
		}
	});

	$('input[type=file]').change(function() {
		var file = $(this).val();
		var week = file.substr((file.lastIndexOf('.')-2),2);
		var nexf = $('#uploadrecapsched :selected').text();
		var prec = ($(this).attr('id')!='printrecap')?true:false;
		if($(this).attr('name')=='file_img') $('input[name=img]').val($('input[name=id]').val()+'.'+file.substr((file.lastIndexOf('.')+1)));
		if($(this).attr('name')=='upfile') $('input[name=dlfile]').val($('input[name=id]').val()+'.'+file.substr((file.lastIndexOf('.')+1)));

		if($(this).hasClass('iscsv')) {
			if(file.substr((file.lastIndexOf('.') +1))!='csv') {
				$('.msg').addClass('bad').text('Please select a valid .csv file');
				$(this).val('');
			}
		}

		if($('#uprecap').length>0) {
			if(parseInt(week)<=parseInt($('#lastrecap').val())&&prec) {
				$(this).next().text('File already uploaded. Week '+week);
				$(this).val('');
			}else if(nexf.substr(0,7)!='Week '+week&&prec) {
				$(this).next().text('File not yet scheduled to be uploaded. Week '+file.substr((file.lastIndexOf('.')-2),2));
				$(this).val('');
			}else $(this).next().text('');
		}

		if($('#updb').length>0) {
			if(file.substr((file.lastIndexOf('\\')+1))!=$(this).next().attr('rel')+'.csv') {
				$(this).next().text('Please select a valid data file');
				$(this).val('');
			}else $(this).next().text('');
		}
	});

	if($('select[name=cat]').val()=='') $('select[name=subcat] option[value!=""]').attr('disabled',true);
	$('select[name=cat]').change(function() {
		if($('select[name=cat]').val()=='') {
			$('select[name=subcat] option').attr('disabled',true);
			$('select[name=subcat] option[value=""]').attr('disabled',false);
			$('select[name=subcat] option:eq(0)').attr('selected','selected');
		}else{
			$('select[name=subcat] option').attr('disabled',false);
			$('select[name=subcat] option[rel!="'+ $('select[name=cat]').val() + '"]').attr('disabled',true);
		}
	});

	$('#smps50').change(function() {
		var rfr = window.location.href;
		var pag = rfr.indexOf('?');
		var newurl='';
		if(pag<0) {
			newurl = rfr+"?p=smps50&do="+$('#smps50 :selected').text();
		}else{
			var w = rfr.indexOf('do=');
			newurl = rfr.substring(0,w)+"do="+$('#smps50 :selected').text();
		}window.location = newurl;
	});

	$('select[name=date]').change(function() {
		var rfr = window.location.href;
		var pag = rfr.indexOf('?');
		var newurl='';
		if(pag<0) {
			newurl = rfr+"?p=stocksactual&do=2&i="+$(this).val();
		}else{
			var w = rfr.indexOf('do=');
			newurl = rfr.substring(0,w)+"do=2&i="+$(this).val();
		}window.location = newurl;
	});

	$('input[name=sort]').click(function() {
		var rfr = window.location.href;
		var pag = rfr.indexOf('?');
		var newurl='';
		if(pag<0) {
			newurl = rfr+"?p=ormstp&do=0&srt="+$(this).val();
		}else{
			var w = rfr.indexOf('do=');
			newurl = rfr.substring(0,w)+"do=0&srt="+$(this).val();
		}window.location = newurl;
	});

	$('input[name=olreg_type]').click(function() {
		var rfr = window.location.href;
		var pag = rfr.indexOf('?');
		var newurl='';
		if(pag<0) {
			newurl = rfr+"?p=olreg&do=0&type="+$(this).val();
		}else{
			var w = rfr.indexOf('do=');
			newurl = rfr.substring(0,w)+"do=0&type="+$(this).val();
		}window.location = newurl;
	});

	$('.stat,.stok').live('click',function() {
		var tbl=$('#buttons').attr('rel');
		var stok=$(this).hasClass('stok');
		var status=$(this).text();
		status^=1;
		if(confirm("Change "+((stok)?"Stock ":"")+"Status to "+status+"?")) {
			var selitem=$(this);
			var tbl=tbl;
			var id=$(this).parent().attr('rel');
			var editvar=(stok)?'stock':'status';
			$.ajax({
				type: 'POST',
				url: '/admin/updatestatus.php',
				data: {'tbl':tbl,'id':id,'status':status,'submit':'Submit','editvar':editvar},
				success: function(n) {
					$(selitem).text(n);
				}
			});
		}return false;
	});

	$(".delistat").live('click',function() {
		if($(this).attr('rel')<1&&$(this).attr('rel')>3) exit;
		var msg=getMsg(parseInt($(this).attr('rel')));
		if(confirm(msg)) {
			var selitem= $(this).parent();
			var id	   = $(this).siblings('a').text();
			var status = parseInt($(this).attr('rel'))+1;
			$.ajax({
				type: 'POST',
				url: '../updatebilling.php',
				data: {'id':id,'status':status+'','paystat':'1','submit':'Submit'},
				success: function(n) {
// alert(n);
					$(selitem).hide();
				},error: function(XMLHttpRequest,textStatus,errorThrown) {
					alert("Error: "+errorThrown);
				}
			});
			return false;
		}
	});

	$(".verifypay").live('click',function() {
		if($(this).attr('rel')==0&&$(this).attr('rel')==4) exit;
		var msg=getMsg(parseInt($(this).attr('rel')));
		var id		= $(this).attr('href');
			id		= id.substring(1);
		var status	= parseInt($(this).attr('rel'))+1;
		if(confirm(msg)) {
			$.ajax({
				type: 'POST',
				url: '../../admin/updatebilling.php',
				data: {'id':id,'status':status+'','paystat':'1','submit':'Submit'},
				success: function(n) {
					location.reload();
				}
			});
			return false;
		}
		return false;
	});

	$('.save a').click(function() {
		if(confirm("Save item?")) {
			$.ajax({
				type: 'POST',
				url: 'update.php',
				data: {'table':tbl,'do':2,'submit':'Submit'},
				success: function(n) {
					document.location=window.location.pathname+'?p='+tbl+'&do=0';
				}
			});
			return false;
		}
	});

	$('.del a').click(function() {
		if(confirm("Delete item?")) {
			var url = '';
			var newid = $('input[name=id]').val();
			var newdate	= $('input[name=date]').val();
			if(tbl=='calendar') { url='?p='+tbl+'&do=list&i='+newdate; }
			else url = '?p='+tbl+'&do=0';
			$.ajax({
				type: 'POST',
				url: '/admin/update.php',
				data: {'id':newid,'date':newdate,'tbl':tbl,'do':3,'submit':'Submit'},
				success: function(n) {
					document.location=window.location.pathname+url;
				}
			});
			return true;
		}
	});

	$('.cancel a').click(function() {
		document.location=window.location.pathname+'?p='+tbl+'&do=0';
		return false;
	});

	$('.back a').click(function() {
		history.back();
		return false;
	});

	$('#responsor_form').submit(function() {
		if($('#distname').val()=='') {
			alert('Unable to continue. No match found.');return false;
		}else if($('#dssid').val().length>12&&!isNumber($('#dssid').val())) {
			$('#dssid').css({'border':'#f00 solid 1px'})
			alert('Unable to continue. Invalid Sponsor ID.');return false;
		}else{
			if(!confirm("Are you sure you want to set this Distributor up for Responsoring?")) return false;
		}
	});

	$('').click(function() {
		bgCookie("d_site",bmon+"/"+bdate+"/"+$("#byear").val());
	});

	$(document).on('blur change','#stock_item,#omdid',function() {
		testList($(this).val(),$(this).attr('data-cod'));
	});

	$(document).on('click','#copyMsg',function() {
		$('#copyThis').select();
		document.execCommand('copy');
	});

	$(document).on('click','#noContact',function() {
		if(confirm('Mark as No Contact?')) {
			$.post('../update.php?t='+tbl,'dsdid='+$('#'+tbl).attr('data-dsdid')+'&contact=0&status=0&do=2&submit=Submit',function(n) {
				window.location=url+'?p=newdistri';
			});
		}
	});

	$(document).on('click','#doneDistri',function() {
		if(confirm('Mark as Done?')) {
			$.post('../update.php?t='+tbl,'dsdid='+$('#'+tbl).attr('data-dsdid')+'&contact=0&status=1&do=2&submit=Submit',function(n) {
				window.location=url+'?p=newdistri';
			});
		}
	});

	$(document).on('click','#showalldistri',function() {
		var s=$(this).val();
		s^=1;
		window.location=url+'?p=newdistri&do=0&showalldistri='+s;
	});

	$(document).on('click','#newdistri li ul .copyLine',function() {
		// var s=$(this).prev('span').text();
		$(this).prev('input').select();
		document.execCommand('copy');
	});

	$(document).on('click','#resetfda',function() {
		if(confirm('Truncate FDA List?')) {
			$.post('index.php?p=fda','submit=RESET',function(n) {
				window.location=url+'?p=fda';
			});
		}
	});

	// if($('#main_logo').length>0) {
		// $('#main_logo').html('<img src="../src/dlc_logo.png" alt="Diamond Lifestyle Corporation" />').animate({opacity:0},0).show().animate({opacity:1},1000);
	// }

	if($('#search').length>0) {
		var delay=(function() {
			var tmr=0;
			return function(callback,ms) {
				clearTimeout(tmr);
				tmr=setTimeout(callback,ms);
			};
		})();
		$('#search input').keyup(function() {
			var x=$(this).val();
			delay(function() {
				searchProducts(x);
			},300);
		});
	}

	if($('#panel').length>0) {
		var o={
			buttonList: ['save','bold','italic','underline','left','center','right','justify',
				'ol','ul','fontSize','fontFamily','fontFormat','indent','outdent',
				'image','link','unlink','forecolor','bgcolor','xhtml'],
				iconsPath:('/admin/editor/nicEditIcons.gif')
		};
		var area;
		area = new nicEditor(o).panelInstance('panel');

		$("#addArea").live('click',function() {
			area = new nicEditor(o).panelInstance('panel');
		})

		$("#remArea").live('click',function() {
			area.removeInstance('panel');
		})
	}

	if($('#orderhdr').length>0) {
		$('#orderhdr ul').append('<li>LOADING...</li>');
		$('.orders ul').empty();
		setInterval(function() {
			var stat=$('.orders').attr('rel');
			getData(stat,1,1,'#orderhdr ul');
			getData(stat,1,0,'.orders ul');
		},1000*1);
	}

	if($('#dropprod').length>0) {
		var nid=$('input[name=id]');
		var nam=$('input[name=name]');
		$("#dropprod")[0].selectedIndex=0;
		$('#dropprod').change(function() {
			nid.val('');nam.val('');
			var cod=$(this).val().substring(0,5);
			testList(cod,0);
		});

		$('form').submit(function() {
			if(nid.val()==''||nam.val()=='') {
				alert('Unable to continue. Please fill-in all fields.');
				return false;
			}else if(nam.val()=='Choose product') {
				alert('Unable to continue. Please choose a product.');
				return false;
			}else if(nid.val()=='xxxxx') {
				alert('Unable to continue. Please SET Product Code.');
				nid.select();
				return false;
			}
		});
	}

	if($('.list,#orders,.totop').length>0) {
		$('body').append('<div id="backtotop"><a href="#"></a></div>');
		$('#backtotop').stop().animate({opacity:.5});

		if($('#backtotop').length>0) {
			$('#backtotop').hover(
				function() { $(this).stop().animate({opacity:1}); },
				function() { $(this).stop().animate({opacity:.5}); }
			);
		}
	}

	if($('#distrilookup').length>0) {
		$('#distrilookup').on('submit',function(e) {
			e.preventDefault();
		});

		$('form').on('change','#distid',function() {
			$('#distrilookup input[type=text]').next().empty().text('Searching... Please wait.');
			findDistri($('#distid').val(),getUrlVars()['p']);
		});
	}

	if($('#showall').length>0) {
		$(document).on('click','#showall',function() {
			document.location=url+'?p='+getUrlVars()['p']+'&do=0&all='+($(this).attr('checked')?1:0);
		});
	}

	if($('#resetstat,#responsor_form,#testsponsoring').length>0) {
		$('input[value=Reset]').slideUp();
		$('form').on('blur change click focus keyup','#distid',function() {
			$('#resetstat').text('');
			var id=$('#distid').val();

			$.post('/admin/getdistname.php',
				'id='+id+'&submit=.|.',
				function(n) {
					var m=n.split('|');
					$('#distname').val(m[0]);
					if($('#dssid').val()=='') $('#dssid').val(m[1]);
					if(n!=''&&n.indexOf('not logged-in')==-1) {
						$('#distname,#distid,#dssid').css({'border':'#00539b solid 1px'})
						$('input[value=Reset]').slideDown(100);
					}else{
						$('#distname,#distid').css({'border':'#f00 solid 1px'})
						$('input[value=Reset]').slideUp(100);
					}
			});
		});

		$('input[value=Reset]').click(function() {
			if(!confirm("Are you sure you want to reset this Distributor's password?")) return false;
		});
	}

	if($('#transferstocks').length>0) {
		var isapc=(apcroot=='../logistics/')?true:false;
		if($('#transferstocks ul li').length<1) $('#submitbuttons').slideUp();

		$('#transferstocks').submit(function() {
			var selfr=($('input[name=whfr]').length>0)?$('input[name=whfr]').val():$('select[name=whfr]').val();
			var selto=($('input[name=whto]').length>0)?$('input[name=whto]').val():$('select[name=whto]').val();
			if($('#transferstocks ul li').length<1) {
				alert('Unable to continue. Transfer list empty.');
				return false;
			}else if(selfr==selto&&$('.assembly').length==0) {
				alert('Unable to continue. FROM is the same as TO.');
				return false;
			}else{
				if(!confirm("Submit transfer list?")) return false;
			}
		});

		$('#transfercsv input').on('change',function() {
			if($(this).val()!='') $('#transfercsv').submit();
			return false;
		});

		$('#cleartrans').click(function() {
			if(confirm("Clear items?")) {
				$.ajax({
					type: 'POST',
					url: apcroot+'updatetransfer.php',
					data: {'clear':'clear'},
					success: function() {
						$('#submitbuttons').slideUp();
						$('input[type=text]').val('');
						$('#transferstocks ul').empty();
						$('#stock_item').val('').focus();
						$('#stock_desc,#omnam').text('');
						$('#stock_qty').val(0);
					}
				});
			}location.reload();
		});

		$('#stock_item,#stock_qty').bind('keypress',function(e) {
			var code=(e.keyCode?e.keyCode:e.which);
			if(code==13) {
				var item=$('#stock_item').val();
				var desc=$('#stock_desc').text();
				var qty =$('#stock_qty').val();
				if(item.length<5||desc=='') {
					alert('Unable to continue. Please enter valid 5-digit code.');
					$('#stock_item').focus();
				}else if(!isNumber(qty)) {
					alert('Unable to continue. Please enter numbers between 0-9 only.');
					$('#stock_item').focus();
				}else{
					$.ajax({
						type: 'POST',
						url: apcroot+'updatetransfer.php',
						data: {'item':item,'desc':desc,'qty':qty},
						beforeSend: function() {$('#transferstocks ul').empty()},
						success: function(n) {
							$('#transferstocks ul').append(n);
							$('#stock_item').val('').focus();
							$('#stock_desc').text('');
							$('#stock_qty').val(0);
							$('#submitbuttons').slideDown();
						}
					});
				}
			}
		});
	}

	if($('#bomstp,#ormstp,#realtime').length>0) {
		$('#omdid').focus();
		$('#realtime').submit(function(e) {
			var pret='Unable to continue. ';
			var err=0;
			$('.req').css({'border-color':'#888'});
			$('.req').each(function() {
				if($(this).val()=='') {err=1;
					$(this).css({'border-color':'darkred'});
				}
			});

			if(err) {
				$('#errmsg').empty().text(pret+'Required field empty.');
				err=1;return false;
			}

			if($('#omnam').text()=='NOT FOUND') {
				$('#omdid').css({'border-color':'darkred'});
				if(!confirm('Distributor ID not found. Continue with submission?')) {
					$('#errmsg').empty().text(pret+'Distributor ID not found.');
					err=1;return false;
				}
			}

			$('#errmsg').empty().text('PROCESSING. PLEASE WAIT.');
			$(this).find(':input[type=text]').prop('readonly', true);
		});

		$('.recalc').click(function() {
			$('#recalcmsg').text('Recalculating... Please wait.').css({'padding':'6px'});
			$.post('updateBomstp.php','recalc='+$(this).attr('data-recalc'),function(n) {
				if($('#bomstp').length>0) {
					location.reload();
					$('#recalcmsg').text('Reloading...');
				}
				$('#recalcmsg').text('');
			});
			return false;
		});
	}

	if($('#referral').length>0) {
		var txt=$('input[type=text]');
		txt.select();

		$(txt).blur(function() {
			$(this).select();
		});
	}

	if($('#slots').length>0) {
		$('input[name=dsdid]').on('keyup',function() {
			$('input[name=ref]').val($('#slots').attr('rel')+$(this).val());
		});
	}

	if(url.indexOf('data')>0) {
		$('.home a').click(function() {
			var uri=$(this).attr('href');
			var msg;
			switch(uri) {
				case 'getlevel':msg='do=wkyyyy';break;
				case 'getpv':msg='do=wkyyyy&pv=500[&tpv=0000&area=1]';break;
				case 'getpvgrouped':msg='do=wkyyyy&tpv=0000&pv=5';break;
				case 'getpvpromo':msg='do=wkyyyy&pv=5[&single=1&mo=true] (mmyyyy if mo=true)';break;
				case 'getsignup':msg='do=yyyymm';break;
				case 'getdistrilist':msg='[do=630000000000&coid=1&ups=1&bday=1&cont=1&mail=1&addr=1&setd=1&tin=1&rem=0&ex=]';break;
				case 'getcontacts':msg='[do=0] (empty if contacts, do=1 if different emails only)';break;
				case 'getleaders':msg='do=[<>n][0=0%,1=6%,2=6%,3=8%,4=10%,5=12%,6=12%][&yr=<>=2012]';break;
				case 'getdllog':msg='dlog=1&sort=0 (sort by 1-id, 2-file, 3-time)';uri='';break;
//				case 'getq1promo':msg='do=frtoyyyy&pv=100';break;
				case 'getrank':msg='do=frtoyyyy&set=[ppv|npv|tamt|pov]';break;
				case 'getweekly':msg='do=frtoyyyy';break;
				case 'getpsconfirm':msg='id=630000000000&yr=9999&wk=99';break;
				default: msg='No params required';break;
			}
			var ans=prompt(msg,msg);
			if(ans!=null) document.location=uri+'?'+ans;
			return false;
		});
	}

	$(document).on('click','#close',function(e) {
		if($('#modal').length>0) $('#modal').animate({'left':'-200px'},100,function() {this.remove()});
		document.title=ttl;
		e.preventDefault();
	});

function getMsg(c_no) {
	switch(c_no) {
		case 1:msg="Verify Payment and Set Status to Processing?";break;
		case 2:msg="Prepare Items and Set Status to Outgoing?";break;
		case 3:msg="Verify Delivery and Set Status to Delivered?";break;
	}return msg;
}

function testList(cod,wat) {
	var nid=$('input[name=id]');
	var nam=$('input[name=name]');
	$.ajax({
		type:'POST',
		url: apcroot+'testlist.php',
		beforeSend: function() {$('#omnam').empty().html('Loading... Please wait.');},
		data:{'admin':true,'do':wat,'id':cod,'submit':'.|.'},
		success:function(n) {
			var p=JSON.parse(n);
			switch(wat) {
				case '0':
					if(n>0) {
						alert('Unable to continue. Item already exists.');
					}else{
						nid.val((cod!='')?cod:'xxxxx');
						nam.val($('#dropprod option:selected').html());
					}
					break;
				case '1':
					$('#stock_desc').empty().text(p[0]);
					$('#stock_cost').empty().text(p[1]);
					$('#stock_amount').empty().text(parseFloat($('#stock_qty').val())*p[1]);
					break;
				case '2':
					$('#omnam').empty().text(p[0]);
					break;
			}
		}
	});
}

function initOLNotif() {
	setTimeout(initOLNotif,1000);
	document.title=ttl;
	$.post('/admin/updatestatus.php','submit=online',function(n) {
		if(n) {
			$('#popup').empty().append(n);
			$('#overlay').css({'height':$('#modal').height()+20});
			$('#modal').animate({'left':0},200);
			document.title='New Online Entry';
		}
	});
}

function findDistri(id,page) {
	$.ajax({
		type: 'POST',
		url: '/admin/updatedistri.php',
		data: {'find':id,'page':page},
		beforeSend: function() {$('#distrilookup input[type=text]').next().empty().text('Updating...');},
		success: function(n) {
			$('#distri').remove();
			$('#distrilookup').append(n);
			$('#distrilookup input[type=text]').next().empty().text('** dist.id, name');
		}
	});
}

function searchProducts(x) {
	$.ajax({
		type: 'POST',
		url: '/admin/updateproducts.php',
		data: {'find':x},
		beforeSend: function() {$('#products').empty().html('<li>Loading... Please wait.</li>');},
		success: function(n) {
			$('#products').empty().html(n);
		}
	});
}

function getData(stat,admin,stathd,ul) {
	$.ajax({
		type: 'POST',
		url: '/admin/updateorders.php',
		data: {'admin':admin,'statheader':stathd,'stat':stat},
		success: function(n) {
			$(ul).empty().append(n);
		}
	});
}

function isNumber(n) {
	return !isNaN(parseFloat(n))&&isFinite(n);
}

function getUrlVars() {
	var vars = {};
	var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,
	function(m,key,value) {
		vars[key] = value;
	});
	return vars;
}

function setCookie(c_name,value,expiredays) {
	var exdate=new Date()
	exdate.setDate(exdate.getDate()+expiredays)
	document.cookie=c_name+ "=" +escape(value)+((expiredays==null) ? "" : ";expires="+exdate.toGMTString())
}

function bgCookie(c_name,c_val) {
	setCookie(c_name,c_val,1)
}
});
