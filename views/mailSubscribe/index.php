<?php

$this->breadcrumbs = array(
	MailSubscribe::label(2),
	'Index',
);

$this->menu = array(
	array('label'=>'Create' . ' ' . MailSubscribe::label(), 'url' => array('create')),
	array('label'=>'Manage' . ' ' . MailSubscribe::label(2), 'url' => array('admin')),
);
?>

<h1><?php echo GxHtml::encode(MailSubscribe::label(2)); ?></h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); 