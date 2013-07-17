<?php
class MyFormatter extends CFormatter {
    
    public function formatBooleanIcon($value) {
        return $value ? CHtml::tag('div', array('class'=>'icon16 icon16-true'), CHtml::tag('span', array('class'=>'offscreen'), "Yes")) : CHtml::tag('div', array('class'=>'icon16 icon16-false'), CHtml::tag('span', array('class'=>'offscreen'), "No"));
    }
    
    public function formatSize($value) {
        if ($value >= 1024*1024*1024) // Go
            return round(($value / 1024)/1024/1024, 2) ." Go";
        elseif ($value >= 1024*1024) // Mo
            return round(($value / 1024)/1024, 2) ." Mo";
        elseif ($value >= 1024) // ko
            return round(($value / 1024), 2) ." Ko";
        else // octets
        return $value ." octets";
    }
}
?>