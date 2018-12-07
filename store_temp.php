<?php

//Paramètre
$filename = 'data.txt';	

//Recupération des données json
$data_json = file_get_contents('php://input');
echo $data;

//Vérification des données reçues
$data = json_decode($data_json);
if (!$data){
	http_response_code(415);
	exit();
}
elseif (! $data->temperature || ! $data->humidite){
	http_response_code(400);
	exit();
}

//Ecriture dans le fichier data.txt
$op = file_put_contents($filename, $data_json);
//Verif écriture
if (! $op) {
	echo "Store error" ;
}