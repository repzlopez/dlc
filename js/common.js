$(document).ready(function() {
	if($('#featured').length>0) {
		$("#featured > ul").tabs({fx:{opacity:"toggle"}}).tabs("rotate",6000,true);
	}

	if($('.prod_cat').length>0) {
		loadProd('',getUrlVars()['p']);
		$('#search input').keyup(function() {
			loadProd($(this).val(),getUrlVars()['p']);
		});
	}

	if($('.newsletters').length>0) {
		$('.newsletters li a').hover(
			function() { $(this).children('div').stop().animate({opacity:.2}); },
			function() { $(this).children('div').stop().animate({opacity:1}); }
		);
	}

	if($('.calendar').length>0) {
		equalHeight($('.calendar_page li'));

		$(".calendar_page li a").hover(
			function(e) {
				var html = '<div class="calinfo">';
				html += '<p><strong>'+ $(this).text() +'</strong></p><p>'+ $(this).attr("rel") +'</p>';
				html += '</div>';

				var pX = e.pageX-140;

				if(e.pageX<screen.width/2);
				else pX = e.pageX-240;

				$('body').append(html).children('.calinfo').css('top',e.pageY).css('left',pX);
				$('.calinfo').stop().hide().fadeIn(600);

			},function() {
				$('.calinfo').fadeOut(100).remove();
		});
	}

	if($('#olreg').length>0) {
		$('#olreg input[type=checkbox]').on('click',function() {
			$(this).parents('li').next().toggleClass('hide');
			var img = $(this).is(':checked')?'sampledaf':'sample';
			$('.sampleimg').attr('src', 'scan/'+img+'.jpg');
			$("input[name=dsdid]").attr('readonly',false);
		});
	}

	$('#nav').lavaLamp({
		fx:'easeInOutBack',
		speed:600,
		click:function(event,menuItem) {
			return true;
		}
	});

	$('.more,.link').live('click',function() {
		window.location = $(this).attr('href');
	});

	$('.panel_btn').toggle(
		function() {$('.othermonths').animate({marginLeft:70},{duration:2500,easing:'easeOutElastic'})},
		function() {$('.othermonths').animate({marginLeft:0},{duration:2500,easing:'easeOutElastic'})}
	);

	$('.hdr a').next().hide();
	$('.hdr a').click(function(e) {
			e.preventDefault();
			$('.hdr a').next().stop().hide(100);
			$(this).next().stop().show(100);
	});

	$(document).on('click','.reflink',function() {
		var $tmp = $('<input>');
		$('body').append($tmp);
		$tmp.val($(this).attr('rel')).select();
		document.execCommand('copy');
		$tmp.remove();
		$(this).hide(100).show(100);
	});

function initPreview() {
	yOff = 150;
	$('a.prev img').hover(
		function(e) {
			var t = fmtCurr($(this).attr('alt'));
			var html  = '<div id="prev">';
				html += '<img src="'+$(this).attr('src')+'" /><br /><p>';
				html += $(this).next().text()+'<br />';
				html +=($(this).attr('alt')!=''?'Php '+t:'')+'</p></div>';

			$('body').append(html);
			$('#prev').css('top',(e.pageY-yOff)+'px').css('left','0');

		},function() {
			$('#prev').fadeOut(100).remove();
	});

	$('a.prev img').mousemove(function(e) {
		$('#prev').css('top',(e.pageY-yOff)+'px').css('left','0');
	});
}

function loadProd(sc,fl) {
	$.ajax({
		type: 'POST',
		url: 'updateproducts.php',
		data: {'find':sc,'p':fl},
		beforeSend: function() {$('.prod_cat').empty().append('<img src="src/loading.gif" alt="" style="height:26px;width:128px" />')},
		success: function(data) {
			$('.prod_cat').empty().append(data);
			initPreview();
		}
	});
}

function equalHeight(group) {
	var tallest = 0;
	group.each(function() {
		var thisHeight = $(this).height();
		if(thisHeight>tallest) tallest = thisHeight;
	});
	group.height(tallest);
}

function getUrlVars() {
	var vars = {};
	var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,
	function(m,key,value) {
		vars[key] = value;
	});
	return vars;
}
});
