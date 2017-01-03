<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\components;

use Yii;
use yii\base\Object;
use yii\imagine\Image;

class Upload extends Object implements UploadInterface
{
	const TYPE_AVATAR = 'avatar';
	const TYPE_COVER = 'cover';

    public static $maxWidth = 1000;

    public static $sizes = [
		'avatar' => [
	        'large'=>'73x73',
	        'normal'=>'48x48',
	        'small'=>'24x24',
		],
		'cover' => [
	        'cover_m'=>'320x122',
	        'cover_s'=>'260x100',
		],
    ];

    public function upload($source, $target)
    {
        if ( Yii::$app->params['settings']['upload_file'] === 'disable') {
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
		$path_parts = pathinfo($target);
        @mkdir($path_parts['dirname'], 0755, true);
        Image::thumbnail($source, $width, $height)->save($target);

        return Yii::getAlias('@web/'.$target);
    }

    public function uploadThumbnails($source, $target, $type=self::TYPE_AVATAR)
    {
		$path_parts = pathinfo($target);
        @mkdir($path_parts['dirname'], 0755, true);
        foreach(self::$sizes[$type] as $key=>$resize) {
			$filePath = str_replace('{size}', $key, $target);
            list($width, $height) = explode('x', $resize);
            Image::thumbnail($source, $width, $height)->save($filePath);
        }
        return true;
    }
}
