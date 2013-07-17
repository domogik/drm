<?php
$this->breadcrumbs=array(
	'Packages'=>array('admin'),
	'upload',
);

$this->menu=array(
	array('label'=>'Manage Packages', 'url'=>array('package/admin')),
);

?>

<h1>Upload Package</h1>
<div id='packageUpload' class='section'>
    <?php $this->widget('ext.EAjaxUpload.EAjaxUpload',
        array(
            'id'=>'upload',
            'config'=>array(
                'action'=>Yii::app()->createUrl('version/send'),
                'allowedExtensions'=>array("tgz"), //array("jpg","jpeg","gif","exe","mov" and etc...
                'sizeLimit'=>2*1024*1024,// maximum file size in bytes
            ),
        )); ?>
</div>