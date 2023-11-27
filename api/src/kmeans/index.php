<?php
$data = get_data("root", "password");
var_dump($data);
// $data = encode_data($data);
// var_dump($data);
// $mappings = $data['mappings'];
// $data = $data['result'];
// $data = kmeans($data, 3, 200);

$data = [
    [
        'datetime' => '2023-01-01 12:00:00',
        'key1' => 'value1',
        'key2' => 'value2',
    ],
    [
        'datetime' => '2023-01-01 13:00:00',
        'key1' => 'value1',
        'key2' => 'value3',
    ],
    [
        'datetime' => '2023-01-01 14:00:00',
        'key1' => 'value2',
        'key2' => 'value2',
    ],
];


$result = encode_data($data);

// Display the result
var_dump($result);

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
    // iter through elem of object
    foreach ($data as $object) {
        $dummy = [];
        // iter through elem of object
        foreach ($object as $key => $value) {
            // if exists mapping with the value in the key
            if ($key != 'datetime') {
                if (isset($tmp_mappings[$key])) {
                    if (isset($tmp_mappings[$key][$value])) {
                        $dummy[] = $tmp_mappings[$key][$value];
                    } else {
                        // create new mapping
                        $tmp_mappings[$key][$value] = count($tmp_mappings[$key]);
                        array_push($dummy, $tmp_mappings[$key][$value]);
                    }
                } else {
                    // this if always goes here
                    $tmp_mappings[$key] = [$value => 0];
                    array_push($dummy, 0);
                }
            }

        }
        $result[] = $dummy;
        $mappings[array_to_string($dummy)] = $object;
    }

    return [
        'result' => $result,
        'mappings' => $mappings
    ];
}


// decode data
function decode_data($encodedData, $labelMapping)
{

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

function array_to_string($array) {
    $string = "";
    foreach($array as $elem) {
        $string .= $elem . ","; 
    }
    return $string;
}
?>