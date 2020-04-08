<?php
// +----------------------------------------------------------------------
// | 小牛Admin
// +----------------------------------------------------------------------
// | Website: www.xnadmin.cn
// +----------------------------------------------------------------------
// | Author: dav <85168163@qq.com>
// +----------------------------------------------------------------------

namespace app\common\lib;

use Qiniu\Auth;
use Qiniu\Config;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;

/**
 * 七牛云
 * Class Qiniu
 * @package app\common\lib
 */
class Qiniu
{
    private $ak='';
    private $sk='';
    private $bucket='';
    private $domain='';
    public function __construct()
    {
        $config = xn_cfg('upload');
        empty($this->ak) && $this->ak = $config['qiniu_ak'];
        empty($this->sk) &&  $this->sk = $config['qiniu_sk'];
        empty($this->bucket) &&  $this->bucket = $config['qiniu_bucket'];
        empty($this->domain) &&  $this->domain = $config['qiniu_domain'];
    }

    /**
     * 上传文件
     * @param $path
     * @param $name
     * @return bool
     * @throws \Exception
     */
    public function putFile($key,$filePath)
    {
        $token = $this->getToken();
        $uploadMgr = new UploadManager();
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        if ($err !== null) {
            return false;
        } else {
            return $this->domain . $ret['key'];
        }
    }

    /**
     * 删除远程文件
     * @param $key
     */
    public function delete($key)
    {
        $auth = new Auth($this->ak,$this->sk);
        $config = new Config();
        $bucketManager = new BucketManager($auth, $config);
        $bucketManager->delete($this->bucket, $key);
    }

    /**
     * 获取token
     * @return string
     */
    protected function getToken()
    {
        $auth = new Auth($this->ak,$this->sk);
        $token = $auth->uploadToken($this->bucket);
        return $token;
    }
}