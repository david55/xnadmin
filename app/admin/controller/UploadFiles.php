<?php
// +----------------------------------------------------------------------
// | 小牛Admin
// +----------------------------------------------------------------------
// | Website: www.xnadmin.cn
// +----------------------------------------------------------------------
// | Author: dav <85168163@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\lib\Oss;
use app\common\lib\Qiniu;
use OSS\Core\OssException;
use think\exception\ValidateException;
use think\facade\Filesystem;
use app\common\model\UploadFiles as UploadFilesModel;

class UploadFiles extends AdminBase
{
    protected $noAuth = ['upload','select'];

    public function index()
    {
        $param = $this->request->param();
        $model = new UploadFilesModel();
        //构造搜索条件
        if( $param['storage']!='' ) {
            $model = $model->where('storage',$param['storage']);
        }
        if( $param['start_date']!=''&&$param['end_date']!='' ) {
            $model = $model->whereBetweenTime('create_time',$param['start_date'],$param['end_date']);
        }
        $list = $model->order('id desc')->paginate(['query' => $param]);
        return view('',['list'=>$list]);
    }

    public function select()
    {
        $list = UploadFilesModel::where('app',1)->where('user_id',$this->getAdminId())->order('id desc')->paginate(['query' => $this->request->param()]);
        $num = intval($this->request->get('num'));
        return view('',['list'=>$list,'num'=>$num]);
    }

    /**
     * 删除文件
     */
    public function delete()
    {
        $id = intval($this->request->get('id'));
        !($id>0) && $this->error('参数错误');
        $file_data = UploadFilesModel::find($id);
        $storage = $file_data->getData('storage');
        if( $storage == 1 ) {
            $key = explode(xn_cfg('upload.oss_endpoint').'/', $file_data->url);
            //删除远程oss文件
            if( isset($key[1]) ) {
                $oss = new Oss();
                $oss->delete($key[1]);
            }
        } elseif ( $storage == 2 ) {
            //删除七牛远程文件
            $domain = xn_cfg('upload.qiniu_domain');
            $key = str_replace($domain,'',$file_data->url);
            $qiniu = new Qiniu();
            $qiniu->delete($key);
        } else {
            //删除本地服务器文件
            $file_path = xn_root()."/".ltrim($file_data->url,'/');
            if( file_exists($file_path) ) {
                unlink($file_path);
            }
        }
        UploadFilesModel::destroy($id);
        xn_add_admin_log('删除文件');
        $this->success('删除成功');
    }

    /**
     * 文件上传
     * @return \think\response\Json
     * @throws OssException
     */
    public function upload()
    {
        try {
            $param = $this->request->param();
            $file = request()->file('file');
            //文件后缀名
            $ext = $file->getOriginalExtension();
            //配置信息
            $config = xn_cfg('upload');
            //存储类型
            $storage = $config['storage'];

            //图片水印处理
            if( UploadFilesModel::setWater($file,$param['water']) === false ) {
                return json(['code'=>0,'msg'=>'水印配置有误']);
            }

            if( $storage==1 ) {
                //上传到阿里云oss
                $oss = new Oss();
                $file_path = $oss->putFile($file,$err);
                if( !$file_path ) {
                    return json(['code'=>0,'msg'=>$err]);
                }
            } elseif ($storage==2){
                //上传到七牛云
                $qiniu = new Qiniu();
                $savename = date('Ymd') . '/' . md5((string) microtime(true)).'.'. $ext;
                $file_path = $qiniu->putFile($savename, $file->getRealPath());
                if( !$file_path ) {
                    return json(['code'=>0,'msg'=>'上传失败']);
                }
            } else {
                //上传到本地服务器
                $folder = config('filesystem.disks.folder'); //根路径文件夹
                $savename = Filesystem::disk('public')->putFile($folder,$file);
                if (!$savename) {
                    return json(['code'=>0,'msg'=>'上传失败']);
                }
                $file_path = '/' . str_replace("\\","/",$savename);

                //缩略图
                if( $param['thumb']!='' ) {
                    UploadFilesModel::createThumb($file,$param['thumb'],$savename);
                }
            }

            //记录入文件表
            $insert_data = [
                'url' => $file_path,
                'storage' => $storage,
                'app' => 1,
                'user_id' => $this->getAdminId(),
                'file_name' => $file->getOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => 'image',
                'extension' => strtolower(pathinfo($file->getOriginalName(), PATHINFO_EXTENSION)),
                'create_time' => time()
            ];
            UploadFilesModel::create($insert_data);

            return json(['code'=>1,'file'=>$file_path]);
        } catch (ValidateException $e) {
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
}
