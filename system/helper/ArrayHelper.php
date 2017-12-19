<?php

/**
 * Created by PhpStorm.
 * User: joecliff
 * Date: 14/12/16
 * Time: 下午4:34
 */
class ArrayHelper {
    public static function pickFields($array,$fieldNameOrIndex) {
        $result=array();
        foreach($array as $row){
            $result[]=$row[$fieldNameOrIndex];
        }
        return $result;
    }
}