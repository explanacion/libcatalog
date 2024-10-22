<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%book}}`.
 */
class m241020_191004_create_book_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%book}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull()->comment('Название книги'),
            'year' => $this->integer()->notNull()->comment('Год выпуска'),
            'description' => $this->string()->null()->comment('Описание'),
            'isbn' => $this->string()->notNull()->comment('ISBN - Международный стандартный книжный номер'),
            'photo_path' => $this->string()->comment('Путь к файлу фото главной страницы'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%book}}');
    }
}
