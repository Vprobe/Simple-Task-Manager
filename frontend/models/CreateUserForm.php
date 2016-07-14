<?php
namespace frontend\models;

use Yii;
use common\models\User;
use yii\base\Model;

/**
 * Create user form
 */
class CreateUserForm extends Model
{
    public $login;
    public $name;
    public $password;
    public $role;
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login', 'role', 'email', 'password', 'name'], 'required'],

            [['login', 'name', 'email'], 'filter', 'filter' => 'trim'],

            [['login', 'name'], 'string', 'min' => 3, 'max' => 20],

            ['login', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This login has already been taken.'],

            ['password', 'string', 'min' => 6],

            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

        ];
    }

    /**
     * Create user.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function createUser()
    {
        // validate model
        if ($this->validate()) {
            // insert and save new user
            $user = new User();
            $user->login = $this->login;
            $user->name = $this->name;
            $user->setPassword($this->password);
            $user->email = $this->email;
            $user->save(false);

            // set role for new user
            $auth = Yii::$app->authManager;
            $authorRole = $auth->getRole($this->role);
            $auth->assign($authorRole, $user->getId());

            // send letter to new user
            $this->sendEmail($user, $this->password);

            return $user;
        } else {
            return null;
        }
    }

    public function sendEmail($user, $pass)
    {
        // get user-creator email for feedback
        $userCreator = User::find()->where(['id' => Yii::$app->user->getId()])->one();
        if(!$userCreator->email) {
            $fromEmail = Yii::$app->params['adminEmail'];
        } else {
            $fromEmail = $userCreator->email;
        }

        Yii::$app->mailer->compose()
            ->setFrom($fromEmail)
            ->setTo($user->email)
            ->setSubject('Welcome to crm.eckpclub.ru !')
            ->setTextBody("
            Welcome to crm.eckpclub !
            You may start work with system follow this link: http://crm.eckpclub.ru/frontend/web/
            Login: $user->login
            Password: $pass
            ")
            ->send();
    }

}
