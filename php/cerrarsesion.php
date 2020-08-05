<?php
  session_start();
  /* se pone error cero, para que si se entra sin hacer login, no aparezca el error de php de que no existe variable usuario */
  error_reporting(0);

  $varsesion = $_SESSION['usuario'];
  if ($varsesion==null || empty($varsesion)) {
    echo '<p class="error"> * No está autorizado para acceder a esta página web</p>';
    die();
  }
  
  session_destroy();
  header("Location:../index.php");
  
?>