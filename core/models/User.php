<?php
/**
 * @link http://simpleforum.org/
 * @copyright Copyright (c) 2015 SimpleForum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;
use app\components\Util;

class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_BANNED = 0;
    const STATUS_INACTIVE = 8;
    const STATUS_ADMIN_VERIFY = 9;
    const STATUS_ACTIVE = 10;
    const ROLE_MEMBER = 0;
    const ROLE_ADMIN = 10;
//    const USERNAME_PATTERN = '/^[a-zA-Z0-9\x{4e00}-\x{9fa5}]*$/u';
//    const USER_MENTION_PATTERN = '/\B\@([a-zA-Z0-9\x{4e00}-\x{9fa5}]{4,16})/u';
    const USERNAME_PATTERN = '/^[a-zA-Z0-9_]*$/u';
    const USER_MENTION_PATTERN = '/\B\@([a-zA-Z0-9_]{4,16})/u';

    public static $roleOptions = [
        0 => 'Member Group',
        10 => 'Admin Group',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['avatar', 'safe'],
//            ['role', 'default', 'value' => self::ROLE_MEMBER],
//            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'integer', 'max'=>self::STATUS_ACTIVE, 'min'=>self::STATUS_BANNED],
        ];
    }

    public function getNoticeCount()
    {
        return (int)Notice::find()->where(['status'=>0, 'target_id' => $this->id])->count('id');
    }

    public function getSystemNoticeCount()
    {
        return Notice::find()->where(['status'=>0, 'target_id' => $this->id])->andWhere(['<>', 'type', Notice::TYPE_MSG])->count('id');
    }

    public function getSmsCount()
    {
        return (int)Notice::find()->where(['status'=>0, 'target_id' => $this->id, 'type'=>Notice::TYPE_MSG])->count('id');
    }

    public function getNotices()
    {
        return $this->hasMany(Notice::className(), ['target_id' => 'id'])
            ->where(['status'=>0])->orderBy(['updated_at'=>SORT_DESC]);
    }

    public function getAuths()
    {
        return $this->hasMany(Auth::className(), ['user_id' => 'id'])
            ->orderBy(['id'=>SORT_DESC]);
    }

    public function getTopics()
    {
        return $this->hasMany(Topic::className(), ['user_id' => 'id'])
            ->select(['id', 'node_id', 'user_id', 'reply_id', 'title', 'comment_count', 'replied_at'])
            ->limit(10)->orderBy(['id'=>SORT_DESC]);
    }

    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['user_id' => 'id'])
            ->select(['id', 'user_id', 'topic_id', 'created_at', 'invisible', 'content'])
            ->limit(10)->orderBy(['id'=>SORT_DESC]);
    }

    public function getUserInfo()
    {
        return $this->hasOne(UserInfo::className(), ['user_id' => 'id']);
    }

    public function getLastAction($action = History::ACTION_ADD_TOPIC)
    {
        return $this->hasOne(History::className(), ['user_id' => 'id'])
                ->select(['action_time', 'action', 'target', 'ext'])
                ->where(['action'=>$action])
                ->orderBy(['action_time'=>SORT_DESC])->limit(1);
    }

    public function getAction($where)
    {
        return $this->hasOne(History::className(), ['user_id' => 'id'])
                ->select(['action_time', 'action', 'target', 'ext'])
                ->where($where)->limit(1);
    }

    public function isAdmin()
    {
        return (intval($this->role) === self::ROLE_ADMIN);
    }

    public function isActive()
    {
        return (intval($this->status) >= self::STATUS_ACTIVE);
    }

    public function isInactive()
    {
        return (intval($this->status) === self::STATUS_INACTIVE || intval($this->status) === self::STATUS_ADMIN_VERIFY);
    }

    public function isWatingActivation()
    {
        return (intval($this->status) === self::STATUS_INACTIVE);
    }

    public function isWatingVerification()
    {
        return (intval($this->status) === self::STATUS_ADMIN_VERIFY);
    }

    public function isAuthor($user_id)
    {
        return ($this->id == $user_id);
    }

    public function isExpired($created_at)
    {
        return ( time() > $created_at + intval(Yii::$app->params['settings']['edit_space'])*60 );
    }

    public function canEdit($model, $status=0)
    {
        return ( self::isAdmin() || $status == 0 && self::isActive()
                 && self::isAuthor($model['user_id'])
                 && !self::isExpired($model['created_at'])
                );
    }

    public function canPost($action=History::ACTION_ADD_TOPIC)
    {
        if ( self::isAdmin() ) {
            return true;
        } else if ( !self::isActive() ) {
            return false;
        }
        $key = 'settings.'. ($action===History::ACTION_ADD_TOPIC ? 'topic_space' : 'comment_space');
        $postSpace = ArrayHelper::getValue(Yii::$app->params, $key, 0);
        $lastTime = ArrayHelper::getValue(self::getLastAction($action)->asArray()->one(), 'action_time', 0);
        return (  time() > intval($lastTime) + intval($postSpace) );
    }

    public function canReply($model)
    {
        return ( intval($model['comment_closed']) === 0 && self::isActive() );
    }

    public function canUpload($settings)
    {
        if ( $settings['upload_file'] === 'disable' ) {
            return false;
        }
        if ( self::isAdmin() ) {
            return true;
        } else if ( !self::isActive() ) {
            return false;
        }

        return (
            $this->created_at+intval($settings['upload_file_regday'])*24*3600 < time()
            && $this->userInfo->topic_count >= intval($settings['upload_file_topicnum'])
        );
    }

    public function hasReplied($topicId)
    {
        return Comment::find()->where(['topic_id'=>$topicId, 'user_id'=>$this->id])->count();
    }

    public function getStatus()
    {
        $statusList = self::getStatusList();
        return Yii::t('app', $statusList[$this->status]);
    }

    public static function getStatusList()
    {
	    return [
	        self::STATUS_BANNED => Yii::t('app', 'Banned User'),
	        self::STATUS_INACTIVE => Yii::t('app', 'Inactive User'),
	        self::STATUS_ADMIN_VERIFY => Yii::t('app', 'Unapproved User'),
	        self::STATUS_ACTIVE => Yii::t('app', 'Active User'),
	    ];
    }

    public function getRole()
    {
        return Yii::t('app', self::$roleOptions[$this->role]);
    }

    public static function getCost($action)
    {
        $costs = [
            'reg' => 1000,
            'addTopic' => -20,
            'addComment' => -5,
            'commented' => 5,
            'sendMsg' => -5,
            'buyInviteCode' => -50,
            'signin_10days' => 200,
            'signin' => function() {return rand(10, 50);}, 
            'thanks' => -20,
        ];
        if ($action === 'signin') {
            return $costs[$action]();
        } else {
            return $costs[$action];
        }
    }

    public function checkActionCost($action)
    {
        return ($this->score + static::getCost($action))>=0;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
//        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
        return static::find()->where(['id' => $id])
                ->andWhere(['>=', 'status', self::STATUS_INACTIVE])
                ->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
//        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
        return static::find()->where(['username' => $username])
                ->andWhere(['>=', 'status', self::STATUS_INACTIVE])
                ->one();
    }

    public static function findByEmail($email)
    {
        return static::find()->where(['email' => $email])
                ->andWhere(['>=', 'status', self::STATUS_INACTIVE])
                ->one();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Util::generateRandomString();
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert === true) {
            $userIP = sprintf("%u", ip2long(Yii::$app->getRequest()->getUserIP()));
            (new UserInfo([
                'user_id' => $this->id,
                'reg_ip' => $userIP,
                'last_login_at'=>$this->created_at,
                'last_login_ip'=>$userIP,
            ]))->save(false);
            Siteinfo::updateCounterInfo('addUser');
/*          if ( intval(Yii::$app->params['settings']['email_verify']) === 1) {
                Token::sendActivateMail($this);
            }*/
            (new History([
                'user_id' => $this->id,
                'type' => History::TYPE_POINT,
                'action' => History::ACTION_REG,
                'action_time' => $this->created_at,
                'target' => $userIP,
                'ext' => json_encode(['score'=>$this->score, 'cost'=>static::getCost('reg')]),
            ]))->save(false);
        }
        return parent::afterSave($insert, $changedAttributes);
    }
/*
    public function afterDelete()
    {
        $userId = Yii::$app->getUser()->id;
        (new History([
            'user_id' => $userId,
            'action' => History::ACTION_DELETE_USER,
            'target' => $this->id,
        ]))->save(false);
        $userInfo = $this->userInfo;
        $userInfo->delete();
        Topic::afterUserDelete($this->id);
        Comment::afterUserDelete($this->id);
        Siteinfo::updateCountersInfo( ['users'=>-1, 'topics'=>$userInfo->topic_count, 'comments'=>$userInfo->comment_count] );
        return parent::afterDelete();
    }
*/

    public function updateScore($cost) {
        $this->score = ($this->score + $cost)<0?0:($this->score + $cost);
        return $this->save(false);
    }

    public function afterAddComment($cost, $comment)
    {
        $this->updateScore($cost);
        $author_id = $comment->topic->user_id;
        if ($this->id != $author_id) {
            $author = static::findOne($author_id);
            if($author) {
                $commentedCost = abs(static::getCost('commented'));
                $author->updateCounters(['score' => $commentedCost]);
                (new History([
                    'user_id' => $author_id,
                    'type' => History::TYPE_POINT,
                    'action' => History::ACTION_COMMENTED,
                    'action_time' => $comment->created_at,
                    'target' => $comment->topic_id,
                    'ext' => json_encode(['topic_id'=>$comment->topic_id, 'title'=>$comment->topic->title, 'commented_by'=>$this->username, 'score'=>$author->score, 'cost'=>$commentedCost]),
                ]))->save(false);
            }
        }
    }

    public function signin()
    {
        $flgToday = false;
        $flg10Days = false;
        $action = $this->getLastAction(History::ACTION_SIGNIN)->one();
        if ( !$action ) {
            $flagToday = true;
            $continue = 1;
        } else if (  date('Y-m-d') === date('Y-m-d', $action->action_time) ) {
            return;
        } else if ( date('Y-m-d', strtotime('-1 days')) == date('Y-m-d', $action->action_time) ) {
            $flagToday = true;
            $ext = json_decode($action->ext, true);
            if ( intval($ext['continue']) % 10 === 9 ) {
                $flg10Days = true;
            }
            $continue = intval($ext['continue'])+1;
        } else {
            $flagToday = true;
            $continue = 1;
        }
        $cost = static::getCost('signin');
        $this->updateScore($cost);
        (new History([
            'user_id' => $this->id,
            'type' => History::TYPE_POINT,
            'action' => History::ACTION_SIGNIN,
            'ext' => json_encode(['score'=>$this->score, 'cost'=>$cost, 'continue'=>$continue]),
        ]))->save(false);
        Yii::$app->getSession()->set('mission_signin_'. date('Ymd'), $continue);

        if ($flg10Days === true) {
            $cost = static::getCost('signin_10days');
            $this->updateScore($cost);
            (new History([
                'user_id' => $this->id,
                'type' => History::TYPE_POINT,
                'action' => History::ACTION_SIGNIN_10DAYS,
                'ext' => json_encode(['score'=>$this->score, 'cost'=>$cost, 'continue'=>$continue]),
            ]))->save(false);
        }

    }
    public function checkTodaySigned()
    {
        $session = Yii::$app->getSession();
        $continue = $session->get('mission_signin_'. date('Ymd'));
        if ( $continue > 0 ) {
            return $continue;
        }

        $action = $this->getLastAction(History::ACTION_SIGNIN)->one();
        if ( $action && date('Y-m-d') === date('Y-m-d', $action->action_time) ) {
            $ext = json_decode($action->ext, true);
            $session->set('mission_signin_'. date('Ymd'), $ext['continue']);
            return $ext['continue'];
        }
        return false;
    }

    public function good($type, $id, $thanks=0)
    {
        $types = [
            'topic' => Notice::TYPE_GOOD_TOPIC,
            'comment' => Notice::TYPE_GOOD_COMMENT,
        ];
        $actions = [
            'topic' => History::ACTION_GOOD_TOPIC,
            'comment' => History::ACTION_GOOD_COMMENT,
        ];
        $thanked = [
            'topic' => History::ACTION_TOPIC_THANKED,
            'comment' => History::ACTION_COMMENT_THANKED,
        ];

        if( !isset($types[$type]) || !isset($actions[$type]) ) {
            return ['result'=>0, 'msg'=>Yii::t('app', 'Parameter error')];
        }

        $action = $this->getAction(['action'=>$actions[$type], 'target'=>$id])->one();
        if ($action) {
            return ['result'=>0, 'msg'=>Yii::t('app', 'Can\'t click \'good\' repeatedly.')];
        }

        if ($type == 'topic') {
            $target = Topic::find()->select(['id', 'user_id', 'good', 'title'])->where(['id'=>$id])->one();
            $notice = [
                'type' => $types[$type],
                'source_id' => $this->id,
                'target_id' => $target->user_id,
                'topic_id' => $id,
            ];
        } else {
            $target = Comment::find()->select(['id', 'user_id', 'topic_id', 'position', 'good'])->where(['id'=>$id])->one();
            $notice = [
                'type' => $types[$type],
                'source_id' => $this->id,
                'target_id' => $target->user_id,
                'topic_id' => $target->topic_id,
                'position' => $target->position,
            ];
        }
        if (!$target) {
            return ['result'=>0, 'msg'=>Yii::t('app', 'Parameter error')];
        } else if ($this->id == $target->user_id) {
            return ['result'=>0, 'msg'=>Yii::t('app', 'Can\'t click \'good\' to your {attribute}.', ['attribute' => ($type===Notice::TYPE_GOOD_TOPIC?Yii::t('app', 'Topic'):Yii::t('app', 'Comment'))])];
        }
        if ( !$target->updateCounters(['good' => 1]) ) {
            return ['result'=>0, 'msg'=>Yii::t('app', 'Error occurred')];
        }
        $result = ['result'=>1, 'count'=>$target->good];
        $history = [
                'user_id' => $this->id,
                'action' => $actions[$type],
                'target' => $id,
        ];
        if ($thanks) {
            $cost = static::getCost('thanks');
            if( $this->score+$cost < 0 ) {
                $result['msg'] = Yii::t('app', 'You don\'t have enough points.');
            } else if( ($author = static::findOne($target->user_id)) && $this->updateScore($cost) ) {
                $thanksCost = abs($cost);

                $author->updateCounters(['score' => $thanksCost]);
                $ext = ['thank_by'=>$this->username, 'score'=>$author->score, 'cost'=>$thanksCost];
                if ($type == 'topic') {
                    $ext = $ext+['topic_id'=>$target->id, 'title'=>$target->title];
                } else {
                    $ext = $ext+['topic_id'=>$target->topic_id, 'title'=>$target->topic->title];
                }

                (new History([
                    'user_id' => $author->id,
                    'type' => History::TYPE_POINT,
                    'action' => $thanked[$type],
                    'ext' => json_encode($ext),
                ]))->save(false);

                $ext = ['thank_to'=>$author->username, 'score'=>$this->score, 'cost'=>$cost];
                if ($type == 'topic') {
                    $ext = $ext+['topic_id'=>$target->id, 'title'=>$target->title];
                } else {
                    $ext = $ext+['topic_id'=>$target->topic_id, 'title'=>$target->topic->title];
                }
                $history = $history+[
                    'type' => History::TYPE_POINT,
                    'ext' => json_encode($ext),
                ];
            }
        }

        (new Notice($notice))->save(false);
        (new History($history))->save(false);
        return $result;
    }
}
