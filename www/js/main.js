/*
 * requires: jquery, vkAPI, vkPlayer, playlistManager
 */

function defaultAction()
{
    siteMenu.init(siteMenuId, menuConfig);
    siteMenu.show(0);
}

function showMyAudio()
{
    debug('>>> showMyAudio()');

    vkAPI.getUserAudio(null, function(r) {
        if(r.response) {
            var playlist = [];
            for (var i=0; i<r.response.length; i++) {
                playlist.push({
                    artist: r.response[i].artist,
                    title:  r.response[i].title,
                    mp3:    r.response[i].url
                });
            }
            playlistManager.init(playlistManagerId);
            playlistManager.setList(playlist);
            playlistManager.setHeader("Мои аудиозаписи");
            playlistManager.displayList();
            vkPlayer.createPlaylist('мой список');
        }
    });
}

function clearPage()
{
    siteMenu.clear();
    playlistManager.clear();
    vkPlayer.clearPlaylist();
}


var siteMenuId          = 'siteMenu';
var playlistManagerId   =  'playlistManager';

var menuConfig = [
    {
        name:   "Мои аудиозаписи",
        action: showMyAudio
    }
];

$(function()
{
    vkPlayer.init({
        id      : "jquery_jplayer"
    });
    vkAPI.init({
        onLogin: defaultAction,
        onLogout: clearPage
    });

}); 