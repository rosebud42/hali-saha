<?php
session_start();
include 'db_operations.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $post_name = ($_POST["post_name"]);
    if($post_name=="login"){
        extract($_POST);

        if(loginUser($username, $password)) {
            $_SESSION["username"] = $username;
            header("Location: index.php");
            exit();
        } else {

            header("Location: login.php?error=1");
            exit();
        }
    }
    else if($post_name=="register"){
        extract($_POST);

        $success = createUser($username, $password, $firstname, $lastname, $favorite_pos, $email);

        if ($success==1) {
            $_SESSION["username"] = $username;
            header("Location: index.php");
            exit();
        }if ($success == 2) {
            header("Location: login.php?error=2&form=register");
            exit();
        } else {
            header("Location: login.php?error=3&form=register");
            exit();
        }

    }else if($post_name=="create_game"){
        extract($_POST);
        $success = createGame($title, $_SESSION["username"], $location, $player_count, $game_date, $position);

        if($success==1){
            header("Location: index.php?stmt=1");
            exit();
        }else{
            header("Location: index.php?stmt=2");
            exit();           
        }
    }
    else if($post_name=="join_game"){
        $game_id = $_GET['game_id'] ?? null;
        if (!$game_id) {
            die("Geçersiz oyun ID.");
        }

        $game = getGameById($game_id);
        $attenders = getGameAttenders($game_id);

        $username = $_SESSION['username'];
        $user = getUserByUsername($username);
        $user_id = $user['id'];
        $position = $_POST['position'];
    
        $success = joinGame($game_id, $user_id, $position);
        if ($success == 1) {
            header("Location: game_details.php?id=$game_id&joined=1");
            exit();
        } else if($success == 3) {
            header("Location: game_details.php?id=$game_id&joined=3");
            exit();
        }else{
            header("Location: game_details.php?id=$game_id&joined=2");
            exit();
        }
    }else if($post_name=="leave_game"){
        extract($_POST);
        $game_id = (int) $game_id;
        $user_id = (int) $user_id;
        $result = leaveGame($game_id,$user_id);
        if($result==1){
            header("Location: game_details.php?id=$game_id&leave=1");
            exit();           
        }else{
            header("Location: game_details.php?id=$game_id&leave=0");
            exit();               
        }

    }else if ($post_name === 'update_pos') {
        $new_position = $_POST['position'] ?? '';
        $game_id = $_POST['game_id'];
        $user_id = $_POST['user_id'];
        if (updatePosition($game_id, $user_id, $new_position)) {
            header("Location: game_details.php?id=$game_id&updated=1");
        } else {
            header("Location: game_details.php?id=$game_id&updated=0");
        }
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
