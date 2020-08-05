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
  
  if ($_GET['idventa']){
    $ventafiltro = $_GET['idventa'];
  } else {
  $sql = "SELECT idventa FROM ventas GROUP BY idventa DESC LIMIT 1";
  $resultado = $mysqli->query($sql);
  $row = $resultado->fetch_assoc();
  $ventafiltro = $row['idventa'];
  }
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
    <link rel="stylesheet" href="estilos/fontawesome/css/all.min.css">
  </head>
<!-- comprobacion de seguridad que usuario ha accedido mediante login -->
  <body>
    <div class="jumbotron jumbotron-fluid border border-white p-1">
        <h2 class="text-center">Venta</h2>
    </div>
    <div class="contenedor">
      <div class="row">
        <div class="col-lg-10 col-sm-10 px-0"> <!-- se pone padding cero a izquierda y derecha, porque coge por defecto padding, y no deba alinear bien después la tabla con los articulos de la venta -->
          <form class= "w-100 shadow-lg form-horizontal" action="guardar_venta.php" method="post" onsubmit="">
          <div class="row">
            <div class="col-md-5">
<!-- se pone display none a todo el form-group de idventa y el id cliente, no es información util para el usuario, sólo para filtrar resultados -->
              <div class="form-group row p-1 d-none">
                <label for ="txtIdVenta" class="col-sm-3 text-sm-right p-sm-1 font-weight-bolder" >Id Venta</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" id="txtIdVenta" name="txtIdVenta" readonly value="<?php echo $ventafiltro;?>">
                  <input type="text" class="form-control" id="txtIdCliente">
                </div>
              </div>
              <div class="form-group row p-1">
                <label for ="txtNumVenta" class="col-sm-4 text-sm-right p-sm-1 font-weight-bolder">Num. venta</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="txtNumVenta" name="txtNumVenta" readonly>
                </div>
              </div>
              <div class="form-group row p-1">
                <label for ="txtFecha" class="col-sm-4 text-sm-right p-sm-1 font-weight-bolder">Fecha</label>
                <div class="col-sm-8">
                  <input type="date" class="form-control" id="txtFecha" name="txtFecha" readonly>
                </div>
              </div>
              <div class="form-group row p-1">
                <label for ="txtCliente" class="col-lg-4 col-md-3 col-sm-4 text-sm-right p-sm-1 font-weight-bolder">Cliente</label>
                <div class="col-lg-8 col-md-9 col-sm-8">
                   <textarea name="txtCliente" class ="txt_area_cliente w-100" id="txtCliente" rows="3" readonly></textarea> 
                </div>
              </div>
            </div>
            <div class="col-md-3">
<!-- hay que poner todas estas clases, para darle un poco de margen al final, d-flex flex, flex-wrap (para que baje el contenido que no quepa=, y align'content-around para que reparta las columnas en around. también hay que dar un height de 100% (h-100)-->
              <div class="importe d-flex align-content-around flex-wrap h-100">
                <div class="w-100">
                  <p class="p-2 text-right">Total: <span class="font-weight-bold" id="txtImporte"></span></p>
                </div>
                <div class="w-100 d-none d-md-block">
                  <p class="p-2 text-right">Iva: <span class="font-weight-bold" id="txtIva"></span>% Importe: <span class="font-weight-bold" id="spanIva"></span></p>
                </div>
              </div>
            </div>
            <div class="col-md-4">
            <div class="row px-3">
              <label for="notas" class="w-100 pr-0 text-center col-sm-2 col-form-label">Notas</label>
                <textarea name="txt_area_notas" class ="txt_area_cliente w-100" id="txt_area_notas" rows="4" placeholder="Se puede escribir alguna nota o comentario" readonly></textarea>
            </div>
            <div class="w-100 d-none importe total d-md-block">
                  <div class="row">
                    <div class="col text-center border border-success">NOTA: Sujeto pasivo de Iva.</div>
                    <div class="col text-center">Total Factura:<span class="font-weight-bold" id="txtImporteTotal"></span></div>
                  </div>
                </div>
            </div>
          </div>
          </form>
        </div>
      <div class="col-lg-2 col-sm-2">
        <button type="button" class="btn btn-warning w-100 mb-2" name="btn_edita_venta" id="btn_edita_venta" data-toggle="modal" data-target="#modal_edita_venta"><i class="far fa-edit"></i> Editar</button>
        <a href="javascript:history.back(-1);" class="btn btn-secondary w-100 mb-2"><i class="fas fa-undo-alt"></i> Volver a ventas</a>
        <button type="button" class="btn btn-primary w-100 mb-2" name="addArticulo" id="addArticulo" data-toggle="modal" data-target="#nuevoArticulo"><i class="far fa-plus-square"></i> Añadir articulos</button>
        <a href="informes/factura.php?idventa=<?php echo $ventafiltro;?>" class="btn btn-outline-primary w-100"> <i class="fas fa-print"></i> Imprimir</a>
      </div> 
    </div>
    <div class="row table-responsible mt-2">
        <table class="table tabla-articulos table-striped table-bordered text-center">
        <thead>
          <tr>
<!-- no hace falta mostrar el campo IdVenta ni IdLinVenta, se llamará después como row['id'] o el atributo id de editar y eliminar para eliminar, modificar, etc. -->
          <!--  <th>IdVenta</th> -->
            <th>Articulo</th>
            <th>Descripcion</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Importe</th>
            <th></th>
            <th></th>
          </tr>
        </thead>
        <tbody id=tablaBody>
<!-- se rellenará la tabla haciendo un ajax desde la funciones_articulos_ventas al cargar la página -->
        </tbody>
      </table>
    </div>
  </div>
  </body>
  <div class="modal fade" id="nuevoArticulo">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="nuevoArticulolabel"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form class="add-articulo w-100 shadow-lg form-horizontal" action="" id="añadirArticulo">
            <div class="form-group d-none">
              <input type="text" class="form-control" id="txtIdArticulo" placeholder="Escribe Articulo">
              <input type="text" class="form-control" id="txtIdLinea">
            </div>
              <label for="txtArticulo">Articulo</label>
            <div class="form-group form-row mx-1">
              <input type="text" class="form-control col-3" id="txtArticulo" placeholder="IdArticulo">
              <input type="text" class="form-control col-9" id="txtDescripcion" value="Descripcion Articulo" readonly tabindex="-1">
              <small id="errorArticulo" class="text-danger font-weight-bold"></small>
            </div>
            <div class="form-row mx-1">
              <div class="form-group col-4 text-center">
                <label for="txtCantidad">Cantidad</label>
                <input type="text" class="form-control text-right" id="txtCantidad" value="0">
                <small id="errorCantidad" class="text-danger font-weight-bold"></small>
              </div>
              <div class="form-group col-4 text-center">
                <label class="text-center" for="txtPrecio">Precio</label>
                <input type="text" class="form-control text-right" id="txtPrecio" value="0">
                <small id="errorPrecio" class="text-danger font-weight-bold"></small>
              </div>
              <div class="form-group col-4 text-center">
                <label class="text-center" for="txtImporteLin">Importe</label>
                <input type="text" class="form-control text-right" id="txtImporteLin" readonly>
              </div>
            </div>
            
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn" data-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" id="btnGuardar"><i class="far fa-save"></i> Guardar</button>
          <button type="button" class="btn btn-warning" id="btnEditar"><i class="fas fa-edit"></i> Editar</button>
        </div>
      </div>
    </div>
  </div>
  <div id="modal_eliminar_linea" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4>Eliminar Articulo</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body" id=>
        <p> ¿Deseas eliminar este articulo de la venta?
        </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <a class="btn btn-danger btn-xs text-light" id="btn_eliminar_linea"><i class="fas fa-trash-alt"></i>Eliminar</a>  
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modal_edita_venta">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">Edita Venta</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>

          <!-- Modal body -->
          <div class="modal-body">
            <form class= "w-100 shadow-lg form-horizontal" id="edit_form">
            <div class="form-row mx-1">
              <div class="form-group col-6 text-center">
                <label for="txt_edit_fecha">Fecha</label>
                <input type="date" class="form-control text-center" id="txt_edit_fecha">
                <small id="error_edit_fecha" class="text-danger font-weight-bold"></small>
              </div>
              <div class="form-group col-6 text-center">
                <label class="text-center w-100" for="txt_edit_numVenta">Num.Venta</label>
                <input type="number" class="form-control text-right w-50 d-inline" id="txt_edit_numVenta" placeholder="Num.Venta">
<!-- se introduce el input txt_duplicado pero se le da display none, es para registrar si existe duplicado o no de num_venta, cuando se edita la compra -->
                <input type="text" class="d-none" id="txt_duplicado">
                <p class="w-50 d-inline">/19</p>
                <small id="error_edit_Precio" class="text-danger font-weight-bold w-100 d-block"></small>
              </div>
            </div>
<!-- se añade este div con el span que mostrará el error de no haber introducido fecha correcta -->
            <div class="form-group row ml-5">
            <span id="spn_errorfecha" class="text-danger small font-weight-bold"></span>
            </div>
            <div class="form-group row">
              <label for="txt_edit_cliente" class="col-sm-2 col-form-label">Cliente</label>
              <div class="col-sm-10">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="spn_idcliente">00</span>
                  </div>
                  <input type="text" class="form-control" id="txt_edit_cliente" placeholder="Escribe cliente">
                </div>
              </div>
            </div>
<!-- se añade este div con el span que mostrará el error de no haber introducido cliente -->
            <div class="form-group row ml-5">
              <span id="spn_errorcliente" class="text-danger small font-weight-bold"></span>
            </div>
            <div class="form-group row">
              <label for="txt_edit_iva" class="col-sm-2 col-form-label">Iva</label>
              <div class="col-sm-10">
                <select class="form-control" id="txt_edit_iva" name="txt_edit_iva" required>
                    <option value="23">23</option>
                    <option value="22">22</option>
                    <option value="21">21</option>
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
                <textarea name="notas" class ="w-100 " id="txtarea_edit_notas" rows="3" placeholder="Escribe notas o comentarios aquí"></textarea>
                </div>
              </div>
            </div>
              </form>
          <!-- Modal footer -->
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
              <button type="button" class="btn btn-warning" id="btn_edit_venta"><i class="fas fa-edit"></i> Editar</button>
            </div>
        </div>
      </div>
    </div>
  </div>


<!-- archivos libreria externa java script -->
  <script src="js/librerias/jquery.js"></script>
  <script src="js/librerias/popper.min.js"></script>
  <script src="js/librerias/bootstrap.min.js"></script>
  <script src="js/librerias/jquery-ui.min.js"></script>
  <script src="js/librerias/eModal.min.js"></script>
  <script src="js/librerias/funciones_jquery.js"></script>
<!-- archivo con funciones java script propios -->
  <script src="js/funciones_articulos_venta.js"></script>
</html>