<?php

/**
 * Created by PhpStorm.
 * User: joecliff
 * Date: 14/12/9
 * Time: 下午7:50
 */
final class DbHelper {

    /**
     * @param $table
     * @param $pkArray array(array(name,value,wrapWithQuotes,escape))
     * @param $dataArray array(array(name,value,wrapWithQuotes,escape))
     */
    public static function update($table, $pkArray = array(), $dataArray = array()) {
        $db = getRegistry()->get('db');

        $sql = "UPDATE `" . DB_PREFIX . "{$table}` SET ";

        $params = self::buildParams($dataArray);
        $sql .= join(',', $params);

        $whereParams = DbHelper::buildParams($pkArray);
        $sql .= ' where ' . join(' & ', $whereParams);

        $db->query($sql);
    }

    /**
     * @param $table
     * @param $pkArray array(array(name,value,wrapWithQuotes,escape))
     */
    public static function get($table, $pkArray = array()) {
        $db = getRegistry()->get('db');

        $sql = "SELECT * FROM `" . DB_PREFIX . "{$table}` ";

        $whereParams = DbHelper::buildParams($pkArray);
        $sql .= ' where ' . join(' & ', $whereParams);

        $rows = $db->query($sql)->rows;
        return isset($rows) && count($rows) > 0 ? $rows[0] : null;
    }

    public static function getSingleValue($sql, $default) {
        $db = getRegistry()->get('db');

        $rows = $db->query($sql,false)->rows;

        if (isset($rows) && count($rows) > 0) {
            $row = $rows[0];

            $data = array_values($row);

            return $data[0];
        } else {
            return $default;
        }
    }

    /**
     * @param $table
     * @param $data  array(array(name,value,wrapWithQuotes,escape))
     */
    public static function insert($table, $data = array()) {
        $db = getRegistry()->get('db');

        $sql = "INSERT INTO `" . DB_PREFIX . "{$table}` SET ";

        $params = self::buildParams($data);
        $sql .= join(',', $params);
        $db->query($sql);
        return $db->getLastId();
    }

    private function filterRowValues($row, $db, $columns) {
        $result = array();

        foreach ($row as $index => $value) {
            $colDefine = $columns[$index];
            $wrapWithQuotes = $colDefine[1];

            $escape = (count($colDefine) > 2 ? $colDefine[2] : false);

            $valueStr = $escape ? $db->escape($value) : $value;
            $result[] = ($wrapWithQuotes ? "'{$valueStr}'" : $valueStr);
        }
        return $result;
    }

    private static function filterRows($data,$columns,$db) {
        $result=array();
        foreach($data as $row){
            $values = self::filterRowValues($row,$db,$columns);
            $rowStr = join(',', $values);
            $result[]= "({$rowStr})";
        }

        return $result;
    }

    /**
     * @param $table
     * @param $columns array(array(name,wrapWithQuotes,escape))
     * @param $data  array(val1,val2,...)
     */
    public static function bulkInsert($table, $columns = array(), $data = array()) {
        $db = getRegistry()->get('db');


        $colHeaders = ArrayHelper::pickFields($columns, 0);
        $columnsStr = join(',', $colHeaders);

        $values=self::filterRows($data,$columns,$db);
        $valuesStr = join(',',$values );

        $sql = "INSERT INTO " . DB_PREFIX . "{$table} ({$columnsStr}) values {$valuesStr} ";

        $db->query($sql);
    }

    /**
     * @param $data
     * @return array
     */
    private static function buildParams($data) {
        $db = getRegistry()->get('db');

        $params = array();
        foreach ($data as $item) {
            $name = $item[0];
            $value = $item[1];
            $wrapWithQuotes = (count($item) > 2 ? $item[2] : false);
            $escape = (count($item) > 3 ? $item[3] : false);
            $spliter = (count($item) > 4 ? $item[4] : '=');
            $valueStr = $escape ? $db->escape($value) : $value;
            $params[] = ($name .' '. $spliter .' '. ($wrapWithQuotes ? "'{$valueStr}'" : $valueStr));
        }
        return $params;
    }
}