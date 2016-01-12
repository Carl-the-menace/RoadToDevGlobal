<?php
require_once 'Classes/User.class.php';

class Players{
	private function __construct(){}
	private function __clone(){}

	static function viewUserProfiles(){
		$database = DB::getInstance();
		$users = array();

		$qGetAllUsers = '
			SELECT steam_id FROM user
			LIMIT 24;
		';

		if( $result = $database->query($qGetAllUsers)){
			while ($row = $result->fetch_assoc()) {
				$users[] = new User($row['steam_id']);
			}
		} else {
			echo "Failed to get users from DB".$database->error;
		}
			/*
             // update the steam stats for all the players, only use for debug
            foreach ($users as $user) 
            $user->fetchSteamStats();
            */                

		return ['loadview' => 'players', 'users' => $users ];
	}

	static function filterUsers(){

		$database = DB::getInstance();

    	$clauses = array();
    	if(empty($_POST['language']) && empty($_POST['rank']) && empty($_POST['hours']) && empty($_POST['nick']) ){

    		return self::viewUserProfiles();
    	}

    	if( isset($_POST['language']) && !empty($_POST['language']) ){

    		$languageClause  = ' primary_language = "';
                $languageClause .= $database->real_escape_string(stripslashes($_POST['language']));
    		$languageClause .= '"';
                
    		$languageClause .= ' OR ';

    		$languageClause .= ' secondary_language = "';
                $languageClause .= $database->real_escape_string(stripslashes($_POST['language']));
    		$languageClause .= '"';
                $clauses[] = $languageClause;
    	}

    	if( isset($_POST['rank']) && !empty($_POST['rank']) ){
			$rankClause  = ' rank = "';
			$rankClause .= $database->real_escape_string(stripslashes($_POST['rank']));
			$rankClause .= '"';
			$clauses[] = $rankClause;
    	}

    	if( isset($_POST['hours']) && !empty($_POST['hours']) ){
			$hoursClause  = ' hours_played > ';
			$hoursClause .= $database->real_escape_string(stripslashes($_POST['hours']));
			$clauses[] = $hoursClause;
    	}

    	if( isset($_POST['nick']) && !empty($_POST['nick']) ){
    		$nickClause = ' nickname LIKE "%';
    		$nickClause .= $database->real_escape_string(stripslashes($_POST['nick']));
    		$nickClause .= '%"';
    		$clauses[] = $nickClause;
    	}


    	$finalClause = ' WHERE ';

    	for ($i = 0; $i != count($clauses); $i++) {
    		// If we are not on the first clause, prefix an "AND" .
    		if($i != 0){
    			$finalClause .= ' AND ';
    		}

    		$finalClause .= $clauses[$i];
    	}
    	$users = array();

		$qGetFilteredUsers = '
			SELECT steam_id FROM user
			'. $finalClause .'
			LIMIT 24;
		';

		if( $result = $database->query($qGetFilteredUsers)){
			while ($row = $result->fetch_assoc()) {
				$users[] = new User($row['steam_id']);
			}
		} else {
			echo "Failed to get users from DB".$database->error;
		}

		return ['loadview' => 'players', 'users' => $users ];
	}

}
