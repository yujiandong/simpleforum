<?php
/**
 * @link http://www.simpleforum.org/
 * @copyright Copyright (c) 2015 Simple Forum
 * @author Jiandong Yu admin@simpleforum.org
 */

namespace app\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\caching\DbDependency;
use app\models\User;
use app\models\Topic;
use app\models\TopicContent;
use app\models\Comment;
use app\models\Node;
use app\models\Tag;
use app\models\History;
use app\lib\Util;

class TopicController extends AppController
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['new', 'add', 'edit'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['new', 'add', 'edit'],
                        'matchCallback' => function ($rule, $action) {
							$me = Yii::$app->getUser();
                            return ( !$me->getIsGuest() && ($me->getIdentity()->isActive() || $me->getIdentity()->isAdmin()) );
                        },
                    ],
                ],
				'denyCallback' => function ($rule, $action) {
					$me = Yii::$app->getUser();
					if( !$me->getIsGuest() && $me->getIdentity()->isInactive() ) {
					    throw new ForbiddenHttpException('您的会员帐号还没有激活，请先激活');
					} else {
					    throw new ForbiddenHttpException('您没有执行此操作的权限。');
					}
				},
            ],
        ];
    }

    public function actionIndex()
    {
	    $pages = new Pagination([
			'totalCount' => Topic::find()->count('id'),
			'pageSize' => intval($this->settings['index_pagesize']),
			'pageParam' => 'p',
		]);
	    return $this->render('index', [
	         'topics' => Topic::getTopicsFromIndex($pages),
	         'pages' => $pages,
	    ]);
    }

	public function actionNode($name)
	{
		$node = $this->findNodeModel($name);
	    $pages = new Pagination([
			'totalCount' => $node['topic_count'],
			'pageSize' => intval($this->settings['list_pagesize']),
			'pageParam' => 'p',
		]);

	    return $this->render('node', [
			 'node' => $node,
	         'topics' => Topic::getTopicsFromNode($node['id'], $pages),
	         'pages' => $pages,
	    ]);
	}

    public function actionSearch($q)
    {
	    $pages = new Pagination([
			'totalCount' => Topic::find()->where(['like', 'title', $q])->count('id'),
			'pageSize' => intval($this->settings['index_pagesize']),
			'pageParam' => 'p',
		]);
	    return $this->render('index', [
	         'topics' => Topic::getTopicsFromSearch($pages, $q),
	         'pages' => $pages,
			 'title' => '搜索结果：'.$q,
	    ]);
    }

    public function actionView($id)
    {
		$topic = Topic::getTopicFromView($id);
		$pages = new Pagination([
			'totalCount' => $topic->comment_count,
			'pageSize' => intval($this->settings['comment_pagesize']),
			'pageParam' => 'p',
		]);

        return $this->render('view', [
            'topic' => Util::convertModelToArray($topic),
			'comments' => Comment::getCommentsFromView($id, $pages),
	        'pages' => $pages,
       ]);
    }
/*
    public function actionClick($id)
	{
		print_r(Topic::afterView($id));die;
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
	    return [
	        'views' => Topic::afterView($id),
	    ];
	}
*/
    public function actionAdd($node)
    {
		$request = Yii::$app->getRequest();
		$node = $this->findNodeModel($node);

        $topic = new Topic(['scenario' => Topic::SCENARIO_ADD, 'node_id' => $node['id'], 'user_id' => Yii::$app->getUser()->id]);
        $content = new TopicContent();
        if ( $topic->load($request->post()) && $topic->validate() && 
			$content->load($request->post()) && $content->validate() ) {

//			$topic->tags = Tag::getTags($topic->tags, $topic->title, $content->content);
			$topic->tags = Tag::getTags($topic->tags);
			$topic->save(false);
			$content->link('topic', $topic);
            return $this->redirect(['view', 'id' => $topic->id]);
        }
        return $this->render('add', [
            'model' => $topic,
            'content' => $content,
            'node' => $node,
        ]);
    }

    public function actionNew()
    {
		$request = Yii::$app->getRequest();

        $topic = new Topic(['scenario' => Topic::SCENARIO_NEW, 'user_id' => Yii::$app->getUser()->id]);
        $content = new TopicContent();

        if ( $topic->load($request->post()) && $topic->validate() && 
			$content->load($request->post()) && $content->validate() ) {

//			$topic->tags = Tag::getTags($topic->tags, $topic->title, $content->content);
			$topic->tags = Tag::getTags($topic->tags);
			$topic->save(false);
			$content->link('topic', $topic);
            return $this->redirect(['view', 'id' => $topic->id]);
        }
        return $this->render('new', [
            'model' => $topic,
            'content' => $content,
        ]);
    }

    public function actionEdit($id)
    {
		$request = Yii::$app->getRequest();
		$me = Yii::$app->getUser()->getIdentity();

        $model = $this->findTopicModel($id, ['content','node']);
		if( !$me->canEdit($model) ) {
			throw new ForbiddenHttpException('您没有权限修改或已超过可修改时间。');
		}
		if( $me->isAdmin() ) {
			$model->scenario = Topic::SCENARIO_ADMIN_EDIT;
		} else {
			$model->scenario = Topic::SCENARIO_AUTHOR_EDIT;
		}
		if( !($content = $model->content) ) {
			$content = new TopicContent(['topic_id'=>$model->id]);
		}
		$oldTags = $model->tags;
        if ($model->load($request->post()) && $model->validate() && 
			$content->load($request->post()) && $content->validate() ) {
//			$model->tags = Tag::editTags($model->tags, $oldTags);
			$model->tags = Tag::getTags($model->tags);
			$model->save(false) && $content->save(false);
			Tag::afterTopicEdit($model->id, $model->tags, $oldTags);
			(new History([
				'user_id' => $me->id,
				'action' => History::ACTION_EDIT_TOPIC,
				'action_time' => $model->updated_at,
				'target' => $model->id,
			]))->save(false);

           return $this->redirect(Topic::getRedirectUrl($id, 0, $request->get('ip', 1), $request->get('np', 1)));
        }
        return $this->render('edit', [
            'model' => $model,
            'content' => $content,
        ]);
    }

    protected function findTopicModel($id, $with=null)
    {
		$model = Topic::find()->where(['id'=>$id]);
		if ( !empty($with) ) {
			$model = $model->with($with);
		}
		$model = $model->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('未找到id为['.$id.']的主题');
        }
    }

    protected function findNodeModel($name, $with=null)
    {
		$model = Node::find()->select(['id', 'name', 'ename', 'topic_count', 'about'])->where(['ename' => $name]);
		if ( !empty($with) ) {
			$model = $model->with($with);
		}
		$model = $model->asArray()->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('未找到['.$name.']的节点');
        }
    }
}
