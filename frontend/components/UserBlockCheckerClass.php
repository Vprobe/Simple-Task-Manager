<?php
namespace frontend\components;

use Yii;
use yii\base\Object;
use common\models\User;

/**
 * Class UserBlockCheckerClass
 *
 * Check field 'block' for user before any action. Forbid access to system & logout user if its true.
 * @package frontend\components
 */
class UserBlockCheckerClass extends Object
{
    public function init()
    {
        if (!Yii::$app->user->isGuest) {
            $cUser = User::findOne(Yii::$app->user->getId());
            if ($cUser->block == 1) {
                Yii::$app->user->logout();
                Yii::$app->session->setFlash('danger', 'This user has been blocked. For unblocking ask admin or PM.');
            }
        }

        parent::init();
    }
}