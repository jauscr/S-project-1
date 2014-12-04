// Soound Header JS File

 $(function(e){
	var login_popup = $('.login_popup');
	var log_in_btn = $('.log-in-btn');
	var showPopup = function(){
		login_popup.fadeIn('slow');
		$(document).on('click', hidePopup);
	};

	var hidePopup = function(e) {
		var target = $(e.target);

		if(target.hasClass('log-in-btn') || target.hasClass('login_popup'))
			return

		if(target.parents('.login_popup').length > 0)
			return

		$(document).off('click', hidePopup);
		login_popup.fadeOut('slow');
		log_in_btn.removeClass('selected-bottom');
	}

	login_popup.hide();
	log_in_btn.click(showPopup);
});