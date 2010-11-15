<?php

class vkAPI
{

    private static $APP_ID            = "1998251";

    private static $APP_PUBLIC_KEY    = "xVIlqIucGX";

    private static $APP_PRIVATE_KEY   = "wd21lt7eGMda0LfLxWap";

    public static function authOpenAPIMember()
    {
        $session = array();
        $member = FALSE;
        $valid_keys = array('expire', 'mid', 'secret', 'sid', 'sig');
        $app_cookie = $_COOKIE['vk_app_' . self::$APP_ID];
        
        if ($app_cookie) {
            $session_data = explode('&', $app_cookie, 10);
            foreach ($session_data as $pair) {
                list($key, $value) = explode('=', $pair, 2);
                if (empty($key) || empty($value) || !in_array($key, $valid_keys)) {
                    continue;
                }
                $session[$key] = $value;
            }
            foreach ($valid_keys as $key) {
                if (!isset($session[$key]))
                    return $member;
            }
            ksort($session);

            $sign = '';
            foreach ($session as $key => $value) {
                if ($key != 'sig') {
                    $sign .= ( $key . '=' . $value);
                }
            }
            $sign .= self::$APP_PRIVATE_KEY;
            $sign = md5($sign);
            if ($session['sig'] == $sign && $session['expire'] > time()) {
                $member = array(
                  'id' => intval($session['mid']),
                  'secret' => $session['secret'],
                  'sid' => $session['sid']
                );
            }
        }
        return $member;
    }
    
}