<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use Faker\Provider\DateTime;
use yii\imagine\Image;
use Imagine\Image\Box;

/**
 * Class CreateProjectForm
 * @package frontend\models
 */
class CreateProjectForm extends Model
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
    public function createProject()
    {
        $project = new Project();
        $project->name = $this->name;
        $project->activity = $this->activity;
        $project->accesses = $this->accesses;
        $project->logo = $this->logo;
        $project->save();

        // relate project and users if users array exist
        if ($this->users_id) {
            $allUsers = User::findAll($this->users_id);
            foreach ($allUsers as $k => $user) {
                $project->link('users', $user);
            }
        }

        return $project;
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
