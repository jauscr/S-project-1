function verifyEmail(email){
    $('.form_field.paymentmethod.emailExist').attr('style','display:none');
    $('#existEmailPopup').remove();

    var template='<div id="existEmailPopup"><img src="/bundles/sooundapp/css/images/gray-arrow.jpg" class="gray-arrow" />'+
        '<div class="block-login">'+
        '<div class="form_field_title spaceLeft">WE ALREADY HAVE THAT E-MAIL. LOGIN?</div>'+
        '<div class="highlight-input--wicon">'+
        '<input type="password" id="project-login-password" name="form[password]" required="required" placeholder="PASSWORD">'+
            '<div class="highlight-input_deco"></div>'+
            '<div class="icon--small icon--key"></div>'+
        '</div>'+
        '</div>'+
    '<div class="block-login">'+
        '<div class="submit_popup">'+
            '<input type="button" id="start-login-submit" name="form[start-login-submit]" class="submit_btn login_popup--a" value="Login">'+
                '<span>Or use</span>'+
                '<a id="star-project-login-google" class="google_btn login_popup--a js_login_popup-google" href="javascript:checkAuth();sessionHandle(document.URL);"></a>'+
                '<a id="star-project-login-facebook" class="facebook_btn login_popup--a js_login_popup-facebook" href="javascript:fb_login();sessionHandle(document.URL);"> </a>'+
            '</div>'+
        '</div>'+
        '<a class="start-login-forgot" href="../../resetting/request">Forgot your password?</a></div>';

    $.ajax({
        type: "POST",
        url: '../../verifyEmail',
        data: {'email': email},
        success: function(res) {
            if(res==1){
                $('#login-extra-fields').hide();
                $('.emailExist').html(template);
                $('.emailExist').show();
                $("#project-login-password").focus();
                $('#start-login-submit').click(function(){
                    fosLogin();
                });
            }
            else {
                $('#login-extra-fields').show();
            }
            result = res;
        }
    });
}

function fosLogin(){
    $('#project-login-password').removeAttr('style');
    if (!isEmpty('#project-login-password')){
        $('#project-login-password').attr('style','border: solid #E74C3C !important');
        $('#project-login-password').attr('placeholder','Insert your password');
    }else{
        data = {
            '_username':$("#form_emailAddress").val(),
            '_password':$("#project-login-password").val()
        };
        $.ajax({
            type: "POST",
            url: '../../login_check?_csrf_token='+$('input[name="_csrf_token"]').val(),
            data: data,
            dataType: "json",
            success: function(res) {
                window.location.reload(true);
            },
            error: function(res) {
                alert('Please, write a valid password');
            }
        });
    }
}

function sessionHandle(url){
    $.ajax({
        type: "POST",
        url: '/redirectTo',
        data: "url=" + url,
        success: function(res) {
            console.log('success')
        },
        error: function(XMLHttpRequest, textStatus, errorThrown, res) {
            console.log("Status: " + res); console.log("Error: " + errorThrown);
        }
    });
}

$(function(){
    $('html').click(function() {
        $('.project_properties_1').removeClass('active');
        $('.project_properties_2').removeClass('active');
    });

    $('.project_properties_1,.project_properties_2').click(function(event){
        event.stopPropagation();
    });

    $('.project_properties_1 li, .project_properties_2 li').hover( function() {
        $(this).animate({ color: "#000" });
    },function() {
        $(this).animate({ color: "#787878" });
    });

    $('#already-have-account-btn').click(function(e){
        $("#modal-content,#modal-background").toggleClass("active");
    });
    $('input[type=text]').focus(function(){ $(this).removeAttr('style')});
    $('.custom-radio_input:first').attr('checked', 'checked');

    $('#reviewAndConfirm').click(function(){
        clearAlert();
        var flag = 0;
        $('input[type="text"]').each(function(){
            if (!isEmpty(this)){
                if($('#form_emailAddress').doesExist()){
                    if(typeof($('#form_emailAddress').val())!= 'undefined'){
                        if($(this).attr('id')=='form_emailAddress'){showAlert('Insert a valid email',1); flag++;}
                    }
                }
                if($('.custom-radio_input[value="new"]').is(':checked')){
                    if($(this).attr('id')=='form_creditCard'){showAlert('Insert a valid credit card number',3); flag++;}
                    if($(this).attr('id')=='form_cvc'){showAlert('Insert a valid CVC',4); flag++;}
                    if($(this).attr('id')=='form_expirationMonth'){showAlert('Insert valid month, e.g: 09',5); flag++;}
                    if($(this).attr('id')=='form_expirationYear'){showAlert('Insert valid card year e.g: 13',6); flag++;}
                    if($(this).attr('id')=='form_cardName'){showAlert('Insert a cardholder name', 7); flag++;}
                    if($(this).attr('id')=='form_billingZip'){showAlert('Insert a valid zipcode', 8); flag++;}
                }

               $(this).attr('style','border: solid #E74C3C !important');
            }
        });

        if(!$('.form_field.paymentmethod input[type=radio]:checked').val()){
            showAlert('Please select the payment method you will use ...',2);
            flag++;
        }
        if(typeof($('#form_emailAddress').val()) != 'undefined'){
            if($('#form_emailAddress').val() === ""){
                flag ++;
            }
        }
        if($('#form_emailAddress').doesExist()){
            if($(".emailExist").is(':visible')){
                flag ++;
                $("#project-login-password").focus();
            }
        }
        if(flag >0){
            return false;
        }
        else{
            $("#step_form_step5").submit();
        }

        //$('input').focus(function(){ $(this).removeAttr('style')});

    });

    $('#start-login-submit').click(function(){
        fosLogin();
    });

    $('#form_emailAddress').on('keyup', function(e){
        if(validateEmail($(this).val())){
            if($(this).val() != ""){
                verifyEmail($(this).val());
            }
        }
        else{
            $('.form_field.paymentmethod.emailExist').attr('style','display:none');
            $('#existEmailPopup').remove();
            $('#login-extra-fields').show();
        }
    });

    $('.form_field.paymentmethod').find('.custom-radio_input').change(function(e){
        if( $(e.target).val() === "new" ){
            $('.paymentmethod_card').show(200);
        } else {
            $('.paymentmethod_card').hide(200);
        }
    });
});