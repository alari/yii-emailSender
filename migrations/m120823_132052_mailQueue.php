<?php

class m120823_132052_mailQueue extends EDbMigration
{
	public function up()
	{
        $this->createTable("{{mail_sender_queue}}", array(
            "id" => "pk",

            "processed" => "tinyint(1) DEFAULT 0",

            "mail_to" => "VARCHAR(255) NOT NULL",
            "mail_subject" => "VARCHAR(255) NOT NULL",
            "mail_body" => "TEXT NOT NULL",
            "mail_type" => "VARCHAR(16) NOT NULL"
        ), "ENGINE=myISAM CHARACTER SET utf8");
        $this->createIndex("processed_mail_queue", "{{mail_sender_queue}}", "processed");
	}

	public function down()
	{
		$this->dropTable("{{mail_sender_queue}}");
		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}