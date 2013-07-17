<?php
$this->layout='//layouts/packages';

$baseUrl = Yii::app()->baseUrl; 
$cs = Yii::app()->getClientScript();
$cs->registerCssFile($baseUrl.'/css/jquery.qtip.min.css');
$cs->registerScriptFile($baseUrl . '/js/jquery.qtip.min.js', CClientScript::POS_HEAD);
$cs->registerScript('tooltips',"
    $('.tooltip').delegate(this, 'mouseover', function(event) {
        $(this).qtip({
            overwrite: false, // Make sure the tooltip won't be overridden once created
            content: {
               attr: 'tooltip'
            },
            title: {
                text: 'Changelog',
            },
            show: {
                event: event.type,
                ready: true // Show the tooltip as soon as it's bound, vital so it shows up the first time you hover!
            },
            position: {
                my: 'right center',  // Position my top left...
                at: 'left center', // at the bottom right of...
            }
        }, event);
    });",CClientScript::POS_READY);
   
Yii::app()->clientScript->registerScript('search', "
    function updateList() {
        $.fn.yiiGridView.update('packages-grid', {
            data: $('#search').serialize()
        });
        return false;
    }
    $('input#q').keyup(function(){updateList();});
    $('#repositories input').change(function(){updateList();});

    ",CClientScript::POS_READY);
?>

<div id='packages' class='section'>
<h1>Domogik Packages</h1>

<!-- add a search box: -->
<?php $form=$this->beginWidget('CActiveForm', array(
    'method'=>'get',
    'id'=>'search',
)); ?>
    <input type="text" id="q" name="q" placeholder="Search..." />
    <div id='repositories'><?php echo CHtml::checkboxList('repositories', $repositories, CHtml::listData(Repository::model()->findAll(), 'id', 'name'), array('separator'=>false)); ?></div>
<?php $this->endWidget(); ?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'packages-grid',
	'dataProvider'=>$dataprovider,
	'filter'=>NULL,
    'summaryText'=>false,
	'columns'=>array(
        array(
            'value'=>'"<span class=\"packageicon\" style=\"background-image:url(" . Yii::app()->createUrl("package/displayIcon", array("type"=>($data->type->id), "package"=>($data->name))) . ")\"></span>"',
            'type'=>'raw',
        ),
        array(
            'name'=>'name',
            'htmlOptions'=>array('class'=>'packagename'),
        ),
		'description',
        'author',
        array(
            'header'=>'Versions',
            'type'=>'raw',
            'value'=>'$data->versionsHtml',
            'htmlOptions'=>array('class'=>'nowrap'),
        ),
        array(
            'name'=>'type_id',
            'value'=>'$data->type->name',
            'filter'=>CHtml::listData(Type::model()->findAll(), 'id', 'name'),   
            'htmlOptions'=>array('class'=>'nowrap'),
        ),
        array(
            'class'=>'CLinkColumn',
            'labelExpression'=>'($data->documentation)?"Specifications":""',
            'urlExpression'=>'$data->documentation',
        ),
	),
)); ?>
</div>
