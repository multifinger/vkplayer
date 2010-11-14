/*
 * requires: jquery
 */

var siteMenu = function()
{

    var _element    = null;

    var _config     = [];

    var _global =
    {
        init: function(init_id, config)
        {
            _element = $("#"+init_id);
            _config = config;

            var html =  "";
            html += "<ul>";
            for(var i=0; i<_config.length; i++) {
                html += "<li><a href='#' class='m"+i+"'>"+_config[i].name+"</a></li>";             
            }
            html += "</ul>";
            _element.html(html);
            
            for(var j in _config) {
                $(".m"+j, _element).click(function(e){
                    var menuItemId = $(this).attr('class').match(/[\d]+/);
                    _global.show(menuItemId);
                    e.preventDefault();
                    e.stopPropagation();
                });
            }
        },

        clear: function()
        {
            if(_element) _element.html('');
        },

        show: function(i)
        {
            $("li", _element).removeClass("current").children().removeClass("current");
            $(".m"+i, _element).addClass("current").parent().addClass("current");
            _config[i].action();
        }
    }

    return _global;

}();