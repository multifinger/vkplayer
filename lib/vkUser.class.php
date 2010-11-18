<?php

class vkUser
{

    protected $personalData = null;

    public function __construct()
    {
        $this->personalData = new PersonalData();
    }

    public function getId()
    {
        return $this->personalData->id;
    }

    public function getEmail()
    {
        return $this->personalData->email;
    }

    public function hydrate($personalData)
    {
        $this->personalData = $personalData;
    }

    public static function getSession()
    {
        $APP_ID     = sfConfig::get('app_vk_app_id');
        $APP_KEY    = sfConfig::get('app_vk_app_key_private');

        $session = array();
        $member = FALSE;
        $valid_keys = array('expire', 'mid', 'secret', 'sid', 'sig');

        

        $app_cookie = $_COOKIE['vk_app_' . $APP_ID];
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
            $sign .= $APP_KEY;
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