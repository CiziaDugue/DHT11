<?php
////// TRAITEMENT DU JSON ENVOYE PAR LE NODEMCU //////
//Paramètre
$filename = 'data.txt';	

//Recupération des données json
$data_json = file_get_contents('php://input');

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

/////// CONNECTEUR PHP / BDD ///////
require 'config.inc.php';
try {
	//Connexion
	$bdd = new PDO("mysql:host=$host", $username, $password);
	echo "Connected to localhost successfully.";
}
catch (PDOException $e) {
	echo "Erreur : " . $e->getMessage() . "<br/>";
	die();
}

///////CREATION BDD + TABLE///////
//Préparation de la requête
$createDb = $bdd->query('CREATE DATABASE IF NOT EXISTS '.$dbname.'
							CHARACTER SET utf8 COLLATE utf8_general_ci');
$bdd = null;

try {
	//Connexion
	$bdd = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
	echo "Connected to $dbname at $host successfully.";
}
catch (PDOException $e) {
	echo "Erreur : " . $e->getMessage() . "<br/>";
	die();
}


$createTable = $bdd->query('CREATE TABLE IF NOT EXISTS `releves_dht11`(
							  `id` smallint(5) AUTO_INCREMENT NOT NULL,
							  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
							  `temperature` tinyint NOT NULL,
							  `humidite` tinyint NOT NULL,
							  PRIMARY KEY (`id`),
							  UNIQUE KEY `datetime` (`datetime`)
							) ENGINE=InnoDB DEFAULT CHARSET=utf8');
//$createTable->execute();

///////ECRITURE EN BDD///////
//Préparation de la requête
$insert = $bdd->prepare('INSERT INTO releves_dht11(temperature, humidite) VALUES (:temperature, :humidite)');
//Execution de la requête SQL
$insert->execute(array(
	'temperature' => $data->temperature,
	'humidite' => $data->humidite
	));

///////LECTURE EN BDD///////
//Convertion date fr
$timeformat = $bdd->query('SET lc_time_names = `fr_FR`');
//Préparation de la requête select avec formatage de la date
$select = $bdd->query('SELECT id, temperature, humidite, datetime FROM releves_dht11 ORDER BY id DESC LIMIT 1');
$row = $select->fetch();
$latestData = [
	'temperature' => $row['temperature'],
	'humidite' => $row['humidite'],
	'datetime' => $row['datetime']
];
$select->closeCursor();

//////ECRITURE DANS LE FICHIER data.txt/////
$latestDataJson = json_encode($latestData);
$op = file_put_contents($filename, $latestDataJson);
//Verif écriture
if (! $op) {
	http_response_code(500);
	echo "Store error" ;
}

$bdd = null;

?>