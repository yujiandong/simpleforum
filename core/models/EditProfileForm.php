<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;
use app\models\UserInfo;

/**
 * Edit profile form
 */
class EditProfileForm extends \yii\base\Model
{
    public $name;
    public $website;
    public $about;
    private $_user;

    public function rules()
    {
        return [
            [['name', 'website', 'about'], 'trim'],
            ['name', 'required'],
            ['name', 'string', 'length' => [4, 40]],
            ['name', 'nameFilter'],
            ['website', 'url', 'defaultScheme' => 'http'],
            ['website', 'match', 'pattern' => '/^[^<>]+$/i'],
            ['website', 'string', 'max' => 100],
            ['about', 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Name'),
            'website' => Yii::t('app', 'Website'),
            'about' => Yii::t('app', 'Bio'),
        ];
    }

    public function nameFilter($attribute, $params)
    {
        if( empty(Yii::$app->params['settings']['name_filter']) ) {
            return;
        }
        $filters = explode(',', Yii::$app->params['settings']['name_filter']);
        foreach($filters as $filter) {
            $pattern = str_replace('*', '.*', $filter);
            $result = preg_match('/^' . $pattern . '$/is', $this->$attribute);
            if ( !empty($result) ) {
                $this->addError($attribute, Yii::t('app', '{attribute} cannot contain "{value}".', ['attribute'=>Yii::t('app', 'Name'), 'value'=>str_replace('*', '', $filter)]));
                return;
            }
        }
    }

    public function save()
    {
        $me = $this->_user;
        $me->name = $this->name;
        $userInfo = $me->userInfo;
        $userInfo->scenario = UserInfo::SCENARIO_EDIT;
        $userInfo->website = $this->website;
        $userInfo->about = $this->about;

        if ( ($rtnCd = $me->save()) && ($rtnCd = $userInfo->save()) ) {

            (new History([
                'user_id' => $me->id,
                'action' => History::ACTION_EDIT_PROFILE,
                'action_time' => $me->updated_at,
                'ext' => '',
            ]))->save(false);
        }

        return $rtnCd;
    }

	public function apply()
	{
        $this->_user = Yii::$app->getUser()->getIdentity();

        if ( !$this->validate() ) {
            $result = ['editProfileOK', implode('<br />', $this->getFirstErrors())];
        } else if ( !$this->save() ) {
            $result = ['editProfileOK', Yii::t('app', 'Error Occurred. Please try again.')];
        } else {
            $result = ['editProfileOK', Yii::t('app', '{attribute} has been changed successfully.', ['attribute' => Yii::t('app', 'Profile')])];
        }
        return $result;
	}


}
