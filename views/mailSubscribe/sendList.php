<?php
/**
 * @author alari
 * @since 8/24/12 12:26 PM
 */
/* @var $form CActiveForm */
if(!$sent) {
    $form = $this->beginWidget('ext.shared-core.widgets.ExtForm', array(
        "model"=>$model,
        'enableAjaxValidation' => false,
    ));
?>

<h1>Отправить письмо подписчикам</h1>


<div class="row">
    <?=$form->labelEx($model, "list")?>
    <?=$form->dropDownList($model, 'list', Yii::app()->getModule("emailSender")->subscribeLists); ?>
    <?=$form->error($model, "list")?>
</div>
    <div class="row">
        <?=$form->labelEx($model, "subject")?>
        <?=$form->textField($model, "subject")?>
        <?=$form->error($model, "subject")?>
    </div>

<div class="row">
    <?=$form->labelEx($model, "body")?>
    <?$form->wysiwyg($model, "body")?>
    <?=$form->error($model, "body")?>
</div>

    <?=CHtml::submitButton("Отправить")?>


<?$this->endWidget(); } else {?>

    <h2>Ваше сообщение успешно отправлено!</h2>

                     <?}?>