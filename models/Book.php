<?php

namespace app\models;

use Yii;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * This is the model class for table "book".
 *
 * @property int $id
 * @property string $title Название книги
 * @property int $year Год выпуска
 * @property string|null $description Описание
 * @property string $isbn ISBN - Международный стандартный книжный номер
 * @property string|null $photo_path Путь к файлу фото главной страницы
 *
 * @property Author[] $authors
 */
class Book extends \yii\db\ActiveRecord
{
    /**
     * @var UploadedFile
     */
    public $imgFile;
    public $authorIds = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'book';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'year', 'isbn'], 'required'],
            [['year'], 'integer'],
            [['title', 'description', 'isbn', 'photo_path'], 'string', 'max' => 255],
            [['isbn'], 'unique'],
            [['authorIds'], 'required'],
            [['imgFile'], 'file', 'skipOnEmpty' => true, 'extensions' => ['png', 'jpg', 'jpeg'], 'checkExtensionByMimeType' => false, 'maxSize' => 1024 * 1024 * 5], // 5 Mb limit
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название книги',
            'year' => 'Год выпуска',
            'description' => 'Описание',
            'isbn' => 'ISBN - Международный стандартный книжный номер',
            'photo_path' => 'Файл фото главной страницы',
        ];
    }

    public function uploadPhoto()
    {
        $this->imgFile = UploadedFile::getInstance($this, 'imgFile');
        if ($this->imgFile && $this->validate()) {
            $photo_path = 'uploads/'. $this->imgFile->baseName. '.'. $this->imgFile->extension;
            if ($this->imgFile->saveAs($photo_path)) {
                $this->photo_path = $photo_path;
            }
            return true;
        }

        return false;
    }

    public function getUrl()
    {
        return Url::to('@web/uploads/' . StringHelper::basename($this->photo_path));
    }

    public function getAuthors()
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])->viaTable('book_author', ['book_id' => 'id']);
    }

    public function getBookAuthors()
    {
        return $this->hasMany(BookAuthor::class, ['book_id' => 'id']);
    }
}
