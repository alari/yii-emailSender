<?php

class m120823_132515_mailSubscribers extends EDbMigration
{
	public function up()
	{
        $this->createTable("{{mail_subscribe}}", array(
            "id"=>"pk",
            "list"=>"VARCHAR(32) NOT NULL",
            "email"=>"VARCHAR(255) NOT NULL",
            "hashcode"=>"VARCHAR(127) NOT NULL"
        ), "ENGINE=myISAM CHARACTER SET utf8");
        $this->createIndex("mail_subscribe_hashcode", "{{mail_subscribe}}", "hashcode", true);
        $this->addColumn("{{mail_sender_queue}}", "list", "VARCHAR(32) DEFAULT NULL");
	}

	public function down()
	{
		echo "m120823_132515_mailSubscribers does not support migration down.\n";
		return false;
	}
}