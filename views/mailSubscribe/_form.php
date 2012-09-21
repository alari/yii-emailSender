<?/* @var $form GxActiveForm */
if(!$model->hashcode) $model->hashcode = uniqid();
?>
<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'mail-subscribe-form',
	'enableAjaxValidation' => false,
));
?>

	<p class="note">
		Fields with <span class="required">*</span> are required.
	</p>

	<?php echo $form->errorSummary($model); ?>

		<div class="row">
		<?php echo $form->labelEx($model,'list'); ?>
		<?php echo $form->dropDownList($model, 'list', Yii::app()->getModule("emailSender")->subscribeLists); ?>
		<?php echo $form->error($model,'list'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model, 'email', array('maxlength' => 255)); ?>
		<?php echo $form->error($model,'email'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'hashcode'); ?>
		<?php echo $form->textField($model, 'hashcode', array('maxlength' => 127)); ?>
		<?php echo $form->error($model,'hashcode'); ?>
		</div><!-- row -->


<?php
echo GxHtml::submitButton('Save');
$this->endWidget();
?>
</div><!-- form -->