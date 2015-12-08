<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require 'vendor/autoload.php';
require '../config/db_config.php'; //$pdo created here as long as database connection is live

$app = new \Slim\Slim([
	'templates.path' => './views'
]);

$client = new Predis\Client();

$app->get('/', function () use ($app) {
	$app->render('splash.view.php');
});

$app->post('/stats', function () use ($app) {
	if (!empty($_POST['name'])) {
		$url = "/stats/".$_POST['name'];
		$app->redirect($url);
	} else {
		$app->redirect('/');
	}
});

//little easter egg for anyone looking 'teemo' stats or trying to challenge teemo
$app->get('/stats/teemo', function () {
	echo "<center>";
		echo "<br><br><br><br>";
		echo "<h1>YOU CANNOT CHALLENGE SATAN AND SURVIVE!</h1>";
		echo "<img src='http://i.imgur.com/NTM4Cmi.jpg'>";
		echo "<br>";
		echo "<audio controls autoplay loop><source src='/teemolaugh.wav' type='audio/wav'></audio>";
	echo "</center>";
});

$app->get('/stats/:name', function ($name) use ($app, $pdo, $client) {
	require 'models/character.model.php';
	
	$character = new Character($pdo, $name);

	$stats = (array) $character->stats;
	$history = $character->getHistory();
	
	//$redisVal = $client->get('herp');
	$redisVal = "derp";
	
	$app->render('home.view.php', [
		"stats"=>$stats,
		"name"=>$character->name,
		"redis"=>$redisVal,
		"history"=>$history,
		"name"=>$name,
		"uuid"=>$character->uuid,
		"randPlayers"=>$character->getRandomPlayers()
	]);
});

$app->get('/challenge/:name1/:name2', function ($name1, $name2) use ($app, $pdo) {
	require 'models/character.model.php';
	
	$character = new Character($pdo, $name1);
	
	$myStats = (array) $character->stats;
	$myStats = array_values($myStats);
	
	if(!$character->challenge($name2)) {
		echo "Can't challenge a player that doesn't exist yet!";
		$app->stop();
	}
	
	$opponent = $character->challenge($name2);
	$opponentStats = (array) json_decode($opponent['stats']);
	$opStats = array_values($opponentStats);

	echo "<pre>";
	echo "My Stats<br>";
	var_dump($myStats);
	
	echo "Opponent Stats<br>";
	var_dump($opStats);
	
	$myCount = 0; 
	$opCount = 0;
	
	$name = $character->name;
	
	for($i=0; $i<sizeof($myStats); $i++) {
		if ($myStats[$i] > $opStats[$i]) {
			$myCount++;
		} elseif ($myStats[$i] == $opStats[$i]) {
			//$opCount++;
		} else {
			$opCount++;
		}
	}
	
	if ($myCount > $opCount) {//you win
		echo "congratulations, you were victorious";
		echo "<br>";
		echo "<a href='http://www.mattgates.xyz/stats/",$name,"'>Back to Stats</a>";
		$id = $character->uuid;
		$id2 = $opponent['id'];
		$character->addRandomStat($name1);
		$character->addMatch($id, $id2, 1);
		
	} elseif ($opCount > $myCount) {//opponent wins
		echo "defeat! better luck next time";
		echo "<br>";
		echo "<a href='http://www.mattgates.xyz/stats/",$name,"'>Back to Stats</a>";
		$id = $character->uuid;
		$id2 = $opponent['id'];
		$character->addEnemyStat($name2);
		$character->addMatch($id, $id2, 0);
			
	} else {//draw condition
		echo "A DRAW!";
	}
	
});

//test route for generating seeded stat data
$app->get('/stats/new', function () {
	$base = [13, 13, 13, 13, 14, 14]; //seeded array
	$titles = [
		"strength",
		"agility",
		"intelligence",
		"stamina",
		"charisma",
		"wisdom"
	];
	
	echo "<pre>";
	for ($i = 0; $i <= 300; $i++) {
		//generate the random indexes and amount to swap between them
		$peter = mt_rand(0,5);
		$paul = mt_rand(0,5);
		$amount = (int) mt_rand(1,2);
		
		if (($base[$peter]-$amount) >= 9) {
			$base[$peter] -= $amount;
			$base[$paul] += $amount;
		}
	}
	/*
	echo json_encode($base);
	echo "<br>";
	echo json_encode($titles);
	*/

	echo $json = json_encode(array_combine($titles, $base));
});

$app->run();
?>
