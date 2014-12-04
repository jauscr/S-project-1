function priceRange(projectType){
    var myArray = [];
    var type=projectType;
    switch (projectType) {
        case "CompleteSongs":
            myArray = [500,1500];
            type = "Complete Song";
            break;
        case "Production":
            myArray = [500,1500];
            break;
        case "Songwriting":
            myArray = [500,1500];
            break;
        case "Musician":
            myArray = [500,1500];
            break;
        case "Vocal":
            myArray = [500,1500];
            break;
        case "Engineering":
            myArray = [500,1500];
            break;
    }
    $("#project_type").text(type);
    $( ".price_field #tempo" ).slider({
        range: true,
        min: (myArray[0]-50),
        max: (myArray[1]+50),
        values: [ myArray[0], myArray[1] ],
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
    $(".price_field #amount1").css('left',amount_left1_price).text("$"+$( ".price_field #tempo" ).slider( "values", 0 ));
    $(".price_field #amount2").css('left',amount_left2_price).text("$"+$( ".price_field #tempo" ).slider( "values", 1 ));
}


$(function(){
    uniqueCheckboxes("custom-checkbox_input");

    $("#deadline").attr( 'readOnly' , 'true' );

    $(".ui-datepicker-trigger").on("click", function(){
        $('#deadline').datepicker("show");
    });

    //If textfield click remove red border
    $('input[type="text"], textarea').focus(function(){ $(this).removeAttr('style')});

    $('.project_submit').click(function(){
        clearAlert();
        var error = 0;
        if (!isEmpty('#form_budget') || $('#form_budget').val()== 0){
            $('#form_budget').attr('style','border: solid #E74C3C !important');
            showAlert('Please write how much are you willing to spend. <br />',1);
            error++;
        }

        if (!isEmpty('#deadline')){
            $('#deadline').attr('style','border: solid #E74C3C !important');
            showAlert('Please select the project deadline. <br />',2);
            error++;
        }

        if(error>0){
            return false;
        }
    });

    $( "#deadline" ).datepicker({
        dayNamesMin: ['S', 'M', 'T', 'W', 'T', 'F', 'S'],
        shortYearCutoff: 99,
        showOtherMonths: true,
        selectOtherMonths: false
    });

});
