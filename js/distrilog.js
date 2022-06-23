$(document).ready(function() {
	$('body').append('<div id="backtotop"><a href="#"></a></div>');
	$('#backtotop').stop().animate({opacity:.5});

	if($('#backtotop').length>0) {
		$('#backtotop').hover(
			function() { $(this).stop().animate({opacity:1}); },
			function() { $(this).stop().animate({opacity:.5}); }
		);
	}

	if($('.updn').length>0) {
		$(document).on('click','.updn',function() {
			var updn = $(this);
			var rel  = updn.parent().attr('rel');

			if(updn.text()=='+') {
				updn.text('-');
				$.post('../distrilog/updatelist.php',{'collapse':rel,'tab':$('#tab').attr('rel')})
				  .done(function(n) {
					  if(n!='') updn.parent().after('<li class="fff"><ul>'+n+'</ul></li>');
				  });

			}else updn.text('+').parent().next('li.fff').remove();

			return false;
		});
	}

	if($('.lookup').length>0) {
		$('.lookup').click(function() {
			var html = '<div class="lookup_data">';
			html += '<p><strong>'+$(this).text()+'</strong> <input type="text" id="searchstring" class="s5" /> <input type="button" id="searchdown" class="s2" value="SEARCH" /> <span class="smaller">Search (partial/full): Distributor ID, First Name, Last Name, Middle Name</span></p><table id="searchresults"></table>';
			html += '<a href="#" id="download" class="lookup_exit">Close</a></div>';

			$('body').append(html);
			$('.lookup_data').show(200);
			$('#searchstring').focus();
		});

		$('#searchstring').live('keypress',function(e) {
			if(e.keyCode==13) {
				findDistri();
			}
		});

		$('#searchdown').live('click',function() {
			findDistri();
		});

		$('.lookup_exit').live('click',function() {
			$('.lookup_data').hide(200).empty();
		});
	}

	if($('#recapsched').length>0) {
		var recap = $('#recapsched');

		recap.change(function() {
			var rfr = window.location.href;
			var pag = rfr.indexOf('?');
			var newurl = '';

			if(pag<0) {
				newurl = rfr+"?w="+$(this).val();

			} else {
				var w = rfr.indexOf('?w');
				newurl = rfr.substring(0,w)+"?w="+$(this).val();
			}

			window.location = newurl;
		});
	}

	$('#dist_level').change(function() {
		$('.loading').css({'display':'block'});
		$('#data_list').empty().html('<li><span class="s0"></span>Loading... Please wait.</li>');

		var rfr = window.location.href;
		rfr = rfr.replace(/#/g,'');
		var hsh = rfr.indexOf('#head');
		if(hsh<0) {
		} else {
			rfr = rfr.substring(0,rfr.indexOf('#head'))
		}

		var pag = rfr.indexOf('?');
		if(pag<0) {
			newurl = rfr+"?lvl="+$(this).val();

		} else {
			var lvl = rfr.indexOf('?lvl');
			if(lvl<0) {
				if(rfr.indexOf('&lvl')<0) {
					newurl = rfr+"&lvl="+$(this).val();
				} else {
					newurl = rfr.substring(0,rfr.indexOf('&lvl'))+"&lvl="+$(this).val();
				}

			} else {
				newurl = rfr.substring(0,lvl)+"?lvl="+$(this).val();
			}
		}

		
		window.location = newurl;
	});

	$('#mon_year').change(function() {
		$('.loading').css({'display':'block'});
		$('#data_list').slideUp(300);

		var rfr = window.location.href;
		var pag = rfr.indexOf('?');

		if(pag<0) {
			newurl = rfr+"?monyr="+$(this).val();

		} else {
			var monyr = rfr.indexOf('?monyr');
			if(monyr<0) {
				if(rfr.indexOf('&monyr')<0) {
					newurl = rfr+"&monyr="+$(this).val();

				} else {
					newurl = rfr.substring(0,rfr.indexOf('&monyr'))+"&monyr="+$(this).val();
				}

			} else {
				newurl = rfr.substring(0,monyr)+"?monyr="+$(this).val();
			}
		}

		window.location = newurl;
	});

	$(document).on('click','.reflink',function() {
		var $tmp = $('<input>');
		$('body').append($tmp);
		$tmp.val($(this).attr('rel')).select();
		document.execCommand('copy');
		$tmp.remove();
	});
});

function findDistri() {
	var src = $('#searchstring').val();
	var res = $('#searchresults');
	res.empty().html('Searching... Please wait...');

	$.post('../distrilog/updatelist.php',{'find':src})
	  .done(function(n) {
		  res.empty().html(n);
	  });
}
