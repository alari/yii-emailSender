<?php

class MailSubscribeController extends GxController {

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions'=>array('index','view','unsubscribe'),
                'users'=>array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions'=>array('create','update'),
                'users'=>array('@'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions'=>array('admin','delete','send'),
                'users'=>array('admin'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public function actionSend() {
        $model = new SendSubscribersForm();
        $sent = false;
        if(isset($_POST[get_class($model)])) {
            $model->attributes = $_POST[get_class($model)];
            if($model->validate()) {
                Yii::app()->getModule("emailSender")->putToList($model->list, $model->subject, $model->body);
                $sent = true;
            }
        }
        $this->render("sendList", array("model"=>$model, "sent"=>$sent));
    }

    public function actionUnsubscribe($hash) {
        $subscribe = MailSubscribe::model()->findByAttributes(array("hashcode"=>$hash));
        if($subscribe) $subscribe->delete();
        $this->render(Yii::app()->getModule("emailSender")->unsubscribeView);
    }

	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'MailSubscribe'),
		));
	}

	public function actionCreate() {
		$model = new MailSubscribe;


		if (isset($_POST['MailSubscribe'])) {
			$model->setAttributes($_POST['MailSubscribe']);

			if ($model->save()) {
				if (Yii::app()->getRequest()->getIsAjaxRequest())
					Yii::app()->end();
				else
					$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create', array( 'model' => $model));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'MailSubscribe');


		if (isset($_POST['MailSubscribe'])) {
			$model->setAttributes($_POST['MailSubscribe']);

			if ($model->save()) {
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('update', array(
				'model' => $model,
				));
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$this->loadModel($id, 'MailSubscribe')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('MailSubscribe');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new MailSubscribe('search');
		$model->unsetAttributes();

		if (isset($_GET['MailSubscribe']))
			$model->setAttributes($_GET['MailSubscribe']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}