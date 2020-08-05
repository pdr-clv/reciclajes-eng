<?php

  session_start();
  $varsesion = $_SESSION['usuario'];
    /* se pone error cero, para que si se entra sin hacer login, no aparezca el error de php de que no existe variable usuario */
  error_reporting(0);

  require("conectar.php");
  require('fun_conversiones.php');

  if ($_GET['action'] == 'comprobar_numcompra'){
    $numCompra = $_GET['numCompra'];
    $fecha = $_GET['fecha'];
    $year = explode('-',$fecha)[0];
    $deducible = $_GET['deducible'];
    
    $sql = "SELECT * FROM compras WHERE YEAR(fecha)='".$year."' AND deducible = '".$deducible."' AND numcompra =".$numCompra;
    
    echo $sql;
  }

  if ($_POST['action'] == 'editar_compra'){
    
    
    //{action:'insertar_compra',numCompra:numCompra,fecha:fecha,idproveedor:idproveedor,iva:iva,sfactura:sfactura,notas:notas,deducible:deducible};
    $idcompra = $_POST['id'];
    $numCompra = $_POST['numCompra'];
    $fecha = $_POST['fecha'];
    $idproveedor = $_POST['idproveedor'];
    $iva = $_POST['iva'];
    $sfactura = $_POST['sfactura'];
    $notas = $_POST['notas'];
    $deducible = $_POST['deducible'];
// $sql = "UPDATE linventas SET idarticulo = ".$idarticulo.", cantidad = ".$cantidad.", pvplin = ".$importe." WHERE idlinventa = ".$id;
    /*$sql = 'INSERT INTO compras (numcompra, fecha, idproveedor, iva, sfactura, deducible, notas)';
    $sql = $sql.' VALUES ('.$numCompra.' ,"'.$fecha.'" ,'.$idproveedor.' ,'.$iva.' ,"'.$sfactura.'" ,"'.$deducible.'","'.$notas.'")'; */
    $sql = "UPDATE compras SET numcompra = ".$numCompra.", fecha = '".$fecha;
    $sql = $sql."', idproveedor = ".$idproveedor.", iva = ".$iva.", sfactura = '".$sfactura;
    $sql = $sql."', notas ='".$notas."', deducible = '".$deducible."' WHERE idcompra = ".$idcompra;
    

    $resultado = $mysqli -> query($sql);
    
    if($resultado) {
      echo true;
    } else{
      echo false;
    }

  }

  if ($_POST['action'] == 'num_compra') {
    $fecha = $_POST['fecha'];
    $deducible = $_POST['deducible'];
  // extraemos el año de la fecha que le pasamoos, hay que ejecutar date y strtotime, porque la fecha hay que pasarla en formato timestamp
    $year = date("Y",strtotime($fecha));

    $sql = "SELECT MAX(numcompra) as num_compra FROM compras WHERE YEAR(fecha) = '$year' AND deducible = '".$deducible."'";
  
    $resultado = $mysqli->query($sql);
// con ejecutar if (resultado) que te dice si tiene valor, se añade uno al número máximo de numero de venta, si está nulo, quiere decir que es la primera venta del año, y se pone 1 a la primera venta
    if ($resultado) {
      $row = $resultado->fetch_assoc();
      $num_compra = ($row['num_compra']+1)."/".date("y",strtotime($fecha));
      echo $num_compra;
    } else {
      echo "1/".date("y",strtotime($fecha));
    }
  }

  if ($_GET['action'] == 'get_idproveedor'){
    $proveedor = $_GET['id'];
  
    $sql = "SELECT idproveedor, razon_social FROM proveedores WHERE idproveedor ='$proveedor'";
    $resultado = $mysqli -> query($sql);
    
    if ($resultado){
      $jsonproveedor = $resultado -> fetch_assoc();
      echo json_encode($jsonproveedor);
    } 
    else{
      echo $error = "Ha habido un error";
    }
    
  }

  if ($_GET['action'] == 'rellena_combo'){
    $proveedor = $_GET['q'];
  

  $resultado = $mysqli -> query("SELECT idproveedor, razon_social FROM proveedores WHERE razon_social LIKE '%$proveedor%' OR idproveedor LIKE'%$proveedor%'");

  $datos = array();

  while ($row = $resultado -> fetch_assoc()){
    $elemento = $row['idproveedor']." ".$row['razon_social'];
    array_push($datos,$elemento);
  
  }
    //echo $cliente;
    echo json_encode($datos);
  }

  if ($_POST['action'] == 'insertar_compra'){
        //{action:'insertar_compra',numCompra:numCompra,fecha:fecha,idproveedor:idproveedor,iva:iva,sfactura:sfactura,notas:notas,deducible:deducible};

/*    
    $numCompra = $_POST['numCompra'];
    $fecha = $_POST['fecha'];
    $idproveedor = $_POST['idproveedor'];
    $iva = $_POST['iva'];
    $sfactura = $_POST['sfactura'];
    $notas = $_POST['notas'];
    $deducible = $_POST['deducible'];

    $sql = 'INSERT INTO compras (numcompra, fecha, idproveedor, iva, sfactura, deducible, notas)';
    $sql = $sql.' VALUES ('.$numCompra.' ,"'.$fecha.'" ,'.$idproveedor.' ,'.$iva.' ,"'.$sfactura.'" ,"'.$deducible.'","'.$notas.'")'; 
    $resultado = $mysqli -> query($sql);
    
    if($resultado) {
      echo true;
    } else{
      echo false;
    }
*/
  }

  if ($_GET['action'] == 'eliminar_compra'){
    
    $id = $_GET['id'];
    
    $sql = "DELETE FROM compras WHERE idcompra = $id";
    
    $resultado = $mysqli -> query($sql);
    
    echo true;

  }
  
  if ($_GET['action'] == 'cargar_compras'){
    
    $deducible = $_GET['deducible'];
    $search = $_GET['search'];
    $filtro = ' AND razon_social LIKE "%'.$search.'%" ';

    $sql='SELECT compras.idcompra AS id, compras.numcompra AS numcompra, compras.sfactura AS sfactura, compras.fecha AS fecha, proveedores.razon_social AS razon_social, (SELECT SUM(pvplin) FROM lincompras WHERE idcompra = id) AS importe,compras.iva AS iva FROM compras INNER JOIN proveedores ON compras.idproveedor = proveedores.idproveedor WHERE compras.deducible = "'.$deducible.'" '.$filtro.' ORDER BY fecha DESC LIMIT 25';

    
    $resultado = $mysqli->query($sql);
    
    $output = '';
    
    while ($row = $resultado->fetch_assoc()){
      $idcompra = $row['id'];
      $sfactura = $row['sfactura'];
      $fecha = $row['fecha'];
      $numcompra = ($deducible == '0') ? $row['numcompra']."/".date("y",strtotime($fecha)) : $row['numcompra'];
      $razon_social = $row['razon_social'];
      $importe = $row['importe'];
      $iva = $row['iva'];
      
      $output = $output.'<tr id="'.$idcompra.'">
      <td class="d-none">'.$idcompra.'</td>
      <td>'.$numcompra.'</td>
      <td>'.convertir_fecha($fecha).'</td>
      <td>'.$razon_social.'</td>
      <td>'.$sfactura.'</td>
      <td>'.convertir_precio($importe).'€</td>
      <td>'.$iva.'</td>
      <td><button idcompra='.$idcompra.' class="btn_editar_linea btn text-primary p-0 px-3"><i class="far fa-eye"></i> View</button></td>
      <td><button idcompra="'.$idcompra.'"class="btn_eliminar_linea btn text-danger p-0 px-3" title="Eliminar"><i class="fas fa-trash-alt"></i> Delete</button></td></tr>';
    }
    
    if ($output =='') {
      $output = '<div class="alert alert-warning" role="alert">No existen compras que cumplan con esta búsqueda</div>';
    }
    
    echo $output;
  }

  

// con esta función, devolvemos en formato html la paginación. Primero se calcula cuantos registros hay, y después se rellena la paginación.
  if ($_GET['action'] == 'cargar_paginacion2'){
    $deducible = $_GET['deducible'];
    $search = $_GET['search'];
    $filtro = ' AND razon_social LIKE "%'.$search.'%" ';

    
    $sql='SELECT COUNT(*) FROM compras INNER JOIN proveedores ON compras.idproveedor = proveedores.idproveedor WHERE deducible ="'.$deducible.'" '.$filtro;
    
    $resultado = $mysqli->query($sql);
    $registros = $resultado->fetch_row();
// es necesario redondear hacia abajo el cociente entre registros y número de páginas, y sumarle uno, vale para todos los casos tener concretado el número de páginas a mostrar en el listado ventas.
    $paginas = floor($registros[0]/25)+1;

    if ($paginas==1) {
      $output = '';
    } else {
      $output = '<ul>';
      $output = $output.'<li class="mr-2"><button id="bloqueUno" title="Ir a página Uno"><i class="fas fa-angle-double-left" title="Página anterior"></i></button></li><li class="mr-1"><button id="anterior"><i class="fas fa-angle-left"></i></button></li>';
      for ($i = 1; $i <= $paginas; $i++) {
        if ($i == 1) {
          $output=$output.'<li><button class="activo" id="pag'.$i.'">'.$i.'</button></li>';
        } else if ($i < 6) {
          $output=$output.'<li><button id="pag'.$i.'">'.$i.'</button></li>';
        } else {
          $output=$output.'<li><button class="d-none" id="pag'.$i.'">'.$i.'</button></li>';
        }
      }
      $output = $output.'<li class="ml-1"><button id="posterior"><i class="fas fa-angle-right" title="Pagina siguiente"></i></button></li><li class="mx-2"><button id="bloqueFin" title="Ir a último bloque"><i class="fas fa-angle-double-right"></i></button></li><small>
      Page num. <span id="pagActiva">1</span> of <span id="pagUltima">'.$paginas.'</span>
    </small></ul>';
    }
    echo $output;
  }

  if ($_GET['action'] == 'cargar_paginacion'){
    $deducible = $_GET['deducible'];
    $search = $_GET['search'];
    $filtro = ' AND razon_social LIKE "%'.$search.'%" ';

    
    $sql='SELECT COUNT(*) FROM compras INNER JOIN proveedores ON compras.idproveedor = proveedores.idproveedor WHERE deducible ="'.$deducible.'" '.$filtro;
    
    $resultado = $mysqli->query($sql);
    $registros = $resultado->fetch_row();
// es necesario redondear hacia abajo el cociente entre registros y número de páginas, y sumarle uno, vale para todos los casos tener concretado el número de páginas a mostrar en el listado ventas.
    $paginas = floor($registros[0]/25)+1;
    if ($paginas==1) {
      $output = '';
    } else {
      $output = '<ul class="pagination">';
      $output = $output.'<li class="page-item d-none" id="bloqueUno"><button class="page-link" id="bloqueUno" title="Ir a página Uno"><i class="fas fa-angle-double-left" title="Página anterior"></i></button></li><li class="page-item ml-2 mr-1 disabled" id="anterior"><button class="page-link" id="anterior"><i class="fas fa-angle-left"></i></button></li>';
      for ($i = 1; $i < $paginas + 1; $i++) {
        if ($i == 1) {
          $output=$output.'<li class="page-item disabled" id="'.$i.'"><button class="page-link" id="'.$i.'">'.$i.'</button></li>';
        } else if ($i > 5) {
          $output=$output.'<li class="page-item d-none" id="'.$i.'"><button class="page-link" id="'.$i.'">'.$i.'</button></li>';
        } else {
          $output=$output.'<li class="page-item" id="'.$i.'"><button class="page-link" id="'.$i.'">'.$i.'</button></li>';
        }
      }
      $output = $output.'<li class="page-item ml-1 mr-2" id="posterior"><button class="page-link" id="posterior"><i class="fas fa-angle-right" title="Pagina siguiente"></i></button></li><li class="page-item" id="bloqueFin"><button class="page-link" id="bloqueFin" title="Ir a último bloque"><i class="fas fa-angle-double-right"></i></button></li><div class="row align-items-center"><div class="col ml-2"><small>Page num. <span id="pagActiva">1</span> of <span id="pagUltima">'.$paginas.'</span></small></div></div></ul>';
    }
    echo $output;
  }

  if ($_GET['action'] == 'actualiza_pagina'){
    
    $deducible = $_GET['deducible'];
    $search = $_GET['search'];
    $filtro = ' AND razon_social LIKE "%'.$search.'%" ';
    $pagina = $_GET['pagina'];
    $limInf = ($pagina-1)*25;
    $limSup = 25;

    $sql='SELECT compras.idcompra AS id, compras.numcompra AS numcompra, compras.sfactura AS sfactura, compras.fecha AS fecha, proveedores.razon_social AS razon_social, (SELECT SUM(pvplin) FROM lincompras WHERE idcompra = id) AS importe,compras.iva AS iva FROM compras INNER JOIN proveedores ON compras.idproveedor = proveedores.idproveedor WHERE compras.deducible = "'.$deducible.'" '.$filtro.' ORDER BY id DESC LIMIT '.$limInf.','.$limSup;
    
    $resultado = $mysqli->query($sql);
    
    $output = '';
    
    while ($row = $resultado->fetch_assoc()){
      $idcompra = $row['id'];
      $sfactura = $row['sfactura'];
      $fecha = $row['fecha'];
      $numcompra = ($deducible == '0') ? $row['numcompra']."/".date("y",strtotime($fecha)) : $row['numcompra'];
      $razon_social = $row['razon_social'];
      $importe = $row['importe'];
      $iva = $row['iva'];
      
      $output = $output.'<tr id="'.$idcompra.'">
      <td class="d-none">'.$idcompra.'</td>
      <td>'.$numcompra.'</td>
      <td>'.convertir_fecha($fecha).'</td>
      <td>'.$razon_social.'</td>
      <td>'.$sfactura.'</td>
      <td>'.convertir_precio($importe).'€</td>
      <td>'.$iva.'</td>
      <td><button idcompra='.$idcompra.' class="btn_editar_linea btn text-primary p-0 px-3"><i class="far fa-eye"></i> View</button></td>
      <td><button idcompra="'.$idcompra.'"class="btn_eliminar_linea btn text-danger p-0 px-3" title="Eliminar"><i class="fas fa-trash-alt"></i> Delete</button></td></tr>';
    }
    
    if ($output =='') {
      $output = '<div class="alert alert-warning" role="alert">No existen compras que cumplan con esta búsqueda</div>';
    }
    
    echo $output;

  }

  $mysqli->close();
  exit;