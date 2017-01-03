<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\plugins\QiniuUpload;

use Yii;
use yii\imagine\Image;
use app\models\Setting;
use app\components\Upload;
use app\components\PluginInterface;

class QiniuUpload extends Upload implements PluginInterface
{
    public $bucketName;
    public $accessKey;
    public $secretKey;
    public $url;
    private $_handler;
    private $_auth;

    public static function info()
    {
        return [
            'id' => 'QiniuUpload',
            'name' => '七牛上传',
            'description' => '将文件上传到七牛云',
            'author' => 'SimpleForum',
            'url' => 'http://simpleforum.org/',
            'version' => '1.0',
            'config' => [
                ['label'=>'空间名', 'key'=>'bucketName', 'type'=>'text', 'value_type'=>'text', 'value'=>'', 'description'=>''],
                ['label'=>'access key', 'key'=>'accessKey', 'type'=>'text', 'value_type'=>'text', 'value'=>'', 'description'=>''],
                ['label'=>'secret key', 'key'=>'secretKey', 'type'=>'text', 'value_type'=>'text', 'value'=>'', 'description'=>''],
                ['label'=>'空间URL', 'key'=>'url', 'type'=>'text', 'value_type'=>'text', 'value'=>'', 'description'=>''],
            ],
        ];
    }

    public static function install()
    {
        if ( ($setting = Setting::findOne(['key'=>'upload_remote'])) ) {
            $option = json_decode($setting->option, true);
            $option['QiniuUpload']='七牛云';
            $setting->option = json_encode($option);
            $setting->save();
        }
        return true;
    }

    public static function uninstall()
    {
        if ( ($setting = Setting::findOne(['key'=>'upload_remote'])) ) {
            $option = json_decode($setting->option, true);
            unset($option['QiniuUpload']);
            $setting->option = json_encode($option);
            $setting->save();
        }
        return true;
    }

    public function init()
    {
//        $settings = Yii::$app->params['settings'];
//        list($bucketName, $accessKey, $secretKey) = explode(',', $settings['upload_remote_info']);
//        $this->_bucketName = $bucketName;
        parent::init();
        $this->_handler = new \Qiniu\Storage\UploadManager();
        $this->_auth = new \Qiniu\Auth($this->accessKey, $this->secretKey);
    }

    public function upload($source, $target)
    {
        $settings = Yii::$app->params['settings'];
        if ( $settings['upload_file'] === 'disable') {
            return false;
        }
        $imgInfo = @getimagesize($source);
        if($imgInfo[0] > self::$maxWidth) {
            $width = self::$maxWidth;
            $height = round($imgInfo[1]*self::$maxWidth/$imgInfo[0]);
        } else {
            $width = $imgInfo[0];
            $height = $imgInfo[1];
        }
        $token = $this->_auth->uploadToken($this->bucketName);
        $format = substr(strrchr($target, '.'), 1);
//          list($ret, $err) = $upManager->putFile($token, $filePath, $file->tempName);
        list($ret, $err) = $this->_handler->put($token, $target, Image::thumbnail($source, $width, $height)->get($format));
        if ($err !== null) {
            Yii::error($err);
            return false;
        }
        return $this->url.'/'.$target;
    }

    public function uploadThumbnails($source, $target, $type=self::AVATAR)
    {
        $settings = Yii::$app->params['settings'];

        $format = substr(strrchr($target, '.'), 1);
//      $token = $auth->uploadToken($bucketName);
        foreach(self::$sizes[$type] as $key=>$resize) {
        $filePath = str_replace('{size}', $key, $target);
            list($width, $height) = explode('x', $resize);
            $token = $this->_auth->uploadToken($this->bucketName.':'.$filePath);
            list($ret, $err) = $this->_handler->put($token, $filePath, Image::thumbnail($source, $width, $height)->get($format));
            if ($err !== null) {
                Yii::error($err);
                return false;
            }
        }
        return true;
    }

}

require(__DIR__ . '/qiniu-sdk/autoload.php');
require(__DIR__ . '/qiniu-sdk/src/Qiniu/functions.php');
