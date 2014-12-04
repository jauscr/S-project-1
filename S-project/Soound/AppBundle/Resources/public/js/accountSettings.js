
//This is the function that closes the pop-up
function endBlackout(){
	$(".blackout").css("display", "none");
	$(".msgbox").css("display", "none");
}

//This is the function that starts the pop-up
function strtBlackout(){
	$(".msgbox").css("display", "block");
	$(".blackout").css("display", "block");
}

$(function(){
	//Sets the buttons to trigger the blackout on clicks
	$("#delete-confirm").click(strtBlackout); // open if btn is pressed
	$(".blackout").click(endBlackout); // close if click outside of popup
	$(".closeBox, .delete-confirm-box-no").click(endBlackout); // close if close btn clicked
	
	$("#account-submenu > a").on("click", function(e){
		$this = $(this);
		if(!$this.hasClass("passive") && !$this.hasClass("active")){
			$(".tab-content.active").hide(400);
			$("#account-submenu > a.active").removeClass('active');
			$(".tab-content.active").removeClass("active");

			var tab = $this.attr("href");
			var section = tab.substr(1);
			window.history.replaceState({tab: section}, "", '?tab='+section);
			$this.addClass("active");
			$(tab).addClass("active").show(200);
		}
		return false;
	});
	var tab = $.url().param('tab');
	if(typeof tab != 'undefined')
		$("#account-submenu a[href='#"+tab+"']").trigger("click");


	$(".side-link").on("click", function(e){
		var $this = $(this);

		$(".side-link").removeClass("active");
		$this.addClass("active");

		var go = $this.data("go");

		if(go === 'payment' || go === 'notification'){
			$("#account-submenu a[href='#"+go+"']").trigger("click");

		}
		else{
			$("#account-submenu a[href='#general']").trigger("click");
			setTimeout(function(){
				$.scrollTo('#'+go);
			},100);
		}

		return false;
	})
	//console.log($(".custom-checkbox_input"));
	//$(".email-notification .custom-checkbox_input, .email-notification .custom-checkbox_input").on("click", function(e){
	$('.email-notification .custom-checkbox_input').click(function(e){
		//console.log($(e.target));

		//var checkBox = $(e.target).find('.custom-checkbox_input');
		//console.log(checkBox);
		//checkBox.click();
		
		$.post("accountSettings/saveNotificationPreferences",{type: $(this).val()}, function(resp) {
		});
	});

	$("#basicSettings").on("submit", function(e){
		$(this).ajaxSubmit({
			dataType:'json',
			success: function(resp){
				console.log(resp);
				if(resp.url)
					window.location.replace(resp.url);
			}
		});
		return false;
	});

	var hideSelector = function(e){
		if( ($(e.target).hasClass('active') && $(e.target).hasClass('selector')) || ($('.selector').has(e.target) && $(e.target).parents('.selector.active').length)  ){
			return;
		}
		else{
			$('.selector.active').removeClass('active');
			$(document).unbind('click', hideSelector);
		}
	};

	$('.selector').click(function(){
		var self = $(this);
		if(self.hasClass('active')){
			self.removeClass('active');
			$(document).unbind('click', hideSelector);
		}
		else{
			$('.selector.active').removeClass('active');
			self.addClass('active');
			$(this).children('.scroll-cont').perfectScrollbar('update');
			$(document).bind('click', hideSelector);
			return false;
		}
	});

	$('.selector li').click(function(){
		$(this).siblings().removeClass('selected');
		$(this).addClass('selected');
		$(this).parent().parent().siblings('.selector-input').val($(this).text());
	});

	$('.selector .scroll-cont').perfectScrollbar({
		'suppressScrollX': true,
		'wheelPropagation': true,
		'minScrollbarLength': 20
	});

	$('#tos-container').perfectScrollbar({
		'suppressScrollX': true,
		'wheelPropagation': true,
		'minScrollbarLength': 20
	});

	function checkFields(){
		var hasErrors = false;
		var fields = ['street-address', 'city', 'state', 'zip-code', 'birth-month', 'birth-day', 'birth-year', 'routing-number', 'account-number'];
		//Check for empty fields
		for(var i=0; i<fields.length; i++){
			var field = $('#'+fields[i]+'-field');
			if(field.hasClass('hasError'))
				field.removeClass('hasError');
			if(field.val() === ""){
				field.addClass('hasError');
				hasErrors = true;
			}
		}

		//Check for specific values
		if($('#birth-month-field').val() === "Month"){
			$('#birth-month-field').addClass('hasError');
			hasErrors = true;
		}
		if($('#birth-day-field').val() === "Day"){
			$('#birth-day-field').addClass('hasError');
			hasErrors = true;
		}
		if($('#birth-year-field').val() === "Year"){
			$('#birth-year-field').addClass('hasError');
			hasErrors = true;
		}
		if($('#routing-number-field').val().length != 9){
			$('#routing-number-field').addClass('hasError');
			hasErrors = true;
		}
		if($('#account-number-field').val().length < 3){
			$('#account-number-field').addClass('hasError');
			hasErrors = true;
		}
		return hasErrors;
	}

	$('#link-account-btn').click(function(){
		if(!checkFields()){
			$('.overlay').css('display', 'block');
			$('#tos-container').perfectScrollbar('update');
		}
	});

	$('#tos-accept').click(function(e){
		$('.overlay').fadeOut(200);
	});

	$('#tos-decline').click(function(e){
		$('.overlay').fadeOut(200);
	});

	$('.overlay').bind('click', function(e){
		if( e.target === this ){
			$(this).fadeOut(200);
		}
	});

	$("#changeEmailForm").on("submit", function(e){
		$(this).ajaxSubmit({
			success: function(resp){
			}
		});
		return false;
	});

	//Change of password ajax submit form
	$('#changePasswordSubmit').click(function(e){
		e.preventDefault(); //Stop normal form submission

		var form = $('#changePasswordForm'), url = form.attr('action');

		var newPassword = form.find('[name="newPassword"]').val(),
		newPasswordAgain = form.find('[name="newPasswordAgain"]').val(),
		currentPassword = form.find('[name="currentPassword"]').val();

		var error = false;

		if(!newPassword){
			error = 'Your new password cannot be blank.';
		}
		else if( newPassword != newPasswordAgain ){
			error = 'Your new password and confirm password fields don\'t match.';
		}
		else if(!currentPassword ){
			error = 'You forgot to enter your old password.';
		}

		if(!error){
			var confirmBtn = form.find('.confirm');
			confirmBtn.text('Saving...');
			form.find('.error').text('');
			$.post( url, { 
				newPassword: newPassword, 
				newPasswordAgain: newPasswordAgain,
				currentPassword: currentPassword
			}).done(function(response){
				if(response === "ok"){
					form.find('.error')
						.text("Password Changed!").css('color', '#2ecc71')
						.delay(3000).animate({opacity: 0}, 2000, function(){
							$(this).text('').css('opacity', 1);
						});
				} else {
					form.find('.error').text(response).css('color', '#e74c3c');
				}
				confirmBtn.text('change password');
			});
		}
		else {
			form.find('.error').text(error).css('color', '#e74c3c');
		}
		return false;
	});

	Dropzone.options.userfile = {
		maxFilesize: 5, //MB
		acceptedFiles: ".jpg,.jpeg,.png,.gif",
		clickable: '.dz-clickable',
		autoDiscover: false,
		init: function(){
			this.on("error", function(file, message){
				if( message === "You can't upload files of this type."){
					var uft = $('#userfile-accepted-types');
					//Turn file types text red and shake it
					uft.css('color', '#e74c3c')
						.animate({'padding-left': 200}, 200, 'easeInOutQuad', function(){
							uft.animate({'padding-left': 0, 'padding-right': 200}, 300, 'easeInOutQuad', function(){
								uft.animate({'padding-right': 0}, 150, 'easeOutQuad', function(){
									setTimeout(function(){
										uft.css('color', '#CCC');
									}, 1500);
								});
							});
						});
				}
				else
					$('#userfile-errors').text(message);
			});
			this.on("success", function(file, data){
				$('#upload-progress-status').text("completed");
				var response = $.parseJSON(data);
				if(response.msg=='ok')
        			$(".icon.icon-member").css('background-image', 'url('+response.url+')');
			});
		}
	};

})