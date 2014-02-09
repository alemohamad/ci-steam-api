<?php

/**
 * Steam API Library
 * 
 * With this CodeIgniter library you can log in through Steam,
 * and get some information about the user and their games.
 * 
 * https://developer.valvesoftware.com/wiki/Steam_Web_API
 */

require 'LightOpenID.php';

class SteamApi
{
    private $CI;
    private $apiKey;
    private $signinBtn;
    private $openId;

    private static $buffer = array();

    function __construct() {
        # Load the config file
        $this->CI =& get_instance();
        $this->CI->load->config('steam_api');
        $apiKey = $this->CI->config->item('steam_api_key');
        $this->signinBtn = $this->CI->config->item('steam_sign_in_button');

        # Set API key if available
        if ( $apiKey != null ) {
            $this->apiKey = $apiKey;
        } else {
            echo "Set a Steam API key correctly in the config file.";
        }

		// Get your domain name
        $this->openId = new LightOpenID( $_SERVER['HTTP_HOST'] );
    }

    public function getLoginLinkCode($redirectUrl = '') {
        if(empty($redirectUrl)) {
            return "You must define an URL to the callback redirect.";
        }

        try {
            if(!$this->openId->mode) {
                $this->openId->identity = 'http://steamcommunity.com/openid';

                $loginLink = '<a href="' . $this->openId->authUrl() . '"><img src="http://cdn.steamcommunity.com/public/images/signinthroughsteam/';

                if($this->signinBtn == 'small'){
                    $loginLink .= 'sits_small.png';
                } elseif($this->signinBtn == 'no-border') {
                    $loginLink .= 'sits_large_noborder.png';
                } else {
                    $loginLink .= 'sits_large_border.png';
                }

                $loginLink .= '" alt=""></a>';

                return $loginLink;
            } elseif($this->openId->mode == 'cancel') {
                return 'User has canceled authentication!';
            } else {
                // login successfull => redirect
                $this->getUserID();
                redirect($redirectUrl);
            }
        } catch(ErrorException $e) {
            echo $e->getMessage();
        }
    }

    public function getUserID() {
        if($this->openId->validate()) {
            $id = $this->openId->identity;
            $ptn = "/^http:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)$/";
            preg_match($ptn, $id, $matches);

            $data['steam_id'] = $matches[1];
            $this->CI->session->set_userdata('steam', $data);

            return $data['steam_id'];
        } else {
            return "User is not logged in.";
        }
    }

    public function getAppNews($appID, $count = 3, $maxlength = 300, $format = 'json') {
        $url = 'http://api.steampowered.com/ISteamNews/GetNewsForApp/v0002/?appid=' . $appID . '&count=' . $count . '&maxlength=' . $maxlength . '&format=' . $format;
        $urlResult = @file_get_contents($url);
        return json_decode($urlResult);
    }

    public function getAppGlobalAchivements($appID, $format = 'json') {
        $url = 'http://api.steampowered.com/ISteamUserStats/GetGlobalAchievementPercentagesForApp/v0002/?gameid=' . $appID . '&format=' . $format;
        $urlResult = @file_get_contents($url);
        return json_decode($urlResult);
    }

    public function getPlayerSummary($userID, $format = 'json') {
        $url = 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=' . $this->apiKey . '&steamids=' . $userID . '&format=' . $format;
        $urlResult = @file_get_contents($url);
        return json_decode($urlResult);
    }

    public function getFriendList($userID, $relationship = 'friend', $format = 'json') {
        $url = 'http://api.steampowered.com/ISteamUser/GetFriendList/v0001/?key=' . $this->apiKey . '&steamid=' . $userID . '&relationship=' . $relationship . '&format=' . $format;
        $urlResult = @file_get_contents($url);
        return json_decode($urlResult);
    }

    public function getPlayerAchivements($userID, $appID, $language = 'english', $format = 'json') {
        $url = 'http://api.steampowered.com/ISteamUserStats/GetPlayerAchievements/v0001/?appid=' . $appID . '&key=' . $this->apiKey . '&steamid=' . $userID . '&l=' . $language . '&format=' . $format;
        $urlResult = @file_get_contents($url);
        return json_decode($urlResult);
    }

    public function getUserStatsForGame($userID, $appID, $language = 'english', $format = 'json') {
        $url = 'http://api.steampowered.com/ISteamUserStats/GetUserStatsForGame/v0002/?appid=' . $appID . '&key=' . $this->apiKey . '&steamid=' . $userID . '&l=' . $language . '&format=' . $format;
        $urlResult = @file_get_contents($url);
        return json_decode($urlResult);
    }

    public function getOwnedGames($userID, $include_appinfo = 1, $format = 'json') {
        // http://media.steampowered.com/steamcommunity/public/images/apps/APPID/IMG_ICON_URL.jpg
        // http://media.steampowered.com/steamcommunity/public/images/apps/APPID/IMG_LOGO_URL.jpg
        $url = 'http://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key=' . $this->apiKey . '&steamid=' . $userID . '&include_appinfo=' . $include_appinfo . '&format=' . $format;
        $urlResult = @file_get_contents($url);
        return json_decode($urlResult);
    }

    public function getRecentlyPlayedGames($userID, $limit = 3, $format = 'json') {
        $url = 'http://api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/v0001/?key=' . $this->apiKey . '&steamid=' . $userID . '&count=' . $limit . '&format=' . $format;
        $urlResult = @file_get_contents($url);
        return json_decode($urlResult);
    }

    public function isPlayingSharedGame($userID, $appIDplaying, $format = 'json') {
        $url = 'http://api.steampowered.com/IPlayerService/IsPlayingSharedGame/v0001/?key=' . $this->apiKey . '&steamid=' . $userID . '&appid_playing=' . $appIDplaying . '&format=' . $format;
        $urlResult = @file_get_contents($url);
        return json_decode($urlResult);
    }

}
