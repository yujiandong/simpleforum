<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2016 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;
use yii\base\Model;
use yii\imagine\Image;
use yii\web\UploadedFile;
use app\lib\Util;
use app\lib\Upload;

/**
 * UploadForm is the model behind the upload form.
 */
class UploadForm extends Model
{
    const SCENARIO_AVATAR = 1;
    const SCENARIO_UPLOAD = 2;

    /**
     * @var UploadedFile file attribute
     */
    public $file;
    public $files;
	private $_avatarSizes = [
		'large'=>'73x73',
		'normal'=>'48x48',
		'small'=>'24x24',
	];

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
        if ($this->files && $this->validate()) {
            foreach ($this->files as $file) {
				$filePath = 'upload/'. date('Ym') . '/' . date('d');
//				$fileName = $uid . '_' . time() . '.' . $file->extension;
				$fileName = $uid . '_' . Util::shorturl(microtime(true));
				$rtn = Upload::upload($file, $filePath, $fileName);
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

    public function uploadAvatar($uid)
    {
		$suffix = 'png';

        if ($this->file && $this->validate()) {
	        $name = $uid;
			$myId = strtolower(Util::shorturl($uid));
			$savePath = 'avatar/'.substr($myId,0,1).'/'.substr($myId,1,1);
//			$this->resizeAvator( $this->_avatarSizes, $this->file->tempName, $savePath, $name, $suffix);
			Upload::uploadAvatar($this->file->tempName, $savePath, $name, $suffix);
			return $savePath. '/'.$name . '_{size}.' . $suffix . '?m='.time();;
        }

        return false;
    }
/*
	private function resizeAvator($resizes, $srcFile, $savePath, $name, $suffix='png')
	{
		@mkdir($savePath, 0755, true);
		foreach($resizes as $key=>$resize) {
			list($width, $height) = explode('x', $resize);
			Image::thumbnail($srcFile, $width, $height)->save($savePath. '/' . $name . '_' . $key . '.' . $suffix);
		}
		return true;
	}
*/
}
