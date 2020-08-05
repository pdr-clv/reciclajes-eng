<?php 
  if (isset($_POST['submit'])) {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];
  }
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <title>Login App</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0, maximum-scale=1.5, minimum-scale=1.0">
<!-- librerias bootstrap 4  junto con sus jquerys, los pongo en la cabecera, pero si no funcionara, interesaría ponerlos antes de que acabe el body -->

<!-- comento los links y los sustituyo por los archivos definitivos. Utiles para cuando no hay internet -->
   
<!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script> -->
    

    <link rel="stylesheet" href="estilos/bootstrap.min.css">
    <link rel="stylesheet" href="estilos/estilos.css">
    </head>
    <header>
      <div class="fondonav shadow-lg">
        <nav class="navbar navbar-expand-sm navbar-dark container fondonav text-center">
    <a class="navbar-brand" href="#">
      <img src="img/reciclajes_logo.svg" width="30" height="30" class="d-inline-block align-top" alt="">
    R.Catalán S.L.
    </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
    <div class="navbar-nav ml-auto">
      <a class="nav-item nav-link active" href="#">Login <span class="sr-only">(current)</span></a>
      <a class="nav-item nav-link" href="#">Remember password</a>
    </div>
  </div>
</nav>
      </div>
    </header>
    <body>
    <main>
    <!-- inicio formulario login, se meterá dentro de un contenedor propio, no de bootstrap, será flexbox, para que esté centrado siempre el formulario -->
      <div class="contenedor d-flex flex-column justify-content-center align-items-center">
        <form class= "border border-dark rounded w-100 mt-5 shadow-lg formulario" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" onsubmit="return validar();">  
          <div class="jumbotron jumbotron-fluid border border-dark p-1">
            <h1 class="text-center">Login</h1>
          </div>
<!-- vamos a ver, se le mete un form-group y row, y se le da col-4 al label, y col 8 al input, para todos los tamaños, debido a que al hacer un align-left al label, en tamaños pequeños queda mal -->
<!-- hay que poner margin 0 y padding 0 a los elementos row, para que no sean mayores de lo que abarca en el formulario y no sobresalgan-->
          <div class="form-group row mb-5 p-0 m-0">
            <label for ="usuario" class="col-sm-4 text-sm-right col-form-label font-weight-bolder">User</label>
            <div class="col-sm-8 m-0">
              <input type="text" class="form-control" id="usuario" name="usuario" placeholder ="User" required>
            </div>
          </div>
          <div class="form-group row mb-5 p-0 m-0">
            <label for ="password" class="col-sm-4 text-sm-right font-weight-bolder">Password</label>
            <div class="col-sm-8 m-0">
              <input type="password" class="form-control" id="password" name="password" placeholder ="Password" required>
            </div>
          </div>
          <div class="row p-0 m-0 mb-5">
            <div class="col-sm-5 m-0 p-0"></div>
            <div class="col-sm-2 m-0 p-0">
  <!-- hay que meter el boton dentro de la columna del medio de 3, las dos de los lados vacias, y cuando sea mas pequeño el formulario, el boton ocupará el 100%-->
            <input type="submit" name="submit" class="btn btn-primary w-100" value="Log In">
            </div>
            <div class="col-sm-5 m-0 p-0"></div>
          </div>
              <?php
              include ("php/validarlogin.php");
              ?>
        </form>
      </div>
<!-- fin de formulario login -->
    </main>
<!-- librerias java script -->
  <script src="js/librerias/jquery.js"></script>
  <script src="js/librerias/popper.min.js"></script>
  <script src="js/librerias/bootstrap.min.js"></script>
<!-- script propio con funciones utiles -->
  <script src="js/validarlogin.js"></script>
  <script src="js/librerias/eModal.min.js"></script>
  </body>
</html>
