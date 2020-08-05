<!DOCTYPE html>
<html lang="es">
  <head>
    <title>Ventas</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0, maximum-scale=1.5, minimum-scale=1.0">
    <link rel="stylesheet" href="estilos/bootstrap.min.css">
    
    <link rel="stylesheet" href="estilos/estilos.css">
    <link rel="stylesheet" href="estilos/jquery-ui.min.css">
    <link rel="stylesheet" href="estilos/fontawesome/css/all.min.css">
  </head>
<!-- comprobacion de seguridad que usuario ha accedido mediante login -->
  <?php
    session_start();
    $varsesion = $_SESSION['usuario'];
      /* se pone error cero, para que si se entra sin hacer login, no aparezca el error de php de que no existe variable usuario */
    error_reporting(0);
    if ($varsesion==null || empty($varsesion)) {
      echo '<div class="alert alert-danger"><strong>*</strong> Hay que acceder a esta página a traves del formulario Login</div>';
      die();
    }
    require ("php/conectar.php");
  
  //where recoge la cadena de texto que se buscará en el botón buscar
    $where ="";
  //esta pagina recibirá datos refrescados por post (si se pulsa el boton buscar del filtro) y por get si se hace clic en los botones de paginacion, para ambos metodos, si se detectan que existen, se hace un llenado de número de página y se le pasa el valor del campo a filtrar en la busqueda. Si el botón buscar no ha sido pulsado (no existe post, el where no se rellena, y no se filtra nada.)

// primer if, si se accede por primera vez, y no tiene post ni get seleccionados, se reenvia por detecto a la página 1
    if (empty($_POST) && empty($_GET)) {
      header('Location:ventas.php?pagina=1');
// segundo if, si se le envia un valor a filtrar en post, se guarda en la variable valor_filtro, y se le selecciona que página es 1.
    }else if (!empty($_POST)) {
      $valor_filtro = $_POST['busqueda'];
      $pagina = 1;
// cuando existe post, también se mira si está rellenado o no el campo de filtrar.
      if(!empty($valor_filtro)) {
        $where = "WHERE razon_social LIKE '%$valor_filtro%'";
      }
// tercer if, cuando se hace clic en un botón de paginación, se detecta tambien si tiene valor en el campo de busqueda y se reciben estos dos valores.
    }else if ($_GET) {
      $pagina = $_GET['pagina'];
      $valor_filtro = $_GET['busqueda'];
// cuando existe get, también se mira si está rellenado o no el campo de filtrar.
      if(!empty($valor_filtro)) {
        $where = "WHERE razon_social LIKE '%$valor_filtro%'";  
      }
    }
  //consulta sql filtrada con where, que detecta solo cuantos datos hay en la consulta, es necesario contar registros para hacer la paginacion.
// SELECT idventa AS v, numventa,(SELECT SUM(cantidad) FROM linventas WHERE idventa=v) FROM ventas LIMIT 10
    $sql = "SELECT ventas.idcliente as idcliente, clientes.razon_social as razon_social FROM ventas INNER JOIN clientes ON ventas.idcliente = clientes.idcliente $where";
  
    $resultado = $mysqli -> query($sql);
  
// ahora vamos a ver cuantas filas existen, cuantos resultados por página queremos poner, y calculamos cuantas páginas son necesarias para mostrar todos los resultados . la sentencia num_rows cuenta cuantas filas hay en resultado obtenido de ejecutar sql.
  
    $num_filas = $resultado->num_rows;
// decido mostrar 20 registros por pagina, se puede variar.
    $resultados_x_pag = 20;
    
// ceil reondea al número entero superior. 
    $num_paginas = ceil($num_filas/$resultados_x_pag);
// se calculan los valores top y min de la paginación, para mostrar páginas sólo de 5 en 5. Ademas se calcula si es el último ciclo de paginas o el primero, para mostrar los botones de ir a ultima página o ir a primera página.
    $num_toppag = 5 * ceil($pagina/5);
    if ($num_toppag > $num_paginas) {
      $num_toppag = $num_paginas;
      $ultima_pagina='SI';
    } else {
      $ultima_pagina='NO';
    }
    $num_minpag = $num_toppag - 5;
    if ($num_minpag <= 0) {
      $num_minpag = 0;
      $primera_pagina = 'SI';
    } else {
      $primera_pagina = 'NO';
    }

// detectamos si existe get de número de página, si no existe, forzamos a que sea página 1, y después, programamos los límites de la sentencia SQL, para que se filtren los resultados por página dentro de los valores $resultados x pagina, número de filas, etc. 
    
    
// se define iniciar, que es el valor limit que se pasa para que empiece el limit, y el valor de registros a mostrar es resultados por pagina.
    $iniciar = ($pagina-1)*$resultados_x_pag;

// este sql es para poner los limites de registros a mostrar por pagina.
    $sql_filtrado = "SELECT ventas.idventa as id, ventas.idcliente as idcliente, ventas.numventa as numventa, ventas.fecha as fecha, clientes.razon_social as razon_social, (SELECT SUM(pvplin) FROM linventas WHERE idventa=id) AS importe, ventas.iva as iva FROM ventas INNER JOIN clientes ON ventas.idcliente = clientes.idcliente $where ORDER BY id DESC LIMIT $iniciar,$resultados_x_pag";
  
    $resultado_filtrado = $mysqli -> query($sql_filtrado);
    
    
  ?>
<!-- para que el footer esté siempre pegado abajo, si el alto de la página no es 100%, utilizamos flexblox en el body, y añadimos un poco de estilos css, a la clase flex-fill -->
  <body class="d-flex flex-column">
    <!-- inicio barra de navegacion panel de ventas -->
    
      <div class="fondonav shadow-lg fixed-top" id="fondonav">
        <nav class="navbar navbar-expand-lg navbar-dark container fondonav">
    <a class="navbar-brand" href="#">
      <img src="img/ventas_icono.png" width="30" height="30" class="d-inline-block align-top" alt="">
    Panel de Ventas
    </a>
    <a href="principal.php" class="btn btn-info mr-lg-2 my-1 my-lg-0 px-3" title="Volver a principal"><i class="fas fa-home"></i></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
    <div class="navbar-nav ml-auto">
      <button class="btn btn-warning text-dark mr-lg-2 my-1 my-lg-0" type="button" name="agregar" id="agregar" data-toggle="modal" data-target="#nuevaVenta"><i class="far fa-plus-square"></i> Nueva Venta</button>
    </div>
    <form class="form-inline" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
    <input id="busqueda" name="busqueda" class="form-control mr-lg-2" type="search" placeholder="Cliente" aria-label="Search" value="<?php echo $valor_filtro; ?>">
    <button class="btn btn-info my-2 my-lg-0 mr-lg-2" type="submit"><i class="fas fa-search"></i> Buscar</button>
    <a href="javascript:history.back(-1);" class="btn btn-secondary mr-lg-2 my-1 my-lg-0" title="Volver"><i class="fas fa-undo-alt"></i></a>
  </form>
  </div>
</nav>
      </div>
    
    <!-- fin de barra de navegacion panel de ventas -->
    <div class="container flex-fill">
<!-- se le da estilos de bootstrap table-responsible a la tabla, para que se comporte responsible. -->
      <div class="row table-responsible mt-5">
        <table class="table table-striped text-center">
          <thead>
            <tr>
<!-- no hace falta mostrar el campo IdVenta, se llamará después como row['id'] para eliminar, modificar, etc. -->
            <!--  <th>IdVenta</th> -->
              <th>Numero</th>
              <th>Fecha</th>
              <th>Cliente</th>
              <th>Importe</th>
              <th>Iva</th>
              <th></th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php 
// vemos si existen registros filtrados del where, y si sale que no existen registros, no se ejecutará el while de llenado de la tabla, y el else es imprimir "no se han encontrados registros seleccionados "
            if ($registros_filtrados = $resultado_filtrado->num_rows >0){
            while ($row = $resultado_filtrado->fetch_assoc()) { ?>
            <tr>
<!-- no hace falta mostrar el campo IdVenta, se llamará después como row['id'] para eliminar, modificar, etc. -->
            <!--  <td class="visible"><?php //echo $row['id'];?></td> -->
              <td><?php echo $row['numventa']. "/". date("y",strtotime($row['fecha']));?></td>
<!-- para el campo fecha se pasa el valor a date con date_create, y después se ejecuta la funcion de php date_format -->  
              <td><?php $date = date_create($row['fecha']);echo date_format($date, 'd/m/y');?></td>
              <td><?php echo $row['razon_social'];?></td>
<!-- la columna importe es especial, se obtiene el valor, si es nulo, es porque no tiene lineas, y se fuerza con el if a que sea 0,00. Además también hay en el echo las funciones number_forta y round para darle formato español a los números que se muestren -->              
              <td><?php 
                  if ($row['importe']){
                  echo number_format(round($row['importe'], 2),2,',','.'). " €";
                  } else {
                  echo "0,00 €";
                  }
                ?></td>
              <td><?php echo $row['iva'];?></td>
              <td><a href="articulos_venta.php?idventa=<?php echo $row['id'];?>"><i class="far fa-eye"></i> Ver</a></td>
              <td><a id="<?php echo $row['id'];?>" class="deleteData text-danger" href="#"  title="Eliminar"><i class="fas fa-trash-alt"></i></a></td>
            </tr>
            <?php }
            }
            else{ echo '<div class="alert alert-danger"><strong>*</strong> No se han encontrado registros</div>';} 
            $mysqli->close();?>
          </tbody>  
        </table>
      </div>
<!-- de bootstrap, copiamos la estructura del nav que tiene la paginación, y después programamos con los datos obtenidos de php la paginación dinámica de los datos de la base de datos -->
      <nav aria-label="Page navigation example">
        <ul class="pagination">
<!-- si estamos en página 1, se bloquea el boton anterior, recordatorio que $pagina es el GET ['pagina'] -->
          <li class="page-item <?php if ($primera_pagina=='SI') {echo 'd-none';} else {echo '';} ?> mr-2">
            <a class="page-link" href="ventas.php?pagina=<?php echo 1 ; ?>&busqueda=<?php echo $valor_filtro; ?>">
              <span><b>&lt;&lt;</b></span>
            </a>
          </li>
          <li class="page-item <?php if ($pagina==1) {echo 'disabled';} else {echo '';} ?> mr-2">
            <a class="page-link" href="ventas.php?pagina=<?php echo $pagina-1 ; ?>&busqueda=<?php echo $valor_filtro; ?>">
              <span><b>&lt;</b></span>
            </a>
          </li>
          <?php for( $i = $num_minpag ; $i < $num_toppag; $i++){ ?>
<!-- pintamos la página activa haciendo un condicional php if, si la página es igual a la página activa, se pinta -->
          <li class="page-item <?php if ($i==$pagina-1) {echo 'active';} else {echo '';} ?>">
<!-- es importante originar un get en php que te diga que numero de página está cargada, después programamos que los botonees Anterior y posterior resten o sumen uno al get obtenido de la página cargada el campo busqueda se puede pasar al get aunque esté vacio, ya que tiene un condicional que comprueba si existe datos o no, y si está vacio no lo filtra, y si está con datos, lo filtra-->
            <a class="page-link" href="ventas.php?pagina=<?php echo $i+1; ?>&busqueda=<?php echo $valor_filtro; ?>">
              <?php echo $i+1; ?>
            </a>
          </li>
          <?php } ?>
<!-- si estamos en la última página, se bloquea el boton siguiente -->
          <li class="page-item <?php if ($pagina==$num_paginas || $registros_filtrados==0) {echo 'disabled';} else {echo '';} ?> ml-2">
            <a class="page-link" href="ventas.php?pagina=<?php echo $pagina+1 ; ?>&busqueda=<?php echo $valor_filtro; ?>">
              <span><b>&gt;</b></span>
            </a>
          </li>
          <li class="page-item <?php if ($ultima_pagina=='SI') {echo 'd-none';} else {echo '';} ?> ml-2">
            <a class="page-link" href="ventas.php?pagina=<?php echo $num_paginas ; ?>&busqueda=<?php echo $valor_filtro; ?>">
              <span><b>&gt;&gt;</b></span>
            </a>
          </li>
<!-- se mete este alert para que se vea cual es el número total de páginas que se pueden mostrar -->
          <div class="alert alert-success <?php if ($ultima_pagina=='SI') {echo 'd-none';} else {echo '';} ?>" role="alert">Total núm. páginas <?php echo $num_paginas ?>
          </div>
        </ul>
      </nav>
    </div>
    <footer class="page-footer pt-4">

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
  <!-- librerias java script -->
  <script src="js/librerias/jquery.js"></script>
  <script src="js/librerias/popper.min.js"></script>
  <script src="js/librerias/bootstrap.min.js"></script>
<!-- scripts propios creados para dar funcionalidad a los elementos de la aplicación -->
    <script src="js/funciones_venta.js"></script>
    <script src="js/librerias/jquery-ui.min.js"></script>
    <script src="js/librerias/eModal.min.js"></script>
  </body>
    <div class="modal fade" id="nuevaVenta">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">Nueva Venta</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>

          <!-- Modal body -->
          <div class="modal-body">
            <form class= "w-100 shadow-lg form-horizontal" id="insert_form">
            <div class="form-group row">
<!-- con pr-0 quitamos el pading a la derecha, y ponemos text-center, para que el label fecha salga separado de la izquierda, y no se quede pegado -->
              <label for="fecha" class="pr-0 text-center col-sm-2 col-form-label">Fecha</label>
              <div class="col-sm-10">
                <div class="input-group">
                  <input type="date" class="form-control" id="fecha" placeholder="Escribe fecha">
                  <div class="input-group-append">
                    <span class="input-group-text" id="numVenta">Num.</span>
                  </div>
                </div>
              </div>
            </div>
<!-- se añade este div con el span que mostrará el error de no haber introducido fecha correcta -->
            <div class="form-group row ml-5">
            <span id="errorfecha" class="text-danger small font-weight-bold"></span>
            </div>
            <div class="form-group row">
              <label for="cliente" class="pr-0 text-center col-sm-2 col-form-label">Cliente</label>
              <div class="col-sm-10">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="idcliente">00</span>
                  </div>
                  <input type="text" class="form-control" id="cliente" placeholder="Escribe cliente">
                </div>
              </div>
            </div>
<!-- se añade este div con el span que mostrará el error de no haber introducido cliente -->
            <div class="form-group row ml-5">
              <span id="errorcliente" class="text-danger small font-weight-bold"></span>
            </div>
            <div class="form-group row">
              <label for="iva" class="pr-0 text-center col-sm-2 col-form-label">Iva</label>
              <div class="col-sm-10">
                <select class="form-control" id="iva" name="iva" required>
                    <option value="23">23</option>
                    <option value="22">22</option>
                    <option value="21" selected>21</option>
                    <option value="20">20</option>
                    <option value="19">19</option>
                    <option value="18">18</option>
                    <option value="17">17</option>
                    <option value="16">16</option>
                    <option value="15">15</option>
                    <option value="14">14</option>
                    <option value="13">13</option>
                    <option value="12">12</option>
                    <option value="11">11</option>
                    <option value="9">9</option>
                    <option value="8">8</option>
<!-- Pendiente de seleccionar mas tipos de iva, para que no sea solo 21, por si algun día cambia el tipo de iva -->
                  </select>
              </div>
            </div>
              <div class="form-group row">
              <label for="notas" class="pr-0 text-center col-sm-2 col-form-label">Notas</label>
              <div class="col-sm-10">
                <div class="input-group">
                <textarea name="notas" class ="w-100" id="notas" rows="3" placeholder="Escribe aquí alguna nota o comentario"></textarea>
                </div>
              </div>
            </div>
              </form>
          <!-- Modal footer -->
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
              <button type="button" class="btn btn-primary" id="btnGuardar">Guardar</button>
              <!--<input type="submit" name="submit" class="btn btn-primary" value="Guardar">-->
            </div>
        </div>
      </div>
    </div>
  </div>
  <!-- modal nuevo de borrado -->
  <div id="deleteModal" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4>Eliminar venta</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body" id=>
        <p id="body-eliminar">
        </p>
<!-- se le pasa el id de venta a borrar en un input oculta, para que se pueda eliminar correctamente la venta con el Id -->
        <input type="text" id="idVenta" class="d-none">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <a class="btn btn-danger btn-xs text-light" id="deleteButton">Eliminar</a>  
        </div>
      </div>
    </div>
  </div>
</html>