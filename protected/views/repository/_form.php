<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'repository-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

    <?php if ($model->isNewRecord): ?>
	<div class="row">
		<?php echo $form->labelEx($model,'id'); ?>
		<?php echo $form->textField($model,'id',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'id'); ?>
	</div>
    <?php endif; ?>
    
	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>
    
    <div class="row">
        <?php echo $form->labelEx($model,'description'); ?>
        <?php echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50)); ?>
        <?php echo $form->error($model,'description'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'priority'); ?>
        <?php echo $form->textField($model,'priority'); ?>
        <?php echo $form->error($model,'priority'); ?>
    </div>

	<div class="row">
		<?php echo $form->labelEx($model,'icon'); ?>
   		<?php echo $form->dropDownList($model,'icon', array('None'=>'', 'stable'=>'Stable', 'experimental'=>'Experimental', 'testing'=>'Testing', 'nightly'=>'Nightly')); ?>
		<?php echo $form->error($model,'icon'); ?>
	</div>
    
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->