/*
 * requires: jquery, vkAPI, vkPlayer, vkLibrary
 */

function defaultAction()
{
    //vkPlayer.authorise(1);
    //vkPlayer.show(1);
    //vkPlayer.setPlaylistName("мой первый список");
    //vkPlayer.loadPlayList();
    siteMenu.init(siteMenuId, menuConfig);
    siteMenu.show(0);
}

function showMyAudio()
{
    vkAPI.getUserAudio(null, function(r) {
        if(r.response) {
            var playlist = [];
            for (var i=0; i<r.response.length; i++) {
                playlist.push({
                    artist  : r.response[i].artist,
                    title   : r.response[i].title,
                    mp3     : r.response[i].url,
                    time    : r.response[i].duration
                });
            }
            vkLibrary.init(vkLibraryId);
            vkLibrary.setList(playlist);
            vkLibrary.setHeader("Мои аудиозаписи");
            vkLibrary.displayList();
        }
    });
}

function clearPage()
{
    siteMenu.clear();
    vkLibrary.clear();
    vkPlayer.authorise(0);
    vkPlayer.clearPlaylist();
    vkPlayer.show(0);
}

var siteMenuId  = 'siteMenu';
var vkLibraryId =  'vkLibrary';

var menuConfig = [
    {
        name    : "Мои аудиозаписи",
        action  : window.showMyAudio
    }
];

$(function()
{
    vkPlayer.init({
        id      : "jquery_jplayer",
        opacity : 0
    });

    vkAPI.init({
        onLogin: defaultAction,
        onLogout: clearPage
    });

}); 