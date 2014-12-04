
var startDate = "";
var endDate = "";

function getTransactions(){
	$.post('transactions/between', {"startDate": startDate, "endDate": endDate}).done(function(data){
		var response = $.parseJSON(data);
		//console.log(response);
		$('#transactions-history').hide().empty();
		
		var html = "";
		for(var i=0; i<response.length; i++){
			var month = response[i];
			html += '<div class="transaction-month"><div class="transaction-month-date">'+month.date+'</div>';
			
			for(var j=0; j<month.transactions.length; j++){
				var tran = month.transactions[j];
				html += '<div class="transaction-cont">';
				if(tran.outgoing)
					html += '<div class="transaction-details-left"><span class="transaction-outgoing-icon"></span><span class="transaction-date">'+tran.date+'</span></div><div class="transaction-details-right"><span class="transaction-light">Paid </span><span class="transaction-dark">$'+tran.amount+'</span><span class="transaction-light"> for funding </span><span class="transaction-dark">"'+tran.projectTitle+'"</span><span class="transaction-light"> project.</span></div>';
				else 
					html += '<div class="transaction-details-left"><span class="transaction-incoming-icon"></span><span class="transaction-date">'+tran.date+'</span></div><div class="transaction-details-right"><span class="transaction-light">Received </span><span class="transaction-dark">$'+tran.amount+'</span><span class="transaction-light"> for winning </span><span class="transaction-dark">"'+tran.projectTitle+'"</span><span class="transaction-light"> project.</span></div>';

				html += '</div>';
			}

			html += '</div>';
		}

		if(response.length < 1)
			html = '<div class="no-results-cont"><span class="none-available">NONE AVAILABLE</span></div>';
		
		$("#transactions-history").append($(html)).show(200);

	});
}

$(function(){
	startDate = $('#date-selector-left').val().split(' / ');
	startDate = startDate[2]+"-"+startDate[1]+"-"+startDate[0];
	endDate = $('#date-selector-right').val().split(' / ');
	endDate = endDate[2]+"-"+endDate[1]+"-"+endDate[0];


	$( "#date-selector-left" ).datepicker({
	    showOn: "button",
	    buttonImage: "../bundles/sooundapp/css/images/calendar.png",
	    buttonImageOnly: true,
	    dateFormat: "d / m / yy",
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
	    		getTransactions();
	    	}
	    }
    });

    $( "#date-selector-right" ).datepicker({
	    showOn: "button",
	    buttonImage: "../bundles/sooundapp/css/images/calendar.png",
	    buttonImageOnly: true,
	    dateFormat: "d / m / yy",
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
	    		getTransactions();
	    	}
	    }
    });

    var leftBlurFun = function(e){
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
    				if( i === 2 )
    					ok = false;
    				else if( text[i].charAt(1) === "0")
    					ok = false;
    				else
    					text[i] = text[i].replace("0", "");
    			}
    		}
    		if(ok){
    			text = text[2]+"-"+text[1]+"-"+text[0];
	    		if(text != startDate){
	    			startDate = text;
	    			getTransactions();
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
    				if( i === 2 )
    					ok = false;
    				else if( text[i].charAt(1) === "0")
    					ok = false;
    				else
    					text[i] = text[i].replace("0", "");
    			}
    		}
    		if(ok){
    			text = text[2]+"-"+text[1]+"-"+text[0];
	    		if(text != endDate){
	    			endDate = text;
	    			getTransactions();
	    		}
    		}
    	}
    };

    $('#date-selector-left').bind("blur", leftBlurFun);
    $('#date-selector-right').bind("blur", rightBlurFun);

});