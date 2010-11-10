/*
 * requires: jquery, jplayer
 */

var vkPlayer = function()
{
    var _playItem = 0;

    var _emptyPlaylistItem = {
        artist  :   "Unknown artist",
        title   :   "Without title",
        mp3     :   false
    }

    var _playListData = []; // [ {artist, title, mp3, ogg} ]

    var _element = null;

    var _playList = null;

    var _opts =
    {
        id          : "jplayer",
        previous    : "jplayer_previous",
        next        : "jplayer_next",
        playlist    : "playlistPlayer",
        listitem    : "jplayer_playList_item_",
        autoplay    : false,
        playnext    : true
    };

    function _playListChange( index )
    {
        _playListConfig( index );
        _element.jPlayer("play");
    }

    function _playListNext()
    {
        var index = (_playItem+1 < _playListData.length) ? _playItem+1 : 0;
        _playListChange( index );
    }

    function _playListPrev()
    {
        var index = (_playItem-1 >= 0) ? _playItem-1 : _playListData.length-1;
        _playListChange( index );
    }

    function _playListConfig( index )
    {
        if (_playList) _playList.html("<h1></h1><ul></ul>");
        
        if (_playListData.length==0) return;
        
        index = index || 0;
                
        $("#"+_opts.listitem+_playItem)
        .removeClass("jplayer_playList_current")
        .parent()
        .removeClass("jplayer_playList_current");
            
        $("#"+_opts.listitem+index)
        .addClass("jplayer_playList_current")
        .parent()
        .addClass("jplayer_playList_current");
            
        _playItem = index;
        _element.jPlayer("setFile", _playListData[_playItem].mp3,
            _playListData[_playItem].ogg);
    }

    function _redrawPlayList()
    {
        debug(_playListData);

        $("ul",_playList).html('');
        
        if (_playListData.length) {
            for (var i=0; i < _playListData.length; i++) {
                var listItem = "";
                listItem += "<li>";
                listItem +=     "<a href='#' id='jplayer_playList_item_"+i+"' tabindex='1'>";
                listItem +=         "<b>"+_playListData[i].artist+"</b>";
                listItem +=         " &#0151; "+_playListData[i].title;
                listItem +=     "</a>";
                listItem += "</li>";

                $("ul",_playList).append(listItem);
                $("#"+_opts.listitem+i).data( "index", i ).click( function(e) {
                    
                    var index = $(this).data("index");
                    debug("call vkPlayer._playList["+index+"] onClick()");
                    if (_playItem != index) {
                        _playListChange( index );
                    } else {
                        _element.jPlayer("play");
                    }
                    $(this).blur();

                    e.stopPropagation();
                    return false;
                });
            }
        }
    }

    var _public =
    {
        
        init: function(opts)
        {
            debug('>>> vkPlayer.init()');

            debug(_opts);

            _opts = $.extend(_opts, opts);

            _playList = $('#'+_opts.playlist);

            _element = $("#"+_opts.id)
            .jPlayer({
                ready: function() {
                    _redrawPlayList();
                    _playListConfig();
                }
            })
            .jPlayer("onSoundComplete", function() {
                if (_opts.playnext) {
                    _playListNext();
                }
            });

            $("#"+_opts.previous).click( function() {
                _playListPrev();
                $(this).blur();
                return false;
            });

            $("#"+_opts.next).click( function() {
                _playListNext();
                $(this).blur();
                return false;
            });
            
            if(_opts.autoplay) {
                _playListChange( _playItem );
            } else {
                _playListConfig( _playItem );
            }
        },

        next: function()
        {
            _playListNext();
        },

        prev: function()
        {
            _playListPrev()();
        },

        loadList: function(list)
        {
            _playListData = list;
            _redrawPlayList();
        },

        clearPlaylist: function()
        {
            _playListData = [];
            _redrawPlayList();
        },

        createEmptyPlaylist: function()
        {
           
        },

        createPlaylist: function(name)
        {
            var h1 = 'Играет <a href="javascript:void(0);">'+ name + '</a>';
            $('h1', _playList).html(h1);
            _redrawPlayList();
            
        },

        pushPlaylist: function(data)
        {
            debug(">>> vkPlayer.pushPlaylist");
            data = $.extend({}, _emptyPlaylistItem, data);

            if (data.mp3) {
                _playListData.push(data);
                debug(_playListData);
                _redrawPlayList();
            }
            
        }
    }
    
    return _public;
}();