
var startDate = "";
var endDate = "";

function getActivity(){
	$.post('activity/between', {"startDate": startDate, "endDate": endDate}).done(function(data){
		var response = $.parseJSON(data);
		//console.log(response);
		$('#activity-history').hide().empty();
		
		var html = "";
		for(var i=0; i<response.length; i++){
			var day = response[i];
			html += '<div class="activity-day"><div class="activity-day-date">'+day.date+'</div>';
			
			for(var j=0; j<day.activities.length; j++){
				var activity = day.activities[j];
				html += '<div class="activity-cont"><div class="activity-left"><div class="activity-icon '+activity.icon+' '+(activity.read ? 'read' : 'not-read')+'"></div><div class="activity-content">';

				//Reconstruct activity content with links
				for( var k=0; k<activity.content.length; k++){
					var content = activity.content[k];
					if(content.ref)
						html += '<a class="activity-link" href="'+content.ref.link+'">'+content.ref.name+'</a>';
					if(content.text)
						html += '<span class="activity-text">'+content.text+'</span>';
				}
				html += '</div></div><div class="activity-right"><div class="activity-time">'+activity.time+'</div></div></div>'; //Activity content closer
			}

			html += '</div>';
		}

		if(response.length < 1)
			html = '<div class="no-results-cont"><span class="none-available">NONE AVAILABLE</span></div>';
		
		$("#activity-history").append($(html)).show(200);

	});
}

$(function(){
	startDate = $('#date-selector-left').val().split(' / ');
	startDate = startDate[2]+"-"+startDate[0]+"-"+startDate[1];
	endDate = $('#date-selector-right').val().split(' / ');
	endDate = endDate[2]+"-"+endDate[0]+"-"+endDate[1];


	$( "#date-selector-left" ).datepicker({
        dayNamesMin: ['S', 'M', 'T', 'W', 'T', 'F', 'S'],
        shortYearCutoff: 99,
        showOtherMonths: true,
        selectOtherMonths: false,
	    showOn: "button",
	    buttonImage: "/bundles/sooundapp/css/images/calendar.png",
	    buttonImageOnly: true,
	    dateFormat: "m / d / yy",
	    showAnim: "slideDown",
	    maxDate: -1,
	    beforeShow: function(input, inst){
	    	$('#date-selector-left').unbind('blur', leftBlurFun);
	    },
	    onClose: function(){
	    	$('#date-selector-left').bind('blur', leftBlurFun);
	    },
	    onSelect: function(dateText, inst){
	    	tempDate = inst.selectedYear+"-"+(inst.selectedMonth+1)+"-"+inst.selectedDay;
	    	if(tempDate != startDate){
	    		$('#date-selector-right').datepicker( "option", "minDate", $('#date-selector-left').datepicker("getDate"));
	    		startDate = tempDate;
	    		getActivity();
	    	}
	    }
    });

    $( "#date-selector-right" ).datepicker({
        dayNamesMin: ['S', 'M', 'T', 'W', 'T', 'F', 'S'],
        shortYearCutoff: 99,
        showOtherMonths: true,
        selectOtherMonths: false,
	    showOn: "button",
	    buttonImage: "../bundles/sooundapp/css/images/calendar.png",
	    buttonImageOnly: true,
	    dateFormat: "m / d / yy",
	    showAnim: "slideDown",
	    maxDate: 0,
	    minDate: -1,
	    beforeShow: function(input, inst){
	    	$('#date-selector-right').unbind('blur', rightBlurFun);
	    },
	    onClose: function(){
	    	$('#date-selector-right').bind('blur', rightBlurFun);
	    },
	    onSelect: function(dateText, inst){
	    	tempDate = inst.selectedYear+"-"+(inst.selectedMonth+1)+"-"+inst.selectedDay;
	    	if(tempDate != endDate){
	    		endDate = tempDate;
	    		getActivity();
	    	}
	    }
    });

    var leftBlurFun = function(e){
    	var text = $(this).val().replace(/ /g, "");
    	//First check to see date is in the correct format
    	//			 mm / dd / yyyy 
    	var reg = /\d*\d+\/\d*\d+\/\d+\d+\d+\d+/;
    	text = text.match(reg);
    	if(text){
    		var ok = true;
    		text = text[0].split('/');
    		for(var i=0; i<text.length; i++){
    			if(text[i].charAt(0) === "0"){
    				if( i === 1 )
    					ok = false;
    				else if( text[i].charAt(1) === "0")
    					ok = false;
    				else
    					text[i] = text[i].replace("0", "");
    			}
    		}
    		if(ok){
    			text = text[2]+"-"+text[0]+"-"+text[1];
	    		if(text != startDate){
	    			startDate = text;
	    			getActivity();
	    		}
    		}
    	}
    };

    var rightBlurFun = function(e){
    	var text = $(this).val().replace(/ /g, "");
    	//First check to see date is in the correct format
    	//			 dd / mm / yyyy 
    	var reg = /\d*\d+\/\d*\d+\/\d+\d+\d+\d+/;
    	text = text.match(reg);
    	if(text){
    		var ok = true;
    		text = text[0].split('/');
    		for(var i=0; i<text.length; i++){
    			if(text[i].charAt(0) === "0"){
    				if( i === 1 )
    					ok = false;
    				else if( text[i].charAt(1) === "0")
    					ok = false;
    				else
    					text[i] = text[i].replace("0", "");
    			}
    		}
    		if(ok){
    			text = text[2]+"-"+text[0]+"-"+text[1];
	    		if(text != endDate){
	    			endDate = text;
	    			getActivity();
	    		}
    		}
    	}
    };

    $('#date-selector-left').bind("blur", leftBlurFun);
    $('#date-selector-right').bind("blur", rightBlurFun);

});