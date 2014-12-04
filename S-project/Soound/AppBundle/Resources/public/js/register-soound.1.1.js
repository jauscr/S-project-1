// JavaScript Document
OO = {};
ooNav = function(obj){
	setClasses = function(){
		var menus = [obj.navMenus, obj.container];
		$(menus).each(function(index, element) {
			var element = $(element);
			var list = $('li', element);
			$(list).each(function(index, element) {
				var firstChild = $(this).is(':first-child'),
					lastChild = $(this).is(':last-child');
				if(firstChild){
					$(this).addClass('oo-ui-first');
				}
				if(lastChild){
					$(this).addClass('oo-ui-last');
				}
			});
			
		});
	},
 	oneMenuOnly = function(openMenu){
		
		$(obj.navMenus).each(function(index, element){ 
			var op = $(openMenu).parent().hasClass('selected-bottom');
			var	visible = $(element).is(':visible')
			if(!op){
				$(element).parent().removeClass('selected-bottom');
				hideMenu(element);
			}
		})
	},
	showMenu = function(menu){
		oneMenuOnly(menu);
		$(menu).fadeIn(500).addClass('open');
  		$(document).on('click', docEvents).on('scroll', docEvents);
	},
	hideMenu = function(menu){
		$(menu).fadeOut('fast');
		$(document).off('click', docEvents);
		$(document).off('scroll', docEvents);
	},
	bindElements = function(){
		$(obj.navLinks).each(function(index, element){
			var menu = element + "-menu";
			$(element).bind('click',function(event){
				event.preventDefault();
				showMenu(menu)
				$(this).parent().addClass('selected-bottom');
				event.stopPropagation();
			});
		});
	},
	docEvents = function(e){
		var target = $(e.target);
		
		if(target.hasClass(obj.actionTwo) || target.hasClass(obj.actionOne))
		return
		if(target.parents(obj.actionTwo + '-menu').length > 0 || target.parents(obj.actionOne + '-menu').length > 0)
		return
		$(obj.navMenus).each(function(index, element){
			hideMenu(element)
			$(element).parent().removeClass('selected-bottom')
		});
	},
	init = function(){
		setClasses();
		bindElements();
	}
	init();
}
OO.Layouts = {
fullWidthCenter : function(obj){
		var obj = $(obj),
			marginLeft = obj.width()/2,
			marginTop = (obj.height()/2) + 40; 
			// + 40 for the bottom wave height
		obj.css({
			"position": "fixed",
			"top" : "50%",
			"left" : "50%",
			"margin-left" :  -marginLeft,
			"margin-top" : -marginTop
	});
	
}}
$(function(){
	$('input, textarea').placeholder();

    $('.register_submit').click(function(){
        clearAlert();
        var error=0;

        if (!isEmpty('#fos_user_registration_form_email')){
            $('#fos_user_registration_form_email').attr('style','border: solid #E74C3C !important');

            $('#fos_user_registration_form_email').focus(function(){ $(this).removeAttr('style')});
            showAlert('Please enter your email <br />',1);
            error++;
        }

        if (!isEmpty('#fos_user_registration_form_plainPassword')){
            $('#fos_user_registration_form_plainPassword').attr('style','border: solid #E74C3C !important');

            $('#fos_user_registration_form_plainPassword').focus(function(){ $(this).removeAttr('style')});
            showAlert('Please enter a password <br />',2);
            error++;
        }
        if(error>0){
            return false;
        }
    });

})