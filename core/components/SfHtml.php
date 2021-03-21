<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\components;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;

class SfHtml
{
    public static function uGroup($score)
    {
        $groups = ArrayHelper::getValue(Yii::$app->params, 'settings.groups');
        if ($groups) {
            foreach($groups as $key => $group) {
                if($group[0]>$score) {
                    break;
                }
            }
            return ' <span class="user-level user-level-'.($key+1).'">V'.($key+1).' [ '.$group[1].' ]</span>';
        }
        return '';
    }

    public static function uGroupRank($score)
    {
        $groups = ArrayHelper::getValue(Yii::$app->params, 'settings.groups');
        if ($groups) {
            foreach($groups as $key => $group) {
                if($group[0]>$score) {
                    break;
                }
            }
            return ' <span class="user-level user-level-'.($key+1).'">V'.($key+1).'</span>';
        }
        return '';
    }

    public static function uScore($score) {
        $result = '';
        $levels = [];
        if ($score > 10000) {
            $levels['gold'] = floor($score / 10000);
            $score = $score % 10000;
        }
        if ($score > 100) {
            $levels['silver'] = floor($score / 100);
            $score = $score % 100;
        }
        if ($score > 0) {
            $levels['copper'] = $score;
        }
        foreach($levels as $key=>$amount) {
            $result .= $amount . '<i class="fa fa-trophy ' . $key . '" aria-hidden="true"></i>';
        }
        return $result;
    }

    public static function uLink($username, $name='', $sep=' ', $ajaxLoad=false, $options=[])
    {
        $username = Html::encode($username);
        $url = ['user/view', 'username'=>$username];
        if( $ajaxLoad ) {
            $options['data-poload'] = Url::to($url);
        }
        if (empty($name)) {
            $text = '@' . $username;
        } else {
            $text = Html::encode($name) . $sep . '<small class="gray">@' . $username . '</small>';
        }
        return Html::a($text, $url, $options);
    }

    public static function uImg($user, $size='normal', $options=['class'=>['item-avatar']])
    {
        $styles = [
            'img-rounded' => 'rounded',
            'img-circle' => 'rounded-circle',
        ];
        $avatarStyle = ArrayHelper::getValue(Yii::$app->params, 'settings.avatar_style', 'rounded');
        return Html::img('@web/'.str_replace('{size}', $size, $user['avatar']), ArrayHelper::merge(['class'=>[ArrayHelper::getValue($styles, $avatarStyle, $avatarStyle)],'alt'=> $user['username']], $options));
    }

    public static function uImgLink($user, $size='normal', $options=['class'=>'item-avatar'], $ajaxLoad=false)
    {
        $url = ['user/view', 'username'=>$user['username']];
        if( $ajaxLoad ) {
            $options['data-poload'] = Url::to($url);
        }
        return Html::a(self::uImg($user, $size, []), $url, $options);
    }

    public static function faIcon($classes)
    {
        return '<i class="fa '.implode(' fa-', $classes).'" aria-hidden="true"></i>';
    }

    public static function afterAllPosts($view)
    {
        $hook = new SfHook(SfHook::EVENT_AFTER_ALL_POSTS);
        $event = new SfEvent(['render'=>$view]);
        $hook->trigger(SfHook::EVENT_AFTER_ALL_POSTS, $event);
        return;
    }

}
