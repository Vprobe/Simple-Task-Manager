<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use frontend\models\Comment;
use frontend\models\Project;

/**
 * User model
 *
 * @property integer $id
 * @property string $login
 * @property string $password_hash
 * @property string $name
 * @property string $email
 * @property integer $block
 */
class User extends ActiveRecord implements IdentityInterface
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'User ID',
            'login' => 'Login',
            'password' => 'Password',
            'name' => 'User name',
            'email' => 'E-mail',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['author_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjects()
    {
        return $this->hasMany(Project::className(), ['id' => 'project_id'])
            ->viaTable('user_project', ['user_id' => 'id']);
    }

    /**
     * @param $name
     * @return null|static
     */
    public static function findByUsername($name)
    {
        return static::findOne(['name' => $name]);
    }

    /**
     * @param $login
     * @return null|static
     */
    public static function findByUserLogin($login)
    {
        return static::findOne(['login' => $login]);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @param int|string $id
     * @return null|static
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
    }

    /**
     * @param $password
     * @return bool
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @param $password
     * @throws \yii\base\Exception
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Set block 1 & block user.
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public static function setUserBlock(User $user)
    {
        $user->block = 1;
        $user->update();
        return true;
    }

    /**
     * Set block 0 & unblock user.
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public static function setUserUnblock(User $user)
    {
        $user->block = 0;
        $user->update();
        return true;
    }

}
