<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use Faker\Provider\DateTime;
use yii\imagine\Image;
use Imagine\Image\Box;
use yii\helpers\ArrayHelper;

/**
 * Class EditProjectForm
 * @package frontend\models
 */
class EditProjectForm extends Model
{
    public $name;
    public $users_id;
    public $activity;
    public $accesses;
    public $logo;
    public $file;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'filter', 'filter' => 'trim'],
            ['name', 'string', 'max' => 30],

            ['users_id', 'default', 'value' => null],

            ['activity', 'boolean'],
            ['activity', 'default', 'value' => true],

            ['accesses', 'default', 'value' => null],

            ['file', 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
        ];
    }

    /**
     * @return Project|null
     */
    public function editProject(Project $project)
    {
        $project->name = $this->name;
        $project->activity = $this->activity;
        $project->accesses = $this->accesses;
        // remove previous logo-image if new logo has been chosen
        if ($this->logo) {
            @unlink($project->logo);
            $project->logo = $this->logo;
        }
        $project->update();

        // link/unlink relations between users and projects
        // $users - all users id which project were already contained, $this->users_id - checked users just now
        $users = $project->users;
        $users = ArrayHelper::getColumn($users, 'id');
        // if in model has been checked at least one (or more) user
        if ($this->users_id) {
            $existValues = array_intersect($this->users_id, $users);
            // remove relations which must be remain untouched further $this->users_id go to link & $users go to unlink
            foreach ($existValues as $key1 => $val1) {
                if ($necessaryKey = array_keys($this->users_id, $val1)) {
                    unset($this->users_id[$necessaryKey[0]]);
                }
                if ($necessaryKey = array_keys($users, $val1)) {
                    unset($users[$necessaryKey[0]]);
                }
            }
        }
        // link/unlink users if they exist
        if($usersLink = User::findAll($this->users_id)) {
            foreach ($usersLink as $k => $user) {
                $project->link('users', $user);
            }
        }
        if ($usersUnlink = User::findAll($users)) {
            foreach ($usersUnlink as $k => $user) {
                $project->unlink('users', $user, true);
            }
        }

        return true;
    }

    /**
     * Save image for project logo. Resize upload image also. And check length of image filename.
     *
     * @return bool
     */
    public function uploadLogo()
    {
        // save image with initial proportions
        $curUnixDate = DateTime::unixTime();
        $filePath = 'uploads/' . $this->file->baseName . $curUnixDate . '.' . $this->file->extension;

        // check file-logo length for backend - only if filename length <= 70 symbols
        if (strlen($filePath) <= 70) {
            $this->logo = $filePath;
            $this->file->saveAs($filePath);

            // resize image 300x300 (in proportion)
            $img = Image::getImagine()->open($filePath);
            $size = $img->getSize();

            if ($size->getWidth() > 300) {
                $ratio = $size->getWidth() / $size->getHeight();
                $width = 300;
                $height = round($width / $ratio);
                $box = new Box($width, $height);
                $img->resize($box)->save($filePath, ['quality' => 90]);
                return true;
            }
            if ($size->getHeight() > 300) {
                $ratio = $size->getHeight() / $size->getWidth();
                $height = 300;
                $width = round($height / $ratio);
                $box = new Box($width, $height);
                $img->resize($box)->save($filePath, ['quality' => 90]);
                return true;
            }
            // if width & height < 300
            return true;
        }
        // if file name is too long
        return false;
    }
}
