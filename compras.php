<?php
  session_start();
  $varsesion = $_SESSION['usuario'];
    /* se pone error cero, para que si se entra sin hacer login, no aparezca el error de php de que no existe variable usuario */
  error_reporting(0);
  if ($varsesion==null || empty($varsesion)) {
    echo 'Hay que acceder a esta página a traves del formulario Login';
    die();
  }
  if ($_GET['deducible']){
    $deducible = $_GET['deducible'];
  }
  
?>


<!DOCTYPE html>
<html lang="es">
  <head>
    <title>Purchases</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0, maximum-scale=1.5, minimum-scale=1.0">
    <link rel="stylesheet" href="estilos/bootstrap.min.css">
    <link rel="stylesheet" href="estilos/jquery-ui.min.css">
    <link rel="stylesheet" href="estilos/fontawesome/css/all.min.css">
    
<!-- aquíe meto la hoja de estilos, para darle unos pocos estilos propios -->
    <link rel="stylesheet" href="estilos/estilos.css">
  </head>
<!-- para que el footer esté siempre pegado abajo, si el alto de la página no es 100%, utilizamos flexblox en el body, y añadimos un poco de estilos css, a la clase flex-fill -->
  <body class="d-flex flex-column">
    <!-- inicio barra de navegación -->
    <section class="fondonav border-bottom border-white fixed-top" id="fondonav">
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
              <a class="nav-link btn btn-sm" href="principal.php">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active btn btn-sm" >Purchases</a>
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
              <a class="nav-link btn btn-sm btn-danger" href="php/cerrarsesion.php" tabindex="-1" aria-disabled="true" title="Cerrar Sesión"><i class="fas fa-sign-out-alt"></i> Close</a>
            </li>
          </ul>
        </div>
      </nav>
    </section>
    <section class="barra_nav">
<!-- si el ancho es menor que md, el display se volverá column, y se centrarán los elementos. -->
    <div class="w-100 p-1 px-3 d-flex flex-column flex-md-row justify-content-between contenedor">
      <div class="text-center">
<!-- se le da la clase spn_switch a los span que rodean al switch como si fuera un label del checkbox -->
        <span id="spn_compras" class="spn_switch spn_switch_select">Tax Free</span>
        <label class="switch align-middle my-1">
        <input type="checkbox" id="chk_deduc" <?php if ($deducible=='1'){echo 'checked';} ?>>
        <span class="slider round"></span>
        </label>
        <span id="spn_deducible"class="spn_switch">With Tax</span>
      </div>
      <div class="text-center">
      <button class="btn btn-warning text-dark mr-lg-2 my-1 my-lg-0" id="btnNuevaCompra"><i class="far fa-plus-square"></i> New Purchase</button>
      <input type="text" class="txt_busqueda p-1 mr-lg-2"
      placeholder="Supplier filter" id="txtSearch">
      </div>
    </div>
    </section>
    <section class="contenedor flex-fill">
      <div class="row">
        <table class="table table-sm-sm table-striped text-center">
          <thead>
            <tr>
<!-- no hace falta mostrar el campo IdVenta, se llamará después como row['id'] para eliminar, modificar, etc. -->
              <th class="d-none">IdCompra</th>
              <th>IdPurchase</th>
              <th>Date</th>
              <th>Supplier</th>
              <th>Invoice Num</th>
              <th>Amount</th>
              <th>Tax %</th>
              <th></th>
              <th></th>
            </tr>
          </thead>
          <tbody id="tbl_body">
          </tbody>
        </table>
      </div>
      <section class="paginacion" id="paginacion">
      </section>
    </section>
    <!-- fin de barra de navegación -->
    <footer class="page-footer font-small blue pt-4">

    <!-- Footer Links -->
    <div class="contenedor text-center">
    <!-- Footer Links -->

    <!-- Copyright -->
    <div class="footer-copyright text-center py-3">© 2018 Copyright: Reciclajes Catalán S.L.</div>
    <div class="footer-copyright text-center py-3">
      <a class="user-nav "href="php/cerrarsesion.php">Close session</a>
    </div>
    <!-- Copyright -->

  </footer>
  </body>
<div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="alertModalLabel"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="contenidoModal">
      </div>
      <div class="modal-footer" id="modal-footer">
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalNuevaCompra" tabindex="-1" role="dialog" aria-labelledby="alertaModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold" id="titelModalNC"><i class="fas fa-cart-arrow-down text-primary"></i> New Purchase</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group row">
<!-- con pr-0 quitamos el pading a la derecha, y ponemos text-center, para que el label fecha salga separado de la izquierda, y no se quede pegado -->
            <label for="fecha" class="pr-0 text-center col-sm-2 col-form-label">Date</label>
            <div class="col-sm-10">
              <div class="input-group">
                <input type="date" class="form-control" id="fecha" placeholder="Type date">
                <div class="input-group-append">
                  <span class="input-group-text" id="numCompra">Num.</span>
                </div>
              </div>
            </div>
          </div>
<!-- se añade este div con el span que mostrará el error de no haber introducido fecha correcta -->
          <div class="form-group row ml-5">
            <span id="errorfecha" class="error text-danger small font-weight-bold"></span>
          </div>
          <div class="form-group row">
            <label for="proveedor" class="pr-0 text-center col-sm-2 col-form-label">Supplier</label>
            <div class="col-sm-10">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="idProveedor">00</span>
                </div>
                <input type="text" class="form-control" id="proveedor" placeholder="Type supplier">
              </div>
            </div>
          </div>
<!-- se añade este div con el span que mostrará el error de no haber introducido cliente -->
          <div class="form-group row ml-5">
            <span id="errorProveedor" class="error text-danger small font-weight-bold"></span>
          </div>
          <div class="row">
            <div class="form-group col row">
              <label for="iva" class="pr-0 text-center col-sm-2 col-form-label">Tax%</label>
              <select class="form-control col-sm-10" id="iva" name="iva" required>
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
            <div class="form_group col row">
              <label for="txtSFactura" class="pr-0 text-center col-sm-4 col-form-label">Invoice Num</label>
              <div class="input-group col-sm-8">
                <input type="text" class="form-control" id="txtSFactura" placeholder="Invoice Num">
              </div>
          <div class="form-group row ml-5">
            <span id="errorSFactura" class="error text-danger small font-weight-bold"></span>
          </div>
            </div>
          </div>
          <div class="form-group row">
            <label for="notas" class="pr-0 text-center col-sm-2 col-form-label">Notes</label>
            <div class="col-sm-10">
              <div class="input-group">
                <textarea name="notas" class ="w-100" id="txtNotas" rows="3" placeholder="Type here notes or comments"></textarea>
              </div>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" id="btnCerrarNC">Cancel</button>
        <button type="button" class="btn btn-primary" id="btnAceptarNC">Accept</button>
      </div>
    </div>
  </div>
</div>

  <!-- Footer -->
  <!-- librerias java script -->
  <script src="js/librerias/jquery.js"></script>
  <script src="js/librerias/popper.min.js"></script>
  <script src="js/librerias/bootstrap.min.js"></script>
  <script src="js/librerias/clases.js"></script>
<!-- scripts propios creados para dar funcionalidad a los elementos de la aplicación -->
  <script src="js/funciones_compras.js"></script>
  <script src="js/librerias/jquery-ui.min.js"></script>
  <script src="js/librerias/eModal.min.js"></script>
    
</html>