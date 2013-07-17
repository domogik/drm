<?php
$this->breadcrumbs=array(
	'Packages'=>array('admin'),
	'Create',
);

$this->menu=array(
	array('label'=>'Manage Packages', 'url'=>array('admin')),
);
?>

<h1>Create Package</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>