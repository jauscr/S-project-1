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
	$(".not-confirmed").on("click", ".icon-close", function(){
		$.post('/closeNotConfirmed', function(data, textStatus, xhr) {
			if(data.msg=='success'){
				$(".not-confirmed-container").slideUp();
				$("#navigation-bar").removeAttr("style");
			}
		},'jSon');
	})

	$("#login-form").on("submit", function(){
	    clearAlert();
	    if(!isStrEmpty($('input[name="_username"]').val())){
	        showAlert('Please write your email <br />',99);
	        return false;
	    }

	    if(!validateEmail($('input[name="_username"]').val())){
	        showAlert('Please write a valid email <br />',99);
	        return false;
	    }

	    if(!isStrEmpty($('input[name="_password"]').val())){
	        showAlert('Please write your password <br />',99);
	        return false;
	    }

		$(this).ajaxSubmit({
			dataType: 'json',
			success: function(msg){
	            if( msg.success == 1 ){
	                window.location.href = msg.route;
	            }else{
	            	//console.log(msg.error);
	                showAlert('Please verify your username and password <br />',99);
	            }
			}
		});
		return false;
	});
	

//$('input, textarea').placeholder();

})