<?php
class Character
{
	private $pdo;
	public $name;
	public $uuid;
	public $stats;
	
	public function __construct($pdo, $name) {
		$this->pdo = $pdo;	
		$this->name = $name;
		
		//check if a character with this name already exists
			//if not, create one
		if($this->getName($name) == false) {
			$this->create($name);
			$this->getName($name);
		}
	}
	
	private function create($name) {
		//generate stats obj here
		$base = [13, 13, 13, 13, 14, 14]; //pre-seeded list of stats
		$titles = [
			"strength",
			"agility",
			"intelligence",
			"stamina",
			"charisma",
			"wisdom"
		];

		for ($i = 0; $i <= 300; $i++) {

			//generate the random indexes and amount to swap between them
			//rob peter to pay paul
			$peter = mt_rand(0,5);
			$paul = mt_rand(0,5);
			$amount = mt_rand(1,2);

			//dont let any values dip below 9
			//more drastic deltas between stats puts player at statistical disadvantage
			//compared to more evenly distributed stat values
			if (($base[$peter]-$amount) >= 9) { 
				$base[$peter] -= $amount;
				$base[$paul] += $amount;
			}
		}

		$combined = array_combine($titles, $base);
		$json = json_encode($combined);
		
		$this->stats = (object) $combined;
		
		//insert new record into character table [name, stats (json obj)]
		$params = [
			"name"=>$name,
			"stats"=>$json
		];
		$sql = "
			INSERT INTO characters (name, stats)
			VALUES (:name, :stats)
		";
		
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);
	}
	
	private function getName($name, $opponent = null) {
		
		$params = ["name"=>$name];
		$sql = "
			SELECT id, name, stats
			FROM characters
			WHERE name = :name
		";
		
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);
		
		if ($stmt->rowCount() == 0) {
			return false;
		} else {
			if ($opponent == null) {
				$result = $stmt->fetchAll()[0];
				$this->name = $result['name'];
				$this->uuid = $result['id'];
				$this->stats = json_decode($result['stats']);
				return json_decode($result['stats']);
			} else {
				$result = $stmt->fetchAll()[0];
				return $result;
			}
		}
	}
	
	public function challenge($opponent) {
		
		$opponentStats = $this->getName($opponent, true);
	
		if($opponentStats == false) {
			return false;
		}
		
		return $opponentStats;
	}
	
	public function getHistory() {
		
		$params = ["id"=>$this->uuid, "id2"=>$this->uuid];
		$sql = "
			SELECT c1.name AS challenger, c2.name as opponent, outcome
			FROM history h
			INNER JOIN characters c1
				ON h.challenger = c1.id
			INNER JOIN characters c2
				ON h.opponent = c2.id
			WHERE h.challenger = :id OR h.opponent = :id2 
		";
		
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);
		
		if($stmt->rowCount() == 0) {
			return false;
		} else {
			return $stmt->fetchAll();
		}
	}
	
	//ugly method, only for testing purposes on small data sets
	public function getRandomPlayers() {
		$params = ["name"=>$this->name];
		$sql = "
			SELECT name
			FROM characters
			WHERE name != :name
			ORDER BY RAND()
			LIMIT 10;
		";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);
		
		if($stmt->rowCount() == 0) {
			return false;
		} else {
			return $stmt->fetchAll();
		}
	}
	
	public function addRandomStat($name){
		
		$stats = (array) $this->getName($name);
		$index = array_rand($stats);
		$stats[$index] += 1;
		$stats = json_encode($stats);
		
		$params = ["stats"=>$stats, "name"=>$name];
		$sql = "
			UPDATE characters
			SET stats = :stats
			WHERE name = :name
		";
		
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);
	}
	
	public function addEnemyStat($name) {
		
		$opponent = $this->getName($name, true);
		$stats = json_decode($opponent['stats'], true);
		$index = array_rand($stats);
		$stats[$index] += 1;
		$stats = json_encode($stats);
		
		$params = ["stats"=>$stats, "name"=>$name];
		$sql = "
			UPDATE characters 
			SET stats = :stats
			WHERE name = :name
		";
		
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);
	}
	
	public function addMatch($ch, $op, $outcome) {
		$params = ["challenger"=>$ch, "opponent"=>$op, "outcome"=>$outcome];
		$sql = "
			INSERT INTO history (challenger, opponent, outcome)
			VALUES (:challenger, :opponent, :outcome)
		";
		
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);
	}
}
?>
