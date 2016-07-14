<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use common\models\User;
use frontend\models\UserProfileForm;
use frontend\models\CreateUserForm;

/**
 * Class UserController
 * @package frontend\controllers
 */
class UserController extends Controller
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
     * View & update user profile. User can see & edit only own profile.
     * @param $userid
     * @return string|\yii\web\Response
     */
    public function actionProfile($userid)
    {
        // only for current login user
        if (Yii::$app->user->getId() == $userid) {
            $model = new UserProfileForm();
            $user = User::findOne($userid);
            if ($model->load(Yii::$app->request->post())) {
                if ($updatedProfile = $model->updateProfile($user)) {
                    Yii::$app->session->setFlash('success', 'Profile was updated successfully.');
                    return $this->goHome();
                }
            }
            return $this->render('userProfile', compact('model', 'user'));
        } else {
            return $this->goHome();
        }
    }

    /**
     * Create new user. Send him email with login & pass.
     * @return string|\yii\web\Response
     */
    public function actionCreateUser()
    {
        if(Yii::$app->user->can('crudUser')) {
            $model = new CreateUserForm();
            if ($model->load(Yii::$app->request->post())) {
                // createUser also call sendEmail method for sending new user login & pass
                if ($user = $model->createUser()) {
                    Yii::$app->session->setFlash('success', 'User was created successfully.');
                    return $this->goHome();
                }
            }

            return $this->render('createUser', [
                'model' => $model,
            ]);
        }
        Yii::$app->session->setFlash('warning', 'You have no permission to create users.');
        return $this->goHome();
    }

    /**
     * Show all users.
     * @return string|\yii\web\Response
     */
    public function actionViewUsers()
    {
        if (Yii::$app->user->can('crudUser')) {
            $users = User::find()->all();

            return $this->render('view', [
                'users' => $users
            ]);
        }
        Yii::$app->session->setFlash('warning', 'You have no permission to see users list.');
        return $this->goHome();
    }

    /**
     * Block user if exist.
     * @param $userId
     * @return bool|\yii\web\Response
     */
    public function actionBlock($userId)
    {
        if (Yii::$app->request->isAjax) {
            if ($user = User::findOne($userId)) {
                User::setUserBlock($user);
                return true;
            }
            return false;
        } else {
            return $this->goHome();
        }
    }

    /**
     * Unblock user if exist.
     * @param $userId
     * @return bool|\yii\web\Response
     */
    public function actionUnblock($userId)
    {
        if (Yii::$app->request->isAjax) {
            if ($user = User::findOne($userId)) {
                User::setUserUnblock($user);
                return true;
            }
            return false;
        } else {
            return $this->goHome();
        }
    }

}
