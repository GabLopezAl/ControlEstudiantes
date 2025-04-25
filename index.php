<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>School System - Login</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <div class="container">
    <div class="logo-section">
      <img src="img/Logo.png" alt="School System Logo" class="logo" href="index.html">
    </div>

    <div class="login-box">
      <h2>LOGIN</h2>
      <form action="PHP/Login/loginAdmin.php" method="POST">
        <label for="correo">Correo</label>
        <input type="email" id="correo" name="correo" required>

        <label for="password">Contraseña</label>
        <div style="position: relative; width: 100%;">
            <input type="password" id="password" name="password" required style="
              width: 100%;
              padding: 10px 40px 10px 10px;
              border: 1px solid #ddd;
              border-radius: 5px;
              box-sizing: border-box;
            ">
            <span id="togglePassword" onclick="togglePassword()" style="
              position: absolute;
              right: 5px;
              top: 33%;
              transform: translateY(-50%);
              cursor: pointer;
              font-size: 20px;
            ">🔒</span>
          </div>


        <button type="submit" href="#" class="alineacion">Iniciar Sesion</button>
        <a href="Screens/Registros/registroOpciones.html" class="register-link">Registrarse</a>
      </form>
    </div>
  </div>

  <?php require 'PHP/Conexion/conexion.php'; ?>
  <script src="js/eyeIcon.js"></script>
</body>

</html>