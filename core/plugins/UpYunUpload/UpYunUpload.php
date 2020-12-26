<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\plugins\UpYunUpload;

use Yii;
use yii\imagine\Image;
use app\models\Setting;
use app\components\Upload;
use app\components\PluginInterface;

class UpYunUpload extends Upload implements PluginInterface
{
    public $bucketName;
    public $userName;
    public $userPwd;
    public $url;
    private $_handler;

    public static function info()
    {
        return [
            'id' => 'UpYunUpload',
            'name' => '又拍云上传',
            'description' => '将文件上传到又拍云',
            'author' => 'SimpleForum',
            'url' => 'http://simpleforum.org/',
            'version' => '1.0',
            'config' => [
                ['label'=>'空间名', 'key'=>'bucketName', 'type'=>'text', 'value_type'=>'text', 'value'=>'', 'description'=>''],
                ['label'=>'操作员', 'key'=>'userName', 'type'=>'text', 'value_type'=>'text', 'value'=>'', 'description'=>''],
                ['label'=>'密码', 'key'=>'userPwd', 'type'=>'text', 'value_type'=>'text', 'value'=>'', 'description'=>''],
                ['label'=>'空间URL', 'key'=>'url', 'type'=>'text', 'value_type'=>'text', 'value'=>'', 'description'=>''],
            ],
        ];
    }

    public static function install()
    {
        if ( ($setting = Setting::findOne(['key'=>'upload_remote'])) ) {
            $option = json_decode($setting->option, true);
            $option['UpYunUpload']='又拍云';
            $setting->option = json_encode($option);
            $setting->save();
    }
        return true;
    }

    public static function uninstall()
    {
        if ( ($setting = Setting::findOne(['key'=>'upload_remote'])) ) {
            $option = json_decode($setting->option, true);
            unset($option['UpYunUpload']);
            $setting->option = json_encode($option);
            $setting->save();
        }
        return true;
    }

    public function init()
    {
        parent::init();
        $this->_handler = new UpYun($this->bucketName, $this->userName, $this->userPwd);
    }

    public function upload($source, $target)
    {
        $settings = Yii::$app->params['settings'];
        if ( $settings['upload_file'] === 'disable') {
            return false;
        }
        $imgInfo = @getimagesize($source);
        if($imgInfo[0] > 600) {
            $width = 600;
            $height = round($imgInfo[1]*600/$imgInfo[0]);
        } else {
            $width = $imgInfo[0];
            $height = $imgInfo[1];
        }
        $format = substr(strrchr($target, '.'), 1);
        $img = Image::thumbnail($source, $width, $height)->get($format);
//          $fh = fopen($file->tempName, 'r');
        if($this->_handler->writeFile('/'.$target, $img, true)){
//              fclose($fh);
            return $this->url.'/'.$target;
        }else{
//              fclose($fh);
            return false;
        }
    }

    public function uploadThumbnails($source, $target, $type=self::AVATAR)
    {
        $format = substr(strrchr($target, '.'), 1);
        foreach(self::$sizes[$type] as $key=>$resize) {
            $filePath = str_replace('{size}', $key, $target);
            list($width, $height) = explode('x', $resize);
            $img = Image::thumbnail($source, $width, $height)->get($format);
            if (!$this->_handler->writeFile('/'.$filePath, $img, true)) {
                return false;
            }
        }
        return true;
    }

}
