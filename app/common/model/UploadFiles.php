<?php
// +----------------------------------------------------------------------
// | 小牛Admin
// +----------------------------------------------------------------------
// | Website: www.xnadmin.cn
// +----------------------------------------------------------------------
// | Author: dav <85168163@qq.com>
// +----------------------------------------------------------------------

namespace app\common\model;

use think\Image;
use think\Model;

class UploadFiles extends Model
{
    /**
     * 获取器 - 文件上传来自应用名
     * @param $value
     * @return mixed
     */
    public function getAppAttr($value)
    {
        $status = [0=>'前台',1=>'后台'];
        return $status[$value];
    }

    /**
     * 获取器 - 存储位置
     * @param $value
     * @return mixed
     */
    public function getStorageAttr($value)
    {
        $status = [0=>'本地',1=>'OSS',2=>'七牛'];
        return $status[$value];
    }

    /**
     * 获取器 - 格式化文件大小
     * @param $value
     * @return string
     */
    public function getFileSizeAttr($value)
    {
        return xn_file_size($value);
    }


    /**
     * 图片添加水印
     * @param $file
     * @param null $is_water
     */
    public function setWater($file,$is_water=null)
    {
        if( $is_water=='0' ) {
            return true;
        }
        $config = xn_cfg('upload');
        $image = Image::open($file);
        //图片水印
        if( $config['is_water']==1 || $is_water=='1' ) {
            $water_path = xn_root()."/".ltrim($config['water_img'],'/');
            if( !file_exists($water_path) ) {
                return false;
            }
            $image->water($water_path, $config['water_locate'], $config['water_alpha']);
            $image->save($file->getPathName());
        }
        return true;
    }

    /**
     * 生成缩略图
     * @param object $file
     * @param string $thumb 缩略图格式,如 200,200  多张用 | 好隔开
     * @param string $save_path 保存文件的路径
     */
    public function createThumb($file,$thumb,$save_path)
    {
        $thumbs = explode('|',$thumb);
        foreach ($thumbs as $w_h) {
            $arr = explode('.',$save_path);
            $w_h = explode(',',$w_h);
            $thumb_name = $arr[0].'_'.$w_h[0].'_'.$w_h[1].'.'.$arr[1];
            $image = Image::open($file);
            $image->thumb($w_h[0],$w_h[1],3);
            $image->save(xn_root()."\\" .$thumb_name);
        }
    }
}