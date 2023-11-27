<?php

var_dump(get_data("root", "password"));
// $data_copy = $data;

// $data = encode_data($data);
// $data = kmeans($data, 3, 200);
// $data = decode_data($data, $data_copy);

// header('Content-Type: application/json');
// echo json_encode($data);




// execute kmeans algorithm on "algorithms" server and retrive result
function kmeans($data, $centroids, $iters) 
{
    $url = 'http://algorithms:5000/kmeans';
    $args = ['data' => json_encode($data), 'centroids' => $centroids, 'iterations' => $iters];
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($args),
        ],
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === false) {
        echo "Error proccessing request";
    }

    var_dump($result);
    return $result;
}


// encode data
function encode_data($data)
{
    $uniqueValues = array_unique($data);
    $labelMapping = array_flip($uniqueValues);
    return array_map(function ($value) use ($labelMapping) {
        return $labelMapping[$value];
    }, $data);
}


// decode data
function decode_data($data, $data_copy)
{
    $labelMapping = array_combine(range(0, count($data_copy) - 1), $data_copy);

    return array_map(function ($item) use ($labelMapping) {
        if (is_array($item)) {
            return decode_data($item, $labelMapping);
        } else {
            return $labelMapping[$item];
        }
    }, $data);
}


// fetch data from db
function get_data($user, $password)
{
    $mysqli = new mysqli("db", $user, $password,  "logs");

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

    return $data;
}

?>