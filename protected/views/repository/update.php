<?php
$this->breadcrumbs=array(
	'Repositories'=>array('admin'),
	$model->name,
	'Update',
);

$this->menu=array(
	array('label'=>'Create Repository', 'url'=>array('create')),
	array('label'=>'View Repository', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Repositories', 'url'=>array('admin')),
);
?>

<h1>Update Repository <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>