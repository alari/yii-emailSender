<?php

Yii::import('application.modules.emailSender.models._base.BaseMailSubscribe');

class MailSubscribe extends BaseMailSubscribe
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public static function representingColumn() {
        return "email";
    }
}