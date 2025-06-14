<?php
include 'connection.php';

function createUsersTable(){

    $conn = getConnection();
    

    $sql = '
    CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL,
    passw VARCHAR(200) NOT NULL,
    firstname VARCHAR(30) NOT NULL,
    lastname VARCHAR(30) NOT NULL,
    favorite_pos VARCHAR(30) NOT NULL,
    email VARCHAR(50),
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
    ';
    mysqli_query($conn, $sql);
    mysqli_close($conn);
}

function createGamesTable(){

    $conn = getConnection();
    

    $sql = '
    CREATE TABLE IF NOT EXISTS games (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(300),
    created_by VARCHAR(30) NOT NULL,
    player_count INT NOT NULL,
    game_date DATETIME,
    game_location VARCHAR(200)
    )
    ';
    mysqli_query($conn, $sql);
    mysqli_close($conn);
}

function createGameParticipantsTable(){
    $conn = getConnection();

    $sql = '
    CREATE TABLE IF NOT EXISTS game_participants (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        game_id INT(6) UNSIGNED NOT NULL,
        user_id INT(6) UNSIGNED NOT NULL,
        username VARCHAR(30) NOT NULL,
        joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        position_in_game VARCHAR(30),
        FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) CHARACTER SET utf8 COLLATE utf8_turkish_ci;
    ';

    mysqli_query($conn, $sql);
    mysqli_close($conn);
}

function createUser($username, $password, $firstname, $lastname, $favorite_pos, $email){
    $conn = getConnection();
    mysqli_set_charset($conn, "utf8");

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $checkSql = "SELECT username FROM users WHERE username = ? OR email = ?";
    $stmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($stmt, "ss", $username, $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        mysqli_close($conn);
        return 2;
    }

    $sql = "INSERT INTO users (username, passw, firstname, lastname, favorite_pos, email) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssss", $username, $hashedPassword, $firstname, $lastname, $favorite_pos, $email);

    $result = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $result;
}

function deleteUser(){
    $conn = getConnection();
    mysqli_set_charset($conn, "utf8");

    $sql = "DELETE FROM users WHERE username=$username";

    if (mysqli_query($conn, $sql)) {
        return TRUE;
    } else {
        return FALSE;
    }

    mysqli_close($conn);
}

function loginUser($username, $password){
    $conn = getConnection();
    mysqli_set_charset($conn, "utf8");

    $sql = "SELECT passw FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $hashedPasswordFromDB);
    mysqli_stmt_fetch($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    // Şifre eşleşmesini kontrol et
    if (password_verify($password, $hashedPasswordFromDB)) {
        echo"doğru";
        return true;
    } else {
        echo"yanlış";
        return false;
    }
}

function getUserByUsername($username) {
    $conn = getConnection();
    $sql = "SELECT id, username, firstname, lastname, favorite_pos, email, reg_date FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $user;
}

function getUserByUserId($id) {
    $conn = getConnection();
    $sql = "SELECT id, username, firstname, lastname, favorite_pos, email, reg_date FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $user;
}

function getGameById($game_id){
    $conn = getConnection();
    $sql = "SELECT * FROM games WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $game_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $game = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $game;
}


function createGame($title, $created_by, $location, $player_count, $game_date, $position) {
    $conn = getConnection();

    $sql = "INSERT INTO games (title, created_by, game_location, player_count, game_date) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssis", $title, $created_by, $location, $player_count, $game_date);

    if (mysqli_stmt_execute($stmt)) {
        $last_id = mysqli_insert_id($conn);
        mysqli_close($conn);
        $user = getUserByUsername($created_by);
        joinGame($last_id, $user['id'], $position);
        return 1;
    } else {
        $error = mysqli_error($conn);
        mysqli_close($conn);
        return 2;
    }
}

function inGameCheck($game_id, $user_id){
    $conn = getConnection();
    $checkSql = "SELECT * FROM game_participants WHERE game_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($stmt, "ii", $game_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_close($conn);
    if (mysqli_num_rows($result) > 0) {
        return 3; //zaten kayıtlı
    }
}

function joinGame($game_id, $user_id, $position = null) {
    $conn = getConnection();
    $user = getUserByUserId($user_id);
    $username = $user['username'];
    $check = inGameCheck($game_id, $user_id);
    if($check == 3){
        return 3;
    }

    $insertSql = "INSERT INTO game_participants (game_id, user_id, username, position_in_game) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insertSql);
    mysqli_stmt_bind_param($stmt, "iiss", $game_id, $user_id, $username, $position);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_close($conn);
        return 1; // başarılı
    } else {
        $error = mysqli_error($conn);
        mysqli_close($conn);
        return 2; // hata
    }
}

function leaveGame($game_id, $user_id) {
    $conn = getConnection();

    $sql = "DELETE FROM game_participants WHERE game_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $game_id, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_close($conn);
        return 1;
    } else {
        $error = mysqli_error($conn);
        mysqli_close($conn);
        return 0;
    }
}

function updatePosition($game_id, $user_id, $new_position) {
    $conn = getConnection();

    $sql = "UPDATE game_participants SET position_in_game = ? WHERE game_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sii", $new_position, $game_id, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_close($conn);
        return 1;
    } else {
        $error = mysqli_error($conn);
        mysqli_close($conn);
        return 0;
    }
}


function getAllGames($sort_by = 'id', $sort_dir = 'DESC') {
    $conn = getConnection();

    if ($sort_by === 'attender_count') {
        $query = "SELECT g.*, (SELECT COUNT(*) FROM game_participants gp WHERE gp.game_id = g.id) AS attender_count FROM games g ORDER BY attender_count $sort_dir";
    } else {
        $query = "SELECT * FROM games ORDER BY $sort_by $sort_dir";
    }

    $result = mysqli_query($conn, $query);
    mysqli_close($conn);
    return $result;
}

function getGameAttenders($game_id){
    $conn = getConnection();
    $stmt = mysqli_prepare($conn, "SELECT * FROM game_participants WHERE game_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $game_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $result;
}



function getAttenderCount($game_id){
    $conn = getConnection();
    $sql = "SELECT COUNT(*) FROM game_participants WHERE game_id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $game_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $count;
}

createUsersTable();
createGamesTable();
createGameParticipantsTable();


?>

<html>
    <head>
        <meta charset="UTF-8">
    </head>
</html>