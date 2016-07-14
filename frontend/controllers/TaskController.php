<?php
namespace frontend\controllers;

use Yii;
use common\models\Task;
use frontend\models\Project;
use frontend\models\CreateTaskForm;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\AccessControl;

/**
 * Class TaskController
 * @package frontend\controllers
 */
class TaskController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        if (Yii::$app->user->can('createTask')) {
            $model = new CreateTaskForm();
            if ($model->load(Yii::$app->request->post())) {
                if ($task = $model->createTask()) {
                    Yii::$app->session->setFlash('success', 'New task was created successfully.');
                    return $this->goHome();
                }
            }

            return $this->render('createTask', [
                'model' => $model,
            ]);
        }
        Yii::$app->session->setFlash('warning', 'You have no permission to create tasks.');
        return $this->redirect(Url::previous('taskView'));
    }

    /**
     * @param $taskId
     * @return bool|\yii\web\Response
     */
    public function actionDone($taskId)
    {
        if (Yii::$app->request->isAjax) {
            if ($task = Task::findOne($taskId)) {
                if ($task->done == 0) {
                    $performerIsCreator = Task::setTaskDone($task);
                    return $performerIsCreator;
                }
            }
            return false;
        } else {
            return $this->goHome();
        }
    }

    /**
     * @param $taskId
     * @return bool|\yii\web\Response
     */
    public function actionClose($taskId)
    {
        if (Yii::$app->request->isAjax) {
            if ($task = Task::findOne($taskId)) {
                if ($task->done == 1) {
                    $taskClosed = Task::closeTask($task);
                    return $taskClosed;
                }
            }
            return false;
        } else {
            return $this->goHome();
        }
    }

    /**
     * @param $taskId
     * @return bool|\yii\web\Response
     */
    public function actionRevert($taskId)
    {
        if (Yii::$app->request->isAjax) {
            if ($task = Task::findOne($taskId)) {
                if ($task->done == 1) {
                    $taskReverted = Task::revertTask($task);
                    return $taskReverted;
                }
            }
            return false;
        } else {
            return $this->goHome();
        }
    }

    /**
     * @param $taskId
     * @return bool|\yii\web\Response
     */
    public function actionUndoFreshComment($taskId)
    {
        if (Yii::$app->request->isAjax) {
            if ($task = Task::findOne($taskId)) {
                if ($task->fresh_comment == 1) {
                    $commentRemoved = Task::removeFreshComment($task);
                    return $commentRemoved;
                }
            }
            return false;
        } else {
            return $this->goHome();
        }
    }

    /**
     * For create task only. Change performers dropdownlist considering chosen project.
     * Return option values for field dropdownlist - 'User_to' in view 'createTask'.
     * @param $id
     * @return \yii\web\Response
     */
    public function actionRefreshPerformers($id)
    {
        if (Yii::$app->request->isPost) {
            if ($id) {
                $project = Project::findOne($id);
                if ($project->users) {
                    foreach ($project->users as $user) {
                        echo "<option value='" . $user->id . "'>" . $user->name . "</option>";
                    }
                } else {
                    echo "<option value=''>Specify project first</option>";
                }
            } else {
                echo "<option value=''>Specify project first</option>";
            }
        } else {
            return $this->redirect('/project/view');
        }
    }

    /*public function actionView($id)
    {
        if ($task = Task::findOne($id)) {
            Url::remember(['task/view', 'id' => $id], 'taskView');
            $uTo = User::findOne($task->user_last);
            $uCreator = User::findOne($task->user_id);
            return $this->render('viewTask', compact('task', 'uTo', 'uCreator'));
        } else {
            return $this->goHome();
        }
    }*/

    /*public function actionUpdate($taskId)
    {
        if ($task = Task::findOne($taskId)) {
            if (Yii::$app->user->can('updateTask', ['task' => $task])) {
                $model = new UpdateTaskForm();
                if ($model->load(Yii::$app->request->post())) {
                    if ($updatedTask = $model->updateTask($task)) {
                        Yii::$app->session->setFlash('success', 'Task was updated successfully.');
                        return $this->goHome();
                    }
                }
                // render update form
                $cancelBtn = Url::previous('taskView');
                $cancelBtn = str_replace(Yii::$app->request->getBaseUrl(), "", $cancelBtn);
                return $this->render('updateTask', compact('model', 'task', 'cancelBtn'));
            }
            Yii::$app->session->setFlash('warning', 'You have no permission to update this task.');
            return $this->redirect(Url::previous('taskView'));
        } else {
            return $this->goHome();
        }
    }*/

    /*public function actionDelete($taskId, $delete = false)
    {
        if ($task = Task::findOne($taskId)) {
            if (Yii::$app->user->can('deleteTask', ['task' => $task])) {
                // when delete was confirmed
                if ($delete) {
                    $task->delete();
                    Yii::$app->session->setFlash('success', 'Task was successfully deleted.');
                    return $this->goHome();
                }
                // render for delete confirm
                $cancelBtn = Url::previous('taskView');
                $cancelBtn = str_replace(Yii::$app->request->getBaseUrl(), "", $cancelBtn);
                return $this->render('deleteTask', compact('task', 'cancelBtn'));
            }
            Yii::$app->session->setFlash('warning', 'You have no permission to delete this task.');
            return $this->redirect(Url::previous('taskView'));
        } else {
            return $this->goHome();
        }
    }*/

}