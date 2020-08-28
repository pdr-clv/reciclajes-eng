$(document).ready(function(){
  var ventafiltro = $('#txtIdVenta').val();
  // la variable ventafiltro es global para todas las funciones
  var params = {
    action : 'cargar_venta',
    idventa : ventafiltro
  };
  $.get("php/ajax_articulos_venta.php", params , function(response){
    //console.log(response);
    var venta = JSON.parse(response);
    var txtIva = venta.iva;
    var txtNumVenta = venta.numventa + "/"+((venta.fecha).toString()).substr(2,2);
    $('#txtFecha').val(venta.fecha);
    $('#txtCliente').val(venta.razon_social);
    $('#txtIdCliente').val(venta.idcliente);
    $('#txtNumVenta').val(txtNumVenta);
    $('#txtIva').text(txtIva);
    
    $('#txt_area_notas').val(venta.notas);
  }).done(actualiza_importe());
    $.ajax({
    type: 'GET',
    url: 'php/ajax_articulos_venta.php',
    data: {'action':'cargar_lineas','idventa': ventafiltro},
      success:function(datosajax){
        $('#tablaBody').html(datosajax);  
      }
    }).fail(function(){
      eModal.alert("<p class='text-danger'>Error loading sales items</p>"," ");
    }).done(function(){
      $('.btn_eliminar_linea').on('click',function(){
      var idlinea_eliminar = $(this).attr('id');
      var action = "eliminar_linea";
  // fila_eliminar es el selector de la fila que se va a eliminar. Se pondrá en rojo para alertar al usuario.
      var fila_eliminar = "tr[id=" + idlinea_eliminar + "]";
      $(fila_eliminar).addClass("text-danger");
      $('#modal_eliminar_linea').modal('show');
      $('#btn_eliminar_linea').on('click',function(){
        $.ajax({
          url:"php/ajax_articulos_venta.php",
          method:"get",
          data:{action:action, id:idlinea_eliminar},
          success:function(data){
            if (data == 1) {
              $('#modal_eliminar_linea').modal('toggle');
              $(fila_eliminar).hide(1800);
              //eModal.alert('El articulo ha sido eliminado correctamente de la venta');
            } else if (data == 0) {
              eModal.alert('Error. It was not possible to delete item');
            } else {
              eModal.alert('user_test is not allowed to perform this action');
            }
          }
        }).done(actualiza_importe());
      });
      });
 // Editar linea, cuando se abre formulario nuevoArticulo, si se abre con el boton de editar linea, cargará todos los valores que hay con su idlinea.
      $('.btn_editar_linea').on('click',function(){
        var idlinea_editar = $(this).attr('id');
        $('#txtIdLinea').val(idlinea_editar);
        var fila_editar = "tr[id=" + idlinea_editar + "]";
        $(fila_editar).addClass("text-warning");
        $('#nuevoArticulo').modal('show');
        var params = {
        action : 'cargar_articulo_edit',
        id : idlinea_editar
        };
        $.get("php/ajax_articulos_venta.php", params , function(response){
          //console.log(response);
          var articulolinea = JSON.parse(response);
          var importe = parseFloat(articulolinea.pvplin);
          var cantidad = parseFloat(articulolinea.cantidad);
          var descripcion = articulolinea.descripcion;
          var articulo = articulolinea.articulo;
          var idarticulo = articulolinea.idarticulo;
          var precio = importe/cantidad;

          $('#txtIdArticulo').val(idarticulo);
          $('#txtImporteLin').val(importe.toFixed(2));
          $('#txtCantidad').val(cantidad);
          $('#txtDescripcion').val(descripcion);
          $('#txtArticulo').val(articulo);
          $('#txtPrecio').val(precio.toFixed(2));

      });
  });
      
    });

  $('#txtArticulo').autocomplete({
    source:function(request,response){
      $.ajax({
        url:"php/ajax_articulos_venta.php",
        dataType:"json",
        data:{q:request.term,action:'rellena_cmb_articulo'},
        success:function(data){
          response(data);
        }
      });
    },
    minLength:0,
    select:function(event,ui){
      //console.log(ui.item.value);
      var params = {
        action : 'get_idarticulo',
// como se filtra referencia que son ocho caracteres + descripcion, se filtra solo la diferencia, que es el substring de los ocho primeros caracteres
        id : (ui.item.value).substring(0,8)
      };
      $.get("php/ajax_articulos_venta.php", params , function(response){
        ///console.log(response);/
        var articulo = JSON.parse(response);
        //  alert('El codigo del cliente seleccionado es: ' + cliente.idcliente);
          $('#txtCantidad').select();
          $('#txtArticulo').val(articulo.articulo);
          $('#txtIdArticulo').val(articulo.idarticulo);
          $('#txtDescripcion').val(articulo.descripcion);
// con la condicion txtIdLinea vacia, comprueba si estamos en modo editar articulo en venta, o en modo guardar.  
          if ($('#txtIdLinea').val() === "") {
          $('#txtPrecio').val(articulo.pvpv);
          }
      });
    },
// hay que poner appendTo para que el desplegable no se quede debajo del modal y no se vea
    appendTo: $('#nuevoArticulo'),
    autoFocus: true

  });
// Por comodida, cada vez que se escriba en txtArticulo, se limpiará el texto de txtIarticulo necesario para guardar la compra, y también el txtDescripcion. Pero si se presiona intro o tab, hará focus en txtCantidad, y ya se podrá introducir la cantidad.
  $('#txtArticulo').on('keydown',function(e){
    $(this).removeClass("is-invalid");
    $('#txtDescripcion').removeClass("is-invalid");
    $('#errorArticulo').html("");
    var codigotecla = e.keyCode;
    if (codigotecla == 13) {
      // codigo 13 es intro
      $('#txtCantidad').select();
    } else if (codigotecla == 9) {
      // codigo 9 es tab, no hará nada.
    } else {
      // para todos los demas casos, ya se limpiarán los textos de txtIdArticulo (necesario para introducir venta) y txtDescripcion.
      $('#txtIdArticulo').val("");
      $('#txtDescripcion').val("");
    }
  });
  $('#txtCantidad').on('blur',function(e){
    var txtnumerico = $(this).val();
    var txtPrecionumerico = $('#txtPrecio').val();
    if (!$.isNumeric(txtnumerico)) { // este primer if, es por si se le mete el decimal con coma, se cambia a punto, que es como lo reconoce SQL
      txtnumerico = txtnumerico.replace(',','.');
    } if ($.isNumeric(txtnumerico)) { // si es numérico, ya se puede pasar al siguiente campo, y se calcula el txtImporte como la multiplicación de cantidad y precio
      var txtImporte = txtnumerico * txtPrecionumerico;
      $(this).val(txtnumerico);
      $('#txtImporteLin').val(txtImporte);
    } else {
      $('#errorCantidad').html("Type number");
      $(this).addClass("is-invalid");
      $('#txtImporteLin').val("");
    }
  }); 
  $('#txtCantidad').on('keydown',function(e){
    
    $('#errorCantidad').html("");
    $(this).removeClass("is-invalid");
    var tecla = e.keyCode;
    if (tecla == 13) { // si es intro, se hace focus en el siguiente txtbox
      $('#txtPrecio').select();
    }
  });
  $('#txtPrecio').on('focus',function(){
    var txtnumerico = $('#txtCantidad').val();
    var txtnumericoPrecio = $(this).val()
    if (!$.isNumeric(txtnumericoPrecio)) {
      $('#txtImporteLin').val("");
    }
    if (!$.isNumeric(txtnumerico))  {
      $('#txtCantidad').select();
      $('#txtImporteLin').val("");
    }
  });
    $('#txtPrecio').on('blur',function(e){
    var txtnumerico = $(this).val();
    var txtCantidadnumerico = $('#txtCantidad').val();
    if (!$.isNumeric(txtnumerico)) { // este primer if, es por si se le mete el decimal con coma, se cambia a punto, que es como lo reconoce SQL
      txtnumerico = txtnumerico.replace(',','.');
    } if ($.isNumeric(txtnumerico)) { // si es numérico, ya se puede pasar al siguiente campo, y se calcula el txtImporte como la multiplicación de cantidad y precio
      var txtImporte = txtnumerico * txtCantidadnumerico;
      $(this).val(txtnumerico);
      $('#txtImporteLin').val(txtImporte);
    } else {
      $('#errorPrecio').html("Escribe número");
      $(this).addClass("is-invalid");
      $('#txtImporteLin').val("");
      //eModal.alert('Debes escribir un valor numérico en el campo cantidad');
    }
  }); 
  $('#txtPrecio').on('keydown',function(e){
    var tecla = e.keyCode;
    $('#errorPrecio').html("");
    $(this).removeClass("is-invalid");
    if (tecla == 13) { // si es intro, se hace focus en el siguiente tab order, que es btnGuardar
      $('#btnGuardar').focus();
      //alert('Se ha pulsado intro');
    }
  });
  $('#txtImporteLin').on('focus',function(){
    var txtnumerico = $('#txtPrecio').val();
    if (!$.isNumeric(txtnumerico)) {
      $('#txtPrecio').select();
      $('#txtImporteLin').val("");
    }
  });
  $('#btnGuardar').on('click',function(){
    if($('#txtIdArticulo').val() === "") {
      eModal.alert("<p class='text-danger'>Check if all data are valid</p>"," ");
      $('#txtArticulo').addClass("is-invalid");
      $('#txtDescripcion').addClass("is-invalid");
      $('#errorArticulo').html("Select article");
    } else if ($('#txtCantidad').val() === 0){
      eModal.alert("<p class='text-danger'>Amount cannot be null value</p>"," ");
      $('#txtCantidad').addClass("is-invalid");
      $('#errorCantidad').html("Cantidad > 0");
    } else if ($('#txtImporteLin').val() === ""){
      eModal.alert("<p class='text-danger'>Only valid numeric values in quantity and price.</p>"," ");
    } 
    else {
    var action = 'guardar_linea';
    var idventaguardar = $('#txtIdVenta').val();
    var idarticuloguardar = $('#txtIdArticulo').val();
    var cantidadguardar = $('#txtCantidad').val();
    var importeguardar = $('#txtImporteLin').val();
    
    $.ajax({
    type: 'GET',
    url: 'php/ajax_articulos_venta.php',
    data: {'action':action,'idventa': idventaguardar,'idarticulo':idarticuloguardar, 'cantidad':cantidadguardar, 'importe': importeguardar}
    })
    .done(function(datosajax){
      if (datosajax == 1){
        alert("Article saved");
        location.reload();
      } else if (datosajax == 0) {
        alert("Error, something went wrong.");
      } else {
        alert('usert_test is not allow to perform this action');
      }
    })
    .fail(function(){
    eModal.alert("<p class='text-danger'>Error saving article</p>"," ");
    });
    }
  });
// cuando se abre el modal de nuevo articulo, se detecta si está en modo editar, o modo guardar. Sólo detectando si el valor oculto txtIdLinea está vacio o no. De ese modo se cambia el titulo del modal, y se visualiza el botón editar o Guardar, según el modo en el que esté el modal abierto.
  
  $("#nuevoArticulo").on('show.bs.modal',function(){
    if ($('#txtIdLinea').val() == "") {
      $('#nuevoArticulolabel').html("New article");
      $('#btnGuardar').show();
      $('#btnEditar').hide();
    } else {
      $('#nuevoArticulolabel').html("Edit article");
      $('#btnGuardar').hide();
      $('#btnEditar').show();
    }
  });
  
  $("#nuevoArticulo").on('hidden.bs.modal',function(){
// si txtIdlinea no está vacio, se ejecuta el evento de cerrar formulario de edición, para que el colorcito amarillo se vaya y quede negro otra vez, haciendo un reload (como en eliminar)  
    if ($('#txtIdLinea').val() !== "") {
      location.reload();
    }
    // cuando se cierra el modal de añadir articulo, se resetea todos sus valores de inputs, para que no aparezcan después valores no deseados.
    $("#añadirArticulo")[0].reset();  
  });
    $('#btnEditar').on('click',function(){
    if($('#txtIdArticulo').val() === "") {
      eModal.alert("<p class='text-danger'>Review if all data are correct.</p>"," ");
      $('#txtArticulo').addClass("is-invalid");
      $('#txtDescripcion').addClass("is-invalid");
      $('#errorArticulo').html("Selecciona un articulo");
    } else if ($('#txtCantidad').val() === 0){
      eModal.alert("<p class='text-danger'>Quantity cannot be zero or null</p>"," ");
      $('#txtCantidad').addClass("is-invalid");
      $('#errorCantidad').html("Cantidad > 0");
    } else if ($('#txtImporteLin').val() === ""){
      eModal.alert("<p class='text-danger'>Type only numeric values in fields quantity and price.</p>"," ");
    } 
    else {
    var action = 'editar_linea';
    var idLineaEditar = $('#txtIdLinea').val();
    var idarticuloEditar = $('#txtIdArticulo').val();
    var cantidadEditar = $('#txtCantidad').val();
    var importeEditar = $('#txtImporteLin').val();
    
    $.ajax({
    type: 'GET',
    url: 'php/ajax_articulos_venta.php',
    data: {'action':action,'id': idLineaEditar,'idarticulo':idarticuloEditar, 'cantidad':cantidadEditar, 'importe': importeEditar}
    })
    .done(function(datosajax){
      if (datosajax == 1){
        var fila_editar = "tr[id=" + idLineaEditar + "]";
        $('#nuevoArticulo').modal('toggle');
        $(fila_editar).hide(2000);
        
      } else if (datosajax == 0) {
        alert("It was not possible to edit article");
      } else {
        alert ('user_test is not allowed to perform this action');
      }
    })
    .fail(function(){
    eModal.alert("<p class='text-danger'>Error saving</p>"," ");
    });
    }
  });
  $('#txt_edit_fecha').on('keydown',function(e){
    //no se va a permitir input manual, sólo haciendo select en datepicker. hay una excepcion, si se pulsa tab, se pasa a siguiente campo.
    var codigotecla = e.keyCode;
    if (codigotecla == 9) {
      // codigo 9 es tab no hace nada, pasa al siguiente input, que es txtcliente.
    } else {
      // para todos los demas casos, no hace nada, y así no permite hacer input manual. Sólo select.
      return false;
    }  
  });
  $('#txt_edit_cliente').on('keydown',function(e){
    $(this).removeClass("is-invalid");
    var codigotecla = e.keyCode;
    if (codigotecla == 13) {
      // codigo 13 es intro se inactiva, para que no haga nada al pulsar intro, ni limpie idcliente ni nada.
      return false;
    } else if (codigotecla == 9) {
      // codigo 9 es tecla tab, no hace nada, no limpia el idcliente.text 
    } else {
    $('#spn_errorcliente').html("");
    $('#spn_idcliente').text("00");      
    }
  });
  $('#txt_edit_fecha').on('change',function(){
    $('#txt_edit_cliente').select();
    $(this).removeClass("is-invalid");
    $('#spn_errorfecha').html("");
// esto es por si hace falta vincular que al cambio de fecha, calcule un nuevo número de Venta, pero no es necesario cuando estamos en modo de edición.
  /*  var fecha = $(this).val();
    var action = 'num_venta';
    $.ajax({
    type: 'POST',
    url: 'php/ajax_ventas.php',
    data: {'action':action,'fecha': fecha}
    })
    .done(function(datosajax){
      var numVenta = datosajax.split("/",1).toString();
      console.log(numVenta);
      $('#spn_numVenta').text(datosajax);
      $('#txt_edit_numVenta').val(parseInt(numVenta));//datosajax.split("/",1));
      $('#txt_edit_cliente').select();
    })
    .fail(function(){
    eModal.alert("<p class='text-danger'>Error al conseguir numero de fecha</p>"," ");
    });*/
  }); 
  $('#txt_edit_cliente').autocomplete({
    source:function(request,response){
      $.ajax({
        url:"php/ajax_ventas.php",
        dataType:"json",
        data:{q:request.term,action:'rellena_combo'},
        success:function(data){
          response(data);
        }
      });
    },
    minLength:1,
    select:function(event,ui){
      //console.log((ui.item.value).split(" ",1).toString());
      var params = {
        action : 'get_idcliente',
        id : (ui.item.value).split(" ",1).toString()
      };
      $.get("php/ajax_ventas.php", params , function(response){
        //console.log(response);
        var cliente = JSON.parse(response);
        //  alert('El codigo del cliente seleccionado es: ' + cliente.idcliente);
          $('#spn_idcliente').text(cliente.idcliente);
          $('#txt_id_cliente').val(cliente.razon_social);
      });
    },
// hay que poner appendTo para que el desplegable no se quede debajo del modal y no se vea
    appendTo: $('#modal_edita_venta'),
    autoFocus: true
  });
  $("#modal_eliminar_linea").on('hidden.bs.modal',function(){
// al cerrar el modal, se quita la clase text-danger, la que permite ver en rojito la linea que se va a eliminar, por si el usuario diera clic en cancelar o cerrar al modal de eliminar linea
      $("tr").removeClass("text-danger");
    
  });
  $("#modal_edita_venta").on('show.bs.modal',function(){
// se hace reset a todas las clases de error, si hay un error, y se colorean de rojo las casillas mal introducidas, al volver a abrir el formulario, vuelven a estar en rojo. Hay que quitar todos los mensajes de error, y las clases is-invalid
    $(':input').removeClass("is-invalid");
    $('#txt_duplicado').val('');
    $('#spn_errorcliente').html("");
    $('#error_edit_fecha').html("");
    
// se coge los valores de los txtinputs de la venta, y se les pasa al modal edita_venta. Estos valores podrán ser modificados y editados pulsando en editar venta.
    var fecha = $('#txtFecha').val();
    var cliente = $('#txtCliente').val();
    var idCliente = $('#txtIdCliente').val();
    var valorNumVenta = $('#txtNumVenta').val();
  //  el valor NumVenta, es un string, hacemos split para que antes del / coja el número de Venta.
    var numVenta = valorNumVenta.split("/",1);
    var iva = $('#txtIva').text();
    var notas = $('#txt_area_notas').val();
    $('#txt_edit_fecha').val(fecha);
    $('#txt_edit_cliente').val(cliente);
    $('#spn_idcliente').text(idCliente);
    $('#txt_edit_numVenta').val(numVenta);
    $('#txt_edit_iva').val(iva);
    $('#txtarea_edit_notas').val(notas);
  });
  $('#btn_edit_venta').on('click',function(){
      var fecha = $('#txt_edit_fecha').val();
      var numVenta= $('#txt_edit_numVenta').val();
    if($('#txt_edit_fecha').val() === "" || $('#txt_edit_fecha').val() == "Num." ) {
    //  alert("El campo fecha no puede estar vacio");
      eModal.alert("<p class='text-danger'>Select correct date</p>"," ");
      $('#error_edit_fecha').html("Select a date");
      $('#txt_edit_fecha').addClass("is-invalid");
    } else if ($('#spn_idcliente').text() == "00") {
      eModal.alert("<p class='text-danger'>Select a client from the list</p>"," ");
      $('#spn_errorcliente').html("Select a client from the list");
      $('#txt_edit_cliente').addClass("is-invalid");
    } else if ($('#txt_edit_iva').val() === ""){
      eModal.alert("<p class='text-danger'>Tax field cannot be null</p>"," ");
// el campo notas no puede tener mas de un número concreto de caracteres, para que no sea muy largo.
    } else if ($('#txtarea_edit_notas').val().length > 150){
      eModal.alert("<p class='text-danger'>You cannot text more than 150 characters.</p>"," ");
// este if, si el txt_input duplicado (que está oculto), tiene un valor mayor que cero, se supone que hemos seleccionado un numVenta duplicado, y no dejará continuar para editar la venta. Así nos aseguramos que no se duplican valores
    } else if ($('#txt_duplicado').val()>0){
      eModal.alert("<p class='text-danger'>It is not possible duplicate values from other sales</p>"," ");
      $('#txt_edit_numVenta').addClass("is-invalid");
    } else {
      var action = 'editar_venta';
      var idVenta = $('#txtIdVenta').val();
      var idCliente = $('#spn_idcliente').text();
      var iva = $('#txt_edit_iva').val();
      var notas= $('#txtarea_edit_notas').val();
      //alert(action);
      $.ajax({
      type: 'GET',
      url: 'php/ajax_articulos_venta.php',
      data: {'action':action, 'idventa':idVenta, 'fecha':fecha, 'idcliente':idCliente, 'numVenta':numVenta, 'iva':iva, 'notas':notas}
      })
      .done(function(datosajax){
        if (datosajax == 1){
          $("#modal_edita_venta").modal('toggle');
          alert("Header modified corrrectly")
          location.reload();
        } else if (datosajax == 0) {
          alert("It was not possible to edit sale header");
        } else {
          alert ('user_test is not allowed to perform this action');
        }
      })
      .fail(function(){
      eModal.alert("<p class='text-danger'>Error saving</p>"," ");
      });
    }
  });
// esta chapucilla es para rellenar el txt_duplicado que registra si el numVenta está duplicado, cada vez que se modifica el numventa, se rellena el txt_duplicado. Después segun su valor que registrará si hay duplicados, entonces se dejará o no se dejara actualizar la venta.
  $('#txt_edit_numVenta').on('change',function(){
    $(this).removeClass("is-invalid");
    var fecha = $('#txt_edit_fecha').val();
    var numVenta= $('#txt_edit_numVenta').val();
    var action = 'check_numventa';
    $.ajax({
      type: 'GET',
      url: 'php/ajax_articulos_venta.php',
      data: {'action':action, 'fecha':fecha, 'numventa':numVenta}
    })
    .done(function(datosajax){
      $('#txt_duplicado').val(datosajax);

    })
  });
  
  
function actualiza_importe() {
// variable que pasa al ajax, para que vaya al if que tiene el action actualiza importe
  var action = 'actualiza_importe';
  $.ajax({
    type: 'GET',
    url: 'php/ajax_articulos_venta.php',
    data: {'action':action,'idventa': ventafiltro},
    success:function(datosajax){
// el resultado datosajax es el importe, se pasa a float
      var importe = parseFloat(datosajax);
  // el iva lo cogemos del span que tiene el id txtIva, ya está en formato número. normalmente 21 (porciento, ) 
      var iva = $('#txtIva').text();
      var iva = importe*iva/100;
  //con la funcion formatNumber y un toFixed 2, (sólo vale para floats, no vale para strings) se devuelven los valores a los campos importe, importe total, e importe iva.
      var importe =formatNumber.new(importe.toFixed(2))+"€";
      var iva = formatNumber.new(iva.toFixed(2))+"€";
      $('#txtImporte').text(importe);
      $('#txtImporteTotal').text(importe);
      $('#spanIva').text(iva);
    }
    }).fail(function(){
      alert('Error updating price');
    });
 
}
});
