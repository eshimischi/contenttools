<?php

namespace bizley\contenttools\models;

use Exception;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * @author Paweł Bizley Brzozowski
 * @version 1.0
 * @license Apache 2.0
 * https://github.com/bizley-code/yii2-content-tools
 * http://www.yiiframework.com/extension/yii2-content-tools
 * 
 * ContentTools was created by Anthony Blackshaw
 * http://getcontenttools.com/
 * https://github.com/GetmeUK/ContentTools
 * 
 * This model is used by UploadAction to validate and save the image uploaded 
 * through Yii 2 ContentTools editor.
 * 
 * Images are stored in the 'content-tools-uploads' web accessible folder.
 */
class ImageForm extends Model
{
    
    const UPLOAD_DIR = 'content-tools-uploads';
    
    /**
     * @var UploadedFile Uploaded image
     */
    public $image;
    
    /**
     * @var string Web accessible path to the uploaded image
     */
    public $url;
    
    /**
     * @var string Server accessible root to the uploaded image
     */
    public $path;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['image', 'image', 'extensions' => ['png', 'jpg','JPG', 'JPEG', 'jpeg', 'PNG', 'gif'], 'maxWidth' => 4000, 'maxHeight' => 30000, 'maxSize' => 10 * 1024 * 1024 * 1000]
        ];
    }
    
    /**
     * Validates and saves the image.
     * Creates the folder to store images if necessary.
     * @return boolean
     */
    public function upload()
    {
        try {
            if ($this->validate()) {

                $save_year  = date('Y');
                $save_month = date('m');

                $save_path = Yii::getAlias('@webroot') . '/../' . 'statics' . DIRECTORY_SEPARATOR . self::UPLOAD_DIR;

                FileHelper::createDirectory($save_path);

                if (!is_dir($save_path . DIRECTORY_SEPARATOR . $save_year)) {
                    mkdir($save_path . DIRECTORY_SEPARATOR . $save_year, 0755, true);
                }

                if (!is_dir($save_path . DIRECTORY_SEPARATOR . $save_year . DIRECTORY_SEPARATOR . $save_month)) {
                    mkdir($save_path . DIRECTORY_SEPARATOR . $save_year . DIRECTORY_SEPARATOR . $save_month, 0755, true);   
                }

                $newName = self::hashName($this->image->baseName);

                $this->path = $save_path . DIRECTORY_SEPARATOR . $save_year . DIRECTORY_SEPARATOR . $save_month . DIRECTORY_SEPARATOR . $newName . '.' . $this->image->extension;
                $this->url  = urldecode(Yii::getAlias('/statics/' . self::UPLOAD_DIR . '/' . $save_year . '/' . $save_month . '/' . $newName . '.' . $this->image->extension));
            
                return $this->image->saveAs($this->path);
            }
        } catch (Exception $e) {
            Yii::error($e->getMessage());
        }
        return false;
    }

    protected function hashName($name)
    {
        return md5($name);
    }
}
