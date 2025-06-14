<?php
session_start();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <title>Oyun Oluştur</title>
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

    .container {
      max-width: 600px;
      margin: auto;
      background: #fff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      color: #2e7d32;
      margin-bottom: 30px;
    }

    label {
      display: block;
      margin-bottom: 10px;
      color: #333;
      font-weight: bold;
    }

    input[type="text"],
    input[type="datetime-local"],
    input[type="number"],
    textarea {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 16px;
      resize: vertical;
    }

    button {
      background-color: #4caf50;
      color: white;
      border: none;
      padding: 14px 24px;
      font-size: 16px;
      font-weight: bold;
      border-radius: 8px;
      cursor: pointer;
      width: 100%;
      transition: background 0.3s ease;
    }

    button:hover {
      background-color: #388e3c;
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

    select {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ccc;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 16px;
    background-color: #fff;
    appearance: none; 
    background-image: url('data:image/svg+xml;utf8,<svg fill="gray" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 18px;
    }


  </style>
</head>
<body>

  <div class="top-buttons">
    <a href="index.php"><button class="top-button">Ana Sayfa</button></a>
    <a href="logout.php"><button class="top-button">Çıkış Yap</button></a>
  </div>

  <div class="container">
    <h2>Yeni Oyun Oluştur</h2>
    <form action="post_operations.php" method="POST">

     <input type="hidden" name="post_name" value="create_game">
      <label for="title">Açıklama / Oyun Başlığı:</label>
      <input type="text" id="title" name="title" required>

        <label for="title">Halı Saha Konumu:</label>
      <input type="text" id="location" name="location" required>

      <label for="game_date">Oyun Tarihi ve Saati:</label>
      <input type="datetime-local" id="game_date" name="game_date" required>
      
      <label for="title">Sizin tercih ettiğiniz mevki:</label>
        <select id="position" name="position" required>
            <option value="" disabled selected>Pozisyon Seçin</option>
            <option value="Hücum">Hücum</option>
            <option value="Kaleci">Kaleci</option>
            <option value="Defans">Defans</option>
            <option value="Orta Saha">Orta Saha</option>
        </select>

      <label for="player_count">Maksimum Oyuncu Sayısı:</label>
      <input type="number" id="player_count" name="player_count" min="1" required>

      <button type="submit">Oluştur</button>
    </form>
  </div>

</body>
</html>
