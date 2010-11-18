/*
 * requires: jquery
 */

var vkLibrary = function()
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
            var html = "";
            
            for (var i=0; i<_playlist.length; i++) {

                var min = Math.floor(_playlist[i].time / 60);
                var sec = _playlist[i].time % 60;
                sec = sec<10 ? "0" + sec : sec;

                html += "<li>";
                html +=     "<a class='playerAdd' href='#' onclick='vkLibrary.playerPush("+i+"); return false;' title='Добавить в плейлист'>";
                html +=         "&nbsp;";
                html +=     "</a>";
                html +=     "<a class='playerPlay' href='#' onclick='vkLibrary.playerPush("+i+", true); return false;' title='Добавить и проиграть'>";
                html +=         "&nbsp;";
                html +=     "</a>";
                html +=     "<div class='playerTime' title='Длительность'>";
                html +=         min + ":" + sec;
                html +=     "</div>";
                html +=     "<div>";
                html +=         "<b>"+_playlist[i].artist+"</b> &#0151; "+_playlist[i].title;
                html +=     "</div>";
                html += "</li>";
            }
            
            $("ul", _element).html(html);
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

        playerPush: function(i, play)
        {
            vkPlayer.pushPlaylist({
                artist  :   _playlist[i].artist,
                title   :   _playlist[i].title,
                mp3     :   _playlist[i].mp3,
                time    :   _playlist[i].time
            }, play);
        }
    }

    return _global;

}();