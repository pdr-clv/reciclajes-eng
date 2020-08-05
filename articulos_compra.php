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
  
  if ($_GET['idcompra']){
    $idCompra = $_GET['idcompra'];
    $deducible = $_GET['deducible'];
  } else {
    $sql = "SELECT idcompra,deducible FROM compras GROUP BY idcompra DESC LIMIT 1";
    $resultado = $mysqli->query($sql);
    $row = $resultado->fetch_assoc();
    $idCompra = $row['idcompra'];
    $deducible = $row['deducible'];
  }
  ?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <title>Introducir Compra</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0, maximum-scale=1.5, minimum-scale=1.0">
    
    <link rel="stylesheet" href="estilos/bootstrap.min.css">
    <link rel="stylesheet" href="estilos/estilos.css">
    <link rel="stylesheet" href="estilos/jquery-ui.min.css">
    <link rel="stylesheet" href="estilos/fontawesome/css/all.min.css">
  </head>
<!-- comprobacion de seguridad que usuario ha accedido mediante login -->
  <body>
    <div class="jumbotron jumbotron-fluid border border-white p-1" id="jumbotron-compra">
        <h2 class="text-center">WITH TAX</h2>
    </div>
    <div class="contenedor">
      <div class="row">
        <div class="col-lg-10 col-sm-10 px-0"> <!-- se pone padding cero a izquierda y derecha, porque coge por defecto padding, y no deba alinear bien después la tabla con los articulos de la venta -->
          <form class= "w-100 shadow-lg form-horizontal">
          <div class="row">
            <div class="col-md-6">
<!-- se pone display none a todo el form-group de idcompra y el idProveedor, no es información util para el usuario, sólo para filtrar resultados -->
              <div class="form-group row p-1 d-none">
                <label for ="txtIdCompra" class="col-sm-3 text-sm-right p-sm-1 font-weight-bolder" >Purchase Id</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" id="txtIdCompra" name="txtIdCompra" readonly value="<?php echo $idCompra?>">
                  <input type="text" class="form-control" id="txtDeducible" name="txtDeducible" readonly value="<?php echo $deducible?>">
                  <input type="text" class="form-control" id="txtIdProveedor" readonly>
                </div>
              </div>
              <div class="form-group row p-1">
                <label for ="txtNumCompra" class="col-sm-4 text-sm-right p-sm-1 font-weight-bolder">Purchase Num</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="txtNumCompra" name="txtNumCompra" readonly>
                </div>
              </div>
              <div class="form-group row p-1">
                <label for ="txtFecha" class="col-sm-4 text-sm-right p-sm-1 font-weight-bolder">Date</label>
                <div class="col-sm-8">
                  <input type="date" class="form-control" id="txtFecha" name="txtFecha" readonly>
                </div>
              </div>
              <div class="form-group row p-1">
                <label for ="txtProveedor" class="col-sm-3 text-sm-right p-sm-1 font-weight-bolder">Supplier</label>
                <div class="col-sm-9">
                   <textarea name="txtProveedor" class ="txt_area_cliente w-100" id="txtProveedor" rows="3" readonly></textarea> 
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="row px-3">
                <label for ="txtSFactura" class="col-sm-4 text-sm-right p-sm-1 font-weight-bolder">Invoice</label>
                <input type="text" class="form-control w-100" id="txtSFactura" name="txtSFactura" readonly>
              </div>
              <div class="row px-3">
                <label for="notas" class="w-100 pr-0 text-center col-sm-2 col-form-label font-weight-bolder">Notes</label>
                <textarea name="txt_area_notas" class ="txt_area_cliente w-100" id="txt_area_notas" rows="4" placeholder="Type note or comment" readonly></textarea>
              </div>
            </div>
            <div class="bloque-importe col-md-3 d-flex flex-column justify-content-end text-right px-4">
              <p class="my-0">Total:</p>
              <span class="border border-success" id="spn-importe"></span>
              <p class="my-0 text-right">TAX: <span id="spn-iva"></span> </p>
              <span class="border border-success" id="spn-suma-iva"></span>
              <span class="span-pasivo"></span>
              <p class="my-0 text-right">Total + TAX: </p>
              <span class="border border-success lead font-weight-bold mb-3" id="spn-importe-total"></span>
            </div>
          </div>
          </form>
        </div>
      <div class="col-lg-2 col-sm-2">
        <button type="button" class="btn btn-warning w-100 mb-2" name="btn_edita_compra" id="btn_edita_compra" title="Modifica datos de la compra"><i class="far fa-edit"></i> Edit</button>
        <button class="btn btn-secondary w-100 mb-2" title="Volver a Compras" id="btn_volver"><i class="fas fa-undo-alt"></i> Back</button>
        <button type="button" class="btn btn-primary w-100 mb-2" name="addArticulo" id="addArticulo" data-toggle="modal" data-target="#nuevoArticulo"><i class="far fa-plus-square"></i> Add articles</button>
        <button type="button" class="btn btn-danger w-100 mb-2" id="btnEliminarCompra"><i class="fas fa-trash-alt"></i> Delete Purchase</button>
        <a href="#?idventa=<?php echo $idCompra;?>" class="btn btn-outline-primary w-100"> <i class="fas fa-print"></i> Print</a>
      </div> 
    </div>
    <div class="row table-responsible mt-2">
        <table class="table tabla-articulos table-striped table-bordered text-center">
        <thead id="table_header_compra">
          <tr>
<!-- no hace falta mostrar el campo IdCompra ni IdLinCompra, se llamará después como row['id'] o el atributo id de editar y eliminar para eliminar, modificar, etc. -->
          <!--  <th>IdCompra</th> -->
            <th>Article</th>
            <th>Description</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Amount</th>
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
              <input type="text" class="form-control" id="txtIdArticulo" placeholder="Type Article">
              <input type="text" class="form-control" id="txtIdLinea">
            </div>
              <label for="txtArticulo">Article</label>
            <div class="form-group form-row mx-1">
              <input type="text" class="form-control col-3" id="txtArticulo" placeholder="IdArticle">
              <input type="text" class="form-control col-9" id="txtDescripcion" value="Article Description" readonly tabindex="-1">
              <small id="errorArticulo" class="text-danger font-weight-bold"></small>
            </div>
            <div class="form-row mx-1">
              <div class="form-group col-4 text-center">
                <label for="txtCantidad">Quantity</label>
                <input type="text" class="form-control text-right" id="txtCantidad" value="0">
                <small id="errorCantidad" class="text-danger font-weight-bold"></small>
              </div>
              <div class="form-group col-4 text-center">
                <label class="text-center" for="txtPrecio">Price</label>
                <input type="text" class="form-control text-right" id="txtPrecio" value="0">
                <small id="errorPrecio" class="text-danger font-weight-bold"></small>
              </div>
              <div class="form-group col-4 text-center">
                <label class="text-center" for="txtImporteLin">Amount</label>
                <input type="text" class="form-control text-right" id="txtImporteLin" readonly>
              </div>
            </div>
            
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="btnGuardar"><i class="far fa-save"></i> Save</button>
          <button type="button" class="btn btn-warning" id="btnEditar"><i class="fas fa-edit"></i> Edit</button>
        </div>
      </div>
    </div>
  </div>
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
  <div class="modal fade" id="modal_edita_compra">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold" id="titelModalNC"><i class="fas fa-cart-arrow-down text-primary"></i> Edita Purchase</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="row">
            <div class="col text-center">
<!-- se le da la clase spn_switch a los span que rodean al switch como si fuera un label del checkbox -->
              <span id="spn_compras" class="spn_switch spn_switch_select">Invoice doc No TAX</span>
              <label class="switch align-middle my-1">
              <input type="checkbox" id="chk_deduc">
              <span class="slider round"></span>
              </label>
              <span id="spn_deducible"class="spn_switch">Invoice doc with TAX</span>
            </div>
          </div>
          <div class="row">
            <div class="col form-group row">
              <label for="txteditFecha" class="text-center col-12 col-form-label">Date</label>
              <div class="col-sm-12">
                <input type="date" class="form-control text-right" id="txteditFecha" placeholder="Type Date">
              </div>
            </div>
            <div class="col form-group row">
              <label for="txtEditNumCompra" class="text-center col-12 col-form-label">Purchase Number</label>
              <div class="col-sm-12">
                <div class="input-group">
                  <input type="number" class="form-control text-right" id="txtEditNumCompra" placeholder="Type Num Purchase">
                  <div class="input-group-append">
                    <span class="input-group-text" id="txtIdEditYear"></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
<!-- se añade este div con el span que mostrará el error de no haber introducido fecha correcta -->
          <div class="form-group row ml-5">
            <span id="errorfecha" class="error text-danger small font-weight-bold"></span>
          </div>
          <div class="form-group row">
            <label for="txtEditProveedor" class="pr-0 text-center col-sm-2 col-form-label">Supplier</label>
            <div class="col-sm-10">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="txtIdEditProveedor">00</span>
                </div>
                <input type="text" class="form-control" id="txtEditProveedor" placeholder="Type Supplier">
              </div>
            </div>
          </div>
<!-- se añade este div con el span que mostrará el error de no haber introducido cliente -->
          <div class="form-group row ml-5">
            <span id="errorProveedor" class="error text-danger small font-weight-bold"></span>
          </div>
          <div class="row">
            <div class="form-group col row">
              <label for="txtEditIva" class="pr-0 text-center col-sm-2 col-form-label">Tax</label>
              <select class="form-control col-sm-10" id="txtEditIva" required>
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
              <label for="txtEditSFactura" class="pr-0 text-center col-sm-4 col-form-label">Supplier Num Invoice</label>
              <div class="input-group col-sm-8">
                <input type="text" class="form-control" id="txtEditSFactura" placeholder="Num Invoice">
              </div>
          <div class="form-group row ml-5">
            <span id="errorSFactura" class="error text-danger small font-weight-bold"></span>
          </div>
            </div>
          </div>
          <div class="form-group row">
            <label for="txtEditNotas" class="pr-0 text-center col-sm-2 col-form-label">Notes</label>
            <div class="col-sm-10">
              <div class="input-group">
                <textarea class ="w-100" id="txtEditNotas" rows="3" placeholder="Type here any note or comment"></textarea>
              </div>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" id="btnCerrarEditar">Cancel</button>
        <button type="button" class="btn btn-primary" id="btnAceptarEditar">Accept</button>
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
  <script src="js/librerias/clases.js"></script>
  <script src="js/librerias/funciones_jquery.js"></script>
<!-- archivo con funciones java script propios -->
  <script src="./js/funciones_articulos_compra.js"></script>
</html>