<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\rbac\UserCreatorRule;

class RbacController extends Controller
{
    /**
     * First initializations for rbac
     */
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        //create update delete tasks
        $createTask = $auth->createPermission('createTask');
        $createTask->description = 'Create task';
        $auth->add($createTask);

        $updateTask = $auth->createPermission('updateTask');
        $updateTask->description = 'Update task';
        $auth->add($updateTask);

        $deleteTask = $auth->createPermission('deleteTask');
        $deleteTask->description = 'Delete task';
        $auth->add($deleteTask);

        $crudUser = $auth->createPermission('crudUser');
        $crudUser->description = 'Create read update delete all users';
        $auth->add($crudUser);

        // create roles: user, admin
        $user = $auth->createRole('user');
        $user->description = 'Can create tasks, see tasks, update/delete own tasks only';
        $auth->add($user);

        $admin = $auth->createRole('admin');
        $admin->description = 'Superuser can do anything';
        $auth->add($admin);

        // give permissions
        $auth->addChild($user, $createTask);
        $auth->addChild($admin, $updateTask);
        $auth->addChild($admin, $deleteTask);
        $auth->addChild($admin, $crudUser);

        // admin inherit all user permissions
        $auth->addChild($admin, $user);

        // assign roles to users in db
        $auth->assign($admin, 1);
        $auth->assign($user, 2);
        $auth->assign($user, 3);

        // add userCreatorRule that allow user update own tasks
        $userCreatorRule = new UserCreatorRule();
        $auth->add($userCreatorRule);

        // add two rules: updateOwnTask and deleteOwnTask
        $updateOwnTask = $auth->createPermission('updateOwnTask');
        $updateOwnTask->description = 'Update own task';
        $updateOwnTask->ruleName = $userCreatorRule->name;
        $auth->add($updateOwnTask);

        $deleteOwnTask = $auth->createPermission('deleteOwnTask');
        $deleteOwnTask->description = 'Delete own task';
        $deleteOwnTask->ruleName = $userCreatorRule->name;
        $auth->add($deleteOwnTask);

        // assign update/deleteOwnTask with user
        $auth->addChild($updateOwnTask, $updateTask);
        $auth->addChild($user, $updateOwnTask);
        $auth->addChild($deleteOwnTask, $deleteTask);
        $auth->addChild($user, $deleteOwnTask);

    }

    /**
     * Add permission to create projects for admins only
     */
    public function actionAddCreateProjectPerm ()
    {
        $auth = Yii::$app->authManager;

        $createProject = $auth->createPermission('createProject');
        $createProject->description = 'Create project';
        $auth->add($createProject);

        $admin = $auth->getRole('admin');

        $auth->addChild($admin, $createProject);
    }

}
