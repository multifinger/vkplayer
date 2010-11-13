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
        shuffle     : "jplayer_shuffle",
        repeat      : "jplayer_repeat",
        playlist    : "playlistPlayer",
        listitem    : "jplayer_playList_item_",
        autoplay    : false
    };

    var _settings =
    {
        shuffle : false,
        repeat  : true
    }

    function _playListPlay( index )
    {
        if (_playListData.length==0) return;
        
        // TODO: add current playing trigger to play/pause

        _playItem = index;
        _element.jPlayer("setFile", _playListData[index].mp3);
        _element.jPlayer("play");
    }

    function _playListNext()
    {
        if (_playListData.length==0) return;

        var index = (_playItem+1 < _playListData.length) ? _playItem+1 : 0;
        _playListPlay( index );
    }

    function _playListPrev()
    {
        if (_playListData.length==0) return;

        var index = (_playItem-1 >= 0) ? _playItem-1 : _playListData.length-1;
        _playListPlay( index );
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
        _element.jPlayer("setFile", _playListData[_playItem].mp3);
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
                $("#"+_opts.listitem+i).data( "index", i ).click( function(e)
                {
                    var index = $(this).data("index");
                    debug("call vkPlayer._playList["+index+"] onClick()");
                    _playListPlay(index);
                    $(this).blur();

                    e.preventDefault();
                    return false;
                });
            }
        }
    }

    function _toggleShuffle(b)
    {
        b = b!==undefined ? b : !_settings.shuffle;
        _settings.shuffle = b;

        debug(">>> vkPlayer._toggleShuffle("+b+")");
        if (b) {
            $("#"+_opts.shuffle).addClass('active');
        } else {
            $("#"+_opts.shuffle).removeClass('active');
        }
    }

    function _toggleRepeat(b)
    {
        b = b!==undefined ? b : !_settings.repeat;
        _settings.repeat = b;

        debug(">>> vkPlayer._toggleRepeat("+b+")");
        if (b) {
            $("#"+_opts.repeat).addClass('active');
        } else {
            $("#"+_opts.repeat).removeClass('active');
        }
    }

    var _public =
    {
        
        init: function(opts)
        {
            debug('>>> vkPlayer.init()');
            _opts = $.extend(_opts, opts);
            debug(_opts);

            _playList = $('#'+_opts.playlist);

            _element = $("#"+_opts.id)
            .jPlayer({
                ready: function() {
                    _redrawPlayList();
                    _playListConfig();
                }
            })
            .jPlayer("onSoundComplete", function() {
                if ( (_playItem+1 != _playListData.length) || _settings.repeat) {
                    _playListNext();
                }
            });

            $("#"+_opts.previous).click( function(e) {
                _playListPrev();
                $(this).blur();
                e.preventDefault();
            });

            $("#"+_opts.next).click( function(e) {
                _playListNext();
                $(this).blur();
                e.preventDefault();
            });

            _toggleShuffle(_settings.shuffle);
            $("#"+_opts.shuffle).click( function(e) {
                _toggleShuffle();
                $(this).blur();
                e.preventDefault();
            });

            _toggleRepeat(_settings.repeat);
            $("#"+_opts.repeat).click( function(e) {
                _toggleRepeat();
                $(this).blur();
                e.preventDefault();
            });
            
            if(_opts.autoplay) {
                _playListPlay( _playItem );
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