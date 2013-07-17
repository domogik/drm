<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'version-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'number'); ?>
		<?php echo $form->textField($model,'number',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'number'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'deployed'); ?>
		<?php echo $form->checkbox($model,'deployed'); ?>
		<?php echo $form->error($model,'deployed'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'comment'); ?>
		<?php echo $form->textArea($model,'comment',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'comment'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'changelog'); ?>
		<?php echo $form->textArea($model,'changelog',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'changelog'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'repository_id'); ?>
		<?php echo $form->dropDownList($model,'repository_id',CHtml::listData(Repository::model()->findAll(), 'id', 'name')); ?>
		<?php echo $form->error($model,'repository_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->