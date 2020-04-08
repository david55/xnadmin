
/**
 * 弹出msg，自动关掉
 * @param tip
 */
function xn_msg(msg){
    var time = arguments[1] ? arguments[1]*1000 : 2000;
    layer.msg(msg, {
        time: time
    });
}

/**
 * 弹出对话窗  不会自动关掉
 * @param msg
 */
function xn_alert(msg){
    var icon = arguments[1] ? arguments[1] : '';
    var btn_text = arguments[2] ? arguments[2] : '确定';
    if( icon!='' ) {
        layer.open({
            content: msg,
            skin: 'layui-layer-black',
            btn: btn_text,
            icon: icon,
            yes: function(index){
                layer.close(index);
            }
        });
    } else {
        layer.open({
            content: msg,
            skin: 'layui-layer-black',
            btn: btn_text,
            yes: function(index){
                layer.close(index);
            }
        });
    }
}

/**
 * 确定后刷新
 * @param msg
 */
function xn_alert_reload(msg){
    var icon = arguments[1] ? arguments[1] : '';
    var btn_text = arguments[2] ? arguments[2] : '确定';
    if( icon!='' ) {
        layer.open({
            content: msg,
            skin: 'layui-layer-black',
            btn: btn_text,
            icon: icon,
            yes: function(index){
                location.reload();
            }
        });
    } else {
        layer.open({
            content: msg,
            skin: 'layui-layer-black',
            btn: btn_text,
            yes: function(index){
                location.reload();
            }
        });
    }
}

/**
 * 点击确定后跳转URL
 * @param msg
 * @param btn_text
 * @param btn_url
 */
function xn_alert_gourl(msg,btn_url){
    var btn_text = arguments[2] ? arguments[2] : '确定';
    layer.open({
        content: msg,
        skin: 'layui-layer-black',
        btn: btn_text,
        yes: function(){
            window.location.href = btn_url;
        }
    });
}