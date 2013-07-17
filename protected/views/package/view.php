<?php
$this->breadcrumbs=array(
	'Packages'=>array('admin'),
	$model->name,
);

$this->menu=array(
	array('label'=>'Create Package', 'url'=>array('create')),
//	array('label'=>'Update Package', 'url'=>array('update', 'id'=>$model->id)),
//	array('label'=>'Delete Package', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Packages', 'url'=>array('admin')),
);

$pageSize=Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize']);
Yii::app()->clientScript->registerScript('initPageSize',"
    $('.change-pagesize').live('change', function() {
        $.fn.yiiGridView.update('versions-grid',{ data:{ pageSize: $(this).val() }})
    });",CClientScript::POS_READY);
    

?>

<h1>Package #<?php echo $model->id; ?></h1>
<div id='packageDetails' class='section'>
    <h2>Details</h2>
    <?php $this->widget('zii.widgets.CDetailView', array(
        'data'=>$model,
        'attributes'=>array(
            'id',
            'name',
            'author',
            'authorEmail:email',
            'documentation:url',
            'description:ntext',
            'type.name',
            'versionsCount',
            array(
                'label'=>'Icon',
                'type'=>'raw',
                'value'=>CHtml::image(Yii::app()->createUrl('package/displayIcon', array('repository'=>'NULL', 'type'=>$model->type_id, 'package'=>$model->id, 'version'=>'NULL')), 'Icon'),
            ),
        ),
    )); ?>
</div>
<div id='packageVersions' class='section'>
    <h2>Versions</h2>
<?php
    echo "<p>Show " . CHtml::dropDownList(
                'pageSize',
                $pageSize,
                array(10=>10,20=>20,50=>50,100=>100, 200=>200, 500=>500),
                array('class'=>'change-pagesize')
            ) . " Versions per page</p>";
?>

    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'versions-grid',
        'dataProvider'=>$modelVersion->search($model->type_id, $model->id),
        'filter'=>$modelVersion,
        'columns'=>array(
            'number',
            array(
                'name'=>'repository_id',
                'value'=>'$data->repository->name',
                'header'=>'Repository',
                'filter'=>CHtml::listData(Repository::model()->findAll(), 'id', 'name'),
            ),
            'date_added',
            array(
                'name'=>'filesize',
                'type'=>'size',
                'filter'=>false,
            ),
            array(
                'name'=>'deployed',
                'type'=>'booleanIcon',
                'filter'=>array(0=>'Undeployed', 1=>'Deployed'),
            ),
            array(
                'name'=>'comment',
                'value'=>'substr($data->comment, 0, 50)',
                'type'=>'ntext',
                'filter'=>false,
            ),
            array(
                'name'=>'stat_downloads',
                'filter'=>false,
            ),
            array(
                'class'=>'CButtonColumn',
                'template'=>'{update}{delete}',
                'buttons'=>array(
                    'update'=>array(
                        'url'=>'Yii::app()->createUrl("/version/update", array("id" => $data->id))',
                    ),
                    'delete'=>array(
                        'url'=>'Yii::app()->createUrl("/version/delete", array("id" => $data->id))',
                    ),
                ),
            ),
            array(
                'header'=>'JSON',
                'class'=>'CButtonColumn',
                'template'=>'{download}',
                'buttons'=>array(
                    'download'=>array(
                        'label'=>'Download',
                        'url'=>'Yii::app()->createUrl("/version/info", array("repository" => $data->repository_id, "type" => $data->type_id, "package" => $data->package_id, "version" => $data->number))',
                        'visible'=>'$data->deployed',
                    ),
                ),
            ),
            array(
                'header'=>'TGZ',
                'class'=>'CButtonColumn',
                'template'=>'{download}',
                'buttons'=>array(
                    'download'=>array(
                        'label'=>'Download',
                        'url'=>'Yii::app()->createUrl("/version/download", array("repository" => $data->repository_id, "type" => $data->type_id, "package" => $data->package_id, "version" => $data->number))',
                        'visible'=>'$data->deployed',
                    ),
                ),
            ),
        ),
    )); ?>
</div>