$(document).ready(function(){
// estas dos variables globales se llamaran todo el rato a la hora de hacer filtros sql.
  const compra = new Compras();
  const proveedor = new Proveedores();
// al hacer load o reload, siempre se vuelve a compras de chatarra, para que no se desfase el checked de compras o deducibles.
  if ($('#chk_deduc').prop('checked')) {
    compra.deducible = '1';
    $('#spn_compras').removeClass("spn_switch_select");
    $('#spn_deducible').addClass("spn_switch_select");
  } else {
    compra.deducible = '0';
  }
  cargar_compra();
  $('#txtSearch').on('keyup',cargar_compra);
// con este evento, cada vez que se teclee en la casilla de busqueda, se filtraran los datos en el listado de compra
  $('#chk_deduc').on('change',callback_deducible);
// cuando se modifique el checkbox de deducible, se cargarán las compras o deducibles o de chatarra.
  
  $('#btnNuevaCompra').on('click',()=>$('#modalNuevaCompra').modal('show'));
  $('#btnAceptarNC').on('click',guardarCompra);
  $('#btnCerrarNC').on('click',alertaLimpiarCompra);
  
  // se pone funcionalidad a los inputs que recogen datos para introducir compra, y así se les da o les quita el estado is-invalid, por si hubiera tenido algun error en la introducción de compra.
  $('#fecha').on('change',carga_num_compra);
  $('#txtSFactura').on('keydown',()=>{
    $('#txtSFactura').removeClass('is-invalid');
    $('#errorSFactura').html('');
  });
  $('#proveedor').on('keydown',()=>{
    $('#proveedor').removeClass('is-invalid');
    $('#errorProveedor').html('');
    $('#idProveedor').text('00');
  });
  
  $('#fecha').on('keydown',function(e){
    //no se va a permitir input manual, sólo haciendo select en datepicker. hay una excepcion, si se pulsa tab, se pasa a siguiente campo.
    var codigotecla = e.keyCode;
    if (codigotecla == 9) {
      // codigo 9 es tab no hace nada, pasa al siguiente input, que es txtcliente.
    }
  });
// al tener que introducir la fecha manual, le añado una alerta al hacer focus out en el campo fecha para que el usuario sepa que está introduciendo una fecha anterior al año actual. Puede ser util si el usuario ha introducido una fecha extraña, y evitar problemas futuros.  
  $('#fecha').on('blur',function(){
    let textFecha = parseInt($(this).val().substr(0,4));
    let year = new Date();
    let yearActual = year.getFullYear();
    if (textFecha < yearActual) {
      //alerta('Revisa la fecha introducida');
      let bodyAlerta = 'La fecha introducida es anterior a ' + yearActual + '. ¿Es correcta?';
      alerta(bodyAlerta,'Revisar fecha','atencion');
    }
  });
  
  $('#txtSearch').on('keyup',function(){
  $('td').addClass('td-filtrado');
  if ($(this).val() !='') {
    $(this).addClass('input-filtrado');
    $('td').addClass('td-filtrado');
  } else{
    $(this).removeClass('input-filtrado');
  }
  });
  
//Se definen en el objeto proveedor las funciones listarAutocomplete para que sirve de source (items) al autocomplete y selectAutoComplete para que se elija que valores se van a pasar al campo idProveedor y proveedor al hacer select
  
  $('#proveedor').autocomplete({
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
        $('#idProveedor').text(proveedor.idproveedor);
        $('#proveedor').val(proveedor.razon_social);
      });
    },
  // hay que poner appendTo para que el desplegable no se quede debajo del modal y no se vea
    appendTo: $('#modalNuevaCompra'),
    autoFocus: true
  });

function botonesDelEdit(){
  $('.btn_editar_linea').on('click',function(){
    let idcompra = $(this).attr('idcompra'); 
     window.location.href='articulos_compra.php?idcompra='+idcompra+'&deducible='+compra.deducible;
  });
  $('.btn_eliminar_linea').on('click',function(){
    let idcompra=$(this).attr('idcompra');
    console.log(idcompra);
// fila_eliminar seleccionará el row con el idcompra, y se pondrá en rojo para ayudar al usuario a detectar que compra va a eliminar. se le añade la clase text-danger. 
    let fila_eliminar = "tr[id=" + idcompra + "]";
    $(fila_eliminar).addClass("text-danger");
//eliminar venta. A la alerta personalizada, le añado una promesa, que una vez se resuelva al seleccionar el botón Aceptar o cancelar, procederá a eliiminar la venta, o a no eliminarla. Se le pasa el idcompra como parámetro.
    alerta('¿Deseas eliminar la compra?','Eliminar compra','critico',idcompra)
      .then(devol => eliminarCompra(devol));
    
  });
} 

function carga_num_compra(){
  $('#fecha').removeClass("is-invalid");
  $('#errorfecha').html("");
  compra.fecha = $('#fecha').val();
  compra.cargarNumCompra().then(data=>$('#numCompra').text(data));
}


  
function guardarCompra(){
  let error = 0;
  if ($('#numCompra').text() == 'Num.' || $('#numCompra').text() ==='') {
    $('#fecha').addClass('is-invalid');
    $('#errorfecha').text('Introduce una fecha');
    error = error+1;
  } 
  if ( $('#idProveedor').text()== '00') {
    $('#proveedor').addClass('is-invalid');
    $('#errorProveedor').text('Introduce un proveedor del desplegable');
    error = error +1;
  } if ( $('#txtSFactura').val()=== '') {
    $('#txtSFactura').addClass('is-invalid');
    $('#errorSFactura').text('Nº Factura Proveedor');
    error = error + 1;
  } 
  if (error === 0) {
    $('#modalNuevaCompra').modal('hide');
    compra.numcompra = (($('#numCompra').text()).split('/',1)).toString();
    compra.fecha = ($('#fecha').val()).toString();
    compra.idproveedor = $('#idProveedor').text();
    compra.iva = $('#iva').val();
    compra.sfactura = $('#txtSFactura').val();
    compra.notas = $('#txtNotas').val();
    compra.insertarCompra().then(devol=>evaluarCompra(devol));
  } else {
    alerta('Alguno de los campos introducidos no son correctos','Error','critico');
  }
}
  
function evaluarCompra(input){
  // el ajax devuelve la promesa 1, entonces si es 1 continuamos, si no, no continuamos.
  if (input==1) {
    alerta('Compra añadida correctamente. Añade articulos a la compra','Compra añadida','exito')
      .then(() =>  window.location.href='articulos_compra.php');
  } else {
    alerta('No se ha podido guardar la compra','Error','critico');
  }
  limpiarinputs();
} 

function alertaLimpiarCompra(){
  if ($('#numCompra').text() !='Num.' || $('#idProveedor').text() != '00' || $('#iva').val() != 21 || $('#txtSFactura').val() != '' || $('#txtNotas').val() != '') {
  alerta('¿Deseas salir sin guardar compra? Se perderan los datos rellenados','¡Atención!','atencion','SiNo')
    .then(respuesta=>limpiarFormCompra(respuesta));
  }
}  
// esta callback es para poner aquí todos los eventos que se podrán ejecutar una vez haya cargado toda la función cargar_compra.  
  
  
function limpiarFormCompra(respuesta){
  if(respuesta.SiNo){
    alerta('No se ha guardado la compra','¡Atención!','critico');
    limpiarinputs();
  } else {
    $('#modalNuevaCompra').modal('show');
  }
  
}
  
function limpiarinputs(){
// removeClass is-invalid es para quitar el rojito, por si estuviera seleccionado marcando un error de algún otro momento. también se elimina todos los textos de error, por si marcara alguna error el span error.
  $('#modalNuevaCompra input').removeClass('is-invalid');
  $('#modalNuevaCompra .error').text('');
  
  
  $('#numCompra').text('Num.');
  $('#fecha').val('');
  $('#idProveedor').text('00');
  $('#iva').val('21');
  $('#txtSFactura').val('');
  $('#txtNotas').val('');
  $('#proveedor').val('');
}
  

// se crea esta función para dar funcionalidad a los botnes Eliminar y Editar (ver compra) una vez ha cargado el evento cargar_compras o actualiza_paginacion.
  
function eliminarCompra(input) {
  let id=input.id;
  let SiNo = input.SiNo;
  let fila_eliminar = "tr[id=" + id + "]";
  compra.idcompra = id;
  if (SiNo){
// si la venta se elimina, se oculta la fila con un delay 1500 segundos, para que el usuario vea que fila se ha eliminado.
    compra.eliminarCompra().then(()=>$(fila_eliminar).hide(1500));
  } else {
//si el usuario decide no eliminar la venta al hacer clic en Cancelar, se devolverá el color normal al row.
    $(fila_eliminar).removeClass("text-danger");
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
      htmlFooter = '<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button><button type="button" class="btn btn-primary" data-dismiss="modal" id="btnAceptar">Aceptar</button>';
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
  })
}  

  
// con esta función, cada vez que se seleccione tipo de compra, se cambiará a deducible o compras de chatarra.  
function callback_deducible(){
  if (compra.deducible == '0') {
    compra.deducible = '1';
    $('#spn_compras').removeClass("spn_switch_select");
    $('#spn_deducible').addClass("spn_switch_select");
// alerta es una función creada por mi para sacar una alerta en pantalla, se le pasa encabezado y cuerpo de alerta.
// Si se le pasa como cuarto argumento "eliminar" será valido para eliminar la venta por ajax.
    alerta("<p>Acabas de seleccionar <span class='font-weight-bold'>Gastos deducibles</span></p>","Aviso","aviso");
  } else if (compra.deducible == '1') {
    compra.deducible = '0';
    $('#spn_compras').addClass("spn_switch_select");
    $('#spn_deducible').removeClass("spn_switch_select");
    alerta("<p>Acabas de seleccionar <span class='font-weight-bold'>Compras de chatarra</span></p>","Aviso","aviso");
  }
  $('#txtSearch').val('');
  cargar_compra();
}  
  
  
function cargar_pagination(){
  var search = $('#txtSearch').val();
  compra.cargarPaginacion(search)
    .then(data=>$('#paginacion').html(data))
    .then(() => $('#paginacion ul li button').on('click',controlesPaginacion));
    
// una vez cargada la página, hay que ejecutar botonesPaginacion, porque si no, no tienen funcionalidad.
  
}  

function cargar_compra(){
  var search = $('#txtSearch').val();
// cargar Tabla compra es un ajax, devuelve promesa.
  compra.cargarTablaCompra(search)
    .then(data => $('#tbl_body').html(data))
    .then(()=>{
    botonesDelEdit();
    estilosFiltrado();
    cargar_pagination();
    });
}
  
// si el txtSearch tiene datos, está filtrando datos, añadimos estilos en rojito para que el usuario tenga noción que están los datos filtrados.
  
function estilosFiltrado(){
  if ($('#txtSearch').val() !=''){
    $('#txtSearch').addClass('input-filtrado');
    $('td').addClass('td-filtrado');
  } else{
    $('#txtSearch').removeClass('input-filtrado');
    $('td').removeClass('td-filtrado');
  }
}

// hay que hacer el truco de crear una función callback después de cargar labotonesDelEdit y cargar_pagination 


function controlesPaginacion(){
 let search = $('#txtSearch').val();
// id o id de nueva página es el id del elemento paginación que se ha hecho clic. El idViejo es el valor text que tiene página activa (antes de ser actualizado) 
  let idPaginaOld = parseInt($('#pagActiva').text());
  let ultimaPagina = parseInt($('#pagUltima').text());
// se tiene que dar formato a pagina pag+numero, para que los id no se mezclen con los id de las tablas que hay en tr.
  let pagina=$(this).attr('id');
//haciendo un substr3, se le quita el pag inicial, y se puede procesar el idPagina como número para hacer calculos.
  let idPagina = parseInt(pagina.substr(3));
// se calcula cual es el inicio del bloqueFin, es el resultado de dividir ultimaPagina * 5, sacar el redonde a la baja de dicho cociente, y multiplicar por 5. util para saber donde empieza el último bloque. Devuelve el número de la primera página del último bloque de Paginación.
  let lastBloqueIni = 5*(Math.floor(ultimaPagina/5))+1;
  
  if (pagina =='posterior'){
      idPagina=idPaginaOld + 1;
      pagina = 'pag'+idPagina;
      let Finbloque = Number.isInteger(idPaginaOld/5);
      if (Finbloque){
        let i=0;
        do {
          i += 1;
          let paginaVisualizada = idPaginaOld + i;
          let paginaOcultada = idPagina - i;
          $('#pag'+paginaVisualizada).toggleClass('d-none');
          $('#pag' + paginaOcultada).toggleClass('d-none');
        } while (i < 5);
      }
      
    } else if (pagina =='anterior') {
      idPagina = idPaginaOld - 1;
      pagina = 'pag'+idPagina;
      let Finbloque = Number.isInteger(idPagina/5);
      if (Finbloque){
        let i=0;
        do {
          i += 1;
          let paginaVisualizada = idPaginaOld - i;
          let paginaOcultada = idPagina + i;
          $('#pag' + paginaVisualizada).toggleClass('d-none');
          $('#pag' + paginaOcultada).toggleClass('d-none');
        } while (i < 5);
      }
    } else if (pagina =='bloqueUno'){
      idPagina = 1;
      pagina = 'pag'+idPagina;
      let i=0;
      do {
        i += 1;
        let paginaVisualizada = i;
        $('#pag' + paginaVisualizada).removeClass('d-none');
      } while (i < 5);
      do {
        i += 1;
        let paginaOcultada = i;
        $('#pag' + paginaOcultada).addClass('d-none');
      } while (i < ultimaPagina);
    } else if (pagina =='bloqueFin'){
      idPagina = lastBloqueIni;
      pagina = 'pag'+idPagina;
      let idPaginaBloqueFin=lastBloqueIni-1;
      let i=0;
      do {
        i += 1;
        let paginaOcultada = i;
        $('#pag' + paginaOcultada).addClass('d-none');
      } while (i < idPaginaBloqueFin);
      do {
        i += 1;
        let paginaVisualizada = i;
        $('#pag' + paginaVisualizada).removeClass('d-none');
      } while (i < ultimaPagina);
    }
    let elementoNuevo =pagina;
    let elementoViejo ='pag'+idPaginaOld;
  $('#'+elementoNuevo).toggleClass('activo');
   
  $('#'+elementoViejo).toggleClass('activo');

  $('#pagActiva').text(idPagina);
  
// dependiendo de que idPagina, ocultaremos o daremos visbilidad a los botones anterior,posterior, bloqueUno o bloqueFin.
  
  if (idPagina < 6){
    $('#bloqueUno').addClass('d-none');
    if(idPagina == 1){
      $('#anterior').addClass('d-none');
    } else {
      $('#anterior').removeClass('d-none');
    }
  } else {
    $('#bloqueUno').removeClass('d-none');
  }
  
  if (idPagina >= lastBloqueIni){
    $('#bloqueFin').addClass('d-none');
    if(idPagina == ultimaPagina){
      $('#posterior').addClass('d-none');
    } else {
      $('#posterior').removeClass('d-none');
    }
  } else {
    $('#bloqueFin').removeClass('d-none');
  }

  compra.cargarTablaPaginacion(search,idPagina)
  .then(data =>{
    $('#tbl_body').html(data);
// se hace scroll to Top de la página, para que se vea la nueva página desde el principio.
    $(window).scrollTop(0);
  })
// cada vez que se cargar el tbl_body, hay que ejecutar el botonesDelEdit, para que al hacer clic en Ver o en eliminar, actuen los botones.
  .then(()=>{
    botonesDelEdit();
    estilosFiltrado();
  });

}

});

