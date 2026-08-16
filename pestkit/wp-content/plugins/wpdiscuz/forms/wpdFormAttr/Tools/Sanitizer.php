<?php

namespace wpdFormAttr\Tools;

class Sanitizer {

    public static function sanitize($action, $variable_name, $filter, $default = "") {
        if ($filter === "FILTER_SANITIZE_STRING" || $filter === "FILTER_SANITIZE_TEXTAREA") {
            $glob = INPUT_POST === $action ? $_POST : $_GET;
            if (key_exists($variable_name, $glob)) {
                // The (string) casts below turn an array into "Array" and raise
                // an "Array to string conversion" warning; non-stringable
                // objects throw an Error. Bail out to $default before that.
                if (!is_scalar($glob[$variable_name])) {
                    return $default;
                }
                if ($filter === "FILTER_SANITIZE_TEXTAREA") {
                    return sanitize_textarea_field((string)$glob[$variable_name]);
                } else {
                    return sanitize_text_field((string)$glob[$variable_name]);
                }
            } else {
                return $default;
            }
        }
        $variable = isset($variable_name) ? filter_input($action, $variable_name, $filter) : '';
        return $variable ? $variable : $default;
    }

}
