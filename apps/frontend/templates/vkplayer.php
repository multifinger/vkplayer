<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <?php include_http_metas() ?>
        <?php include_metas() ?>
        <?php include_title() ?>
        <link rel="shortcut icon" href="/favicon.ico" />
        <?php include_stylesheets() ?>
        <script type="text/javascript">
            _DEBUG = <?php echo json_encode(sfConfig::get("sf_web_debug", false)); ?>;
            var serverUrl = {
                savePlaylist : <?php echo json_encode(url_for("vkplayer/savePlaylist")); ?>,
                loadPlaylist : <?php echo json_encode(url_for("vkplayer/loadPlaylist")); ?>
            }
        </script>
        <?php include_javascripts() ?>
    </head>
    <body>
        <div id="vk_api_transport"></div>
        <div class="page">
            <div class="header">
                <div class="header_l"></div>
                <div class="header_r"></div>

                <div id="jquery_jplayer"></div>
                <div class="header_logo"><a href="#">vkplayer</a></div>
                <div class="header_user" id="header_user"></div>
                <div class="header_logout" id="header_logout">Выйти</div>
                <div class="jp-playlist-player">
                    <div class="jp-interface">
                        <ul class="jp-controls">
                            <li><a href="#" id="jplayer_play" class="jp-play" tabindex="1">play</a></li>
                            <li><a href="#" id="jplayer_pause" class="jp-pause" tabindex="1">pause</a></li>
                            <li><a href="#" id="jplayer_stop" class="jp-stop" tabindex="1">stop</a></li>
                            <li><a href="#" id="jplayer_volume_min" class="jp-volume-min" tabindex="1">min volume</a></li>
                            <li><a href="#" id="jplayer_volume_max" class="jp-volume-max" tabindex="1">max volume</a></li>
                            <li><a href="#" id="jplayer_previous" class="jp-previous" tabindex="1">previous</a></li>
                            <li><a href="#" id="jplayer_next" class="jp-next" tabindex="1">next</a></li>
                            <li><a href="#" id="jplayer_shuffle" class="jp-shuffle" tabindex="1">shuffle</a></li>
                            <li><a href="#" id="jplayer_repeat" class="jp-repeat" tabindex="1">repeat</a></li>
                        </ul>
                        <div class="jp-progress">
                            <div id="jplayer_load_bar" class="jp-load-bar">
                                <div id="jplayer_play_bar" class="jp-play-bar"></div>
                            </div>
                        </div>
                        <div id="jplayer_volume_bar" class="jp-volume-bar">
                            <div id="jplayer_volume_bar_value" class="jp-volume-bar-value"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="login_button" id="login_button"></div>

            <div id="siteMenu"></div>

            <div id="vkLibrary"></div>

            <div id="playlistPlayer"></div>

        </div>
    </body>
</html>
