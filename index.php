<!doctype html>
<?php
$pageTemp = file_get_contents('data.txt');
$tuff = json_decode($pageTemp);
date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, 'fr_FR.utf8','fra');
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
		
<!--faire varier le trait rouge en fonction de la temperature
mettre à jour votre dépôt sur GitHub-->
		<p>Il fait <?php echo $tuff->temperature; ?>° avec <?php echo $tuff->humidite ?>% d'humidité.</p>
		
		<p>Dernière mise à jour : <?php echo strftime("%A %d %B %Y à %H:%M:%S", filemtime('data.txt')); ?>.</p>
		
		<div id="thermometer">
			<div id="bargraph"></div>
		</div>

	</body>
</html>