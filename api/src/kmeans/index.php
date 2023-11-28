<?php

$groups = [];

$data = json_decode(get_data("root", "password"));
$data = encode_data($data);
echo json_encode($data);
$mappings = $data['mappings'];
$data = $data['result'];
$data = decode_data($data, $mappings);
echo json_encode($data);


header('Content-Type: application/json');
echo json_encode($data);


// execute kmeans algorithm on "algorithms" server and retrive result
function kmeans($data, $centroids, $iters)
{
    $url = 'http://algorithms:5000/kmeans';

    $postData = [
        'data' => $data, 
        'centroids' => $centroids,
        'iterations' => $iters,
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($postData),
        ],
    ];

    $context = stream_context_create($options);

    $result = file_get_contents($url, false, $context);
    if ($result === false) {
        echo "Error processing request";
        return false;
    }

    echo "Server response: ";
    var_dump($result);

    return $result;
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
                if ($key === "message"){
                    $dummy[] = encode_message($value, $groups);
                }else {
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
    // foreach($data as $array) {
        $tmp_array = [];

        // to delete \/\/\/\/\/\/\
        foreach($data as $elem) {
        //foreach($array as $elem) {
            $tmp = $mappings[array_to_str($elem)];
            $tmp_array[] = $tmp;
        }

        $result[] = $tmp_array;
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

    $statement = $mysqli->prepare("SELECT * FROM logs");
    $statement->execute();
    $result = $statement->get_result();
    $data = [];

    foreach ($result as $row) {
        $data[] = $row;
    }
    $statement->close();

    return json_encode($data);
}

function encode_message($message, &$groups){
    foreach($groups as $key => $value) {
        if(preg_match($value, $message)) {
            return $key;
        }
    }

    preg_match('/(.*?)(?=msg=)/', $message, $matches);
    
    if (!empty($matches)) {
        $id = count($groups);
        $groups[] = '/' . $matches[0] . '/';
        return $id;
    }

    return null;
}

function array_to_str($array) {
    $result = "";
    foreach($array as $elem) {
        $result .= $elem . ", ";
    } 
    return $result;
}
?>