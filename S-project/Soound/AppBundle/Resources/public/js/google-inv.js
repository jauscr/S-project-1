var clientId = '679781770194.apps.googleusercontent.com';
var apiKey = 'AIzaSyB4rVE4H8gbvhl337U5sHyAzR11GORx4HE';
var scopes = 'https://www.google.com/m8/feeds https://www.googleapis.com/auth/userinfo.email';
//var scopes = '//www.google.com/m8/feeds //www.googleapis.com/auth/userinfo.email';
var _token ="";

var _googleUsername="";

function getFirstWord(str){
    var arr = str.split(" ", 2);
    var firstWord = arr[0];
    return firstWord;
}

function sendGoogleInvite(){
    var arrayMails = [];
    $('.alert.alert-blue').remove();
    $('input.google_checkbox:checked').each(function(){
        arrayMails.push($(this).val());
    });

    if(arrayMails.length > 0){
        $.ajax({
            type: "POST",
            url: "../send/template/emails",
            data: {arrayMails : arrayMails, username: _googleUsername},
            success: function(msg){
                $('.box-content--google').after('<div class="alert alert-blue"><p><b>Complete!</b> your friends will receive the invitation on Google.</p></div>');
                $('.tagit-choice.ui-widget-content').remove();
                console.log(_googleUsername);
            }
        });
    }else{
        alert('You must select any friends');
    }
}

function createGoogleFriendUi(username,email,img,pos,f){
    var theName ="";
    username = (!isStrEmpty(username))? email : username;

    if(username.indexOf('@') >= 0){
        theName = getTheName(username);
    }else{
        theName = getFirstWord(username);
    }

    var html=(f==1)?'<li class="first" onclick="checkImage($(this))">':'<li onclick="checkImage($(this))">';

    html+='<img class="img-avatar" src="'+img+'" onerror="ImgError(this)"/>'+
        '<input style="display:none" type="checkbox" id="googleFriend_'+pos+'" name="googleFriends[]" value="'+email+'" class="google_checkbox">'+
        '<div class="red-check" style="display: none"></div>'+
        '<label title="'+username+'">'+theName+'</label>'+
        '</li>';
    return html;
}

function handleClientLoad() {
    gapi.client.setApiKey(apiKey);
    window.setTimeout(checkAuth,1);
}

function checkAuth() {
    gapi.auth.authorize({client_id: clientId, scope: scopes, immediate: true}, handleAuthResult);
}

function handleAuthResult(authResult) {
    //console.log(gapi);
    var authorizeButton = document.getElementById('authorize-button');
    if (authResult && !authResult.error) {
        authorizeButton.style.display = 'none';
        $('#google_txt').removeAttr('readonly');
        $('#google_txt').attr('placeholder','write your friends emails here');
        $('#google-email-btn').removeAttr('style');
        getMyContacts();
    } else {
        authorizeButton.style.display = '';
        authorizeButton.onclick = handleAuthClick;
    }
}

function handleAuthClick(event) {
    gapi.auth.authorize({client_id: clientId, scope: scopes, immediate: false}, handleAuthResult);
    return false;
}



function getMyContacts() {
    $('.invite-container.google').removeAttr('style');
    var authParams = gapi.auth.getToken() // from Google oAuth
    _token = authParams.access_token;
    authParams.alt = 'json';
    $.ajax({
        url: 'https://www.google.com/m8/feeds/contacts/default/full',
        //url: '//www.google.com/m8/feeds/contacts/default/full',
        dataType: 'jsonp',
        data: authParams,
        success: function(data) {
            var pos = 1;
            var f = 1;
            $('ul.googleFriendList-container li').remove();
            $.each(data.feed.entry,function(){
                if(f == 6){ f=1}
                $('.googleFriendList-container').append(createGoogleFriendUi(this.title.$t,this.gd$email[0].address,this.link[0].href+'?access_token='+_token,pos,f));
                pos++;
                f++;
            });
        }
    });
}