/*
 * requires: openapi(vkontakte.ru), jsCoreExt, jQuery
 */

var vkAPI = function()
{
    const PUBLIC_KEY    = 'xVIlqIucGX';
    
    const APP_ID        = 1998251;
    
    const API_SRC       = "http://vkontakte.ru/js/api/openapi.js";

    const SETTINGS      = 11;

    var LOGIN_BUTTON_ID = "login_button";

    var USER_INFO_ID    = "header_user";
    
    var USER_LOGOUT_ID  = "header_logout";

    var _data = new Object();
    
    function _init(callback)
    {
        callback = $.extend({
            onLogin : null,
            onLogout: null
        }, callback);
        
        this._onLoginCallback  = callback.onLogin;
        this._onLogoutCallback = callback.onLogout;

        VK.init({
            apiId: APP_ID
        });

        VK.UI.button(LOGIN_BUTTON_ID);

        $('#'+USER_LOGOUT_ID).click(function(){
            VK.Auth.logout(function(){
                _onLogout();
            });
        });

        VK.Observer.subscribe('auth.login', function(r){
            _onLogin(r.session);
        });

        VK.Observer.subscribe('auth.logout', function(event){
            _onLogout();
        });

        VK.Auth.getLoginStatus(function(r){
            if (r.session) {
                // _onLogin() will fired with observer
                //_onLogin(r.session);
            } else {
                // login() popup blocked by browser, show login button
                //_login();
                _onLogout();
            }
        });
    }

    function _login()
    {
        VK.Auth.login(null, SETTINGS);
    }

    function _showUserInfo(u)
    {
        var name = u.first_name+ ' '+u.last_name;
        $('#'+USER_INFO_ID).html(name).show();
        $('#'+USER_LOGOUT_ID).show();
                
        
    }

    function _setUserInfo(u)
    {      
        _setData('user', u);

        // only now we are loged in
        if (typeof _onLoginCallback == "function") {
            _onLoginCallback();
            // don't clear _callback, can use it once again
            // it's default action after login
            //_callback = null;
        }
    }

    function _deleteUserInfo()
    {
        _setData('user', false);
    }

    function _hideUserInfo()
    {
        $('#'+USER_INFO_ID).html('').hide();
        $('#'+USER_LOGOUT_ID).hide();
    }

    function _showLoginButton()
    {
        $('#'+LOGIN_BUTTON_ID).show();
    }
    
    function _hideLoginButton()
    {
        $('#'+LOGIN_BUTTON_ID).hide();
    }

    function _setData(name, object)
    {
        _data[name] = object;
    }

    function _getData(name)
    {
        return _data[name];
    }

    function _onLogin(session)
    {
        _hideLoginButton();
        
        VK.Api.call('getUserSettings', {}, function(r) {
            if(r.response){
                if(r.response != SETTINGS) {
                    _login();
                } else {
                    VK.Api.call('getProfiles', {
                        uids: session.mid
                    }, function(r) {
                        if(r.response) {
                            _setUserInfo(r.response[0]); // <= _onLogoutCallback here
                            _showUserInfo(r.response[0]);
                        } else {
                            _onLogout();
                        }
                    });
                    
                }
            }
        });
    }

    function _onLogout()
    {
        _showLoginButton();
        _hideUserInfo();
        _deleteUserInfo();
        _onLogoutCallback();
    }

    function _onUpdateSession(response)
    {
        
    }

    var _global =
    {
        init: function(callback) {
            _init(callback);
        },

        login: function()
        {
            _login();
        },

        getUserAudio: function(id, callback)
        {
            id = id || this.getUser().uid;
         
            callback = callback || null;

            VK.Api.call('audio.get', {
                uid:id
            }, callback);
        },

        getUser:function()
        {
            return _getData('user');
        },

        getData: function()
        {
            return _data;
        }
    };
    
    return _global;
    
}();