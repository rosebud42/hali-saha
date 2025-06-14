<?php
include 'db_operations.php';
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$game_id = $_GET['id'] ?? null;
if (!$game_id) {
    die("Geçersiz oyun ID.");
}

$game = getGameById($game_id);
$attenders = getGameAttenders($game_id);

$username = $_SESSION['username'];
$user = getUserByUsername($username);
$user_id = $user['id'];


$current_position = null;
$attenders_result = getGameAttenders($game_id);
while ($row = mysqli_fetch_assoc($attenders_result)) {
    if ($row['user_id'] == $user_id && $row['game_id'] == $game_id) {
        $current_position = $row['position_in_game'];
        break;
    }
}

$positions = ["Kaleci", "Defans", "Orta Saha", "Hücum"];
$available_positions = array_filter($positions, function($pos) use ($current_position) {
    return $pos !== $current_position;
});

?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Oyun Detayları</title>
  <style>
    @import url('https://fonts.googleapis.com/css?family=Roboto:300');
    * {
      box-sizing: border-box;
    }

    body {
      background: linear-gradient(90deg, rgba(141,194,111,1) 0%, rgba(118,184,82,1) 50%);
      font-family: "Roboto", sans-serif;
      margin: 0;
      padding-top: 60px;
    }

    .top-buttons {
      position: absolute;
      top: 20px;
      right: 30px;
      display: flex;
      gap: 10px;
    }

    .top-button {
      background-color:rgb(4, 48, 6);
      color: white;
      border: none;
      padding: 10px 18px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
      font-size: 14px;
      transition: background 0.3s ease;
    }

    .top-button:hover {
      background-color: #43a047;
    }

    .container {
      max-width: 1000px;
      margin: auto;
      background: #fff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      display: flex;
      justify-content: space-between;
      gap: 40px;
    }

    .left, .right {
      width: 48%;
    }

    h2 {
      color: #2e7d32;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 10px;
      font-weight: bold;
    }

    select, button {
      width: 100%;
      padding: 12px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 16px;
      margin-bottom: 20px;
    }

    button {
      background-color: #4caf50;
      color: white;
      border: none;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button:hover {
      background-color: #388e3c;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      text-align: left;
      padding: 10px;
    }

    th {
      background-color: #a5d6a7;
      color: #2e7d32;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }
  </style>
</head>
<body>

<div class="top-buttons">
  <a href="index.php"><button class="top-button">Ana Sayfa</button></a>
  <a href="logout.php"><button class="top-button">Çıkış Yap</button></a>
</div>

<div class="container">
  <div class="left">
    <h2>Oyun Bilgileri: </h2>
    <table>
      <thead>
        <tr>
          <th>Kurucu</th>
          <th>Açıklama</th>
          <th>Oyuncu Sayısı</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?= htmlspecialchars($game['created_by']) ?></td>
          <td><?= htmlspecialchars($game['title']) ?></td>
          <td><?= getAttenderCount($game_id) .' / '.htmlspecialchars($game['player_count']) ?></td>
        </tr>
      </tbody>
    </table>
    <h2>Oyuna Katıl</h2>
    <?php if (inGameCheck($game_id, $user_id)!=3): ?>
    <form method="POST" action="post_operations.php?game_id=<?= htmlspecialchars($game_id) ?>">
      <input type="hidden" name="post_name" value="join_game">
      <label for="position">Pozisyonunuzu Seçin:</label>
      <select id="position" name="position" required>
        <option value="">Seçiniz</option>
        <option value="Kaleci">Kaleci</option>
        <option value="Defans">Defans</option>
        <option value="Orta Saha">Orta Saha</option>
        <option value="Hücum">Hücum</option>
      </select>
      <button type="submit">Katıl</button>
    </form>
    <?php else: ?>

    <form method="POST" action="post_operations.php?game_id=<?= htmlspecialchars($game_id) ?>">
      <input type="hidden" name="post_name" value="update_pos">
      <input type="hidden" name="game_id" value="<?= htmlspecialchars($game_id) ?>">
      <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">
      <label for="position">Pozisyonunu değiştir:</label>
      <select id="position" name="position" required>
        <option value="">Seçiniz</option>
        <?php foreach($available_positions as $pos): ?>
            <option value="<?= htmlspecialchars($pos) ?>"><?= htmlspecialchars($pos) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit">Güncelle</button>
    </form>


    <form method="POST" action="post_operations.php">
      <label for="position">Lobiden Ayrıl:</label>
      <input type="hidden" name="post_name" value="leave_game">
      <input type="hidden" name="game_id" value="<?= htmlspecialchars($game_id) ?>">
      <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">
      <button style="background-color:red;" type="submit">Ayrıl</button>
    </form>


    <?php endif; ?>

    <?php if (isset($_GET['joined']) && $_GET['joined'] == 1): ?>
      <p style="color:green;font-weight:bold">Katılım başarılı.</p>
    <?php elseif (isset($_GET['joined']) && $_GET['joined'] == 3): ?>
      <p style="color:red;font-weight:bold">Zaten oyuna kayıtlısınız.</p>
    <?php elseif (isset($_GET['joined']) && $_GET['joined'] == 2): ?>
      <p style="color:red;font-weight:bold">Katılırken hata oluştu.</p>
    <?php endif; ?>

    <?php if (isset($_GET['leave']) && $_GET['leave'] == 1): ?>
      <p style="color:green;font-weight:bold">Ayrılma başarılı.</p>
    <?php elseif (isset($_GET['leave']) && $_GET['leave'] == 0): ?>
      <p style="color:red;font-weight:bold">Ayrılma başarısız.</p>
    <?php endif; ?>

    <?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
      <p style="color:green;font-weight:bold">Pozisyonunuz başarıyla güncellendi.</p>
    <?php elseif (isset($_GET['updated']) && $_GET['updated'] == 0): ?>
      <p style="color:red;font-weight:bold">Pozisyon güncellenirken bir hata oluştu.</p>
    <?php endif; ?>

  </div>


  <div class="right">
    <h2>Katılımcılar</h2>
    <?php if (mysqli_num_rows($attenders) > 0): ?>
    <table>
      <thead>
        <tr>
          <th>Oyuncu</th>
          <th>Pozisyon</th>
        </tr>
      </thead>
      <tbody>
      <?php while($attender = mysqli_fetch_assoc($attenders)): ?>
          <tr>
            <td><?= htmlspecialchars($attender['username']) ?></td>
            <td><?= htmlspecialchars($attender['position_in_game']) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div class="no-data">Henüz oyuna katılımcı bulunmamaktadır.</div>
  <?php endif; ?>
  </div>
</div>

</body>
</html>
