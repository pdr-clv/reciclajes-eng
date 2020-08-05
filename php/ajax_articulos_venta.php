<?php


  require ('conectar.php');
  require('fun_conversiones.php');
  
  if ($_GET['action'] == 'cargar_venta') {
    $idventa = $_GET['idventa'];
    $sql = "SELECT ventas.idventa as id, ventas.idcliente as idcliente, ventas.numventa as numventa, ventas.fecha as fecha, clientes.razon_social as razon_social, ventas.iva as iva, ventas.notas as notas FROM ventas INNER JOIN clientes ON ventas.idcliente = clientes.idcliente WHERE idventa = $idventa";
    $resultado = $mysqli -> query($sql);
    
    if ($resultado){
      $jsonventa = $resultado -> fetch_assoc();
      echo json_encode($jsonventa);
    }
    else{
      echo $error = "Ha habido un error";
    }
    
  }

  if ($_GET['action'] == 'rellena_cmb_articulo'){
  $idarticulo = $_GET['q'];
  

  $resultado = $mysqli -> query("SELECT articulo, descripcion FROM articulos WHERE articulo LIKE '%$idarticulo%' OR descripcion LIKE '%$idarticulo%'");

  $datos = array();

  while ($row = $resultado -> fetch_assoc()){
    $elemento = $row['articulo']." ".$row['descripcion'];
    array_push($datos,$elemento);
  
  }
    //echo $cliente;
  echo json_encode($datos);
  }

  if ($_GET['action'] == 'get_idarticulo'){
  $idarticulo = $_GET['id'];
  
  $sql = "SELECT * FROM articulos WHERE articulo ='$idarticulo'";
  $resultado = $mysqli -> query($sql);
    
  if ($resultado){
    $jsoncliente = $resultado -> fetch_assoc();
    echo json_encode($jsoncliente);
  }
  else{
    echo $error = "Ha habido un error";
  }
  }
  
  if ($_GET['action'] == 'guardar_linea'){
    $idventa = $_GET['idventa'];
    $idarticulo = $_GET['idarticulo'];
    //si el valor es introducido con número decimal está delimitado por coma, lo pasa a . con esta función de string_replace
    $cantidad = str_replace(",",".",$_GET['cantidad']);
    $importe = $_GET['importe'];
    
    $sql = "INSERT INTO linventas(idventa, idarticulo, cantidad, pvplin) VALUES($idventa,$idarticulo,$cantidad,$importe)";
    
    $resultado = $mysqli -> query($sql);

    if ($resultado) {
// se ejecuta un update en la tabla de articulos para que se grabe el precio de venta.
      $precio = $importe/$cantidad;
      $sql = "UPDATE articulos SET pvpv = ".$precio." WHERE idarticulo = ".$idarticulo;
      $resultado = $mysqli -> query($sql);
      echo '1';
    } else {
      echo '0';
    }
  }
  if ($_GET['action'] == 'eliminar_linea') {
    $id = $_GET['id'];
    $sql = "DELETE FROM linventas WHERE idlinventa = $id";
    $resultado = $mysqli -> query($sql);
    if ($resultado) {
      echo "1";
    } else {
      echo "0";
    }
  }
  
  if ($_GET['action'] == 'cargar_articulo_edit') {
    $id = $_GET['id'];
    $sql = "SELECT linventas.idarticulo AS idarticulo, articulos.articulo AS articulo, articulos.descripcion AS descripcion, linventas.cantidad AS cantidad, linventas.pvplin AS pvplin FROM linventas INNER JOIN articulos ON linventas.idarticulo = articulos.idarticulo WHERE linventas.idlinventa = $id";
    
    $resultado = $mysqli -> query($sql);
    
    if ($resultado){
      $jsonarticuloedit = $resultado -> fetch_assoc();
      echo json_encode($jsonarticuloedit);
    }
    else{
      echo $error = "Ha habido un error";
    }
    
  }

  if ($_GET['action'] == 'editar_linea') {
    $id= $_GET['id'];
    $idarticulo = $_GET['idarticulo'];
    $cantidad = $_GET['cantidad'];
    $importe = $_GET['importe'];
    
    $sql = "UPDATE linventas SET idarticulo = ".$idarticulo.", cantidad = ".$cantidad.", pvplin = ".$importe." WHERE idlinventa = ".$id;
    
    $resultado = $mysqli -> query($sql);
    if ($resultado) {
      echo "1";
    } else {
      echo "0";
    }
    
  }
  if ($_GET['action'] == 'editar_venta'){
        
    $idVenta=$_GET['idventa'];
    $fecha=$_GET['fecha'];
    $idCliente=$_GET['idcliente'];
    $numVenta=$_GET['numVenta'];
    $iva=$_GET['iva'];
    $notas=$_GET['notas'];
    
    $sql = "UPDATE ventas SET numventa = ".$numVenta.", fecha = '".$fecha."', idcliente = ".$idCliente.", iva = ".$iva.", notas = '".$notas."' WHERE idventa = ".$idVenta;
    
    $resultado = $mysqli -> query($sql);
    
    if ($resultado) {
      echo "1";
    } else {
      echo "0";
    }
  }

  if ($_GET['action'] == 'cargar_lineas') {
    $idVenta=$_GET['idventa'];
    $sql = "SELECT linventas.idlinventa AS idlinventa, linventas.idventa AS idventa, articulos.articulo AS idarticulo,articulos.descripcion AS descripcion,linventas.cantidad AS cantidad,linventas.pvplin AS importe FROM linventas INNER JOIN articulos ON linventas.idarticulo = articulos.idarticulo WHERE linventas.idventa = $idVenta";
    $resultado = $mysqli->query($sql);
    $output = '';
    while ($row = $resultado->fetch_assoc()){
                $idlinventa = $row['idlinventa'];
                $idarticulo = $row['idarticulo'];
                $descripcion = $row['descripcion'];
                $cantidad = $row['cantidad'];
                $importe = $row['importe'];
    
    $output = $output.'<tr id="'.$idlinventa.'"> <!-- al tr se le da el valor id, después será util a la hora de borrar articulos -->
<!-- se le da classe a id articulo articulo-codigo para que en estilos se le de unos caracteres mono (mismo ancho todos los caracteres)-->
            <td class="articulo-codigo">'.$idarticulo.'</td>
            <td>'.$descripcion.'</td>
<!-- hay que transformar el resultado cantidad a string, después de redondearlo como mucho a 3 decimales, y después cambiar el punto por coma como simbolo se separación decimal -->
            <td>'.convertir_cantidad($cantidad).'</td>
            <td>'.convertir_precio($importe/$cantidad).'€</td>
            <td>'.convertir_precio($importe).'€</td>
<!-- para llamar después a editar y eliminar, se les da una clase btn_ etc, y se les da el atributo id, utiles para después seleccionarlos desde jquerry -->
            <td><a id="'.$idlinventa.'" class="btn_editar_linea btn text-primary p-0 px-3" title="Editar"><i class="fas fa-edit"></i></a></td>
            <td><a id="'.$idlinventa.'"class="btn_eliminar_linea btn text-danger p-0 px-3" title="Eliminar"><i class="fas fa-trash-alt"></i></a></td></tr>';
    }
    if ($output != '') {
      echo $output;
    } else {
      //echo '<div class="alert alert-danger" role="alert">No existen articulos en esta venta. Haz clic en añadir articulos</div>';
      echo '<p class="text-danger">No existen articulos en esta venta</p>';
    }
    
  }

  if ($_GET['action'] == 'actualiza_importe'){
    $idventa=$_GET['idventa'];
    $sql="SELECT SUM(pvplin) AS importe FROM linventas WHERE idventa=".$idventa;
    $resultado = $mysqli->query($sql);
    $devolucion = $resultado->fetch_assoc();
    $devol = $devolucion['importe'];
    //si la venta no tiene lineas, el importe será valor nulo, y forzamos para que sea 0
    if ($devol===NULL) {
      $devol = 0;
    }
    echo $devol;
  }
// comprueba si hay duplicados de numVenta. devuelve cuantos valores hay, normalmente uno si está duplicado, o cero si no hay duplicado
  if ($_GET['action'] == 'check_numventa'){
    $numventa = $_GET['numventa'];
    $fecha = $_GET['fecha'];
    $anyo = date("Y",strtotime($fecha));// hay que convertir $fecha en strotime, para que PHP pueda sacarle el año con la función date("Y")
    //SELECT * FROM `ventas` WHERE numventa = 10 AND YEAR(fecha)=2009
    $sql = "SELECT * FROM ventas WHERE numventa=".$numventa. " AND YEAR(fecha)=".$anyo;
    $resultado = $mysqli->query($sql);
    echo $rows=$resultado->num_rows;
  }
  
  $mysqli->close();
  exit;