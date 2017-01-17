<?php


class m170116_125100_usertoken extends \atans\user\migrations\Migration
{
    public function up()
    {
        $this->createTable('{{%user_token}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->null(),
            'type' => $this->string(32)->notNull(),
            'token' => $this->string(255)->notNull(),
            'data' => $this->text()->null(),
            'created_at' => $this->dateTime()->notNull(),
            'expired_at' => $this->dateTime()->null(),
        ], $this->tableOptions);

        $this->createIndex('{{%user_token_user_id}}', '{{%user_token}}', 'user_id');
        $this->createIndex('{{%user_token_type}}', '{{%user_token}}', 'type');
        $this->createIndex('{{%user_token_token}}', '{{%user_token}}', 'token');
    }

    public function down()
    {
        $this->dropTable('{{%user_token}}');
    }
}