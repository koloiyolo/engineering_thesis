<?php
include("../data_manipulation/get_data.php");
include("../data_manipulation/decoder.php");


// get data from db
$data = json_decode(get_data('root', 'password'));

// init decoder
$decoder = new Decoder($data);
$data = $decoder->encode_data();

// execute query on kmeans server
$data = kmeans($data, '3', '100');
$result = $decoder->decode_data($data);

header('Content-Type: application/json');
echo json_encode($result);


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
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonContent);

    $result = curl_exec($curl);
    if ($result === false) {
        echo "Error processing request";
        return false;
    }
    curl_close($curl);
    return json_decode($result);
}

?>