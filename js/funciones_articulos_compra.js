$(document).ready(function(){
  const linCompra = new LinCompras();
  const compra = new Compras();
  linCompra.idcompra = $('#txtIdCompra').val();
  let deducible = $('#txtDeducible').val();
  const proveedor = new Proveedores();
  
  mostrarEncabezado();
  cargarLineas();
  
//vamos a listar todos los eventos que se ejecutarán cuando se haga clic o se modifique cualquier elemento del DOM.
  
//cuando se selecciona el botón de btn_edita_compra aparece el formulario de editar venta, similar al de añadir compra. Y se cargar todos los valores que tiene la venta, para que se puedan eliminar  
  $('#btn_edita_compra').on('click',cargaEditCompra);
  
// cuando se apreta el botón de Aceptar editar venta, se llama al evento de la clase editar venta.
  $('#btnAceptarEditar').on('click',editCompra);
  
// en el formulario de edita venta, si se modifica el checkbox de tipo de venta, cambiará ciertos elementos del DOM  
  $('#chk_deduc').on('change',modifCheck);
  
  $('#btnEliminarCompra').on('click',deleteCompra);
  
  $('#btn_volver').on('click',()=>window.location.href='compras.php?deducible='+deducible)
// este evento de txteditFecha tiene un poco de mala leche, es por si se cambia la fecha, y el usuario no se da cuenta que hay que cambiar el campo idcompra, para que no genere duplicados.   
  $('#txteditFecha').on('change',getNuevaCompra);
  
  $('#txtEditProveedor').autocomplete({
    source:(request,response)=>{
      proveedor.listarAutocomplete(request.term)
      .then(data => response(JSON.parse(data)));
    },
    minLength:1,
    select:(event,ui) => {
      let idProv=(ui.item.value).split(" ",1).toString();
      proveedor.selectAutoComplete(idProv)
      .then(data => {
        let proveedor = JSON.parse(data);
        $('#txtIdEditProveedor').text(proveedor.idproveedor);
        $('#txtEditProveedor').val(proveedor.razon_social);
      });
    },
  // hay que poner appendTo para que el desplegable no se quede debajo del modal y no se vea
    appendTo: $('#modal_edita_compra'),
    autoFocus: true
  });
  
function getNuevaCompra(){
  if ($('#txteditFecha').val().split('-',1).toString() != $('#txtFecha').val().split('-',1).toString()) {
    compra.fecha = $('#txteditFecha').val();
    if ($('#chk_deduc').prop('checked')){
      compra.deducible='1'
      compra.cargarNumCompra()
          .then(data => {
          $('#txtEditNumCompra').val(parseInt(data.split('/',1)));
          $('#txtIdEditYear').text('');
        });
    } else {
      compra.deducible='0'
      compra.cargarNumCompra()
          .then(data => {
          let idYear = data.split('/',2)[1].toString();
          $('#txtEditNumCompra').val(parseInt(data.split('/',1)));
          $('#txtIdEditYear').text('/'+idYear);
        });
    }
  }
}  
  
function editCompra(){
  compra.idcompra = linCompra.idcompra;
  compra.numcompra = $('#txtEditNumCompra').val();
  compra.fecha = $('#txteditFecha').val();
  compra.idproveedor = $('#txtIdEditProveedor').text();
  compra.iva = $('#txtEditIva').val();
  compra.sfactura = $('#txtEditSFactura').val();
  compra.notas = $('#txtEditNotas').val();
  if ($('#chk_deduc').prop('checked')) {
    compra.deducible = '1';
  } else {
    compra.deducible = '0';
  }
  
  alerta('¿Quieres editar la compra con los datos que has modificado?','Editar Compra','atencion',compra.idcompra)
  .then(data=> evaluarEdicion(data.SiNo))
  .catch(err => console.error(err));
  
  function evaluarEdicion(SiNo){
    if (SiNo) {
      compra.comprobarIdCompra().then(data=>console.log(data));
      compra.editarCompra()
      .then(()=>$('#modal_edita_compra').modal('hide'))
      .catch(err => console.error(err));
      alerta('Compra modificada correctamente','Editar compra','exito')
      .then(()=>window.location.href='articulos_compra.php?idcompra='+compra.idcompra+'&deducible='+compra.deducible);
    } else {
      $('#modal_edita_compra').modal('hide');
      alerta('La compra no se ha modificado','Editar compra','critico');
    }
  }
}

function modifCheck(){
  if ($('#txteditFecha').val().split('-',1).toString() != $('#txtFecha').val().split('-',1).toString()) {
    $('#txteditFecha').val($('#txtFecha').val());
    alerta('Se va a resetear el campo fecha a su valor inicial previo a su modificación.','Reset fecha','aviso');
  }
  compra.fecha = $('#txteditFecha').val();
  if ($('#chk_deduc').prop('checked')) {
    compra.deducible = '1';
    if (compra.deducible == $('#txtDeducible').val()){
      $('#txtEditNumCompra').val($('#txtNumCompra').val().split('/',1).toString());
      $('#txtIdEditYear').text('');
    } else {
      compra.cargarNumCompra()
        .then(data => {
        $('#txtEditNumCompra').val(parseInt(data.split('/',1)));
        $('#txtIdEditYear').text('');
      });
    }
    //$('#txtEditNumCompra').val(data.split('/',1).toString())
  } else {
    compra.deducible = '0';
    if (compra.deducible == $('#txtDeducible').val()){
      $('#txtEditNumCompra').val($('#txtNumCompra').val().split('/',1).toString());
      let idYear = $('#txtNumCompra').val().split('/',2)[1].toString();
      $('#txtIdEditYear').text('/'+idYear);
    } else {
      compra.cargarNumCompra()
        .then(data => {
        let idYear = data.split('/',2)[1].toString();
        $('#txtEditNumCompra').val(parseInt(data.split('/',1)));
        $('#txtIdEditYear').text('/'+idYear);
      });
    }
  }
  
  $('#spn_compras').toggleClass("spn_switch_select");
  $('#spn_deducible').toggleClass("spn_switch_select");
}
  
function cargaEditCompra(){
  if(deducible=='1'){
    document.getElementById('chk_deduc').checked = true;
    $('#spn_compras').removeClass("spn_switch_select");
    $('#spn_deducible').addClass("spn_switch_select");
  }else {
    document.getElementById('chk_deduc').checked = false;
    $('#spn_compras').addClass("spn_switch_select");
    $('#spn_deducible').removeClass("spn_switch_select");
    let idYear = $('#txtNumCompra').val().split('/',2)[1].toString()
    $('#txtIdEditYear').text('/'+idYear);
  }
  $('#modal_edita_compra').modal('show');
  $('#txteditFecha').val($('#txtFecha').val());
  $('#txtEditNumCompra').val($('#txtNumCompra').val().split('/',1).toString());
// se hace un split '\n' y se coge el primer string, y se hace un toString.
  $('#txtEditProveedor').val($('#txtProveedor').val().split('\n',1).toString());
  $('#txtEditNotas').val($('#txt_area_notas').val());
  $('#txtEditSFactura').val($('#txtSFactura').val());
  $('#txtIdEditProveedor').text($('#txtIdProveedor').val());
// el span-iva tiene el % asginado, se hace un split de espacio " ", se pone 1, y se le hace un toString, porque devuelve un array.
  $('#txtEditIva').val(($('#spn-iva').text()).split(" ",1).toString());
}
    
function deleteCompra(){
  alerta('¿Quieres eliminar esta compra?','Eliminar Compra','critico',linCompra.idcompra)
  .then(devol => eliminarCompra(devol));
  
  function eliminarCompra(input){
    if (input.SiNo){
      compra.idcompra = input.id;
      compra.eliminarCompra()
      .then(()=>history.back(-1))
      .catch(err=>console.error(err));
    }
  }
}
  
function cargarLineas(){
  linCompra.listarLineas()
  .then(data => {
    $('#tablaBody').html(data);
    botonesDelEdit();
  })
  .catch(err => console.error(err));
}

function botonesDelEdit(){
  $('.btn_editar_linea').on('click',function(){alert('Editar linea');});
  $('.btn_eliminar_linea').on('click',function(){
    let idlinea=$(this).attr('id');
// fila_eliminar seleccionará el row con el idcompra, y se pondrá en rojo para ayudar al usuario a detectar que compra va a eliminar. se le añade la clase text-danger. 
    let fila_eliminar = "tr[id=" + idlinea + "]";
    $(fila_eliminar).addClass("text-danger");
//eliminar venta. A la alerta personalizada, le añado una promesa, que una vez se resuelva al seleccionar el botón Aceptar o cancelar, procederá a eliiminar la venta, o a no eliminarla. Se le pasa el idcompra como parámetro.
    alerta('¿Deseas eliminar este articulo de la compra?','Eliminar articulo','critico',idlinea)
      .then(devol => eliminarArticulo(devol));
    
  });
}  

function eliminarArticulo(request){
  let fila_eliminar = "tr[id=" + request.id + "]";
  if (request.SiNo){
    linCompra.idlin=request.id;
    linCompra.eliminarLinea()
    .then(()=>mostrarImporte())
    .catch(err => console.error(err));
    $(fila_eliminar).hide(1500);
  }else {
    
    $(fila_eliminar).removeClass("text-danger");
  }
}
  
function mostrarImporte(){
  
  linCompra.displayImporte()
  .then(data =>actualizaImporte(data))
  .catch(err => console.error(err));
  
  function actualizaImporte(importeCompra){
    let deduc = deducible;
    let importe=parseFloat(importeCompra);
    let iva = parseInt($('#spn-iva').text());
    let ivaImporte;
    let total;
    if (deduc=='1'){
      ivaImporte = importe*(iva/100);
      total = importe + ivaImporte;
      ivaImporte=formatNumber.new(ivaImporte.toFixed(2)) + ' €';
      total=formatNumber.new(total.toFixed(2)) + ' €';
    } else if (deduc =='0') {
      ivaImporte = 0;
      total = importe + ivaImporte;
      const notaIva="NOTE: THIS SUPPLIER DOESN'T NEED TO DECLARE TAX";
      $('#spn-nota-iva').text(notaIva);
      ivaImporte=formatNumber.new(ivaImporte.toFixed(2)) + ' €';
      total=formatNumber.new(total.toFixed(2)) + ' €';
    }
// con la función formatNumber se redonde a dos decimales y se le añade el simbolo de euro.
    importe=formatNumber.new(importe.toFixed(2)) + ' €';
    /*ivaImporte=formatNumber.new(ivaImporte.toFixed(2)) + ' €';
    total=formatNumber.new(total.toFixed(2)) + ' €';*/
    iva = iva +' %';
    $('#spn-importe').text(importe);
    $('#spn-suma-iva').text(ivaImporte);
    $('#spn-importe-total').text(total);
    $('#spn-iva').text(iva);
  }
}
  
function mostrarEncabezado(){  
  linCompra.cargarEncabezado()
  .then(data => {
    // se transforma a Json la respuesta de la funcion cargarEncabezado.
    let datos = JSON.parse(data);
    getEncabezado(datos);
    mostrarImporte();
  })
  .catch(err => console.error(err));
  
  function getEncabezado(datos){
    let datosCompra = datos;
    let numeroCompra;
    $('#header_compra,#table_header_compra tr th').css('color','#3E1089');
    if(deducible == '0') {
      numeroCompra = datosCompra.numcompra + "/"+((datosCompra.fecha).toString()).substr(2,2);
      $('#jumbotron-compra,#table_header_compra').css('background','#6BADF4');
    //  $('#header_compra').html('TAX FREE');

    } else {
      numeroCompra = datosCompra.numcompra;
      $('#jumbotron-compra,#table_header_compra').css('background','#E4EE19');
    //  $('#header_compra').html('WITH TAX');
    }
    
    $('#txtNumCompra').val(numeroCompra);
    $('#txtFecha').val(datosCompra.fecha);
    $('#txtProveedor').val(datosCompra.razon_social+'\n'+datosCompra.direccion+'\nCIF:'+datosCompra.cif);
    $('#txt_area_notas').val(datosCompra.notas);
    $('#txtSFactura').val(datosCompra.sfactura);
    $('#spn-iva').text(datosCompra.iva);
    $('#txtIdProveedor').val(datosCompra.idproveedor);

  // damos unos pocos estilos para que sea ligeramente diferente los colores y el formulario para introducir compras de chatarra y gastos deducibles.
  }
}

// me creo una función para que muestre una alerta personalizada mía, es un modal que hay que pasarles los parámetros encabezado y cuerpo. Se les puede dar estilos bootstrap. También le pasaré tipo 'aviso', 'atencion', critico o exito, y si se le pasa id, se creará un dialogo SiNo para confirmar si se va a borrar la compra o no.
// mi primera Promise ;) deolverá true o false segun el botón al que se aprete (Aceptar o cancelar).
function alerta(cuerpo,encabezado,tipo,id) {
// primera parte, se rellenan los htmls del modal con los argumentos pasados de cuerpo, encabezado, tipo.
  if (tipo == 'aviso') {
    encabezado = '<i class="fas fa-info-circle text-primary"></i>'+ ' ' + '<span class="font-weight-bold text-primary">'+encabezado+'</span>';
  }
  if (tipo == 'atencion') {
    encabezado = '<i class="fas fa-exclamation-triangle  text-warning"></i>'+ ' ' +'<span class="font-weight-bold text-warning">'+encabezado+'</span>';
  }
  if (tipo == 'critico') {
    encabezado = '<i class="fas fa-window-close text-danger"></i>'+ ' ' +'<span class="font-weight-bold text-danger">'+encabezado+'</span>';
  }
  if (tipo == 'exito') {
    encabezado = '<i class="fas fa-check-circle text-success"></i>'+ ' ' +'<span class="font-weight-bold text-success">'+encabezado+'</span>';
  }
  $('#alertModalLabel').html(encabezado);
  $('#contenidoModal').html(cuerpo);

// ahora es crucial, se genera la devolución de la función que sea un Promise. Si id es distintode cero, el modal será tipo SiNo, se devolverá en la Promesa un json que enviará información de id a procesar y si se ha hecho click en Aceptar SINO = true o Cancelar SINo = false. Si no hay un id, el modal es tipo Aceptar. Se devolverá la promesa true si es de Aceptar.
  return new Promise((resolve,reject) =>{
    if (id) {
      let htmlFooter = '<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button><button type="button" class="btn btn-primary" data-dismiss="modal" id="btnAceptar">Aceptar</button>';
      $('#modal-footer').html(htmlFooter);
      $('#alertModal').modal('show');
      $('#alertModal').on('hidden.bs.modal',()=>{
        let devol ={
          id:id,
          SiNo:false
        }
        resolve(devol);
      });
      $('#btnAceptar').on('click',()=>{
        let devol ={
          id:id,
          SiNo:true
        }
        resolve(devol);
      });
    } else {
      let htmlFooter = '<button type="button" class="btn btn-primary" data-dismiss="modal" id="aceptar">Aceptar</button>';
      $('#modal-footer').html(htmlFooter);
      $('#alertModal').modal('show');
      $('#alertModal').on('hidden.bs.modal',()=>resolve(true));
    }
  });
}  
});