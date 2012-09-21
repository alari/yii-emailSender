<?php
/**
 * @author alari
 * @since 8/24/12 12:32 PM
 */
class SendSubscribersForm extends CFormModel
{
    public $subject;
    public $body;
    public $list;

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules()
    {
        return array(
            array('subject,body,list', 'required'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'subject'=>'Тема сообщения',
            'body'=>"Сообщение",
            'list'=>'Список рассылки'
        );
    }
}
