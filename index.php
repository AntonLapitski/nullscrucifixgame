<?php

require_once(dirname(__FILE__) . '/classes.php');

session_start();


$game = isset($_SESSION['game'])? $_SESSION['game']: null;
if(!$game || !is_object($game)) {
    $game = new TicTacGame();
}

$params = $_GET + $_POST;
if(isset($params['action'])) {
    $action = $params['action'];
    
    if($action == 'move') {
        $game->makeMove((int)$params['x'], (int)$params['y']);
        
    } else if($action == 'newGame') {
        $game = new TicTacGame();
    }
}

$_SESSION['game'] = $game;


$width = $game->getFieldWidth();
$height = $game->getFieldHeight();
$field = $game->getField();
$winnerCells = $game->getWinnerCells();
$myVals = [];

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN"
  "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" version="XHTML+RDFa 1.0" dir="ltr">
<head profile="http://www.w3.org/1999/xhtml/vocab">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>

<style type="text/css">
    .ticTacField {overflow:hidden;}
    .ticTacRow {clear:both;}
    .ticTacCell {float:left; border: 1px solid #ccc; width: 20px; height:20px;
                position:relative; text-align:center;}
    .ticTacCell a {position:absolute; left:0;top:0;right:0;bottom:0}
    .ticTacCell a:hover { background: #aaa; }
    .ticTacCell.winner { background:#f00;}

    .icon { display:inline-block; }
    .player1:after { content: 'X'; }
    .player2:after { content: 'O'; }
</style>



<form action="index.php" method="get">
    <input type="text" placeholder="nickname" name="start">...
    <input type="submit">
</form>

<?php
    if (!empty($_GET['start'])){
        if (count($_SESSION['cart']) === 2) {
            echo 'Only Two players can play';
        }

        if (count($_SESSION['cart']) < 2) {
            $_SESSION['cart'][$_GET['start']] = $_GET['start'];
        }
    }
 ?>

<?php
if (count($_SESSION['cart']) === 2) {
    foreach ($_SESSION['cart'] as $key => $value) {
        $myVals[] = $key;
    }
    $game->nicks($myVals);
}
?>

<?php if(count($_SESSION['cart']) === 2) { ?>

    <?php if($game->getCurrentPlayer()) { ?>
        Ход делает игрок
        <div class="icon player<?php echo $game->getCurrentPlayer() ?>"></div>...
    <?php } ?>

    <?php if($game->getWinner()) { ?>
        Победил игрок
        <div class="icon player<?php echo $game->getWinner() ?>"></div>!
    <?php } ?>

    <div class="ticTacField">
        <?php for($y=0; $y < $height; $y++) { ?>
            <div class="ticTacRow">
                <?php for($x=0; $x < $width; $x++) {

                    $player = isset($field[$x][$y])? $field[$x][$y]: null;
                    $winner = isset($winnerCells[$x][$y]);
                    $class = ($player? ' player' . $player: '') . ($winner? ' winner': '');
                    ?>
                    <div class="ticTacCell<?php echo $class ?>">
                        <?php if(!$player) { ?>
                            <a href="?action=move&amp;x=<?php echo $x ?>&amp;y=<?php echo $y ?>"></a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
    <br/><a href="?action=newGame">Начать новую игру</a>

<?php } ?>

<?php
    $servername = "localhost";
    $username = "username";
    $password = "password";
    $dbname = "myDB";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT id, nickname, winvalue FROM wins";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            echo "id: " . $row["id"]. " - Name: " . $row["nickname"]. " " . $row["winvalue"]. "<br>";
        }
    } else {
        echo "0 results";
    }
    $conn->close();
?>
</body>
</html>

