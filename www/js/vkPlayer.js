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

    var _playListId         = 0;

    var _playListName       = "мой первый список";

    var _autosaveTimeout    = 1000;

    var _saveState          = false;

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

    // PRIVATE METHODS

    function _init(opts)
    {
        debug(">>> _init ");

        _opts = $.extend(_opts, opts);

        //_playListInit();

        _opacity(_opts.opacity);

        _element = $("#"+_opts.id).jPlayer({
            ready           : _playListInit,
            swfPath         : _opts.swfPath,
            nativeSupport   : _opts.nativeSupport,
            warningAlerts   : _opts.warningAlerts,
            errorAlerts     : _opts.errorAlerts,
            onSoundComplete : _onSoundComplete
        });
        //_element.jPlayer("onSoundComplete", _onSoundComplete);

        _toggleShuffle(_settings.shuffle);
        _toggleRepeat(_settings.repeat);
        
        $("#"+_opts.shuffle).click(_onShuffleBut);
        $("#"+_opts.repeat ).click(_onRepeatBut);
        $("#"+_opts.previous).click(_onPrevBut);
        $("#"+_opts.next).click(_onNextBut);
        if(_opts.autoplay) {
            _playListPlay( _playItem );
        }
    }

    function _playListPlay(index)
    {
        debug(">>> _playListPlay "+index);

        if (_playListData.length==0) return;

        // TODO: add current playing trigger to play/pause
        if (_playListData[index].time==0) {
            _playListNext();
        }

        _playItem = index;
        
        _element.jPlayer("setFile", _playListData[index].mp3).jPlayer("play");

        $("#"+_opts.listitem+_playItem).removeClass("jplayer_playList_current");
        $("#"+_opts.listitem+index).addClass("jplayer_playList_current");
    }

    function _playListNext(e)
    {
        debug(">>> _playListNext");

        if (_playListData.length==0) return;

        var index = (_playItem+1 < _playListData.length) ? _playItem+1 : 0;

        if (_settings.shuffle) do {
            Math.random();
            index = Math.floor(Math.random()*_playListData.length);
        } while (index==_playItem);

        _playListPlay( index );

        if (e) {e.stopPropagation();e.preventDefault();}
    }

    function _playListPrev(e)
    {
        debug(">>> _playListPrev");

        if (_playListData.length==0) return;

        var index = (_playItem-1 >= 0) ? _playItem-1 : _playListData.length-1;

        if (_settings.shuffle) do {
            Math.random();
            index = Math.floor(Math.random()*_playListData.length);
        } while (index==_playItem);

        _playListPlay( index );

        if (e) {e.stopPropagation();e.preventDefault();}
    }

    function _playListDelete(index)
    {
        debug(">>> _playListDelete "+index);
        
        _playListData.splice(index, 1);
        _redrawPlayList();
    }

    function _playListInit()
    {
        debug(">>> _playListInit");
        
        _playList = $('#'+_opts.playlist);
        _playList.html("<h1></h1><ul></ul>");

        $("ul",_playList)
        .sortable( {cursor:"move"} )
        .bind("sortstop.sorting", _onSortStop);

        _setPlayListName(_playListName);        
        $("h1 .save", _playList).click(_savePlayList);

        _loadPlayList();
    }

    function _redrawPlayList()
    {
        debug(">>> _redrawPlayList");

        _saveState = false;

        if (_playListData.length) {
            
            var li = ""

            for (var i=0; i < _playListData.length; i++) {

                var m = Math.floor(_playListData[i].time / 60);
                var s = _playListData[i].time % 60;
                s = s<10 ? "0" + s : s;
                
                li += "<li id='jplayer_playList_item_"+i+"' index='"+i+"'>";
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
            }

            $("ul",_playList).html(li);

            $("ul li .play",   _playList).click(_onPlayBut);
            $('ul li .delete', _playList).click(_onDeleteBut);
        }

        _savePlayList();
    }

    function _loadPlayList()
    {
        debug(">>> _loadPlayList");

        $.ajax({
            url         : serverUrl.loadPlaylist,
            type        : "POST",
            dataType    : "json",
            success     : _onLoadPlaylist
        });
    }

    function _savePlayList(callback)
    {
        debug(">>> _savePlayList "+callback);

        if (_saveState) return;

        var data = {};

        data.id     = _playListId;
        data.length = _playListData.length;

        for (var i=0; i<_playListData.length; i++) {
            if (_playListData[i].time > 0) {
                data['item_artist_'+i]  = _playListData[i].artist;
                data['item_title_'+i]   = _playListData[i].title;
                data['item_mp3_'+i]     = _playListData[i].mp3;
                data['item_time_'+i]    = _playListData[i].time;
            }
        }

        var success = function(data)
        {
            if (data){
                _playListId = data.id;
                _saveState  = true;
            }
            if (callback) {
                callback(data);
            }
        }

        $.ajax({
            url         : serverUrl.savePlaylist,
            data        : data,
            type        : "POST",
            dataType    : "json",
            success     : success
        });
    }

    function _setPlayListName(name)
    {
        debug(">>> _setPlayListName "+name);
        
        _playListName = name;
        
        var html = "";
        // html += "<a href='#' class='save'>&nbsp;</a>";
        html += "<div class='text'>";
        html +=     "Играет <a href='javascript:void(0);'>"+name+"</a>";
        html += "</div>";
        $("h1", _playList).html(html);
    }

    function _toggleShuffle(b)
    {
        debug(">>> _toggleShuffle "+b);

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
        debug(">>> _toggleRepeat "+b);

        b = b!==undefined ? b : !_settings.repeat;
        _settings.repeat = b;

        if (b) {
            $("#"+_opts.repeat).addClass('active');
        } else {
            $("#"+_opts.repeat).removeClass('active');
        }
    }

    function _opacity(val)
    {
        if (!_playerContainer) {
            _playerContainer = $(_opts.playerSelector);
        }

        /*_playerContainer.css({
            opacity : val
        });*/
    }

    // EVENT HANDLERS
    
    function _onLoadPlaylist(data)
    {
        debug(">>> _onLoadPlaylist "+data);

        _saveState = true;
        if (!data) return;
        
        _playListId = data.id;
        _setPlayListName(data.name);

        _playListData = [];
        for (var i=0; i<data.PlaylistItems.length; i++) {
            var it = data.PlaylistItems[i];
            debug(it);
            _playListData.push({
                artist  : it.artist,
                title   : it.title,
                mp3     : it.mp3,
                time    : it.time
            });
        }
        _redrawPlayList();
    }

    function _onSortStop()
    {
        debug(">>> _onSortStop ");

        var arr = $(this).sortable('toArray');

        var newPLData = [];
        
        for (var i=0; i<arr.length; i++) {
            newPLData[i] = _playListData[arr[i].match(/[\d]+/g)];
        }
        _playListData = newPLData;
        _redrawPlayList();
        // TODO: _savePlayList(); find where else (after _redraw..l)
    }

    function _onPlayBut(e)
    {
        debug(">>> _onPlayBut ");

        var $this = $(this);
        var index = $this.parent().attr("index");

        _playListPlay(index);

        $this.blur();
        if (e) {e.stopPropagation();e.preventDefault();}
    }

    function _onDeleteBut(e)
    {
        debug(">>> _onDeleteBut ");

        var $this = $(this);
        var index = $this.parent().attr("index");
        _playListDelete(index);

        $this.blur();
        if (e) {e.stopPropagation();e.preventDefault();}
    }

    function _onShuffleBut(e)
    {
        debug(">>> _onShuffleBut ");

        _toggleShuffle();

        $(this).blur();
        if (e) {e.stopPropagation();e.preventDefault();}
    }

    function _onRepeatBut(e)
    {
        debug(">>> _onRepeatBut ");

        _toggleRepeat();

        $(this).blur();
        if (e) {e.stopPropagation();e.preventDefault();}
    }

    function _onNextBut(e)
    {
        debug(">>> _onNextBut ");

        _playListNext();

        $(this).blur();
        if (e) {e.stopPropagation();e.preventDefault();}
    }

    function _onPrevBut(e)
    {
        debug(">>> _onPrevBut ");

        _playListPrev();

        $(this).blur();
        if (e) {e.stopPropagation();e.preventDefault();}
    }
    
    function _onSoundComplete()
    {
        if ( (_playItem+1 != _playListData.length) || _settings.repeat) {
            _playListNext();
        }
    }

    // PUBLIC METHODS

    var _public =
    {
        
        init: function(opts)
        {
            _init(opts);
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

        pushPlaylist: function(data, play)
        {
            debug(">>> pushPlaylist()");

            /* var it = {
                artist  : data.artist,
                title   : data.title,
                mp3     : data.mp3,
                time    : data.time
            }*/

            var it = $.extend({}, _emptyPlaylistItem, data);

            if (it.mp3) {
                _playListData.push(it);
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
            _opacity(opacity);
        },

        setPlaylistName: function(header)
        {
            _setPlayListName(header);
        },

        loadPlayList: function()
        {
            _loadPlayList();
        }
    }
    
    return _public;
}();