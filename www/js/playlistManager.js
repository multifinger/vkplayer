/*
 * requires: jquery
 */

var playlistManager = function()
{
    var _playlist   = [];

    var _header     = 'Безымянный список воспроизведения';

    var _element    = null;

    var _global =
    {
        init: function(init_id)
        {
            _element = $("#"+init_id);
            var html =  "<h1></h1><ul></ul>";
            _element.html(html);
        },

        setList: function(list)
        {
            _playlist = list;
        },

        displayList: function()
        {
            for (var i=0; i<_playlist.length; i++) {
                var html = "";
                html += "<li>";
                html +=     "<a href='#' onclick='return false;'>";
                html +=         "<b>"+_playlist[i].artist+"</b> &#0151; "+_playlist[i].title;
                html +=     "</a>";
                html +=     "<a href='#' onclick='playlistManager.playerPush("+i+"); return false;' title='В список воспроизведения'>";
                html +=         "+";
                html +=     "</a>";
                html += "</li>";
                $("ul", _element).append(html);
            }
        },

        setHeader: function(h)
        {
            _header = h || _header;
            $("h1", _element).text(_header);
        },

        clear: function()
        {
            if(_element) _element.html('');
        },

        playerPush: function(i)
        {
            debug(">>> playlistManager.playerPush("+i+")");

            vkPlayer.pushPlaylist({
                artist  :   _playlist[i].artist,
                title   :   _playlist[i].title,
                mp3     :   _playlist[i].mp3
            });
        }
    }

    return _global;

}();