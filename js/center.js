$(document).ready(function() {
	var center_type = $('#user').attr('data-login');
	$('.txt').live('click',function() {
		$(this).select();
	});

	if($('#user').length>0) {
		$('body').append('<ul id="shortnav"><li><a href="/'+center_type+'/" title="Home" alt="Home"></a></li></ul>');
	}

	if($('.distri,.remit').length>0) {
		$('body').append('<div id="backtotop"><a href="#"></a></div>');
		$('#backtotop').stop().animate({opacity:.5});

		if($('#backtotop').length>0) {
			$('#backtotop').hover(
				function() { $(this).stop().animate({opacity:1}); },
				function() { $(this).stop().animate({opacity:.5}); }
			);
		}
	}

	if($('.distri').length>0) {
		var go = 0;

		$('input[name=gc_dsscan]').live('click',function() {
			if(testDistri('dsdid',12)) return false;
		});

		$('form').submit(function() {
			if(testDistri('dsdid',12)) return false;
		});

		$('#cmod').live('click',function() {
			$('input[name=mod]').val(1);
			$('.bad').text('');
		});
	}

	if($('.setup').length>0) {
		$('#updatedelivery').submit(function() {
			$('form span').text('');
			var x = confirm('Update Settings?');
			if(x) {
				$.ajax({
					type: 'POST',
					url: 'post.php',
					data: $(this).serialize()+'&submit=_ _',
					success: function(n) {
						if(n==1) {$('#updatedelivery span').addClass('good').text('Update Successful');}
						else{$('#updatedelivery span').addClass('bad').text('Failed');}
					}
				});
			}return false;
		});

		$('#changepass').submit(function() {
			var stat = 'bad';
			var msg  = 'Failed.';
			var csp  = $('#changepass span');
			var pascur = $('input[name=acpascur]').val();
			var pasold = $('input[name=acpasold]').val();
			var pasnew = $('input[name=acpasnew]').val();

			$(this).find('.txt').css({borderColor:'#777'});
			csp.removeClass().addClass(stat).text('');

			if( pascur=='' || pasold=='' || pasnew=='' ) {
				$(this).find('.txt').css({borderColor:'#f00'});
				csp.text(msg+' Required field empty.');

			}else if(pasold.length<8||pasnew.length<8) {
				$(this).find('input[name=acpasold],input[name=acpasnew]').css({borderColor:'#f00'});
				csp.text(msg+' Minimum of 8 characters.');

			}else if(pasold!=pasnew) {
				$(this).find('input[name=acpasold],input[name=acpasnew]').css({borderColor:'#f00'});
				csp.text(msg+' Old and New Passwords do not match.');

			} else {
				var x = confirm('Change Password?');

				if(x) {
					$.ajax({
						type: 'POST',
						url: 'post.php',
						data: $(this).serialize()+'&submit=@ @',
						success: function(n) {
							switch(n.substr(0,1)) {
								case '1':
									msg += n.substr(1);
									break;

								default:
									stat = 'good';
									msg=n.substr(1);
							}

							csp.removeClass().addClass(stat).text(msg);
						}
					});
				}
			}

			return false;
		});
	}

	if($('.remit').length>0) {
		var pid = '';
		var txt = $('.batclick').text();

		$('.voidorder').live('click',function() {
			var x = confirm('Are you sure you want to VOID this transaction?');
			if(!x) return false;
		});

		$('.batclick').toggle(
			function() { toggleBatch($(this).attr('rel'),this,0,'');$('#datePicker').val(setDate()); },
			function() { toggleBatch($(this).attr('rel'),this,1,txt); }
		);

		$('.chkall').live('click',function() {
			$('.chkbox').prop('checked',$(this).is(':checked'));
			pid = checkBoxes();
		});

		$('input[name=gc_batch]').live('click',function() {
			pid = checkBoxes();
		});

		$('.delidet').toggle(
			function() { $(this).text('CLOSE').next().show();},
			function() { $(this).text('DELIVERY DETAILS').next().hide(); }
		);

		$('select').live('change',function() {
			$(this).siblings('form').children().val('').hide();
			switch($(this).val()) {
				case 'Cheque':
				case 'Fund Transfer':
					$(this).siblings('form').children('.txt').show();
					break;

				case 'Credit Card':
					$(this).siblings('form').children('input[type=file]').show();
					break;
			}
		});

		$('.multi').live({
			mouseenter:function() { $(this).append('<span title="Remove Entry"></span>'); },
			mouseleave:function() { $(this).find('span').remove(); }
		});

		$('.multi span').live('click',function() {
			if($('.multi').length>1) {
				var x = confirm('Remove this entry?');
				if(x) $(this).parent().remove();
				sumRemit();
			}
		});

		$('.multi input[type=text]').live('blur',function() {
			sumRemit();
			var idx = $(this).parent('.multi').index();
			var tam = parseFloat($('#tam').text().replace(/\,/g,''),10);
			if($(this).hasClass('rt')) $(this).val(fmtCurr($(this).val()));
			if(idx===$('.multi').length&&tam>0&&parseInt($(this).val())>0) {
				var d = $(this).parent('li');
				d.after($(d).clone());
				$(this).parent().next().find('.rt').val('0.00').end().find('form').children().hide();
			}
		});

		$('.remittance').live('click',function() {
			$('.'+ $(this).attr('rel')).toggle();
			// return false;
		});

		$('.batrepl a').live('click',function() {
			var msg = '';
			cid = $(this).attr('rel');

			$('#'+cid+' .txt').each(function() {
				msg += $(this).attr('rel') +'|'+ $(this).val() +'~';
			});

			var x = confirm( ($(this).text()=='CONFIRM' ? 'Confirm' : 'Request') +' replenishment?');
			if(x) {
				$.ajax({
					type: 'POST',
					url: 'batch.php',
					data: {
						'bid':cid,
						'rdat':msg,
						'submit':'! !'},
					beforeSend: function() {
						$('.batrepl a').text('REQUESTING...')},
					success: function(data) {
						$('.batrepl a').text('REPLENISH');
						alert('DONE');
						location.reload();}
				});
			}

			return false;
		});

		$('.batrepl .txt').live('blur',function() {
			if(parseInt($(this).val())>parseInt($(this).parent().prev().text())) {
				errMsg('Remaining quantity exceeded.');
				$(this).val(0).focus().select();
			}

			return false;
		});

		$('#batconf').live('click',function() {
			if(testTrans(pid)) updateStat(pid);
		});

		$('#batpaym .dobatch').live('click',function() {
			var amt = $('#tam').text();
			if($('#batpaym').attr('rel')=='') {
				errMsg('No transaction selected.');

			}else if(parseFloat(amt.replace(/\,/g,''),10)>0) {
				errMsg('Insufficient payment (P'+amt+' remaining).');

			} else {
				var x = confirm('Proceed with Batch Payment?');
				if(x) {
					$.ajax({
						type: 'POST',
						url: 'batch.php',
						data: {
							'rmids':$('#batpaym').attr('rel'),
							'rdata':remit(),
							'rdate':$('#datePicker').val(),
							'submit':'_ _'},
						beforeSend: function() {
							$('.dobatch').text('SUBMITTING...')},
						success: function(data) {
							$('.dobatch').text('SUBMIT');
							if(data) alert('Batch Payment successful.');
							window.location = '/'+ center_type +'/remit/?tab=replenish';
						}
					});
				}
			}

			return false;
		});
	}

	if($('.orders').length>0) {
		var kartht = 82;
		olddist    = '';
		oldname    = '';
		oldsearch  = '';

		updateProducts();
		updateOrder(null,0);
		loadCart();

		$('#gos_cart').animate({height:kartht});
		$(window).resize(function() {
			if($('#gos_tab').text()=='MORE ORDERS') $('#gos_cart').stop().animate({height:$(window).height()-2});
		});

		$('#filter input').click(function() {
			if($(this).attr('id')=='checkall') $('#filter input').prop('checked',$(this).is(':checked'));
			updateProducts();
		});

		$('#search input').live('blur',function() {
			if(oldsearch!='') updateProducts();
			oldsearch=$(this).val();
		});

		$('#search input').live('keypress',function(e) {
			if(e.keyCode==13) updateProducts();
		});

		$('#search select').change(function() {
			sortProducts();
		});

		$('input[name=oqty]').live('keyup',function() {
			var max = 12;

			if($('.oqty[value!="0"]').length>max) {
				errMsg('Maximum of '+max+' Items per transaction only.');
				$(this).val(0);

			} else {
				var num = $(this).val();
				if(!isNum(num)) {
					$(this).val(0);
					num = 0;
				}

				updateOrder($(this).attr('rel'),num);
				sumTotal();
			}
		});

		$('#csh,#crd,#chq,#fnd').live('blur',function() {
			var conf = $('input[name=gc_conf]');
			if(conf.val()=='') conf.val('[CASH] [CHK] [CC] [FUND]');
			sumChange();
		});

		$('input[name=gc_dist]').live('focus',function() {
			olddist = $(this).val();
		});

		$('input[name=gc_dist]').live('blur',function() {
			var str = $.trim($(this).val());
			if( str!='' && olddist!=str ) {
				$('#msg').text('Searching... Please wait...');
				lockProp(1);
				$('#uldist input').prop('disabled',false);
				findDistri(str);
				lockProp(0);
			}
		});

		$('input[name=gc_name]').live('blur',function() {
			var str = $.trim($(this).val());
			oldname = $.trim($(this).attr('rel'));
			if( str!='' && oldname!=str ) {
				$('#msg').text('Searching... Please wait...');
				lockProp(1);
				$('#uldist input').prop('disabled',false);
				lookupName(str);
				lockProp(0);
			}
		});

		$('#distrilist li').live('click',function() {
			$('#msg').text('Loading... Please wait...');
			lockProp(1);
			$('#uldist input').prop('disabled',false);
			findDistri($(this).find('.s5').text());
			$('#distrilist').remove();
			lockProp(0);
		});

		$('#distrilist p').live('click',function() {
			$('#distrilist').remove();
			lockProp(0);
		});

		$('#gos_tab').live('click',function() {
			if($(this).text()=='CHECKOUT') {
				if(testOrders()) {
					$('#gos_cart').css({height:$(window).height()-2});
					lockProp(1);
					$('#gos_cart input').prop('disabled',false);
					$('#datePicker').val(setDate());
					$(this).text('MORE ORDERS');
					sumChange();
				} else {
					alert('You forgot your orders!');return false;
				}

			} else {
				$('#gos_cart').css({height:kartht});
				lockProp(0);
				$('#gos_cart input').prop('disabled',true);
				$(this).text('CHECKOUT');
				$('#distrilist').remove();
			}
		});

		$('#gos_clear').live('click',function() {
			var x=confirm('Clear order form?');

			if(x) {
				$.ajax({
					type: 'POST',
					url: 'shop.php',
					data: {'clear':true},
					beforeSend: function() {
						$('#gos_clear').text('CLEARING...');
						lockProp(1); },
					success: function() {
						clearInput();
						$('#gos_clear').text('CLEAR');
						lockProp(0);
					}
				});
			}
		});

		$('#gos_submit').live('click',function() {
			if(testSubmit()) {
			} else {
				var msg = '';
				var gdate= $('input[name=gc_date]').val();
				var cash = $('#csh').val().replace(/\,/g,'');
				var chek = $('#chq').val().replace(/\,/g,'');
				var card = $('#crd').val().replace(/\,/g,'');
				var fund = $('#fnd').val().replace(/\,/g,'');
				var paid = $('#cng').text();
				var conf = $('input[name=gc_conf]').val();

				$.ajax({
					type: 'POST',
					url: 'post.php',
					beforeSend: function() {
						$('#gos_submit').text('SUBMITTING...');
						lockProp(1); },
						data: {
							'dsdid': $('input[name=gc_dist]').attr('rel')+$('input[name=gc_dist]').val(),
							'dsnam': $('input[name=gc_name]').val(),
							'dscon': $('input[name=gc_cont]').val(),
							'dstin': $('input[name=gc_ctin]').val(),
							'paycash': cash,
							'paychek': chek,
							'paycard': card,
							'payfund': fund,
							'paydate': gdate.replace(/\./g,''),
							'payconf': conf,
							'paystat': 0,
							'replen': 0,
							'status': 0,
							'submit': '_ _'
						},
					success: function(data) {
						lockProp(0);
						$('#gos_submit').text('SUBMIT');
						if( data!='' && clearInput() ) {
							var rfr=window.location.href;
							rfr = rfr.replace(/orders/g,'remit');
							window.location = rfr+'?p=remit&do=1&prt=1&i='+data;
							return false;

						} else{
							alert('Submission failed');
						}

						location.reload();
					}
				});
			}
		});
	}

	if($('#datePicker').length>0) {
		$('#datePicker').Zebra_DatePicker({format:'Y.m.d',direction:false,first_day_of_week:0,show_icon:false,zero_pad:true,offset:[-495,30]});
	}

	if($('.monyr').length>0) {
		var tab=getUrlVars()['tab'];
		tab = tab!=null ? 'tab='+getUrlVars()['tab']+'&' :'';

		$('select.monyr').change(function() {
			document.location=document.location.pathname+'?'+tab+'monyr='+$('select[name=yr]').val()+$('select[name=mo]').val();
		});

		$('input.monyr').click(function() {
			document.location=document.location.pathname+'?'+tab+'monyr=';
		});
	}
});

function changeDaily(idx,v) {
	var rfr = last;
	var w   = rfr.indexOf('&'+v);
	var url = (w<0)?rfr+"&"+v+"="+idx:rfr.substring(0,w)+"&"+v+"="+idx;

	window.location=url;
}

function remit(e) {
	d   = 0;
	n   = 4;
	str = '';

	$('.multi .rt').each(function() {
		if($(this).val()<=0) $(this).parent().remove();
	});

	var tags=$('.multi').find('select,input').map(function() {
		return ($(this).val().indexOf('fakepath')>0) ? '1' : $(this).val().replace(/\,/g,'');
	}).get().join('|');

	$.each(tags,function(i,val) {
		if(val=='|') d++;
		str += (d<n) ? val :'~';
		d = (d<n) ? d++ :0;
	});

	return str;
}

function sumRemit() {
	var tam = 0;
	var old = $('#tam').attr('rel');
	old = parseFloat(old.replace(/\,/g,''),10);

	$('.multi input.rt').each(function() {
		tam += parseFloat($(this).val().replace(/\,/g,''),10);
	});

	$('#tam').text(fmtCurr(parseFloat(old)-parseFloat(tam)));
}

function checkBoxes() {
	tdue = 0;
	pid = '';

	$('input[name=gc_batch]').each(function() {
		if($(this).is(':checked')) {
			pid+=$(this).next().text()+'|';

			if($('#batpaym').length>0) {
				pdue  = $(this).parent().siblings('.tdue').text();
				tdue += parseFloat(pdue.replace(/\,/g,''),10);
			}
		}
	});

	if($('#batpaym').length>0) {
		$('#batpaym').attr('rel',pid.substring(0,pid.length-1));
		$('#tam').text(fmtCurr(tdue)).attr('rel',tdue);
		sumRemit();
	}

	return pid;
}

function toggleBatch(togBatch,togButton,to,txt) {
	if(to==0) {
		$('#bat'+togBatch).show();
		$('input[name=gc_batch]').show();
		$('.list .chkbox').show();
		$(togButton).text('CANCEL');

	} else {
		$('#bat'+togBatch).hide();
		$('input[name=gc_batch]').hide();
		$('.list .chkbox').hide();
		$(togButton).text(txt);
	}
}

function loadCart() {
	var kart  = '<ul id="ulkart"><li><span class="gos_totals nobor">Total Due</span><span id="tam" class="gos_totals">0.00</span>';
		kart += '<span class="gos_totals nobor">Total PV</span><span id="tpv" class="gos_totals">0.00</span><span id="gos_tab" class="gos_totals">CHECKOUT</span></li></ul>';

	var dist='<ul id="uldist"><li><label class="blue gos_totals nobor">Distributor Details</label></li><li><label id="msg" class="good ital"></label></li>';
		dist += '<li><span class="gos_totals nobor">Distributor ID</span><input type="text" class="txt gos_totals" name="gc_dist" rel="" maxlength=12 /></li>';
		dist += '<li><span class="gos_totals nobor">Name</span><input type="text" class="txt gos_totals" name="gc_name" /></li>';
		dist += '<li><span class="gos_totals nobor">Contact #</span><input type="text" class="txt gos_totals" name="gc_cont" /></li>';
		dist += '<li><span class="gos_totals nobor">TIN</span><input type="text" class="txt gos_totals" name="gc_ctin" maxlength=12 /><div class="clear"></div></li></ul>';

	var paym  = '<ul id="ulpaym"><li><label class="blue gos_totals nobor">Payment Details</label><span class="gos_totals nobor"></span></li>';
		paym += '<li><span class="gos_totals nobor">Date Paid</span><input type="text" class="txt gos_totals lt" id="datePicker" name="gc_date" /></li>';
		paym += '<li><span class="gos_totals nobor">Cash</span><input type="text" id="csh" class="txt gos_totals" name="gc_cash" value="0.00" /></li>';
		paym += '<li><span class="gos_totals nobor">Cheque</span><input type="text" id="chq" class="txt gos_totals" name="gc_chek" value="0.00" /></li>';
		paym += '<li><span class="gos_totals nobor">Card</span><input type="text" id="crd" class="txt gos_totals" name="gc_card" value="0.00" /></li>';
		paym += '<li><span class="gos_totals nobor">Fund Transfer</span><input type="text" id="fnd" class="txt gos_totals" name="gc_fund" value="0.00" /></li>';
		paym += '<li><span class="gos_totals nobor">* NOTE</span><input type="text" class="txt gos_totals" name="gc_conf" rel="" /></li>';
		paym += '<li><span class="gos_totals nobor">Change</span><span id="cng" class="txt gos_totals">0.00</span><div class="clear"></div></li></ul>';

	var subm  = '<ul id="ulsubm"><li><span id="gos_submit" class="gos_totals">SUBMIT</span><span id="gos_clear" class="gos_totals">CANCEL</span><span class="smaller">';
		subm += '<span class="smaller">* For CHEQUE PAYMENTS, replace [CHK] with Check#, and allow THREE (3) DAYS for CLEARANCE</span><br />';
		subm += '<span class="smaller">** For FUND TRANSFERS, replace [FUND] with DATE and ACCOUNT NAME of customer</span><br />';
		subm += '<span class="smaller">** For DEBIT/CREDIT CARD PAYMENTS, please scan payment slip</span>';
		subm += '</span></li></ul>';

	var html = '<div id="gos_cart">'+ kart + dist + paym +subm +'</div>';

	if($('#gos_cart').length==0) { $('body').append(html);}
}

function findDistri(id) {
	$.ajax({
		type: 'POST',
		url: 'updatedistri.php',
		data: {'find':id,'submit':'_ _'},
		success: function(data) {
			var dat=data.split('|');
			$('input[name=gc_dist]').attr('rel',(dat[0]=='NOT FOUND')?'*':'');
			$('input[name=gc_dist]').val(id);
			$('input[name=gc_name]').val(dat[0]);
			$('input[name=gc_cont]').val(dat[1]);
			$('input[name=gc_ctin]').val(dat[2]);
			$('input[name=gc_name]').attr('rel',dat[0]);
			$('#msg').text('');
		}
	});
}

function lookupName(str) {
	$.ajax({
		type: 'POST',
		url: 'updatedistri.php',
		data: {'find':str,'submit':'@@@'},
		success: function(data) {
			$('#distrilist').remove();
			$('body').append('<div id="distrilist"><br><span class="blue">SEARCH DISTRIBUTOR</span><p>CLOSE</p><ul>'+data+'</ul></div>');
			$('#msg').text('');
		}
	});
}

function setDate() {
	var nn = new Date();
	var dd = nn.getDate();
	var mm = nn.getMonth()+1;
	var yy = nn.getFullYear();

	if(dd<10) { dd = '0'+dd; }
	if(mm<10) { mm = '0'+mm; }

	return yy+'.'+mm+'.'+dd;
}

function updateStat(pid) {
	$.ajax({
		type: 'POST',
		url: 'batch.php',
		data: { 'payid':pid,'submit':'@bat'+$('.batclick').attr('rel') },
		beforeSend: function() {
			$('.dobatch').text('CONFIRMING...')},
		success: function(data) {
			location.reload();}
	});

	return false;
}

function updateOrder(id,qy) {
	$.ajax({
		type: 'POST',
		url: 'shop.php',
		data: {'id':id,'qy':qy,'submit':'pabili'},
		success: function(data) {
			updateEditData();
			$('#center_orders ul').empty().html(data);
			updateTotals();
		}
	});
}

function updateTotals() {
	$.ajax({
		type: 'POST',
		url: 'updateorders.php',
		data: {'totals':true},
		success: function(data) {
			var dat = data.split('|');
			$('#tam').text(fmtCurr(dat[0]));
			$('#tpv').text(fmtCurr(dat[1]));
			$('#num_order').text(dat[2]);
			sumChange();
			lockProp(0);
		}
	});
}

function updateEditData() {
	if($('#editorder').length>0&&$('#editorder').val()!='') {
		var eo = $('#editorder').val().split('|');
		$('input[name=gc_dist]').val(eo[0]);
		$('input[name=gc_name]').val(eo[1]);
		$('input[name=gc_cont]').val(eo[2]);
		$('input[name=gc_ctin]').val(eo[3]);
		$('input[name=gc_date]').val(eo[8]);
		$('#csh').val(fmtCurr(eo[4]));
		$('#crd').val(fmtCurr(eo[5]));
		$('#chq').val(fmtCurr(eo[6]));
		$('#fnd').val(fmtCurr(eo[7]));
		$('input[name=gc_conf]').val(eo[9]);
	}
}

function updateProducts() {
	var filter = '';
	var find   = $('#search input').val();
	var sort   = $('#search select').val();

	$("#filter input[type=checkbox]").each(function() {
		filter += $(this).is(':checked') ? '1' : '0';
	});

	$.ajax({
		type: 'POST',
		url: 'updateproducts.php',
		data: {'find':find,'filter':filter,'sort':sort},
		beforeSend: function() {
			$('#loadgif').hide();
			lockProp(1);
			$('#center_products ul,#gos_found').empty();
			$('#center_products ul').stop().after('<img src="../../src/loading.gif" id="loadgif" alt="" style="height:26px;width:128px" />')},
		success: function(data) {
			var dat=data.split('|');
			$('#loadgif').hide();
			lockProp(0);
			$('#center_products ul,#gos_found').empty();
			$('#center_products ul').stop().html(dat[0]);
			$('#gos_found').stop().text(dat[1]);
		}
	});
}

function sortProducts() {
	var sort = $('#search select').val();
	$.ajax({
		type: 'POST',
		url: 'updateproducts.php',
		data: {
			'sortonly':true,
			'sortorder':sort
		},
		beforeSend: function() {
			$('#loadgif').hide();
			lockProp(1);
			$('#center_products ul').stop().empty().after('<img src="../../src/loading.gif" id="loadgif" alt="" style="height:26px;width:128px" />')},
		success: function(data) {
			$('#loadgif').hide();
			lockProp(0);
			$('#center_products ul').stop().empty().html(data);
		}
	});
}

function lockProp(stat) {
	$('input,select').prop('disabled',stat);
}

function testTrans(pid) {
	if(pid=='') {
		errMsg('No transaction selected.');
		$('.batclick').click();
		return false;

	} else return true;
}

function testSubmit() {
	var msg  = '';
	var x    = 1;
	var fail = 0;
	var isbat= ($('#batpaym').length>0);
	var cash = parseFloat($('#csh').val().replace(/\,/g,''),10);
	var card = parseFloat($('#crd').val().replace(/\,/g,''),10);
	var chek = parseFloat($('#chq').val().replace(/\,/g,''),10);
	var fund = parseFloat($('#fnd').val().replace(/\,/g,''),10);
	var tdue = parseFloat($('#tam').text().replace(/\,/g,''),10);

	$('#gos_cart input.txt,#batpaym input.txt').css({borderColor:'#777'});

	$('#uldist input.txt,#batpaym input.txt').each(function() {
		if(($.trim($(this).val())==''||$.trim($(this).val())=='NOT FOUND')&&$(this).attr('name')!='gc_conf') {
			fail = 1;
			$(this).css({borderColor:'#f00'});
		}
	});

	if(fail) {
		msg = 'Items in RED are REQUIRED.';

	} else if(isbat&&tdue==0) {
		fail = 1;
		msg  = 'No transaction selected.';

	} else if(card+cash+chek+fund<tdue) {
		msg = 'Total Amount Due is P'+fmtCurr(tdue)+'.';
		fail = 1;
		$('#csh,#crd,#chq,#fnd').css({borderColor:'#f00'});
	}

	if(fail&&x) errMsg(msg);
	return fail;
}

function testOrders() {
	return ($('#center_orders li').length>0);
}

function testDistri(id,d) {
	var fail = 0;
	var str = $('input[name='+id+']').val();

	if($.trim(str)==''||(str.length>d||str.length<d)) {
		errMsg('A VALID ID is required.');
		fail = 1;
	}

	return fail;
}

function errMsg(msg) {
	alert('Unable to continue. '+msg);
}

function clearInput() {
	$('#center_orders ul').empty();
	$('input[name=oqty]').val(0);
	$('#gos_cart input').val('').css({borderColor:'#777'});;
	updateOrder(null,0);
	sumTotal();
	$('#gos_tab').click();
	return true;
}

function sumTotal() {
	var tam = 0;
	var tpv = 0;

	$('input[name=oqty]').each(function() {
		var qty = $(this).val();
		var pid = $(this).attr('rel');
		var wsp = $('#ws'+pid).text();
		var wpv = $('#pv'+pid).text();

		qty = parseFloat(qty.replace(/\,/g,''),10);

		var amt = parseFloat(wsp.replace(/\,/g,''),10)*parseInt(qty);
		var apv = parseFloat(wpv.replace(/\,/g,''),10)*parseInt(qty);

		$('#am' + pid).text(fmtCurr(amt));
		$('#ap' + pid).text(fmtCurr(apv));

		tam += amt;
		tpv += apv;
	});

	$('#tam').text(fmtCurr(tam));
	$('#tpv').text(fmtCurr(tpv));
}

function sumChange() {
	var cng = 0;
	var csh = $('#csh').val();
	var crd = $('#crd').val();
	var chq = $('#chq').val();
	var fnd = $('#fnd').val();
	var due = $('#tam').text();

	csh = parseFloat(csh.replace(/\,/g,''),10);
	crd = parseFloat(crd.replace(/\,/g,''),10);
	chq = parseFloat(chq.replace(/\,/g,''),10);
	fnd = parseFloat(fnd.replace(/\,/g,''),10);
	due = parseFloat(due.replace(/\,/g,''),10);
	cng = (csh+crd+chq+fnd)-due;
	col = (cng<0)?'#f00':'#000';

	$('#csh').val(fmtCurr(csh));
	$('#crd').val(fmtCurr(crd));
	$('#chq').val(fmtCurr(chq));
	$('#fnd').val(fmtCurr(fnd));
	$('#cng').text(fmtCurr(cng)).css({color:col});
}

function isNum(n) {
	return !isNaN(parseFloat(n))&&isFinite(n);
}

function fmtCurr(num) {
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

function getUrlVars() {
	var vars = {};
	var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,
	function(m,key,value) {
		vars[key] = value;
	});
	return vars;
}
