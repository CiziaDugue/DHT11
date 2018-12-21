<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>DHT11 Traitement</title>

        
    </head>
    <body>
		<?php

		//Paramètre
		$filename = 'data.txt';	

		//Recupération des données json
		$data_json = file_get_contents('php://input');

		//Vérification des données reçues
		$data = json_decode($data_json);
		echo $data;

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

		//Ecriture en bdd
		require_once 'pdoconfig.php';
		try {
			//Connexion
			$bdd = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
			echo "Connected to $dbname at $host successfully.";

			//Recupération de la date de MAJ
			//$datetime = filemtime('store_temp.php');
			$now = getdate();

			//Préparation de la requête
			$req = $bdd->prepare('INSERT INTO releves_dht11 (temperature, humidite)'.' VALUES (:temperature, :humidite)');

			//Requête SQL
			$req->execute(array(
				'temperature' => $data->temperature,
				'humidite' => $data->humidite
				));
			//Affichage du résultat
			echo('<div>Un nouveau relevé a été ajouté à '.$now.' : Température '.$data->temperature.'°C - Humidité '.$data->humidite.'%</div>');

			$req = null;

			$bdd = null;
		}
		catch (PDOException $e) {
			echo "Erreur : " . $e->getMessage() . "<br/>";
			die();
		} ?>
    </body>
</html>