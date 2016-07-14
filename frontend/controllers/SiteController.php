<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use common\models\Task;
use frontend\models\SendCommentForm;
use common\models\LoginForm;
use yii\helpers\BaseJson;

/**
 * Site controller
 */
class SiteController extends Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'signup', 'error', 'captcha'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'logout', 'error', 'check-cookies'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
            ],
        ];
    }

    /**
     * Displays homepage. Content refresh by pjax.
     * @param null $uCreatorId for 'Sort by user' filter.
     * @return string|\yii\web\Response
     */
    public function actionIndex($uCreatorId = null)
    {
        // check open tasks
        if (!$openTaskId = $this->_checkOpenTasks()) {
            $openTaskId = array();
        }

        // get all tasks
        $id = Yii::$app->user->getId();
        $tasksTo = Task::getTasksTo($id);
        $tasksFrom = Task::getTasksFrom($id, $uCreatorId);

        // add comment
        $model = new SendCommentForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($commentSend = $model->sendComment()) {
                $task = Task::findOne($commentSend->task_id);
                // return task to creator with new comment
                Task::revertWithComment($task);
                Yii::$app->session->setFlash('success', 'Comment was added successfully.');
                return $this->goHome();
            }
        }

        return $this->render('index', compact('tasksFrom', 'tasksTo', 'openTaskId', 'model', 'uCreatorId'));
    }

    /**
     * Ajax query on task title click.
     * Add cookie to browser if not exist, remove cookie from cookie collection - if exist.
     * Cookie separate on two categories 'taskidto' & 'taskidfrom'.
     *
     * @param $taskid
     * @param bool $to
     * @param bool $from
     * @return bool
     */
    public function actionCheckCookies($taskid, $to=false, $from=false)
    {
        if (Yii::$app->request->isAjax) {
            $cookiesReq = Yii::$app->request->cookies;
            $cookiesRes = Yii::$app->response->cookies;
            $to = BaseJson::decode($to);
            $from = BaseJson::decode($from);

            if ($to) {
                $coName = 'taskidto';
            } elseif ($from) {
                $coName = 'taskidfrom';
            }

            if ($cookiesReq->has($coName . $taskid)) {
                $cookiesRes->remove($coName . $taskid);
                return true;
            }
            $cookiesRes->add(new \yii\web\Cookie([
                'name' => $coName . $taskid,
                'value' => $taskid,
            ]));
            return true;
        }
        return false;
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            Yii::$app->session->setFlash('success', 'You were successfully logged in.');
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Collect all tasks id from request cookies. It allows render page(pjax) 'site/index' with already open tasks.
     * Create two arrays for both tasks columns.
     *
     * @return array|bool
     */
    private function _checkOpenTasks()
    {
        $openTaskId = array();
        $openTaskId['to'] = array();
        $openTaskId['from'] = array();
        foreach (Yii::$app->request->cookies->toArray() as $key => $val) {
            if (strstr($key, 'taskid')) {
                if (strstr($key, 'to')) {
                    $openTaskId['to'][] = $val->value;
                } elseif (strstr($key, 'from')) {
                    $openTaskId['from'][] = $val->value;
                }
            }
        }
        return $openTaskId;
    }

}
