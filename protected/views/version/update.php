<?php
$this->breadcrumbs=array(
	'Packages'=>array('/package/index'),
    $model->package->name=>array('/package/view', 'id'=>$model->package_id),
    'Version',
    $model->number,
);

$this->menu=array(
	array('label'=>'Package', 'url'=>array('/package/view', 'id'=>$model->package_id)),
	array('label'=>'Delete Version', 'url'=>array('delete', 'id'=>$model->id)),
);
?>

<h1>Update Version</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>