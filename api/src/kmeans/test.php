<?php 
include("../data_manipulation/get_data.php");
include("../data_manipulation/decoder.php");

$data = json_decode(get_data('root', 'password'));
$decoder = new Decoder($data);

$data = $decoder->encode_data();
var_dump($data);





?>