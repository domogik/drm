<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'member-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

<?php if ($model->isNewRecord): ?>
	<div class="row">
		<?php echo $form->labelEx($model,'type_id'); ?>
   		<?php echo $form->dropDownList($model,'type_id', CHtml::listData(Type::model()->findAll(), 'id', 'name')); ?>
		<?php echo $form->error($model,'type_id'); ?>
	</div>
<?php endif; ?>

	<div class="row">
		<?php echo $form->labelEx($model,'id'); ?>
		<?php echo $form->textField($model,'id',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'author'); ?>
		<?php echo $form->textField($model,'author',array('size'=>45,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'author'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'authorEmail'); ?>
		<?php echo $form->textField($model,'authorEmail',array('size'=>45,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'authorEmail'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'documentation'); ?>
		<?php echo $form->textField($model,'documentation',array('size'=>45,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'documentation'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->