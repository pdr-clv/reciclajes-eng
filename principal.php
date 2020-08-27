<?php
  session_start();
  $varsesion = $_SESSION['usuario'];
    /* se pone error cero, para que si se entra sin hacer login, no aparezca el error de php de que no existe variable usuario */
  error_reporting(0);
  if ($varsesion==null || empty($varsesion)) {
    echo '<p class="error"> * No está autorizado para acceder a esta página web</p>';
    die();
  }
?>


<!DOCTYPE html>
<html lang="EN">
  <head>
    <title>Main Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0, maximum-scale=1.5, minimum-scale=1.0">
    <link rel="stylesheet" href="estilos/bootstrap.min.css">
    
<!-- aquíe meto la hoja de estilos, para darle unos pocos estilos propios -->
    <link rel="stylesheet" href="estilos/estilos.css">
  </head>
  <body>
    <!-- inicio barra de navegación -->
    <div class="fondonav border-bottom border-white fixed-top">
      <nav class="navbar navbar-expand-lg navbar-dark contenedor d-flex justify-content-between">
<!-- si no se le mete un ancho de 30 px a la a, la imagen coge un ancho automatico superior a 30 px y descuadra el centrado a la izquierda del logo -->
        <span class="navbar-brand" href="#">
          <img src="img/reciclajes_logo.svg" width="30" height="30" class="d-inline-block align-top mr-2" alt="">
          Recycling Ltd.
        </span>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
<!-- poniendo ml-auto al ul, se desplazan a la izquierda todos los items. -->
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a class="nav-link active btn btn-sm" href="#">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link btn btn-sm" href="ventas.php">Sales</a>
            </li>
            <li class="nav-item">
              <a class="nav-link btn btn-sm btn-danger" href="php/cerrarsesion.php" tabindex="-1" aria-disabled="true"> &times; Close</a>
            </li>
          </ul>
        </div>
      </nav>
    </div> 
    <!-- fin de barra de navegación -->
      
    <section class="banner">
      <img src="img/img1.jpg" alt="" class="img-banner">
      <div class="banner-contenido">
        <h1>Recycling Ltd.</h1>
        <p>Main Dashboard</p>
      </div>
    </section>
    <main>
      <div class="contenedor">
        <div class="row p-3 introduccion mb-3 shadow-lg border border-white">
          <h1 class="w-100" align="center">Test Version</h1>
          <p class="text-center w-100">This is a test version, with data mock in server, in order to use functionality of this responsive web-site.</p>
<!-- para hacer invisible en elementos mas pequeños que sm, se utiliza d-none (display none y d-sm bloque para mas grandes que sm) -->
          <p class="text-center w-100 d-none d-sm-block "> Front end developed with JS, JQuery. Back end with PHP + mySQL.</p>
        </div>
      </div>
    </main>
    <!-- Footer -->
<footer class="page-footer font-small blue pt-4">

    <!-- Footer Links -->
    <div class="contenedor text-center">
    <!-- Footer Links -->

    <!-- Copyright -->
    <div class="footer-copyright text-center py-3">© 2018 Copyright: Recycling Ltd.</div>
    <div class="footer-copyright text-center py-3">
      <a class="user-nav "href="php/cerrarsesion.php">Close session: <?php  echo $varsesion ?></a>
    </div>
    <!-- Copyright -->

  </footer>
  <!-- Footer -->
  <!-- librerias java script -->
  <script src="js/librerias/jquery.js"></script>
  <script src="js/librerias/popper.min.js"></script>
  <script src="js/librerias/bootstrap.min.js"></script>
    
  </body>
</html>