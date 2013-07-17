<?php
$this->breadcrumbs=array(
	'Packages'=>array('admin'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Create Package', 'url'=>array('create')),
	array('label'=>'View Package', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Packages', 'url'=>array('admin')),
);
?>

<h1>Update Package <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>