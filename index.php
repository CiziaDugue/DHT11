<!doctype html>
<?php
//Inclusion des classes de la librairie pChart
include("class/pDraw.class.php");
include("class/pImage.class.php");
include("class/pData.class.php");

//Récupération de l'objet json
$fileName = 'data.txt';
$pageTemp = file_get_contents($fileName);
$data = json_decode($pageTemp);

//Configuration du format de la date de maj
date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, 'fr_FR.utf8','fra');
$majTime = strftime("%A %d %B %Y à %H:%M:%S", filemtime('data.txt'));

//Conversion degré / pixels
$bargraphHeight = 161 + $data->temperature * 4;
$bargraphTop = 315 - $data->temperature * 4;

//Création d'un objet pData
$dataChart = new pData();

//Connexion à la base de données
$db = mysql_connect("localhost", "dht11", "etg?45èàg41M(jsg!D");
mysql_select_db("pchart",$db);
?>
<html>
	<head>
		<title>DHT11 NodeMCU</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="initial-scale=1.0">
		<meta http-equiv="refresh" content="1">
		<link href="main.css" type="text/css" rel="stylesheet">
	</head>

	<body>
		<h1>Température</h1>
		
		<p>Il fait <?php echo $data->temperature; ?>°C avec <?php echo $data->humidite ?>% d'humidité.</p>
		
		<p>Dernière mise à jour : <?php echo $majTime; ?>.</p>
		
		<div id="thermometer">
			<div id="bargraph" style="height: <?php echo $bargraphHeight ; ?>px; top: <?php echo $bargraphTop ; ?>px;"></div>
		</div>
		
		<form action="" method="post">
			<input type="date">
			<input type="date">
		</form>
		
		<?php
			// Requête en bdd pour récupérer les données
			$Requete = "SELECT * FROM `releves_dht11`";
			$Result  = mysql_query($Requete,$db);
			$timestamp="";
			$temperature="";
			$humidite="";
			while($row = mysql_fetch_array($Result))
			 {
			  //Mettre les résultats de la requête dans un tableau
			  $dateReleve[]   = $row["datetime"];
			  $temperature[] = $row["temperature"];
			  $humidite[]    = $row["humidite"];
			 }

			// Enregistrer les données dans tableau pData
			$dataChart->addPoints($dateReleve,"Date du relevé");
			$dataChart->addPoints($temperature,"Température");
			$myData->addPoints($humidite,"Humidité");
		
			//Définir la date en abscisse
			$dataChart->setAbscissa("Date du relevé");
			//Nommer cet axe
			$dataChart->setXAxisName("Date du relevé");
			//Spécifier que l'on affiche du format date
			$dataChart->setXAxisDisplay(AXIS_FORMAT_TIME,"H:i");
		
			//Associer l'humidité à un second axe Y
			$dataChart->setSerieOnAxis("Humidité", 1);
		
			//Température sur le premier axe y
			$dataChart->setAxisName(0,"Température");
			$dataChart->setAxisUnit(0,"°C");

			//Humidité sur le second axe y
			$dataChart->setAxisName(1,"Humidité");
			$dataChart->setAxisUnit(0,"%");
			
			




		?>

	</body>
</html>