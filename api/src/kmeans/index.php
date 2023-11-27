<?php
$data = get_data("root", "password");
$data = json_decode($data);
$data = convert_from_obj_to_array_of_str($data);

$encodingResult = encode_data($data);
$data = $encodingResult['encodedData'];
$labelMapping = $encodingResult['labelMapping'];
$data = kmeans($data, 3, 200);
$data = decode_data($data, $labelMapping);

header('Content-Type: application/json');
echo json_encode($data);




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
    $flatData = array_merge(...array_map('array_values', $data));
    $uniqueValues = array_unique($flatData);
    $labelMapping = array_flip($uniqueValues);
    $encodedData = array_map(function ($innerArray) use ($labelMapping) {
        return array_map(function ($value) use ($labelMapping) {
            return $labelMapping[$value];
        }, $innerArray);
    }, $data);

    return ['encodedData' => $encodedData, 'labelMapping' => $labelMapping];
}


// decode data
function decode_data($encodedData, $labelMapping)
{
    // Decode the data
    return array_map(function ($innerArray) use ($labelMapping) {
        // Map each encoded value in the inner array using the label mapping
        return array_map(function ($encodedValue) use ($labelMapping) {
            return $labelMapping[$encodedValue];
        }, $innerArray);
    }, $encodedData);
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

    return json_encode($data);
}

// f$@k OOP
function convert_from_obj_to_array_of_str($data)
{
    $result = [];
    foreach($data as $object) {
        $obj = [ 
            'datetime' => $object->datetime,
            'host'     => $object->host,
            'program'  => $object->program,
            'message'  => $object->message
        ];
        $result[] = $obj;
    }

    return $result;
}
?>