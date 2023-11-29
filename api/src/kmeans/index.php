<?php
include("../data_manipulation/encoder.php");
include("../data_manipulation/get_data.php");



$groups = [];
$data = json_decode(get_data("root", "password"));
$data = encode_data($data);
$mappings = $data['mappings'];
$data = $data['result'];
$data = kmeans($data, '3', '100');
$result = decode_data($data, $mappings);
$count = 0;
foreach ($result as $cluster) {
    echo ++$count . json_encode($cluster) . "\n";
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