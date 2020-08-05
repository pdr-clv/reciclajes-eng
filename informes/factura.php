<?php
  session_start();
  $varsesion = $_SESSION['usuario'];
    /* se pone error cero, para que si se entra sin hacer login, no aparezca el error de php de que no existe variable usuario */
  error_reporting(0);
  if ($varsesion==null || empty($varsesion)) {
    echo 'Hay que acceder a esta página a traves del formulario Login';
    die();
  }

  //require ('php/conectar.php');
  
  $ventafiltro = $_GET['idventa'];

?>


<!DOCTYPE html>
<html lang="es">
  <head>
    <title>Introducir Venta</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0, maximum-scale=1.5, minimum-scale=1.0">
    
    <link rel="stylesheet" href="../estilos/bootstrap.min.css">
    <link rel="stylesheet" href="../estilos/estilos.css">
    <link rel="stylesheet" href="../estilos/jquery-ui.min.css">
    <link rel="stylesheet" href="../estilos/fontawesome/css/all.min.css">
  </head>

<body class="text-right py-3">
  <a href="javascript:history.back(-1);" class="btn btn-secondary mr-5 mb-2" title="Volver a Venta"><i class="fas fa-undo-alt"></i></a>
  <embed type="application/pdf" id="pdfEmbebed" src="factura_venta.php?idventa=<?php echo $ventafiltro;?>" width="100%" height="500px"> 
  </embed>
</body>
</html> 