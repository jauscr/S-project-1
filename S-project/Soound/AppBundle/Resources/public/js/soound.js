 $(document).ready(function(e) {
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


$(document).ready(function(e) {


	var navBar = $('.navigation-bar');
	navBar.on('click', 'a', function(e) {
		navBar.find('a').removeClass('selected-bottom');
		$(this).addClass('selected-bottom');
		$('.login_popup--a').removeClass('selected-bottom');
	});
	var Logo = $('.navigation-bar_container a:first-child');
		Logo.on('click','a',function(e){
		$('.navigation-bar_container a:first-child').removeClass('selected-bottom');
	});
});


$(document).ready(function(e) {

	$( ".tempo_field #tempo" ).slider({
      range: true,
      min: 0,
      max: 500,
      values: [ 75, 300 ],
      slide: function( event, ui ) {
      	if ( (ui.values[0] + 16) >= ui.values[1] ) {
                return false;
        }
      	$(".tempo_field #amount1").text($( ".tempo_field #tempo" ).slider( "values", 0 ));
    	$(".tempo_field #amount2").text($( ".tempo_field #tempo" ).slider( "values", 1 ));

    	var tempo_progress1 = $('.tempo_field .ui-slider-handle.ui-state-default.ui-corner-all:nth-child(2)');
    	var tempo_progress2 = $('.tempo_field .ui-slider-handle.ui-state-default.ui-corner-all:last-child()');
    	
    	var add1 = ($(".tempo_field #amount1").text().length == 2)?36:($(".tempo_field #amount1").text().length == 1)?38:33;
	    var add2 = ($(".tempo_field #amount2").text().length == 2)?36:($(".tempo_field #amount2").text().length == 1)?38:33;

    	var amount_left1 = Math.round(parseInt(tempo_progress1.css('left'))+add1 );
	    var amount_left2 = Math.round(parseInt(tempo_progress2.css('left'))+add2 );

	    $( ".tempo_field #amount1" ).css('left',amount_left1).text( $( ".tempo_field #tempo" ).slider( "values", 0 )+18);
	    $(".tempo_field #amount2").css('left',amount_left2).text($( ".tempo_field #tempo" ).slider( "values", 1 ));
      }
    });

    var tempo_progress1 = $('.tempo_field .ui-slider-handle.ui-state-default.ui-corner-all:nth-child(2)');
	var tempo_progress2 = $('.tempo_field .ui-slider-handle.ui-state-default.ui-corner-all:last-child()');

	var add1 = ($(".tempo_field #amount1").text().length == 2)?36:($(".tempo_field #amount1").text().length == 1)?38:36;
    var add2 = ($(".tempo_field #amount2").text().length == 2)?36:($(".tempo_field #amount2").text().length == 1)?38:33;

    var amount_left1 = Math.round(parseInt(tempo_progress1.css('left'))+add1 );
    var amount_left2 = Math.round(parseInt(tempo_progress2.css('left'))+add2 );

    $(".tempo_field #amount1" ).css('left',amount_left1).text( $( ".tempo_field #tempo" ).slider( "values", 0 ));
    $(".tempo_field #amount2").css('left',amount_left2).text($( ".tempo_field #tempo" ).slider( "values", 1 ));

    // Work on prince range by type of project.
    // This function will reloaded on budget-and-deadline.js

    /*$( ".price_field #tempo" ).slider({
      range: true,
      min: 0,
      max: 500,
      values: [ 75, 300 ],
      slide: function( event, ui ) {
          current = ui.value;
          if(current > 75 || current < 75 ){
              current =75;
              return false;
          }
      	$( ".price_field #amount1" ).text( $( ".price_field #tempo" ).slider( "values", 0 ));
    	$(".price_field #amount2").text($( ".price_field #tempo" ).slider( "values", 1 ));

    	var tempo_progress1_price = $('.price_field .ui-slider-handle.ui-state-default.ui-corner-all:nth-child(2)');
    	var tempo_progress2_price = $('.price_field .ui-slider-handle.ui-state-default.ui-corner-all:last-child()');
    	
    	var add1 = ($(".price_field #amount1").text().length == 2)?-9:($(".price_field #amount1").text().length == 1)?-5:-13;
	    var add2 = ($(".price_field #amount2").text().length == 2)?-9:($(".price_field #amount2").text().length == 1)?-5:-13;

    	var amount_left1_price = Math.round(parseInt(tempo_progress1_price.css('left'))+add1 );
	    var amount_left2_price = Math.round(parseInt(tempo_progress2_price.css('left'))+add2 );

	    $( ".price_field #amount1" ).css('left',amount_left1_price).text("$"+ $( ".price_field #tempo" ).slider( "values", 0 ));
	    $(".price_field #amount2").css('left',amount_left2_price).text("$"+$( ".price_field #tempo" ).slider( "values", 1 ));
      }
    });

    var tempo_progress1_price = $('.price_field .ui-slider-handle.ui-state-default.ui-corner-all:nth-child(2)');
	var tempo_progress2_price = $('.price_field .ui-slider-handle.ui-state-default.ui-corner-all:last-child()');

	var add1 = ($(".price_field #amount1").text().length == 2)?-1:($(".price_field #amount1").text().length == 1)?0:-8;
    var add2 = ($(".price_field #amount2").text().length == 2)?-1:($(".price_field #amount2").text().length == 1)?0:-12;

    var amount_left1_price = Math.round(parseInt(tempo_progress1_price.css('left'))+add1 );
    var amount_left2_price = Math.round(parseInt(tempo_progress2_price.css('left'))+add2 );

    $(".price_field #amount1" ).css('left',amount_left1_price).text("$"+$( ".price_field #tempo" ).slider( "values", 0 ));
    $(".price_field #amount2").css('left',amount_left2_price).text("$"+$( ".price_field #tempo" ).slider( "values", 1 )); */

	    

  $( "#datepicker" ).datepicker({
      showOn: "button",
      buttonImage: "/soound/bundles/sooundapp/css/images/calendar.png",
      buttonImageOnly: true
    });
/*
    $( ".browse_projects_slider" ).slider({
      value:0,
      min: 0,
      max: 2400,
      step: 1,
      slide: function( event, ui ) {
        $( "#amount_browse" ).val( "$" + ui.value ).css('left',parseInt($(".browse_projects_slider a").css('left')));
      }
    });
    $("#amount_browse" ).val( "$" + $( ".browse_projects_slider" ).slider( "value" ) );
  	$(".browse_projects_slider a").addClass('browse_projects_slider_object') ;

*/
  	
/*
  $( "#datepicker" ).datepicker({
      showOn: "button",
      buttonImage: "/soound/bundles/sooundapp/css/images/calendar.png",
      buttonImageOnly: true
    });
*/
  $(".review .review-budget").hover(
  	function () {
   		$(this).append('<div class="edit">Edit</div>');
  	},
  	function () {
    	$('.edit').remove();
  	}
 );

  $(".extras_title .custom-checkbox_input").click(function(){
  		$('.custom-checkbox_input').each(function(){
  			$(this).removeAttr("checked");
  		})
  		$(this).attr("checked","checked");
  });

$('.project_properties_1').click(function(){
		if($(this).hasClass("active")){
			$(this).removeClass('active');
		}
		else {
      $(".properties.active").removeClass("active");
			$(this).addClass('active');
		}
});

$('.project_properties_2').click(function(){
    if($(this).hasClass("active")){
        $(this).removeClass('active');
    }
    else {
      $(".properties.active").removeClass("active");
        $(this).addClass('active');
    }
});

$('.project_properties_3').click(function(){
		if($(this).hasClass("active")){
			$(this).removeClass('active');
		}
		else {
      $(".properties.active").removeClass("active");
			$(this).addClass('active');
		}
});

$('.project_properties_4').click(function(){
		if($(this).hasClass("active")){
			$(this).removeClass('active');
		}
		else {
      $(".properties.active").removeClass("active");
			$(this).addClass('active');
		}
});

$('.project_properties_5').click(function(){
    if($(this).hasClass("active")){
      $(this).removeClass('active');
    }
    else {
      $(".properties.active").removeClass("active");
      $(this).addClass('active');
    }
});



$('.project_properties_1 li').click(function(){
	$('.project_properties_1 li').each(function(){
		$(this).removeClass('selected');
	});
	$(this).addClass('selected');
	var li_sel = $(this);
	$('.project_properties_1 .project_property_selected').val($(li_sel).text()).trigger("keydown");
});

$('.project_properties_2 li').click(function(){
    $('.project_properties_2 li').each(function(){
        $(this).removeClass('selected');
    });
    $(this).addClass('selected');
    var li_sel = $(this);
    $('.project_properties_2 .project_property_selected').val($(li_sel).text()).trigger("keydown");
});

$('.project_properties_3 li').click(function(){
    $('.project_properties_3 li').each(function(){
        $(this).removeClass('selected');
    });
    $(this).addClass('selected');
    var li_sel = $(this);
    $('.project_properties_3 .project_property_selected').val($(li_sel).text()).trigger("keydown");
});

$('.project_properties_4 li').click(function(){
	$('.project_properties_4 li').each(function(){
		$(this).removeClass('selected');
	});
	$(this).addClass('selected');
	var li_sel = $(this);
	$('.project_properties_4 .project_property_selected').val($(li_sel).text()).trigger("keydown");
});

$('.project_properties_5 li').click(function(){
  $('.project_properties_5 li').each(function(){
    $(this).removeClass('selected');
  });
  $(this).addClass('selected');
  var li_sel = $(this);
  $('.project_properties_5 .project_property_selected').val($(li_sel).text()).trigger("keydown");
});


$('.project_properties_1 .project_property_selected').val($('.project_properties_1 li.selected').text());
$('.project_properties_2 .project_property_selected').val($('.project_properties_2 li.selected').text());
$('.project_properties_3 .project_property_selected').val($('.project_properties_3 li.selected').text());
$('.project_properties_4 .project_property_selected').val($('.project_properties_4 li.selected').text());
$('.project_properties_5 .project_property_selected').val($('.project_properties_5 li.selected').text());



/*
$('.sidebar_types a').bind('click',function(){
	$('.sidebar_types a').removeClass('sidebar_active');
	$(this).addClass('sidebar_active');
})

$('.project-info_description a').bind('click',function(){
	$(this).addClass('active');
})

$('.browse-container_project').bind('click', function(){
	$('.overlay').fadeIn(200);
});

$('.overlay').bind('click', function(e){
	if( e.target === this ){
		$(this).fadeOut(200, function(e){
			$(this).find('#join-project-nda').css({'visibility': 'hidden', 'opacity': 0});
		});
	}
});
*/
var read = false;
/*
$('#star').raty({
	path: '/soound/bundles/sooundapp/css/images/',
  	click:function(score,evt){
  		$(this).find('img').unbind();
	}
});
*/
//
//
$(document).keyup(function(e){

    if(e.keyCode === 27)
        
    	$(".overlay").fadeOut(500);

});
	
});

 






