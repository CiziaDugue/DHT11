<!doctype html>
<?php
//Récupération de l'objet json
$
$pageTemp = file_get_contents('data.txt');
$data = json_decode($pageTemp);

//Configuration du format de la date de maj
date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, 'fr_FR.utf8','fra');
$majTime = strftime("%A %d %B %Y à %H:%M:%S", filemtime('data.txt'));

//Conversion degré / pixels
$bargraphHeight = 161 + $data->temperature * 4;
$bargraphTop = 315 - $data->temperature * 4;
?>
<html>
	<head>
		<title>Thermomètre</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="initial-scale=1.0">
		<link href="main.css" type="text/css" rel="stylesheet">
	</head>

	<body>
		<h1>Température</h1>
		
		<p>Il fait <?php echo $data->temperature; ?>°C avec <?php echo $data->humidite ?>% d'humidité.</p>
		
		<p>Dernière mise à jour : <?php echo $majTime; ?>.</p>
		
		<div id="thermometer">
			<div id="bargraph"></div>
		</div>

		<?php echo '<style>#bargraph{height:' . $bargraphHeight .'px;top:' . $bargraphTop . 'px;}</style>'; ?>
	</body>
</html>