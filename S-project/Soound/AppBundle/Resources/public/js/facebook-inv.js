var _name ="";
var _username ="";

window.fbAsyncInit = function() {
    // init the FB JS SDK
    FB.init({
        appId      : '270905026326592',                        // App ID from the app dashboard
        status     : true,                                 // Check Facebook Login status
        xfbml      : true                                  // Look for social plugins on the page
    });
};

// Load the SDK asynchronously
(function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/all.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

function htmlSpecialCharacter(mystring){
    return mystring.replace("&gt;",'>').replace("&lt;",'<').replace("&quot;",'"');
}

function sendFacebookInvitation(){
    var arrayMails = [];
    $('.alert.alert-blue').remove();

    $('input.facebook_checkbox:checked').each(function(){
        arrayMails.push($(this).val());
    });

        if(arrayMails.length > 0){
            $.ajax({
                type: "POST",
                url: "../send/arrayEmails/"+arrayMails,
                data: "name=" + _name + '&' + "username=" + _username,
                success: function(msg){
                    $('.box-content--facebook').after('<div class="alert alert-blue"><p><b>Complete!</b> your friends will receive the invitation on Facebook.</p></div>');
                    $('.tagit-choice.ui-widget-content').remove();
                    console.log(msg);
                }
            });
        }else{
            alert('You must select any friends');
        }
}

function createFriendUi(username,email,img,pos,f){
    var html=(f==1)?'<li class="first" onclick="checkImage($(this))">':'<li onclick="checkImage($(this))">';

    html+='<img class="img-avatar" src="'+img+'" onerror="ImgError(this)"/>'+
          '<input style="display:none" type="checkbox" id="facebookFriend_'+pos+'" name="facebookFriends[]" value="'+email+'" class="facebook_checkbox">'+
          '<div class="red-check" style="display: none"></div>'+
          '<label>'+username+'</label>'+
    '</li>';
    //'<label class="custom-checkbox_label" for="facebookFriend_'+pos+'"></label>'+
    return html;
}

function login(){
    FB.getLoginStatus(function(response) {
        if (response.status != 'connected') {
            FB.login(function(response)
            {
                if (response.authResponse) {
                    $('#inviteByFacebook').removeAttr('onclick');
                    $('#inviteByFacebook').val('Invite friends by Facebook');

                    $('ul.facebookFriendList-container li').remove();
                    FB.api('/me/friends',{fields: 'name,id,username,first_name'}, function(response) {
                        if(response.data) {
                            var pos = 1;
                            var f = 1;
                            $.each(response.data,function(index,friend) {
                                if(f == 6){ f=1}
                                $('.facebookFriendList-container').append(createFriendUi(friend.first_name ,friend.username+'@facebook.com','https://graph.facebook.com/'+friend.id+'/picture?width=90&height=90',pos,f));
                                pos++;
                                f++;
                            });
                        }
                    });
                    $('#sendInviteByFacebook').attr('onclick',"sendFacebookInvitation()");
                    $('.invite-container.facebook').removeAttr('style');
                    $('#sendInviteByFacebook').removeAttr('style');
                    $('#inviteByFacebook').attr('style',"display:none");

                    FB.api('/me', function(response) {
                        _name = response.name;
                        _username = response.username;
                        console.log('username: '+response.username);
                    });

                } else {
                    $('#inviteByFacebook').val('Login to Facebook');
                    //Message error login
                }
            });
        }else{
            FB.api('/me/friends',{fields: 'name,id,username,first_name'}, function(response) {
                if(response.data) {
                    var pos = 1;
                    var f = 1;
                    $('ul.facebookFriendList-container li').remove();
                    $.each(response.data,function(index,friend) {
                        if(f == 6){ f=1}
                        $('.facebookFriendList-container').append(createFriendUi(friend.first_name ,friend.username+'@facebook.com','https://graph.facebook.com/'+friend.id+'/picture?width=90&height=90',pos, f));
                        pos++;
                        f++;
                    });
                }

            });
            $('#sendInviteByFacebook').attr('onclick',"sendFacebookInvitation()");
            $('.invite-container.facebook').removeAttr('style');
            $('#sendInviteByFacebook').removeAttr('style');
            $('#inviteByFacebook').attr('style',"display:none");
            FB.api('/me', function(response) {
                _name = response.name;
                _username = response.username;
                console.log('username: '+response.username);
            });
        }
    });
}