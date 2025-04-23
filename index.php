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
      <form action="PHP/conexion.php" method="POST">
        <label for="correo">Correo</label>
        <input type="email" id="correo" name="correo" required>

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" href="#" class="alineacion">Iniciar Sesion</button>
        <a href="Registros/registroOpciones.html" class="register-link">Registrarse</a>
      </form>
    </div>
  </div>

  <?php require 'PHP/conexion.php'; ?>
</body>

</html>