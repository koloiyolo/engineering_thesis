<?php

$groups = [];

$data = json_decode(get_data("root", "password"));
$data = encode_data($data);
$mappings = $data['mappings'];
$data = $data['result'];
$data = kmeans($data, '3', '100');
$result = decode_data($data, $mappings);
var_dump($result);
foreach ($result as $cluster) {
    echo json_encode($cluster) . "\n";
}


header('Content-Type: application/json');
echo json_encode($data);


// execute kmeans algorithm on "algorithms" server and retrive result
function kmeans($data, $centroids, $iters)
{
    $postData = [
        'centroids' => $centroids,
        'iterations' => $iters,
        'data' => json_encode($data)
    ];

    // Encode the JSON data
    $jsonContent = http_build_query($postData);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "http://algorithms:5000/kmeans");
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonContent);

    $result = curl_exec($curl);
    if ($result === false) {
        echo "Error processing request";
        return false;
    }
    curl_close($curl);
    var_dump($result);
    return json_decode($result);
}

// encode data working now
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
    // foreach ($data as $result) {
        foreach ($data as $cluster) {
            $tmp_array = [];

            // to delete \/\/\/\/\/\/\
            // foreach($data as $elem) {
            foreach ($cluster as $elem) {
                $tmp = $mappings[array_to_str($elem)];
                $tmp_array[] = $tmp;
            }

            $result[] = $tmp_array;
        }
    // }
    return $result;
}


// fetch data from db
function get_data($user, $password)
{
    $mysqli = new mysqli("db", $user, $password, "logs");

    // catches errors from db connections
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }

    $statement = $mysqli->prepare("SELECT datetime, tags, message FROM logs");
    $statement->execute();
    $result = $statement->get_result();
    $data = [];

    foreach ($result as $row) {
        $data[] = $row;
    }
    $statement->close();

    return json_encode($data);
}

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