<?php

/**
 * Created by PhpStorm.
 * User: joecliff
 * Date: 14/12/9
 * Time: 下午7:50
 */
final class ReqHelper {

    public static function param($req, $method, $paramName, $default) {
        $params = $req->{$method};
        return isset($params[$paramName]) ? $params[$paramName] : $default;
    }


    public static function parseQueryParams($req, $paramMetas = array()) {
        $queryParams = array();

        foreach ($paramMetas as $meta) {
            $method = $meta[0];
            $paramName = $meta[1];
            $default = $meta[2];
            $queryParams[$paramName] = self::param($req, $method, $paramName, $default);
        }
        return $queryParams;
    }

    public static function joinQueryParams($parsedParams, $includeParamNames) {
        $filteredParams = array();
        foreach ($includeParamNames as $name) {
            $val = $parsedParams[$name];
            if (isset($val)) {
                $filteredParams[] = $name . '=' . $val;
            }
        }
        return '&'.join('&', $filteredParams);
    }
}