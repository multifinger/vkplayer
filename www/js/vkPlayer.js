/*
 * requires: jquery, jplayer
 */

var vkPlayer = function()
{
    var _playItem = 0;

    var _emptyPlaylistItem = {
        artist  : "Unknown artist",
        title   : "Without title",
        mp3     : false,
        time    : 0
    }

    var _playerContainer    = null;

    var _playListData       = []; // [ {artist, title, mp3, ogg, $this} ]

    var _element            = null;

    var _playList           = null;

    var _opts =
    {
        id              : "jplayer",
        playerSelector  : ".jp-playlist-player",
        previous        : "jplayer_previous",
        next            : "jplayer_next",
        shuffle         : "jplayer_shuffle",
        repeat          : "jplayer_repeat",
        playlist        : "playlistPlayer",
        listitem        : "jplayer_playList_item_",
        autoplay        : false,
        swfPath         : '/swf',
        nativeSupport   : false,
        errorAlerts     : window._DEBUG,
        warningAlerts   : window._DEBUG,
        opaicty         : 1
    };

    var _settings =
    {
        shuffle : false,
        repeat  : true
    }

    function _playListPlay(index)
    {
        if (_playListData.length==0) return;

        debug("play "+index);
        debug(_playListData[index].mp3);

        // TODO: add current playing trigger to play/pause
        if (_playListData[index].time==0) {
            _playListNext();
        }
        _playItem = index;
        _element.jPlayer("setFile", _playListData[index].mp3);
        _element.jPlayer("play");
    }

    function _playListNext()
    {
        if (_playListData.length==0) return;

        var index = (_playItem+1 < _playListData.length) ? _playItem+1 : 0;

        if (_settings.shuffle) do {
            Math.random();
            index = Math.floor(Math.random()*_playListData.length);
        } while (index==_playItem);

        _playListPlay( index );
    }

    function _playListPrev()
    {
        if (_playListData.length==0) return;

        var index = (_playItem-1 >= 0) ? _playItem-1 : _playListData.length-1;

        if (_settings.shuffle) do {
            Math.random();
            index = Math.floor(Math.random()*_playListData.length);
        } while (index==_playItem);

        _playListPlay( index );
    }

    function _playListDelete(index)
    {
        _playListData.splice(index, 1);
        _redrawPlayList();
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
        $("ul",_playList).html('');
        
        if (_playListData.length) {
            
            for (var i=0; i < _playListData.length; i++) {

                var m = Math.floor(_playListData[i].time / 60);
                var s = _playListData[i].time % 60;
                s = s<10 ? "0" + s : s;
                
                var li = "";
                li += "<li id='jplayer_playList_item_"+i+"'>";
                li +=     "<a class='play' href='#' title='Играть!'>";
                li +=         "&nbsp;";
                li +=     "</a>";
                li +=     "<a class='delete' href='#' title='Удалить!'>";
                li +=         "&nbsp;";
                li +=     "</a>";
                li +=     "<div class='playerTime' title='Длительность'>";
                li +=         m + ":" + s;
                li +=     "</div>";
                li +=     "<div>";
                li +=         "<b>"+_playListData[i].artist+"</b>";
                li +=         " &#0151; "+_playListData[i].title;
                li +=     "</div>";
                li += "</li>";

                
                $("ul",_playList).append(li);

                var $li = $("#"+_opts.listitem+i).data( "index", i );

                $li.click(function(e)
                {
                    $(this).blur();

                    e.preventDefault();
                    return false;
                });

                $('.play', $li).click( function(e)
                {
                    var index = $(this).parent().data("index");
                    _playListPlay(index);
                    $(this).blur();

                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                });

                $('.delete', $li).click( function(e)
                {
                    var index = $(this).parent().data("index");
                    _playListDelete(index);
                    $(this).blur();

                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                });
            }

            $("ul",_playList)
                .unbind("sortstop.sorting")
                .sortable( "destroy" )
                .sortable( {cursor:"move"} )
                .disableSelection()
                .bind("sortstop.sorting", function(e, ui)
                {
                    var arr = $(this).sortable('toArray');
                    var newPLData = [];

                    for (var i=0; i<arr.length; i++) {
                        newPLData[i] = _playListData[arr[i].match(/[\d]+/g)];
                    }
                    _playListData = newPLData;
                    _redrawPlayList();
                });
        }
    }

    function _setPlayListHeader(header)
    {
        var save = "<a href='#' class='save'>Сохранить</a>"
        $("h1", _playList).html(header+save);
        $("h1 .save", _playList).click(function(e){

            $.ajax({
                url     : '/frontend_dev.php/vkplayer/savePlaylist',
                data    : {
                    artist  : "asdasd",
                    mp3     : "qqq.mp3"
                },
                type    : "POST"
            });
            
            e.preventDefault();
            e.stopPropagation();
        });
    }

    function _toggleShuffle(b)
    {
        b = b!==undefined ? b : !_settings.shuffle;
        _settings.shuffle = b;

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
            _opts = $.extend(_opts, opts);

            _playList = $('#'+_opts.playlist);

            this.show(_opts.opacity);
            
            _element = $("#"+_opts.id)
            .jPlayer({
                ready: function() {
                    _redrawPlayList();
                    _playListConfig();
                },
                swfPath         : _opts.swfPath,
                nativeSupport   : _opts.nativeSupport,
                warningAlerts   : _opts.warningAlerts,
                errorAlerts     : _opts.errorAlerts
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
            var header = 'Играет <a href="javascript:void(0);">'+ name + '</a>';
            _setPlayListHeader(header);
            _redrawPlayList();
        },

        pushPlaylist: function(data, play)
        {
            data = $.extend({}, _emptyPlaylistItem, data);

            if (data.mp3) {
                _playListData.push(data);
                _redrawPlayList();
            }

            if (play!==undefined) {
                _playListPlay(_playListData.length-1);
            }
            
        },

        debugPlayList: function()
        {
            debug(_playListData);
        },

        show: function(opacity)
        {
            if (!_playerContainer) {
                _playerContainer = $(_opts.playerSelector);
            }

            _playerContainer.css({
                opacity : opacity
            });
        },

        setPlaylistHeader: function(header)
        {
            _setPlayListHeader(header);
        }
    }
    
    return _public;
}();