<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Giriş Sistemi</title>
  <style>
    @import url('https://fonts.googleapis.com/css?family=Roboto:300');

    * {
      box-sizing: border-box;
    }

    body {
      background: linear-gradient(90deg, rgba(141,194,111,1) 0%, rgba(118,184,82,1) 50%);
      font-family: "Roboto", sans-serif;
      margin: 0;
      overflow : hidden;
    }

    .login-page {
      width: 360px;
      margin: 100px auto;
      background: #fff;
      box-shadow: 0 0 20px rgba(0,0,0,0.2);
      border-radius: 10px;
      overflow: hidden;
      position: relative;
    }

    .form-title {
      text-align: center;
      font-size: 24px;
      padding: 20px 0 10px;
      color: #4CAF50;
      font-weight: bold;
      border-bottom: 1px solid #eee;
      margin: 0;
      background: #fff;
    }

    .slider {
      display: flex;
      width: 720px;
      transition: transform 0.5s ease-in-out;
    }

    .form-container {
      width: 360px;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px 20px;
    }

    form {
      display: flex;
      flex-direction: column;
      width: 100%;
    }

    input, select {
      background: #f2f2f2;
      border: 0;
      margin: 0 0 15px;
      padding: 15px;
      font-size: 14px;
      font-family: "Roboto", sans-serif;
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      border-radius: 4px;
    }

    select {
      background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='10' viewBox='0 0 14 10'%3E%3Cpath fill='%23666' d='M7 10L0 0h14z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 10px center;
      background-size: 12px;
      padding-right: 35px;
      cursor: pointer;
    }

    button {
      background: #4CAF50;
      border: 0;
      padding: 15px;
      color: #fff;
      font-size: 14px;
      text-transform: uppercase;
      cursor: pointer;
      transition: background 0.3s ease;
      border-radius: 4px;
    }

    button:hover {
      background: #43A047;
    }

    .message {
      font-size: 12px;
      color: #777;
      text-align: center;
      margin-top: 15px;
    }

    .message a {
      color: #4CAF50;
      text-decoration: none;
      cursor: pointer;
    }
    .error-message {
        color: #f44336;
        background: #ffe6e6;
        padding: 10px;
        border-radius: 4px;
        text-align: center;
        font-weight: bold;
        margin-bottom: 15px;
    }

  </style>
</head>
<body>

  <div class="login-page">
    <h2 class="form-title">Halısaha Oyuncu Bulma Sistemi</h2>
    <div class="slider" id="slider">
      <div class="form-container">
        <form id="login-form" action="post_operations.php" method="post">
        <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
            <div class="error-message">Kullanıcı adı veya şifre hatalı.</div>
        <?php endif; ?>
        <input type="hidden" name="post_name" value="login">
          <input type="text" name="username" placeholder="Kullanıcı Adı" required />
          <input type="password" name="password" placeholder="Şifre" required />
          <button>Giriş Yap</button>
          <p class="message">Hesabın yok mu? <a id="to-register">Kayıt Ol</a></p>
        </form>
      </div>
      <div class="form-container">
        <form id="register-form" action="post_operations.php" method="post">
        <?php if (isset($_GET['error']) && $_GET['error'] == 2): ?>
            <div class="error-message">Kullanıcı adı veya email zaten kayıtlı.</div>
        <?php endif; ?>
                <?php if (isset($_GET['error']) && $_GET['error'] == 3): ?>
            <div class="error-message">Bir hata oluştu, yeniden deneyiniz.</div>
        <?php endif; ?>
          <input type="hidden" name="post_name" value="register"/>
          <input name="username" type="text" placeholder="Kullanıcı Adı" required/>
          <input name="password" type="password" placeholder="Şifre" required/>
          <input name="firstname" type="text" placeholder="Ad" required/>
          <input name="lastname" type="text" placeholder="Soyad" required/>
          <input name="email" type="email" placeholder="E-posta" required/>
          <select id="position" name="favorite_pos" required>
            <option value="" disabled selected>Pozisyon Seçin</option>
            <option value="Hücum">Hücum</option>
            <option value="Kaleci">Kaleci</option>
            <option value="Defans">Defans</option>
            <option value="Orta Saha">Orta Saha</option>
          </select>
          <button>Kayıt Ol</button>   
          <p class="message">Zaten bir hesabın var mı? <a id="to-login">Giriş Yap</a></p>
        </form>
      </div>
    </div>
  </div>

<script>
  const slider = document.getElementById("slider");
  const toRegister = document.getElementById("to-register");
  const toLogin = document.getElementById("to-login");

  toRegister.addEventListener("click", function(e) {
    e.preventDefault();
    slider.style.transform = "translateX(-360px)";
  });

  toLogin.addEventListener("click", function(e) {
    e.preventDefault();
    slider.style.transform = "translateX(0)";
  });


  window.addEventListener("DOMContentLoaded", () => {
    const params = new URLSearchParams(window.location.search);
    if (params.get("form") === "register") {
      slider.style.transform = "translateX(-360px)";
    }
  });
</script>


</body>
</html>
