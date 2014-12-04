/**
 *  Genral purpose JS namespace to load outh libs, such as the FB connect SDK, or google´s
 *  @author Hertzel Armengol
 **/

 window.fbAsyncInit = function() {
            // init the FB JS SDK
            FB.init({
                appId      : soound.oauth.facebook.client_id,
                status     : true,
                xfbml      : true
            });
        };

 // Load the SDK asynchronously

function fb_login() {
    FB.getLoginStatus(function(response) {
        if (response.status === 'connected') {
            // connected
            document.location = soound.oauth.facebook.redirect;
        } else {
            // not_authorized
            FB.login(function(response) {
                if (response.authResponse) {
                    document.location = soound.oauth.facebook.redirect;
                } else {
                    //User canceled the process
                }

            }, {scope: soound.oauth.facebook.scope});
        }
    });
}

/*
Load Google SDK
 */
$.getScript("//connect.facebook.net/en_US/all.js", function(){
    $(".js_login_popup-facebook").click(
        function(e){
            e.preventDefault();
            fb_login();
        }
    );
});
$.getScript("https://apis.google.com/js/client.js?onload=handleClientLoad", function(){
//$.getScript("http://apis.google.com/js/client.js?onload=handleClientLoad", function(){
    $('.js_login_popup-google').click(
        function(e){
        e.preventDefault();
        checkAuth();
    }
    );
});



function handleClientLoad() {
    gapi.client.setApiKey(soound.oauth.google.api_key);
}

function checkAuth() {
    gapi.auth.authorize({client_id: soound.oauth.google.client_id, scope: soound.oauth.google.scope, immediate: true}, handleAuthResult);
}

function handleAuthResult(authResult) {
    if (authResult && !authResult.error) {
        makeApiCall();
    } else {
        handleAuth();
    }
}

function handleAuth() {
    gapi.auth.authorize({client_id: soound.oauth.google.client_id, scope: soound.oauth.google.scope, immediate: false}, handleAuthResult);
    return false;
}

function makeApiCall() {
    document.location = soound.oauth.google.redirect;
}
