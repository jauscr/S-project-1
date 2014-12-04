/**
 * User: jaus
 * Less and better code to validate in JS
 */
function validateTempo(){
    clearAlert();
    $('#form_labelTempo').focus(function(){ $(this).removeAttr('style')});
    if(isEmpty('#form_labelTempo')){
        if(!isNumber($('#form_labelTempo').val())){
            showAlert('Song Tempo admits numbers only  <br />','tempo');
            $('#form_labelTempo').attr('style','border: solid #E74C3C !important');
            return false;
        }else{
            return true;
        }
    }
}

function isNumber(num) {
    if (!/^([0-9\.])*$/.test(num)) {
        return false;
    } else {
        return true;
    }
}

jQuery.fn.doesExist = function(){
    return jQuery(this).length > 0;
};

function ucFirst(string)
{
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function getTheName(str){
    var nameReplace = str.replace(/@.*$/,"");
    var name = nameReplace!==str ? nameReplace : null;
    return name;
}

function checkImage(ui){
    ui.find('.red-check').toggle();
    if(ui.find('input').is(':checked')==false){
        ui.find('input').attr('checked', true)
    }else{
        ui.find('input').attr('checked', false)
    }
}

function showAlert(error, pos, display){
    if(typeof display == 'undefined')
        display = 'block';

    $(".global-alert"+pos).append(error);
    $(".global-alert"+pos).attr('style','display:'+display);
}

function clearAlert(){
    $(".alert.alert-danger").each(function(){
        $(this).attr('style','display:none');
        $(this).html("");
    })
}

function imposeMaxLength(Object, MaxLen)
{
    return (Object.value.length <= MaxLen);
}

function isEmpty(id){
    var result =($(id).val().length <= 0 || $.trim($(id).val())== "")? false : true ;
    if(result==true){$(id).css('border-color','#dcdcdc');}
    return result;
}

function isStrEmpty(string){
    var result =(string.length <= 0 || $.trim(string)== "")? false : true ;
    return result;
}

function checkURL(value) {
    var urlregex = new RegExp("([a-zA-Z0-9]+://)?([a-zA-Z0-9_]+:[a-zA-Z0-9_]+@)?([a-zA-Z0-9.-]+\\.[A-Za-z]{2,4})(:[0-9]+)?(/.*)?");
    if (urlregex.test(value)) {
        return (true);
    }
    return (false);
}

function validateEmail($email) {
    var emailReg = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if( !emailReg.test( $email ) ) {
        return false;
    } else {
        return true;
    }
}

function ImgError(source){
    source.src = "/images/demo-proj.png";
    source.onerror = "";
    return true;
}

function extractNumber(text){
    var number = text.replace(/[^0-9]/gi, '');
    return parseInt(number);
}

function tagsDeco(){
    $('input.ui-widget-content').focus(function(){
        $(this).parent().parent().addClass('ui-tags-deco');
        $(this).parent().parent().attr('style','border-style: none !important; border-left: 3px solid #E74C3C !important;');
    });
    $('input.ui-widget-content:not(".project-edit")').focusout(function(){
        $(this).parent().parent().removeClass('ui-tags-deco');
        $(this).parent().parent().removeAttr('style');
    });
}

function uniqueCheckboxes(className){
    var $unique = $('input.'+className);
    $unique.click(function() {
        $unique.filter(':checked').not(this).removeAttr('checked');
    });
}

function time() {
  return Math.floor(new Date()
    .getTime() / 1000);
}
function monkeyPatchAutocomplete() {

  $.ui.autocomplete.prototype._renderItem = function( ul, item) {
      var re = new RegExp(this.term, "i") ;
      var t = item.label.replace(re,"<span>" + 
              item.label.match(re) + 
              "</span>");
      return $( "<li></li>" )
          .data( "item.autocomplete", item )
          .append( "<a>" + t + "</a>" )
          .appendTo( ul );
  };
}
$(function(){
    if(typeof $.ui !== 'undefined')
        monkeyPatchAutocomplete()
    /*$('#nav > li').hover(function () {
        $(this).addClass('selected-bottom');
    }, function () {
        if($(this).children().find('div').is(':visible')==false){
            $(this).removeClass('selected-bottom')
        }else{
            $(this).addClass('selected-bottom');
        }
    }); */
})
