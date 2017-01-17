<?php


class m170116_172500_user extends \atans\user\migrations\Migration
{
    public function up()
    {
        $this->dropColumn('{{%user}}', 'password_reset_token');

        $this->addColumn('{{%user}}', 'logged_in_ip', 'VARCHAR(40) NULL AFTER `registration_ip`');
        $this->addColumn('{{%user}}', 'logged_in_at', 'DATETIME NULL AFTER `logged_in_ip`');
    }
}