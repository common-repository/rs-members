jQuery(document).ready(function($){
	$(".rs-flipping-link").click(function(){
		$(this).parents(".rs-main-div").children(".rs-login-header").hide().empty();
		$(this).parents('div [class*="rs-widget-"]').hide();
		var clicked = $(this).attr('href');
		switch (clicked){
		case '#rs-register':
			$(this).parents('div [class*="rs-widget-"]').siblings('.rs-widget-register-div').show();
			break;
		case '#lost-pass':
			$(this).parents('div [class*="rs-widget-"]').siblings('.rs-widget-lost_pass-div').show();
			break;
		case '#rs-login':
			$(this).parents('div [class*="rs-widget-"]').siblings('.rs-widget-login-div').show();
			break;
			default:
				return true;
		}
		return false;
	});	
});

