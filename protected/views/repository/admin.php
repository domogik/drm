<?php
$this->breadcrumbs=array(
	'Repositories',
);

$this->menu=array(
	array('label'=>'Create Repository', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('pay', "
jQuery('#repository-grid a.clear').live('click',function() {
        if(!confirm('Are you sure you want to clear this repository?')) return false;
        
        var url = $(this).attr('href');
        //  do your post request here
        $.post(url,function(res){
             $.fn.yiiGridView.update('repository-grid');
         });
        return false;
});
");

?>

<h1>Repositories</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'repository-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
        'priority',
		'name',
        array(
            'name'=>'versionsCount',
            'filter'=>false,
        ),
		array(
			'class'=>'CButtonColumn',
            'template'=>'{view}{update}{delete}{clear}',
            'buttons'=>array(
                'delete' => array(
                    'visible'=>'$data->id != Yii::app()->params["defaultRepository"]',
                ),
                'clear'=>array(
                    'label'=>'Clear',
                    'url'=>'Yii::app()->createUrl("/repository/clear", array("id" => $data->id))',
                    'visible'=>'$data->versionsCount > 0',
                    'options'=>array('class'=>'clear'),
                ),
            ),
		),
	),
)); ?>
