<?php

  session_start();
  $varsesion = $_SESSION['usuario'];
    /* se pone error cero, para que si se entra sin hacer login, no aparezca el error de php de que no existe variable usuario */
  error_reporting(0);

  require("conectar.php");

  if ($_POST['action'] == 'eliminar_venta'){

  $id = $_POST['id'];
  // se hace un select-numventa, para calcular que venta se va a eliminar, y después mostrarla en un alert, para que el usuario vea el número de venta que se ha eliminado.
  $sql_numventa = "SELECT numventa,fecha FROM ventas WHERE idventa = $id";
  
  $resultado_numventa = $mysqli -> query($sql_numventa);
  $row = $resultado_numventa->fetch_assoc();
  
  // este select es para ejecutar la consulta y eliminar la venta con el id que se ha pasado en el ajax.
  $sql = "DELETE FROM ventas WHERE idventa = $id";


  $resultado = $mysqli -> query($sql);
  
//se devuelve el valor de la venta eliminada.
  echo $row['numventa']. "/". date("y",strtotime($row['fecha']));  
    
  }
  
  if ($_POST['action'] == 'mostrar_venta'){
  $id = $_POST['id'];
  // se hace un select-numventa, para calcular que venta se va a eliminar, y después mostrarla en un alert, para que el usuario vea el número de venta que se ha eliminado.
  $sql_numventa = "SELECT ventas.idcliente as idcliente, clientes.razon_social as razon_social, ventas.fecha as fecha, ventas.numventa as numventa FROM ventas INNER JOIN clientes ON ventas.idcliente = clientes.idcliente WHERE idventa = $id";
  
  $resultado_numventa = $mysqli -> query($sql_numventa);
  $row = $resultado_numventa->fetch_assoc();
  
  echo '¿Deseas eliminar la venta <b>'.$row['numventa']. "/". date("y",strtotime($row['fecha'])). " </b>del cliente<b> ".$row['razon_social']. "</b>?";
  
  }
  if ($_POST['action'] == 'num_venta') {
    $fecha = $_POST['fecha'];
  // extraemos el año de la fecha que le pasamoos, hay que ejecutar date y strtotime, porque la fecha hay que pasarla en formato timestamp
    $year = date("Y",strtotime($fecha));

    $sql = "SELECT MAX(numventa) as num_venta FROM ventas WHERE YEAR(fecha) = '$year'";
  
    $resultado = $mysqli->query($sql);
// con ejecutar if (resultado) que te dice si tiene valor, se añade uno al número máximo de numero de venta, si está nulo, quiere decir que es la primera venta del año, y se pone 1 a la primera venta
    if ($resultado) {
      $row = $resultado->fetch_assoc();
      $num_venta = ($row['num_venta']+1)."/".date("y",strtotime($fecha));
      echo $num_venta;
    } else {
      echo "1/".date("y",strtotime($fecha));
    }
  }
  
  if ($_POST['action'] == 'guardar_venta'){
    
    $fecha = $_POST['fecha'];
    $idcliente = $_POST['idcliente'];
    $iva = $_POST['iva'];
    $numVenta = $_POST['numVenta'];
    $notas = $_POST['notas'];

    $sql = "INSERT INTO ventas(numventa, fecha, idcliente, iva, notas) VALUES($numVenta,'$fecha',$idcliente,$iva,'$notas')";


    $resultado = $mysqli -> query($sql);

    if ($resultado) {
      echo '1';
    } else {
      echo '0';
    }
  
  }
  if ($_GET['action'] == 'rellena_combo'){
    $cliente = $_GET['q'];
  

  $resultado = $mysqli -> query("SELECT idcliente, razon_social FROM clientes WHERE razon_social LIKE '%$cliente%' OR idcliente LIKE'%$cliente%'");

  $datos = array();

  while ($row = $resultado -> fetch_assoc()){
    $elemento = $row['idcliente']." ".$row['razon_social'];
    array_push($datos,$elemento);
  
  }
    //echo $cliente;
    echo json_encode($datos);
  }

  if ($_GET['action'] == 'get_idcliente'){
    $cliente = $_GET['id'];
  
  $sql = "SELECT * FROM clientes WHERE idcliente ='$cliente'";
  $resultado = $mysqli -> query($sql);
    
  if ($resultado){
    $jsoncliente = $resultado -> fetch_assoc();
    echo json_encode($jsoncliente);
  }
  else{
    echo $error = "Ha habido un error";
  }
    
  }

  $mysqli->close();
  exit;

