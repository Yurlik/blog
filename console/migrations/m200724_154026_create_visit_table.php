<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%visit}}`.
 */
class m200724_154026_create_visit_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%visit}}', [
            'id' => $this->primaryKey(),
            'blog_id' => $this->integer()->notNull(),
            'client_ip' => $this->string(50)->notNull(),
            'client_agent' => $this->text()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%visit}}');
    }
}
