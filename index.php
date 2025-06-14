<?php
include 'db_operations.php';
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}


$allowed_sort_columns = ['id', 'game_date', 'player_count', 'attender_count'];
$sort_by = $_GET['sort_by'] ?? 'id';
$sort_dir = $_GET['sort_dir'] ?? 'desc';


if (!in_array($sort_by, $allowed_sort_columns)) {
    $sort_by = 'id';
}


$sort_dir = strtolower($sort_dir) === 'asc' ? 'ASC' : 'DESC';

$games = getAllGames($sort_by, $sort_dir);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Ana Sayfa</title>
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

    .top-buttons a {
      text-decoration: none;
    }

    .top-button {
      background-color:rgb(4, 48, 6);
      color: white;
      border: none;
      padding: 10px 18px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
      transition: background 0.3s ease;
      font-size: 14px;
    }

    .top-button:hover {
      background-color: #43a047;
    }

    h1 {
      text-align: center;
      color: #388e3c;
      margin-bottom: 30px;
    }

    table {
      width: 90%;
      margin: 0 auto;
      border-collapse: collapse;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      background-color: #ffffff;
      border-radius: 12px;
      overflow: hidden;
    }

    th, td {
      padding: 15px 20px;
      text-align: left;
    }

    th {
      background-color: #a5d6a7;
      color: #2e7d32;
      text-transform: uppercase;
      font-size: 14px;
      letter-spacing: 0.5px;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    tr:hover {
      background-color: #e0f2f1;
    }

    .details-button {
      background-color: #66bb6a;
      color: white;
      border: none;
      padding: 10px 18px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
      transition: background 0.3s ease;
    }

    .details-button:hover {
      background-color: #43a047;
    }

    .no-data {
      text-align: center;
      font-size: 18px;
      color: #888;
      margin-top: 50px;
    }

    .error {
        color: #f44336;
        background: #ffe6e6;
        padding: 10px;
        border-radius: 4px;
        text-align: center;
        font-weight: bold;
        margin-bottom: 15px;
    }
    .success {
        color:rgb(0, 255, 85);
        background:rgb(255, 255, 255);
        padding: 10px;
        border-radius: 4px;
        text-align: center;
        font-weight: bold;
        margin-bottom: 15px;
    }

    .sortable {
      color: inherit; 
      text-decoration: underline; 
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .sortable:hover {
      text-decoration: none; 
      color: #2e7d32; 
      text-shadow: 0 0 5px rgba(0,0,0,0.2); 
    }
  </style>
</head>
<body>

  <div class="top-buttons">
    <a href="create_game.php"><button class="top-button">Oyun Oluştur</button></a>
    <a href="logout.php"><button class="top-button">Çıkış Yap</button></a>
  </div>

  <h1 style="color:rgb(234, 236, 234);">Hoşgeldin, <?= htmlspecialchars($_SESSION['username'] ?? 'Misafir') ?>! </h1>

  <?php if (isset($_GET['stmt']) && $_GET['stmt'] == 1): ?>
    <div class="success">Oda başarıyla oluşturuldu.</div>
<?php endif; ?>


  <?php if (isset($_GET['stmt']) && $_GET['stmt'] == 2): ?>
    <div class="error">Oda kurulurken bir hata oluştu.</div>
<?php endif; ?>
  <h1>Mevcut Oyunlar</h1>

  
  <?php if (mysqli_num_rows($games) > 0): ?>
    <table>
      <thead>
        <tr>
            <th><a class="sortable" href="?sort_by=id&sort_dir=<?= ($sort_by === 'id' && $sort_dir === 'ASC') ? 'desc' : 'asc' ?>">Oyun ID</a></th>
            <th><a class="sortable" href="?sort_by=game_date&sort_dir=<?= ($sort_by === 'game_date' && $sort_dir === 'ASC') ? 'desc' : 'asc' ?>">Oyun Tarihi</a></th>
            <th>Açıklama</th>
            <th>Konum</th>
            <th><a class="sortable" href="?sort_by=attender_count&sort_dir=<?= ($sort_by === 'attender_count' && $sort_dir === 'ASC') ? 'desc' : 'asc' ?>">Oyuncu Sayısı</a></th>
            <th>Oluşturan</th>
            <th>Detay</th>
        </tr>
      </thead>
      <tbody>
        <?php while($game = mysqli_fetch_assoc($games)): ?>
          <tr>
            <td><?= htmlspecialchars($game['id']) ?></td>
            <td><?= htmlspecialchars($game['game_date']) ?></td>
            <td><?= htmlspecialchars($game['title']) ?></td>
            <td><?= htmlspecialchars($game['game_location']) ?></td>
            <td><?= htmlspecialchars(getAttenderCount($game['id'])) . ' / ' . htmlspecialchars($game['player_count']) ?></td>
            <td><?= htmlspecialchars($game['created_by']) ?></td>
            <td>
              <button class="details-button" onclick="goToDetails(<?= $game['id'] ?>)">Detaylar</button>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="no-data">Henüz kayıtlı bir oyun bulunmamaktadır.</div>
  <?php endif; ?>

  <script>
    function goToDetails(id) {
      window.location.href = 'game_details.php?id=' + id;
    }
  </script>

</body>
</html>
