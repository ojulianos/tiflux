<?php

if(!function_exists('is_cli')) {
    function is_cli() {
        if( empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0) 
        {
            return true;
        } 
        return false;
    }
}

if(!function_exists('toSnakeCase')) {
    function toSnakeCase($value, $delimiter = '_') {
        if (!ctype_lower($value)) {
            $value = trim(preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $value));
            $value = strtolower(str_replace(' ', $delimiter, $value));
        }

        return $value;
    }
}

if(!function_exists('filterField')) {
    function filterField($input)
    {
        return filter_var(
            str_replace(
                ['-', ' ', '/', '\\', '(', ')', '.'], '', trim($input)
            ), FILTER_SANITIZE_FULL_SPECIAL_CHARS
        );
    }
}