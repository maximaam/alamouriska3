window.fbAsyncInit = function() {
    FB.init({
        appId      : '440835073385326',
        cookie     : true,
        xfbml      : true,
        version    : 'v3.3'
    });

    FB.getLoginStatus(function(response) {
        statusChangeCallback(response);
    });

};

(function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));


function statusChangeCallback(response) {
    let isAppLogged = $('body').data('fb-logged');
    if (response.status === 'connected' && isAppLogged === 'false') {
        loginFbUser();
    } else {
    }
}

function checkLoginState() {
    FB.getLoginStatus(function(response) {
        statusChangeCallback(response);
    });
}

function fbLogout() {
    FB.logout(function (response) {
        console.log('logged out');
    });
}

function loginFbUser() {
    FB.api('/me?fields=name,email', function (response) {
        let name = response.name.replace(/\s+/g, '');
        $.get('/async/fb-login?name='+name+'&email='+response.email+'&id='+response.id, function (data) {
            if (+data.status === 1) {
                if (+data.register === 1) {
                    location.href= '/profile/';
                } else {
                    location.reload();
                }
            }
        });
    })
}