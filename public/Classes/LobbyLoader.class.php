<?php
require_once 'Lobby.class.php';

class LobbyLoader {
	private function __construct(){}
	private function __clone(){}

    static function addUserToPlfl(){
      $database = DB::getInstance();

      $qInserCurrentPlayerIntoPlfl = '
        INSERT INTO player_looking_for_lobby (steam_id)
        VALUES ('.$_SESSION['currentUser']->steamId.')
      ';

      // send the query and print an error if it fails
      if ( !$result = $database->query($qInserCurrentPlayerIntoPlfl)){
        /* echo "something went wrong when trying to add user to the plfl table: ".$database->error; */
      }

      return ['loadview' => 'loadingpage', 'randomTip' => self::getRandomTip() ];
    }

    static function lookForLobby(){
      $database = DB::getInstance();

      $qAmIInALobby = '
        SELECT * FROM lobby
        WHERE lobby.steam_id = '.$_SESSION['currentUser']->steamId.'
        LIMIT 1
      ';

      $result = $database->query($qAmIInALobby);
      if ($result->num_rows == 1){
        $row = $result->fetch_assoc();
        $lobbyId = $row['lobby_id'];
        $foundLobby = TRUE;
      } else {
        $foundLobby = FALSE;
      }

      if($foundLobby) {
					$_SESSION['currentUser']->setLobby($lobbyId);
          return Lobby::viewLobby();
      } else {
          return ['loadview' => 'loadingpage','randomTip' => self::getRandomTip()];
      }
    }

    static function getRandomTip(){

      $tips = array(
        'If you have any questions you might find your answers in the FAQ-tab.',
        'If someone is acting rude or griefs you can report them.',
        'It\'s always better to be nice to other players in the long run.',
        'Respect the elders',
        'Life is real until you rip',
        'The red bulls are for drinking',
        'They don\'t deserve for rekt',
        'As a premium member you will have access to more features on the site.',
        'One player in the lobby will get the leader role and it\'s up to the leader to make sure that you start up a game together.',
        'As soon as you land in a lobby you can join a team chat together with your lobby members',
        'You will know who the lobby leader is by looking at the profile portraits in the lobby.'
        );
      $random = mt_rand(0, count($tips)-1);

      return $tips[$random];
    }
}
