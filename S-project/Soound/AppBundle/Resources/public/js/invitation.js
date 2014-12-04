function makeTags(){
    $("#freeMail_txt").tagit({
        placeholderText: 'Separate emails by commas',
        beforeTagAdded: function(event, ui) {
            /* create tag only if email is valid */
            if(validateEmail(ui.tagLabel)== false) return false
        }
    });
}

function sendInvitation(Username){
    $('.alert.alert-blue').remove();
    $('.alert.alert-danger').remove();
    if(!isStrEmpty($('#freeMail_txt').val())){
        $('.highlight-input.step_three_input').after('<div class="alert alert-danger" style="margin-top: 15px;"><p><b>Alert!</b> please make sure you write the emails in the textfield</p></div>');

    }else{
        var arrayMails = new Array();
        arrayMails = $('#freeMail_txt').val().split(',');
        $.ajax({
            type: "POST",
            url: "teamInvite",
            data: {arrayMails : arrayMails, username: Username},
            dataType: 'json',
            success: function(response){
                /*$('#inviteByEmail').before('<div class="alert alert-blue"><p><b>Complete!</b> your friends will receive the invitation.</p></div>');
                setTimeout(function(){
                    $("#invite-team-cont").find(".alert").fadeOut("200");
                }, 2000);
                */
                $('.tagit-choice.ui-widget-content').remove();
                $('#freeMail_txt').val("");
                emails = "";
                for(var i=0; i<response.emails.length; i++){
                    emails += '<div class="teamEmail pending pull-left"><span class="icon-pending pull-left"></span><div class="pull-left">'+response.emails[i]+'</div><div class="removeTeam" data-email="'+response.emails[i]+'"><span class="icon-remove pull-left"></span><span class="text">Remove</span></div></div>';
                }
                var emailsCont = $(".invitedUsers");
                emailsCont.append(emails).addClass("show");
                emailsCont.next().addClass("addMore");
                emailsCont.parent().addClass("noPadding");

            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $('#inviteByEmail').before('<div class="alert alert-danger"><p><b>Alert!</b> Status: '+textStatus+', Error: '+errorThrown+'</p></div>');
                console.log(textStatus, errorThrown);
            }
        });
    }
}