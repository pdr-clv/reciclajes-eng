<?php
if (isset($_POST['submit'])){
  /* se crea la variable errores, si se acumulan errores que vamos a chequear en las primeras cajas de texto, no se llamará a la consulta sql */
  $errores = 0;
  /*$usuario=$_POST['usuario'];
  $password=$_POST['password']; */
  /* las variables usuario y password vienen de hacer un include en el formulario index.html que es el que hace login */
  if (empty($usuario)){
    echo '<div class="alert alert-danger"><strong>*</strong> El campo Usuario no puede estar vacio</div>';
    $errores++;
  } elseif (strlen ($usuario) > 25){
    echo '<div class="alert alert-danger"><strong>*</strong> El campo Usuario es muy largo</div>';
    $errores++;
  }/* esta comprobación de si contiene el caracter ' ya no es necesaria si mas adelante, después de generar el objeto mysqli se filtra el string con real_escape_string 
  
    elseif (strpos($usuario, "'") !== false ) { /* con esta comprobación, aseguramos que no existe el caracter no deseado ' */
  /*  echo '<div class="alert alert-danger"><strong>*</strong> Has escrito un caracter no permitido</div>';
    $errores++;
  }*/
  if (empty($password)) {
    echo '<div class="alert alert-danger"><strong>*</strong> El campo password está vacio</div>';
    $errores++;
  } elseif (strlen ($password) > 25){
    echo '<div class="alert alert-danger"><strong>*</strong> El campo password es muy largo</div>';
  }/* esta comprobación de si contiene el caracter ' ya no es necesaria si mas adelante, después de generar el objeto mysqli se filtra el string con real_escape_string 
  
    elseif (strpos($password, "'") !== false ) {
    echo '<div class="alert alert-danger"><strong>*</strong> Has escrito un caracter no permitido</div>';
  } */
  /* si existen errores en las cajas de texto anteriores, no se llamará al sql para comprobar el usuario */
  if($errores==0) {
    require("conectar.php");
//la funcion mysqli->real_escape_string eliminar los caracteres ' " & etc, que se usan para hacer inyección de sql 
    $usuario = $mysqli->real_escape_string($usuario);
// aqui comentamos las dos siguientes lineas, lo que seria ejecutar la consulta de una forma normal, y vamos a hacer una cosulta preparada
    $sql = "SELECT * FROM usuarios where login='$usuario' and acceso='1'";
    $resultado = $mysqli -> query($sql);

//$sql = "SELECT login FROM usuarios where login= ? and acceso='1'";
//    $resultado = $mysqli->prepare($sql);
//    $ok = $mysqli->stmt_bind_param($resultado, "s", $usuario);
//    if ($ok){
//      $ok = $mysqli->stmt_execute($resultado);
//      if ($ok == false) {
//        echo "Error al ejecutar la consulta";
//      } else {
//        $ok = $mysqli->stmt_bind_result($resultado, $userlog); // se le asigna variable userlog, hay que poner tantas variables como campos haya en el sql
//      }
//      
//    } else {
//      die ("Ha habido un error ejecutando la consulta");
//      
//    }
    if ($resultado->num_rows>0) {
      $password = $mysqli->real_escape_string($password);
      $sql = "SELECT * FROM usuarios where login='$usuario' and password='$password' and acceso='1'";
      $resultado = $mysqli -> query($sql);
        if ($resultado->num_rows>0) {
          $mysqli->close();
          $errores=0;
          session_start();
          $_SESSION['usuario']=$usuario;
          header("Location:principal.php");
        } else {
          echo '<div class="alert alert-danger"><strong>*</strong> El password es incorrecto</div>';
          $errores=0;
          $mysqli->close();
        }
          /* se cierra automaticamente la conexion msqli, pgusta cerrarla manualmente por si a caso */
    } else {
      echo '<div class="alert alert-danger"><strong>*</strong> El usuario no está registrado</div>';
      $errores=0;
      $mysqli->close();
    }
  }
  
}    
?>