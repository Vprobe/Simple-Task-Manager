<?php
namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\User;
use common\models\Task;

/**
 * Project model
 *
 * @property integer $id
 * @property string $name
 * @property string $logo
 * @property string $accesses
 * @property boolean $activity
 */
class Project extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Project ID',
            'name' => 'Project name',
            'logo' => 'Project logo',
            'accesses' => 'Accesses to project',
            'activity' => 'Is project active',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])
            ->viaTable('user_project', ['project_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::className(), ['project_id' => 'id']);
    }

}
