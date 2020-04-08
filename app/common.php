<?php
// 应用公共文件

/**
 * 字节数Byte转换为KB、MB、GB、TB
 * @param int $size
 * @return string
 */
function xn_file_size($size){
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $units[$i];
}

/**
 * 驼峰命名转下划线命名
 * @param $camelCaps
 * @param string $separator
 * @return string
 */
function xn_uncamelize($camelCaps,$separator='_')
{
    return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
}

/**
 * 密码加密函数
 * @param $password
 * @return string
 */
function xn_encrypt($password)
{
    $salt = 'xiaoniu_admin';
    return md5(md5($password.$salt));
}

/**
 * 管理员操作日志
 * @param $remark
 */
function xn_add_admin_log($remark)
{
    $data = [
        'admin_id' => session('admin_auth.id'),
        'url' => request()->url(true),
        'ip' => request()->ip(),
        'remark' => $remark,
        'method' =>request()->method(),
        'param' => json_encode(request()->param()),
        'create_time' => time()
    ];
    \app\common\model\AdminLog::insert($data);
}

/**
 * 获取自定义config/cfg目录下的配置
 * 用法： xn_cfg('base') 或 xn_cfg('base.website') 不支持无限极
 * @param string|null $name
 * @param null $default
 * @return array
 */
function xn_cfg($name)
{
    if (false === strpos($name, '.')) {
        $name = strtolower($name);
        $config  = \think\facade\Config::load('cfg/'.$name, $name);
        return $config ?? [];
    }
    $name_arr    = explode('.', $name);
    $name_arr[0] = strtolower($name_arr[0]);
    $filename = $name_arr[0];
    $config  = \think\facade\Config::load('cfg/'.$filename, $filename);
    return $config[$name_arr[1]] ?? [];
}

/**
 * 根目录物理路径
 * @return string
 */
function xn_root()
{
    return app()->getRootPath() . 'public';
}

/**
 * 构建图片上传HTML 单图
 * @param string $value
 * @param string $file_name
 * @param null $water 是否添加水印 null-系统配置设定 1-添加水印 0-不添加水印
 * @param null $thumb 生成缩略图，传入宽高，用英文逗号隔开，如：200,200（仅对本地存储方式生效，七牛、oss存储方式建议使用服务商提供的图片接口）
 * @return string
 */
function xn_upload_one($value,$file_name,$water=null,$thumb=null)
{
$html=<<<php
    <div class="xn-upload-box">
        <div class="t layui-col-md12 layui-col-space10">
            <input type="hidden" name="{$file_name}" class="layui-input xn-images" value="{$value}">
            <div class="layui-col-md4">
                <div type="button" class="layui-btn webuploader-container" id="{$file_name}" data-water="{$water}" data-thumb="{$thumb}" style="width: 113px;"><i class="layui-icon layui-icon-picture"></i>上传图片</div>
                <div type="button" class="layui-btn chooseImage" data-num="1"><i class="layui-icon layui-icon-table"></i>选择图片</div>
            </div>
        </div>
        <ul class="upload-ul clearfix">
            <span class="imagelist"></span>
        </ul>
        <script>$('#{$file_name}').uploadOne();</script>
    </div>
php;
    return $html;
}

/**
 * 构建图片上传HTML 多图
 * @param string $value
 * @param string $file_name
 * @param null $water 是否添加水印 null-系统配置设定 1-添加水印 0-不添加水印
 * @param null $thumb 生成缩略图，传入宽高，用英文逗号隔开，如：200,200（仅对本地存储方式生效，七牛、oss存储方式建议使用服务商提供的图片接口）
 * @return string
 */
function xn_upload_multi($value,$file_name,$water=null,$thumb=null)
{
    $html=<<<php
    <div class="xn-upload-box">
        <div class="t layui-col-md12 layui-col-space10">
            <div class="layui-col-md8">
                <input type="text" name="{$file_name}" class="layui-input xn-images" value="{$value}">
            </div>
            <div class="layui-col-md4">
                <div type="button" class="layui-btn webuploader-container" id="{$file_name}" data-water="{$water}" data-thumb="{$thumb}" style="width: 113px;"><i class="layui-icon layui-icon-picture"></i>上传图片</div>
                <div type="button" class="layui-btn chooseImage"><i class="layui-icon layui-icon-table"></i>选择图片</div>
            </div>
        </div>
        <ul class="upload-ul clearfix">
            <span class="imagelist"></span>
        </ul>
        <script>$('#{$file_name}').upload();</script>
    </div>
php;
    return $html;
}