/*
 * requires: openapi(vkontakte.ru), jsCoreExt, jQuery
 */

var vkAPI = function()
{
    // var PUBLIC_KEY      = 'xVIlqIucGX';
    
    var _APP_ID          = 1998251;
    
    // var API_SRC         = "http://vkontakte.ru/js/api/openapi.js";

    var _SETTINGS        = 11;

    var LOGIN_BUTTON_ID = "login_button";

    var USER_INFO_ID    = "header_user";
    
    var USER_LOGOUT_ID  = "header_logout";

    var _data = new Object();

    var _callbacks = {
        onLogin : null,
        onLogout: null
    }
    
    function _init(callbacks)
    {
        $.extend(_callbacks, callbacks);

        VK.init({
            apiId: _APP_ID
        });

        VK.UI.button(LOGIN_BUTTON_ID);
        $("#"+LOGIN_BUTTON_ID).click(function(){
            _login();
        });

        $('#'+USER_LOGOUT_ID).click(function(){
            VK.Auth.logout(function(){
                _onLogout();
            });
        });

        VK.Observer.subscribe('auth.login', function(r){
            debug(">>> VK.Observer auth.login");
            _onLogin(r.session);
        });

        VK.Observer.subscribe('auth.logout', function(event){
            _onLogout();
        });

        _updateLoginStatus();
    }

    function _updateLoginStatus()
    {
        VK.Auth.getLoginStatus(function(r){
            if (r.session) {
                _onLogin(r.session);
            } else {
                // login() popup blocked by browser, show login button
                _onLogout();
            }
        });
    }

    function _login()
    {
        VK.Auth.login(_updateLoginStatus, _SETTINGS);
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
        if (typeof _callbacks.onLogin == "function") {
            _callbacks.onLogin();
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
        
        VK.Api.call('getUserSettings', {}, function(r)
        {
            if(r.response!==undefined){
                if(r.response != _SETTINGS) {
                    // we need loginButton for popup
                    _onLogout();
                } else {
                    VK.Api.call('getProfiles', {
                        uids: session.mid
                    }, function(r) {
                        if(r.response) {
                            _setUserInfo(r.response[0]); // <= _callbacks.onLogout here
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
        _callbacks.onLogout();
    }

    function _onUpdateSession(response)
    {
        
    }

    var _global =
    {
        init: function(callbacks) {
            _init(callbacks);
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