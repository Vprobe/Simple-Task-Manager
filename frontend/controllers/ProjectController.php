<?php
namespace frontend\controllers;

use Yii;
use common\models\User;
use yii\web\Controller;
use yii\filters\AccessControl;
use frontend\models\Project;
use frontend\models\CreateProjectForm;
use frontend\models\EditProjectForm;
use yii\web\UploadedFile;

/**
 * Class ProjectController
 * @package frontend\controllers
 */
class ProjectController extends Controller
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
     * Create project. Not for users, admins(or PM`s) only.
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        if (Yii::$app->user->can('createProject')) {
            $model = new CreateProjectForm();
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $model->file = UploadedFile::getInstance($model, 'file');
                if ($model->file) {
                    if(!$model->uploadLogo()) {
                        Yii::$app->session->setFlash('danger', 'New project couldn`t be created cause logo filename is
                         too long (more than 70 symbols).');
                        return $this->redirect('/project/view');
                    }
                }
                $model->createProject();
                Yii::$app->session->setFlash('success', 'New project was created successfully.');
                return $this->redirect('/project/view');
            }

            $model->activity = 1;
            return $this->render('create', [
                'model' => $model,
            ]);
        }
        Yii::$app->session->setFlash('warning', 'You have no permission to create projects.');
        return $this->goHome();
    }


    /**
     * Show projects on dashboard.
     *
     * @return string
     */
    public function actionView()
    {
        // if you are admin - all projects
        if (Yii::$app->user->can('createProject')) {
            $projects = Project::find()->all();
            return $this->render('view', compact('projects'));
        }

        // if you are user - projects in which you consist
        $user = User::find()
            ->where(['id' => Yii::$app->user->getId()])
            ->one();
        $projects = $user->projects;

        return $this->render('view', compact('projects'));
    }

    /**
     * Edit project fields. Include upload, remove logo and manager between projects and users relations.
     *
     * @param $projectId
     * @return string|\yii\web\Response
     */
    public function actionEdit($projectId)
    {
        if (Yii::$app->user->can('createProject')) {
            $model = new EditProjectForm();
            $project = Project::findOne($projectId);
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $model->file = UploadedFile::getInstance($model, 'file');
                if ($model->file) {
                    if(!$model->uploadLogo($project)) {
                        Yii::$app->session->setFlash('danger', 'New project couldn`t be created cause logo filename is
                         too long (more than 70 symbols).');
                        return $this->redirect('/project/view');
                    }
                }
                $model->editProject($project);
                Yii::$app->session->setFlash('success', 'New project was edited successfully.');
                return $this->redirect('/project/view');
            }

            // check existing users (for checkboxList)
            if ($project->users) {
                $existUsers = array();
                foreach ($project->users as $k => $u) {
                    $existUsers[] = $u->id;
                }
                $model->users_id = $existUsers;
            }
            // check is project active (for checkboxList)
            if ($project->activity == 1) {
                $model->activity = 1;
            }
            return $this->render('edit', compact('model', 'project'));
        }
        Yii::$app->session->setFlash('warning', 'You have no permission to edit projects.');
        return $this->goHome();
    }

}
