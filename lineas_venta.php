  <?php
  session_start();
  $varsesion = $_SESSION['usuario'];
    /* se pone error cero, para que si se entra sin hacer login, no aparezca el error de php de que no existe variable usuario */
  error_reporting(0);
  if ($varsesion==null || empty($varsesion)) {
    echo 'Hay que acceder a esta página a traves del formulario Login';
    die();
  }
  require ('php/conectar.php');
  
  $sql = "SELECT * FROM ventas ORDER BY idventa DESC LIMIT 1 ";
  $resultado = $mysqli->query($sql);
  if ($resultado) {
  $row = $resultado->fetch_assoc();
  } else {
    echo "No se ha podido cargar datos venta";
  }
  $idventa = $row['idventa'];
  $fecha = $row['fecha'];
  $idCliente = $row['idcliente'];
  $numVenta = $row['numventa'];
  $iva = $row['iva'];
  ?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <title>Introducir Venta</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0, maximum-scale=1.5, minimum-scale=1.0">
    
    <link rel="stylesheet" href="estilos/bootstrap.min.css">
    <link rel="stylesheet" href="estilos/estilos.css">
    <link rel="stylesheet" href="estilos/jquery-ui.min.css">
  </head>
<!-- comprobacion de seguridad que usuario ha accedido mediante login -->
  <body>
    <div class="jumbotron jumbotron-fluid border border-white p-1">
        <h2 class="text-center">Nueva Venta</h2>
    </div>
    <div class="container p-0">
      <form class= "w-100 shadow-lg form-horizontal" action="guardar_venta.php" method="post" onsubmit="">
        <div class="row justify-content-end p-2">
            <a href="ventas.php" class="btn btn-secondary btn-sm rounded">X</a>        
        </div>
        <div class="row">
          <div class="col-lg-6">
            <div class="form-group row p-1">
              <label for ="cliente" class="col-sm-3 text-sm-right p-sm-1 font-weight-bolder">Id Venta</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="idventa" name="idventa" value="<?php echo $idventa;?>" readonly>
              </div>
            </div>
            <div class="form-group row p-1">
              <label for ="fecha" class="col-sm-3 text-sm-right p-sm-1 font-weight-bolder">Fecha</label>
              <div class="col-sm-9">
                <input type="date" class="form-control" id="fecha" name="fecha" placeholder ="Fecha" value="<?php echo $fecha; ?>" readonly>
              </div>
            </div>
            <div class="form-group row p-1">
              <label for ="id_cliente" class="col-sm-3 text-sm-right p-sm-1 font-weight-bolder">Num. Cliente</label>
              <div class="col-sm-9">
                <input type="text" class="form-control d-none" id="clientex" name="clientex" placeholder ="Cliente" value="<?php echo $idCliente; ?>" readonly>
                <select class="form-control" id="id_cliente" name="id_cliente">
<!--                  <option value="0">Selecciona un valor</option>
                  <option value="1">Hola</option> -->
                </select>
                <!-- <input type="text" class="form-control" id="id_cliente" name="id_cliente" placeholder ="Id Cliente"> -->
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="form-group row p-1">
              <label for ="num_venta" class="col-sm-3 text-sm-right p-sm-1 font-weight-bolder">Num. venta</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="num_venta" name="num_venta" placeholder ="Num. Venta" value="<?php echo $numVenta; ?>" readonly>
              </div>
            </div>
            <div class="form-group row p-1">
              <label for ="importe" class="col-sm-3 text-sm-right p-sm-1 font-weight-bolder">Importe</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="importe" name="importe" placeholder ="Importe" readonly>
              </div>
            </div>
            <div class="form-group row p-1">
              <label for ="iva" class="col-sm-3 text-sm-right p-sm-1 font-weight-bolder">Iva</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="iva" name="iva" placeholder="iva" value="<?php echo $iva; ?>" readonly>
              </div>
            </div>
          </div>
      </div>
      <div class="row">
        <div class="col">
          <div class="row justify-content-center">
          <input type="submit" name="submit" class="btn btn-primary my-3" value="Guardar">
          </div> 
        </div>
        <div class="col">
          <div class="row justify-content-center">
          <input type="submit" name="editar" class="btn btn-outline-success my-3" value="Editar">
          </div> 
        </div>
      </div>
    </form>
  </div>
  </body>
<!-- archivos libreria externa java script -->
  <script src="js/librerias/popper.min.js"></script>
  <script src="js/librerias/bootstrap.min.js"></script>
  <script src="js/librerias/jquery.js"></script>
  <script src="js/librerias/jquery-ui.min.js"></script>
  <script src="js/librerias/eModal.min.js"></script>
<!-- archivo con funciones java script propios -->
  <script src="js/funciones_lineas_venta.js"></script>
</html>