# CodeIgniter Library: Steam API

**ci-steam-api**

## About this library

This CodeIgniter's library connects with the Steam API.

Its usage is recommended for CodeIgniter 2 or greater.  

![Sign in through STEAM](http://cdn.steamcommunity.com/public/images/signinthroughsteam/sits_large_border.png)

## Usage

```php
$this->load->library('SteamApi');

echo $this->steamapi->getLoginLinkCode( site_url('steam/info') );

// Note: the steam user id of the logged in user is in a session variable.

$this->steamapi->getAppNews(STEAM_GAME_ID);

$this->steamapi->getAppGlobalAchivements(STEAM_GAME_ID);

$this->steamapi->getPlayerSummary(STEAM_USER_ID);

$this->steamapi->getFriendList(STEAM_USER_ID);

$this->steamapi->getPlayerAchivements(STEAM_USER_ID, STEAM_GAME_ID);

$this->steamapi->getUserStatsForGame(STEAM_USER_ID, STEAM_GAME_ID);

$this->steamapi->getOwnedGames(STEAM_USER_ID);

$this->steamapi->getRecentlyPlayedGames(STEAM_USER_ID);

$this->steamapi->isPlayingSharedGame(STEAM_USER_ID, STEAM_GAME_ID);
```

![Ale Mohamad](http://alemohamad.com/github/logo2012am.png)
