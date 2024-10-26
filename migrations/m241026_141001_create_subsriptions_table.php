<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subsriptions}}`.
 */
class m241026_141001_create_subsriptions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%subsriptions}}', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer(),
            'phone_number' => $this->string(),
        ]);

        $this->addForeignKey(
            'fk-subsriptions-author_id',
            '{{%subsriptions}}',
            'author_id',
            '{{%author}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%subsriptions}}');

        $this->dropForeignKey('fk-subsriptions-author_id', '{{%subsriptions}}');
    }
}
