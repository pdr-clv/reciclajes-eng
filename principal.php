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
    <title>Panel principal</title>
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
          <img src="img/reciclajes_logo.svg" width="30" height="30" class="d-inline-block align-top mr-2" alt="">R.Catalán S.L.
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
              <a class="nav-link btn btn-sm" href="compras.php">Purchases</a>
            </li>
            <li class="nav-item">
              <a class="nav-link btn btn-sm" href="ventas.php">Sales</a>
            </li>
            <li class="nav-item">
              <a class="nav-link btn btn-sm" href="#" tabindex="-1" aria-disabled="true">Maintenance</a>
            </li>
            <li class="nav-item">
              <a class="nav-link btn btn-sm mr-lg-4" href="#" tabindex="-1" aria-disabled="true">Reports</a>
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
        <h1>Reciclajes Catalán</h1>
        <p>Panel de control</p>
      </div>
    </section>
    <main>
      <div class="contenedor">
        <div class="row p-3 introduccion mb-3 shadow-lg border border-white">
          <h1 class="w-100" align="center">Guía para la navegación</h1>
          <p class="text-center w-100">Aplicación web diseñada para el registro de datos del negocio Reciclajes Catalán S.L. </p>
<!-- para hacer invisible en elementos mas pequeños que sm, se utiliza d-none (display none y d-sm bloque para mas grandes que sm) -->
          <p class="text-center w-100 d-none d-sm-block "> Panel de control para acceder a los diferentes apartados de la aplicación. Explicación básica de como navegar en la aplicación para administrar diferentes registros de datos.</p>
        </div>
        <div class="row shadow-lg">
          <div class="col-sm-6 mb-2 border-bottom border-secondary">
            <div class="row d-flex align-items-center justify-content-center p-2 mb-3 justify">
              <img class="col-lg-5 w-50"src="img/ventas_icono.png" alt="Panel de ventas">
              <h2 class="col-lg-7" align="center"><a href="ventas.php">Registro de ventas</a></h2>
            </div>
<!-- al siguiente elemento p, si se le da clase row, hace saltos de linea al añadir los href en los elementos a, simplemente se le da la clase text-justify para que esté justificado el texto -->
            <p class="text-justify mx-2">Para acceder al <a href="ventas.php">Panel de Ventas</a> de una forma rápida, busca <b>Ventas</b> en la barra superior de navegación de la aplicación. En este panel de administración de ventas, se pueden registrar <b>Nueva Venta</b>. También se puede <b>editar</b> una venta, o <b>eliminar</b> una venta. Debe estar dado de alta el cliente para poder añadir la venta, si no existiera el cliente, habría que darlo de alta al cliente en el apartado de Mantenimiento</p>
          </div>
          <div class="col-sm-6 mb-2 border-bottom border-secondary">
            <div class="row d-flex align-items-center p-2 mb-3">
              <img class="col-lg-5" src="img/img2.jpg" alt="">
              <h2 class="col-lg-7" align="center"><a href="ventas.php">Registro de compras</a></h2>
            </div>
              <p class="text-justify">Lorem ipsum dolor sit amet, consectetur adipisicing elit. <a href="ventas.php">Ventas</a> Soluta quod dolore aspernatur, eos expedita, nobis placeat consequatur vero ipsa similique excepturi odio suscipit facere in. Veritatis, magnam cum impedit dolorem? Lorem ipsum dolor sit amet, consectetur adipisicing elit. Soluta quod dolore aspernatur, eos expedita, nobis placeat consequatur vero ipsa similique excepturi odio suscipit facere in. Veritatis, magnam cum impedit dolorem?</p>
          </div>
          <div class="col-sm-6 mb-2 border-bottom border-secondary">
            <div class="row d-flex align-items-center p-2 mb-3">
              <img class="col-lg-6" src="img/img2.jpg" alt="">
              <h2 class="col-lg-6" align="center">Bienvenido a la página principal</h2>
            </div>
              <p class="text-justify">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Soluta quod dolore aspernatur, eos expedita, nobis placeat consequatur vero ipsa similique excepturi odio suscipit facere in. Veritatis, magnam cum impedit dolorem?</p>
          </div>
          <div class="col-sm-6 mb-2 border-bottom border-secondary">
            <div class="row d-flex align-items-center p-2 mb-3">
              <img class="col-lg-6" src="img/img2.jpg" alt="">
              <h2 class="col-lg-6" align="center">Bienvenido a la página principal</h2>
            </div>
              <p class="text-justify">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Soluta quod dolore aspernatur, eos expedita, nobis placeat consequatur vero ipsa similique excepturi odio suscipit facere in. Veritatis, magnam cum impedit dolorem?</p>
          </div>
        </div>
      </div>
    </main>
    <!-- Footer -->
<footer class="page-footer font-small blue pt-4">

    <!-- Footer Links -->
    <div class="contenedor text-center">
    <!-- Footer Links -->

    <!-- Copyright -->
    <div class="footer-copyright text-center py-3">© 2018 Copyright: Reciclajes Catalán S.L.</div>
    <div class="footer-copyright text-center py-3">
      <a class="user-nav "href="php/cerrarsesion.php">Cerrar sesion: <?php  echo $varsesion ?></a>
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