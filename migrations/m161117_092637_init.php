<?php


class m161117_092637_init extends \atans\user\migrations\Migration
{
    public function up()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'email' => $this->string(255)->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string(255)->notNull(),
            'password_reset_token' => $this->string(255)->unique(),
            'registration_ip' => $this->string(42)->null(),
            'status' => $this->string(32)->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $this->tableOptions);

        $this->createIndex('{{%user_username}}', '{{%user}}', 'username', true);
        $this->createIndex('{{%user_auth_key}}', '{{%user}}', 'auth_key');
        $this->createIndex('{{%user_email}}', '{{%user}}', 'email', true);
        $this->createIndex('{{%user_status}}', '{{%user}}', 'status');
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}