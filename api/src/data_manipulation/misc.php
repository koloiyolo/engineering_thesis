<?php

function encode_message($message, &$groups)
{
    foreach ($groups as $key => $value) {
        if (preg_match($value, $message)) {
            return $key;
        }
    }

    if (preg_match('/(msg=)/', $message, $matches, PREG_OFFSET_CAPTURE)) {
        $regex = "/(" . substr($message, 0, $matches[0][1]) . ")/";
        $id = count($groups);
        $groups[] = $regex;
        return $id;
    } else {
        $regex = "/(" . $message . ")/";
        $id = count($groups);
        $groups[] = $regex;
        return $id;
    }
}

function array_to_str($array)
{
    $result = "";
    foreach ($array as $elem) {
        $result .= $elem . ", ";
    }
    return $result;
}
?>