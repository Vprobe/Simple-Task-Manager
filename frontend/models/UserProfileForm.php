<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;


/**
 * Class UserProfileForm
 * @package frontend\models
 */
class UserProfileForm extends Model
{
    public $name;
    public $login;
    public $password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login', 'name'], 'filter', 'filter' => 'trim'],
            [['login', 'name'], 'required'],
            [['login', 'name'], 'string', 'min' => 2, 'max' => 20],

            ['login', 'validateLoginUnique'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    public function updateProfile(User $user)
    {
        // validate signup model
        if ($this->validate()) {
            // update user
            $user->name = $this->name;
            $user->login = $this->login;
            $user->setPassword($this->password);
            $user->update();
            return $user;
        } else {
            return null;
        }
    }

    public function validateLoginUnique($attribute, $params)
    {
        // get all users except current
        $users = User::find()->where('id != :id', ['id'=>Yii::$app->user->getId()])->all();
        foreach ($users as $k => $v) {
            if ($this->$attribute == $v->login) {
                // if input login exist in db call exception
                $this->addError($attribute, 'This login already exist');
            }
        }
    }

}
