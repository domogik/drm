<?php
    $json = array(
        'json_version' => 1,
        'id' => $model->id,
        'name' => $model->name,
        'generated' => $model->generated,
        'count' => $model->count,
        'status_url' => Yii::app()->createAbsoluteUrl("/repository/view", array("id" => $model->id)),
        'data_url' => Yii::app()->createAbsoluteUrl("/repository/data", array("id" => $model->id)),
        'icon_url' => Yii::app()->createAbsoluteUrl("/repository/icon", array("id" => $model->id)),
    );
    $jsonEncoded = json_encode($json);
    echo str_replace('\\/', '/', $jsonEncoded)
?>
