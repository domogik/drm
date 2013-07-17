<?php
    $this->pageTitle = "Domogik Repositories";
?>
<div id='repositories'>
    <ul>
    <?php $this->widget('zii.widgets.CListView', array(
        'dataProvider'=>$dataProvider,
        'itemView'=>'_view',
        'enableSorting'=>false,
        'enablePagination'=>false,
        'summaryText'=>false,
    )); ?>
    </ul>
</div>

