<?php
$this->breadcrumbs=array(
	'Repositories'=>array('admin'),
	'Create',
);

$this->menu=array(
	array('label'=>'Manage Repositories', 'url'=>array('admin')),
);
?>

<h1>Create Repository</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>