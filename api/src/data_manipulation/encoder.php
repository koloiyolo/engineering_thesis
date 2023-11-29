<?php

include("misc.php");

// prepare data for algorithms



// encode data

function encode_data($data)
{
    $result = [];
    $tmp_mappings = [];
    $mappings = [];
    $groups = [];

    // iter through elem of object
    foreach ($data as $object) {
        $dummy = [];
        // iter through elem of object
        foreach ($object as $key => $value) {
            // if exists mapping with the value in the key
            if ($key !== "datetime") {
                if ($key === "message") {
                    $dummy[] = encode_message($value, $groups);
                } else {
                    if (isset($tmp_mappings[$key])) {
                        if (isset($tmp_mappings[$key][$value])) {
                            $dummy[] = $tmp_mappings[$key][$value];
                        } else {
                            // create new mapping
                            $tmp_mappings[$key][$value] = count($tmp_mappings[$key]);
                            $dummy[] = $tmp_mappings[$key][$value];
                        }
                    } else {
                        // this if always goes here
                        $tmp_mappings[$key] = [$value => 0];
                        $dummy[] = 0;
                    }
                }
            }

        }
        $result[] = $dummy;
        $mappings[array_to_str($dummy)] = $object;
    }

    return [
        'result' => $result,
        'mappings' => $mappings
    ];
}



// decode data
function decode_data($data, $mappings)
{
    $result = [];
    foreach ($data as $cluster) {
        $tmp_array = [];
        foreach ($cluster as $elem) {
            $tmp = $mappings[array_to_str($elem)];
            $tmp_array[] = $tmp;
        }

        $result[] = $tmp_array;
    }
    return $result;
}


?>