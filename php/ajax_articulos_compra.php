<?php


  require ('conectar.php');
  require('fun_conversiones.php');

  if ($_GET['action'] == 'es_deducible') {
    $idcompra = $_GET['idcompra'];
    $sql= "SELECT deducible FROM compras WHERE idcompra = $idcompra";
    $resultado = $mysqli -> query($sql);
    
    if ($resultado){
      $output = $resultado -> fetch_assoc();
      echo $output['deducible'];
    }
    else{
      echo $error = "Ha habido un error";
    }
    
  }

  if ($_GET['action'] == 'eliminar_linea'){
    
    $idlin = $_GET['idlin'];
    
    $sql = "DELETE FROM lincompras WHERE idlin = $idlin";
    
    $resultado = $mysqli -> query($sql);
    
    echo true;

  }

  if ($_GET['action'] == 'cargar_lineas'){
    
    $idcompra = $_GET['idcompra'];

    $sql="SELECT lincompras.idlin AS idlin,articulos.articulo AS articulo,articulos.descripcion AS descripcion,lincompras.cantidad AS cantidad,lincompras.pvplin AS pvplin FROM lincompras INNER JOIN articulos ON lincompras.idarticulo=articulos.idarticulo WHERE lincompras.idcompra = $idcompra";

    
    $resultado = $mysqli->query($sql);
    
    $output = '';
    
    while ($row = $resultado->fetch_assoc()){
      $idlinventa = $row['idlin'];
      $articulo = $row['articulo'];
      $descripcion = $row['descripcion'];
      $cantidad = $row['cantidad'];
      $importe = $row['pvplin'];
    
      $output = $output.'<tr id="'.$idlinventa.'"> <!-- al tr se le da el valor id, después será util a la hora de borrar articulos -->
<!-- se le da classe a id articulo articulo-codigo para que en estilos se le de unos caracteres mono (mismo ancho todos los caracteres)-->
      <td class="articulo-codigo">'.$articulo.'</td>
      <td>'.$descripcion.'</td>
<!-- hay que transformar el resultado cantidad a string, después de redondearlo como mucho a 3 decimales, y después cambiar el punto por coma como simbolo se separación decimal -->
      <td>'.convertir_cantidad($cantidad).'</td>
      <td>'.convertir_precio($importe/$cantidad).'€</td>
      <td>'.convertir_precio($importe).'€</td>
<!-- para llamar después a editar y eliminar, se les da una clase btn_ etc, y se les da el atributo id, utiles para después seleccionarlos desde jquerry -->
      <td><a id="'.$idlinventa.'" class="btn_editar_linea btn text-primary p-0 px-3" title="Editar"><i class="fas fa-edit"></i></a></td>
      <td><a id="'.$idlinventa.'"class="btn_eliminar_linea btn text-danger p-0 px-3" title="Eliminar"><i class="fas fa-trash-alt"></i></a></td></tr>';
    }
    
    if ($output =='') {
      $output = '<div class="alert alert-danger" role="alert">No existen articulos asociados a esta compra</div>';
    }
    
    echo $output;
  }
  
  if ($_GET['action'] == 'cargar_encabezado') {
    $idcompra = $_GET['idcompra'];
    $sql= "SELECT compras.*, proveedores.razon_social, proveedores.cif, proveedores.direccion, proveedores.codpost, proveedores.poblacion, proveedores.provincia FROM compras LEFT JOIN proveedores ON compras.idproveedor = proveedores.idproveedor WHERE idcompra = $idcompra";
    $resultado = $mysqli -> query($sql);
    
    if ($resultado){
      $jsoncompra = $resultado -> fetch_assoc();
      echo json_encode($jsoncompra);
    }
    else{
      echo $error = "Ha habido un error";
    }
    
  }

  if ($_GET['action'] == 'display_importe'){
    $idcompra=$_GET['idcompra'];
    $sql="SELECT SUM(pvplin) AS importe FROM lincompras WHERE idcompra=$idcompra";
    $resultado = $mysqli->query($sql);
    $devolucion = $resultado->fetch_assoc();
    $devol = $devolucion['importe'];
    //si la venta no tiene lineas, el importe será valor nulo, y forzamos para que sea 0
    if ($devol===NULL) {
      $devol = 0;
    }
    echo $devol;
  }

  $mysqli->close();
  exit;