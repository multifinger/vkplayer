/* Recursive copy of javascript object (properties & methods)
 * 
 * @param   o   Object
 * @return  c   Object
 */
function clone(o)
{
    if(!o || 'object' !== typeof o)  {
        return o;
    }

    var c = 'function' === typeof o.pop ? [] : {};
    
    var p, v;

    for(p in o) {
        if(o.hasOwnProperty(p)) {
            v = o[p];
            if(v && 'object' === typeof v) {
                c[p] = clone(v);
            } else {
                c[p] = v;
            }
        }
    }

    return c;
}

function debug(mes)
{
    if(window._DEBUG) {
        if(console && console.log) {
            console.log(mes);
        }
    }
}
