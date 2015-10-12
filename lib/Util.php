<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\lib;

use Yii;
use yii\helpers\ArrayHelper;

class Util
{
	public static function convertModelToArray($models)
	{
        if (is_array($models)) {
            $arrayMode = true;
        } else {
            $models = array($models);
            $arrayMode = false;
        }

        $result = [];
        foreach ($models as $model) {
            $attributes = ArrayHelper::toArray($model);
            $relations = [];
			if( $model instanceof yii\base\Model ) {
	            foreach ($model->getRelatedRecords() as $key => $related) {
	                if ($model->getRelation($key)) {
						if( (is_array($model->$key)) || ($model->$key instanceof yii\base\Model)){
							$relations[$key] = self::convertModelToArray($model->$key);
						} else {
							$relations[$key] = $model->$key;
						}
	                }
	            }
			}
            $all = array_merge($attributes, $relations);

            if ($arrayMode) {
                array_push($result, $all);
            } else {
                $result = $all;
			}
        }
        return $result;
    }

	public static function convertArrayToString($arr, $pre='')
	{
		$str = '['."\n";
		foreach($arr as $k=>$v) {
			if( is_string($k) ) {
				$k = '\''.$k.'\'';
			}
			if( is_int($v) ) {
			} else if( is_string($v) ) {
				$v = '\'' . $v . '\'';
			} else if( is_bool($v) ) {
				$v = ($v===true?'true':'false');
			} else if( is_array($v) ) {
				$v = self::convertArrayToString($v, $pre. '  ');
			} else {
				$v = '';
			}
			$str = $str . $pre. '  ' . $k.' => ' . $v . ','."\n";
		}
		return $str. $pre. ']';
	}

	public static function code62($x)
	{
	    $show='';
	    while($x>0) {
	        $s=$x % 62;
	        if ($s>35) {
	            $s=chr($s+61);
	        } else if($s>9&&$s<=35) {
	            $s=chr($s+55);
	        }
	        $show.=$s;
	        $x=floor($x/62);
	    }
	    return $show;
	}

	public static function shorturl($url)
	{
	    $url=crc32($url);
	    $result=sprintf("%u",$url);
	    return self::code62($result);
	}

	public static function generateRandomString($length = 32)
	{
		$str = '';
		if (extension_loaded('openssl')) {
			$str = Yii::$app->security->generateRandomString($length);
		} else {
			$possible = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_';
			$possible_len = strlen($possible);
			$i = 0;

			while ($i < $length) {
				$str .= $possible[rand(0, $possible_len-1)];
				$i++;
			}
		}
		return $str;
	}

	public static function getReferrer()
	{
		$request = Yii::$app->getRequest();
		$referrer = $request->getReferrer();
		$hostInfo = $request->getHostInfo();
		if ( !$referrer || strpos($referrer, $hostInfo) !== 0 ) {
			$referrer = null;
		} else {
			$referrer = str_replace($hostInfo, '', $referrer);
		}
		return $referrer;
	}

	public static function getRedirect()
	{
		if ( !($redirect = Yii::$app->getRequest()->get('redirect')) || !\yii\helpers\Url::isRelative($redirect) ) {
        	$redirect = null;
		}
		return $redirect;
	}
}
