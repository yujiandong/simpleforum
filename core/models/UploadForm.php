<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;
use yii\base\Model;
use app\components\Util;
use app\components\Upload;

/**
 * UploadForm is the model behind the upload form.
 */
class UploadForm extends Model
{
    const SCENARIO_AVATAR = 1;
    const SCENARIO_UPLOAD = 2;

    public $file;
    public $files;
    private $_uploader;

    public function __construct($uploader, $config=[])
    {
        $this->_uploader = $uploader;
        parent::__construct($config);
    }

/*   public function getUploader($space)
    {
        if($space == 'local') {
            $classname = 'app\components\Upload';
        } else {
            $classname = 'app\plugins\\'.$space.'\\'.ucfirst($space);
        }
        return new $classname();
    }
*/
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_AVATAR] = ['file'];
        $scenarios[self::SCENARIO_UPLOAD] = ['files'];
        return $scenarios;
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        $params = [
            'extensions' => 'png,jpg,gif,jpeg',
            'mimeTypes'=>'image/jpeg,image/png,image/gif',
            'maxSize'=>2*1024*1024,
            'minWidth' => 30,
            'maxWidth' => 2000,
            'minHeight' => 30,
            'maxHeight' => 2000
        ];
        return [
            [['file','files'], 'required'],
            extension_loaded('fileinfo')? ['file', 'image'] + $params :  ['file', 'checkImage', 'params' => $params],
            extension_loaded('fileinfo')? ['files', 'image', 'maxFiles' => 4] + $params : ['files', 'checkImage', 'params' => ['maxFiles' => 4]+$params],
        ];
    }

    public function attributeLabels()
    {
        return [
            'file' => '选择一个图片文件',
        ];
    }

    public function checkImage($attribute, $params)
    {
        $files = $this->$attribute;
        if(!empty($params['maxFiles']) && count($files) > $params['maxFiles']) {
            $this->addError($attribute, '一次最多只能上传'.$params['maxFiles'].'个图片');
            return false;
        }

        if(empty($params['maxFiles'])) {
            $files = [$files];
        }

        foreach($files as $file) {
            if(!empty($params['extensions']) && !in_array($file->getExtension(), explode(",", $params['extensions']))){
                $this->addError($attribute, '该图片格式不允许上传，只支持'. $params['extensions']);
                return;
            }
            if(!empty($params['maxSize']) && $file->size > $params['maxSize']) {
                $this->addError($attribute, '该图片太大，不能超过'. $params['maxSize']/1024 . 'KB');
                return;
            }
            $imgInfo = @getimagesize($file->tempName);
            if(!empty($params['extensions']) && !$imgInfo){
                $this->addError($attribute, '该图片格式不允许上传，只支持' . $params['extensions']);
                return;
            }
            if(!empty($params['mimeTypes']) && !in_array($imgInfo['mime'], explode(",", $params['mimeTypes']))){
                $this->addError($attribute, '该图片格式不允许上传，只支持' . $params['extensions']);
                return;
            }
            if($imgInfo[0] < $params['minWidth'] || $imgInfo[0] > $params['maxWidth'] || $imgInfo[1] < $params['minHeight'] || $imgInfo[1] > $params['maxHeight']) {
                $this->addError($attribute, '该图片尺寸不符合条件('. $params['minWidth'] . 'x' . $params['minHeight'] . ' - ' . $params['maxWidth'] . 'x'. $params['maxHeight'] . ')');
                return;
            }
        }
    }

    public function upload($uid)
    {
        $suffix = 'jpg';
//        $settings = Yii::$app->params['settings'];
//        $uploader = $this->getUploader($settings['upload_file']=='local'?'local':$settings['upload_remote']);
        if ($this->files && $this->validate()) {
            foreach ($this->files as $file) {
                $filePath = 'upload/'. date('Ym') . '/' . date('d');
//              $fileName = $uid . '_' . time() . '.' . $file->extension;
                $fileName = $uid . '_' . Util::shorturl(microtime(true));
                $rtn = $this->_uploader->upload($file->tempName, $filePath.'/'.$fileName.'.'.$suffix);
                if ($rtn) {
                    $result[] = $rtn;
                } else {
                    return false;
                }
            }
            return $result;
        }

        return false;
    }

    public function uploadAvatar($uid, $type=Upload::TYPE_AVATAR)
    {
        $suffix = 'jpg';

//        $settings = Yii::$app->params['settings'];
//        $uploader = $this->getUploader($settings['upload_avatar']=='local'?'local':$settings['upload_remote']);
        if ($this->file && $this->validate()) {
            $name = $uid;
            $myId = strtolower(Util::shorturl($uid));
            $savePath = 'avatar/'.substr($myId,0,1).'/'.substr($myId,1,1);
			$filePath = $savePath. '/'.$name . '_{size}.' . $suffix;
            $this->_uploader->uploadThumbnails($this->file->tempName, $filePath, $type);
            return $filePath . '?m='.time();
        }

        return false;
    }
}
