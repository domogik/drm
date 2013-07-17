<li id='<?php echo $data->id; ?>' class='icon-<?php echo $data->icon; ?>'>
<?php
    echo CHtml::link($data->name, array('/repository/view', 'id'=>$data->id), array('class'=>'title'));
    echo "<div class='description'>";
    echo "<p>" . $data->description . "</p>";
    echo "<pre>#" . $data->name . " repository";
    echo "\n#priority      url";
    echo "\n" . $data->priority . "            " . Yii::app()->createAbsoluteUrl('/repository/view', array('id'=>$data->id));
    echo "</pre></div>";
?>
</li>
