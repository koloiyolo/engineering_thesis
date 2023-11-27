<?php
$data = json_decode(get_data("root", "password"));

echo json_encode(encode_data($data));
$encodingResult = encode_data($data);
$data = $encodingResult['encodedData'];
$labelMapping = $encodingResult['labelMapping'];
$data = kmeans($data, 3, 200);
//$data = decode_data($data, $labelMapping);

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
    $result = [];
    $mappings = [];
    // iter through elem of object
    foreach ($data as $object) {
        $dummy = [];
        // iter through elem of object
        foreach ($object as $key => $value) {
            // if exists mapping with the value in the key
            if (isset($mappings[$key])) {
                if (isset($mappings[$key][$value])) {
                    $dummy[] = $mappings[$key][$value];
                } else {
                    // create new mapping
                    $mappings[$key][] = [$value => count($mappings[$key])];
                    array_push($dummy, $mappings[$key][$value]);
                }
            } else {
                $mappings[] = [$key => [$value => 0]];
                array_push($dummy, 0);
            }
            
        }
        $result[] = $dummy;
    }

    return ['result' => $result, 
            'mappings' => $mappings];
}


// decode data
function decode_data($encodedData, $labelMapping)
{

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
?>