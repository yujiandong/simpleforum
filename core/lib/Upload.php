<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\lib;

use Yii;
use yii\base\Component;
use yii\imagine\Image;

class Upload extends Component
{
	public static $avatarSizes = [
		'large'=>'73x73',
		'normal'=>'48x48',
		'small'=>'24x24',
	];

	public static function upload($file, $fileDir, $fileName, $suffix='png')
	{
		$settings = Yii::$app->params['settings'];
		$filePath = $fileDir.'/'.$fileName.'.'.$suffix;
		if ( $settings['upload_file'] === 'disable') {
			return false;
		}
		$imgInfo = @getimagesize($file->tempName);
		if($imgInfo[0] > 600) {
            $width = 600;
            $height = round($imgInfo[1]*600/$imgInfo[0]);
		} else {
            $width = $imgInfo[0];
            $height = $imgInfo[1];
		}
		$img = Image::thumbnail($file->tempName, $width, $height);
		if ($settings['upload_file'] === 'local') {
			@mkdir($fileDir, 0755, true);
//			$file->saveAs($filePath);
			$img->save($filePath);
			return Yii::getAlias('@web'.'/'.$filePath);
		} else if ($settings['upload_remote'] === 'upyun') {
			list($bucketName, $userName, $userPwd) = explode(',', $settings['upload_remote_info']);
            $upyun = new \app\lib\UpYun($bucketName, $userName, $userPwd);
//			$fh = fopen($file->tempName, 'r');
            if($upyun->writeFile('/'.$filePath, $img->get($suffix), true)){
//				fclose($fh);
				return $settings['upload_remote_url'].'/'.$filePath;
            }else{
//				fclose($fh);
                return false;
            }
		} else if ($settings['upload_remote'] === 'qiniu') {
			list($bucketName, $accessKey, $secretKey) = explode(',', $settings['upload_remote_info']);
		    $upManager = new \Qiniu\Storage\UploadManager();
		    $auth = new \Qiniu\Auth($accessKey, $secretKey);
		    $token = $auth->uploadToken($bucketName);
//			list($ret, $err) = $upManager->putFile($token, $filePath, $file->tempName);
			list($ret, $err) = $upManager->put($token, $filePath, $img->get($suffix));
			if ($err !== null) {
				Yii::error($err);
			    return false;
			}
			return $settings['upload_remote_url'].'/'.$filePath;
		}
    }

	public static function uploadAvatar($srcFile, $fileDir, $name, $suffix='png')
	{
		$settings = Yii::$app->params['settings'];

		if ($settings['upload_avatar'] === 'remote' && !empty($settings['upload_remote']) && !empty($settings['upload_remote_info']) ) {
			if ($settings['upload_remote'] === 'upyun') {
				list($bucketName, $userName, $userPwd) = explode(',', $settings['upload_remote_info']);
	            $upyun = new \app\lib\UpYun($bucketName, $userName, $userPwd);
				foreach(self::$avatarSizes as $key=>$resize) {
					list($width, $height) = explode('x', $resize);
					$img = Image::thumbnail($srcFile, $width, $height)->get($suffix);
					if (!$upyun->writeFile('/'.$fileDir. '/' . $name . '_' . $key . '.' . $suffix, $img, true)) {
						return false;
					}
				}
			} else if ($settings['upload_remote'] === 'qiniu') {
				list($bucketName, $accessKey, $secretKey) = explode(',', $settings['upload_remote_info']);
			    $upManager = new \Qiniu\Storage\UploadManager();
			    $auth = new \Qiniu\Auth($accessKey, $secretKey);
//			    $token = $auth->uploadToken($bucketName);
				foreach(self::$avatarSizes as $key=>$resize) {
					list($width, $height) = explode('x', $resize);
					$img = Image::thumbnail($srcFile, $width, $height)->get($suffix);
			    	$token = $auth->uploadToken($bucketName.':'.$fileDir. '/' . $name . '_' . $key . '.' . $suffix);
					list($ret, $err) = $upManager->put($token, $fileDir. '/' . $name . '_' . $key . '.' . $suffix, $img);
					if ($err !== null) {
						Yii::error($err);
					    return false;
					}
				}
			}
		} else {
			@mkdir($fileDir, 0755, true);
			foreach(self::$avatarSizes as $key=>$resize) {
				list($width, $height) = explode('x', $resize);
				Image::thumbnail($srcFile, $width, $height)->save($fileDir. '/' . $name . '_' . $key . '.' . $suffix);
			}
		}
		return true;
	}

}
