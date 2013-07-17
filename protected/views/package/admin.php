<?php
$this->breadcrumbs=array(
	'Packages',
);

$this->menu=array(
	array('label'=>'Create Package', 'url'=>array('create')),
);

$pageSize=Yii::app()->user->getState('pageSize',50);
Yii::app()->clientScript->registerScript('initPageSize',"
    $('.change-pagesize').live('change', function() {
        $.fn.yiiGridView.update('packages-grid',{ data:{ pageSize: $(this).val() }})
    });",CClientScript::POS_READY);
    
?>

<h1>Packages</h1>
<div id='packages' class='section'>
<?php
    echo "<p>Show " . CHtml::dropDownList(
                'pageSize',
                $pageSize,
                array(10=>10,20=>20,50=>50,100=>100, 200=>200, 500=>500),
                array('class'=>'change-pagesize')
            ) . " Packages per page</p>";
?>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'packages-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'name',
		'description',
        array(
            'name'=>'type_id',
            'value'=>'$data->type->name',
            'filter'=>CHtml::listData(Type::model()->findAll(), 'id', 'name'),   
        ),
        array(
            'name'=>'versionsCount',
            'filter'=>false,
        ),
		array(
            'class'=>'CButtonColumn',
            'template'=>'{view}{update}{delete}',
            'buttons'=>array(
                'view'=>array(
                    'url'=>'Yii::app()->createUrl("/package/view", array("type_id" => $data->type_id, "id" => $data->id))',
                ),
                'update'=>array(
                    'url'=>'Yii::app()->createUrl("/package/update", array("type_id" => $data->type_id, "id" => $data->id))',
                ),
                'delete'=>array(
                    'url'=>'Yii::app()->createUrl("/package/delete", array("type_id" => $data->type_id, "id" => $data->id))',
                ),
            ),
		),
	),
)); ?>
</div>
