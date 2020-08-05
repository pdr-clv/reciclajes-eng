$(document).ready(function(){
  $('.deleteData').on('click', function () {
    //asignamos al input oculto del modal de borrar venta el valor id que tiene la fila a borrar, de cuando se da al botón de deleteData
    $('#idVenta').val($(this).attr('id'));
    var id = $('#idVenta').val();
    // se abre el modal
    $('#deleteModal').modal("show");
// se carga la información de la venta a eliminar, para que el usuario tenga mas información de que venta se va eliminar.
    var action = "mostrar_venta"
    $.ajax({
      type:'POST',
      url:"php/ajax_ventas.php",
      data:{'action':action,'id': id}
    })
    .done(function(datosajax){
      $('#body-eliminar').html(datosajax);
    })
    .fail(function(){
      eModal.alert("<p class='text-danger'>Error al intentar cargar información de venta a eliminar</p>"," ");
    });  
  });
    
  // cuando se da a borrar en el botón deleteButton del modal, se ejecuta el ajax, primero asignaremos el valor id a borrar que viene del input oculto.
  $('#deleteButton').on('click',function(){
      //console.log('Venta eliminada '.concat(id));
    var id = $('#idVenta').val();
    var action = 'eliminar_venta';
    $.ajax({
      type:'POST',
      url:"php/ajax_ventas.php",
      data:{'action':action,'id': id}
    })
    .done(function(datosajax){
      $('#deleteModal').modal("hide");
      alert('Se ha eliminado la venta '.concat(datosajax));
      location.reload();
    })
    .fail(function(){
      eModal.alert("<p class='text-danger'>Error al intentar eliminar Venta</p>"," ");
    });
  });
  // se detecta cuando se abre el modal, y se oculta la barra de navegación que nos da problemas de z-index. Cuando se detecta que se cierra el modal, se vuelve a mostrar.
  $('#agregar,.deleteData').on('click',function(){
  //  alert('Entramos al modal');
    $('#fondonav').hide();
  });
  $('#nuevaVenta,#deleteModal').on('hidden.bs.modal',function(){
    $('#fondonav').show();
    $("#insert_form")[0].reset(); 
    $('#idcliente').text("00");
    $('#numVenta').text("Num.");
    $('#errorfecha').html("");
    $('#fecha').removeClass("is-invalid");
    $('#errorcliente').html("");
    $('#cliente').removeClass("is-invalid");
  });
// se registra el evento submit y se inicia el ajax, antes se comprueban que todos los campos estan rellenados, o tienen valores diferente de cero como es el campo id_cliente
//  $('#insert_form').on('submit',function(event){
//no se porque se pone event, y esta sentencia de even.preventDefault(), pero funciona
//    event.preventDefault();
  $('#btnGuardar').on('click',function(){
    if($('#fecha').val() == "" || $('#fecha').val() == "Num." ) {
    //  alert("El campo fecha no puede estar vacio");
      eModal.alert("<p class='text-danger'>Selecciona una fecha correcta</p>"," ");
      $('#errorfecha').html("Selecciona una fecha");
      $('#fecha').addClass("is-invalid");
    } else if ($('#idcliente').text() == "00") {
      eModal.alert("<p class='text-danger'>Selecciona un cliente de la lista desplegable</p>"," ");
      $('#errorcliente').html("Selecciona un cliente de la lista");
      $('#cliente').addClass("is-invalid");
    } else if ($('#iva').val() == ""){
      eModal.alert("<p class='text-danger'>El campo iva no puede estar vacio</p>"," ");
// el campo notas no puede tener mas de un número concreto de caracteres, para que no sea muy largo.
    } else if ($('#notas').val().length > 150){
      eModal.alert("<p class='text-danger'>No se pueden escribir mas de 150 caracteres en la casilla notas</p>"," ");
    } else {
      var action = 'guardar_venta';
      var fecha = $('#fecha').val();
      var id_cliente = $('#idcliente').text();
      var iva = $('#iva').val();
      var notas = $('#notas').val();
// se coge el valor del span numVenta (obtenido al actualizar campo fecha), se selecciona el valor que hay a la izquierda del / utilizando la función split del texto, y como devuelve un array, se pasa a String, este será el valor que se pase al ajax como el número de venta
      var numVenta =$('#numVenta').text();
      var numVenta = numVenta.split('/',1);
      var numVenta = numVenta.toString();
//cuando se inicia el ajax, en el done y en el fail no se pone punto y coma (semicolon)
      $.ajax({
//se le pasa que es de tipo post, que el url es el archivo guardar_venta, y los datos es el form (this) serializado (todos los nombres de sus inputs o selects), así no hay que pasar uno a uno todas las variables.
        type:'POST',
        url:"php/ajax_ventas.php",
        data:{'action':action,'fecha':fecha,'idcliente':id_cliente,'iva':iva,'numVenta':numVenta,'notas':notas}
      })
      .done(function(datosajax){
        
//cuando se ejecuta la funciona done, que es si todo sale bien, primero se pone a cero todos los valores de del formulario modal. Se hace reset.
        $('#insert_form')[0].reset();
        $('#nuevaVenta').modal('hide');
        if (datosajax==1){
          alert("Venta guardada correctamente");
          window.location = 'articulos_venta.php';
        } else {
          eModal.alert("<p class='text-danger'>No se ha podido guardar la venta, algún campo escrito no es correcto</p>"," ");
        }
      })
      .fail(function(){
        eModal.alert("<p class='text-danger'>Error al intentar añadir la venta</p>"," ");
      });
    }
  });
// cada vez que el campo fecha se modifica, se carga el número de venta automático (47/19) que se introducirá en cada factura
  $('#fecha').on('keydown',function(e){
    //no se va a permitir input manual, sólo haciendo select en datepicker. hay una excepcion, si se pulsa tab, se pasa a siguiente campo.
    var codigotecla = e.keyCode;
    if (codigotecla == 9) {
      // codigo 9 es tab no hace nada, pasa al siguiente input, que es txtcliente.
    } else {
      // para todos los demas casos, no hace nada, y así no permite hacer input manual. Sólo select.
      return false;
    }  
  });
  $('#cliente').on('keydown',function(e){
    $(this).removeClass("is-invalid");
    var codigotecla = e.keyCode;
    if (codigotecla == 13) {
      // codigo 13 es intro se inactiva, para que no haga nada al pulsar intro, ni limpie idcliente ni nada.
      return false;
    } else if (codigotecla == 9) {
      // codigo 9 es tecla tab, no hace nada, no limpia el idcliente.text 
    } else {
    $('#errorcliente').html("");
    $('#idcliente').text("00");      
    }
  });
  $('#fecha').on('change',function(){
    $(this).removeClass("is-invalid");
    $('#errorfecha').html("");
    var fecha = $(this).val();
    var action = 'num_venta';
    $.ajax({
    type: 'POST',
    url: 'php/ajax_ventas.php',
    data: {'action':action,'fecha': fecha}
    })
    .done(function(datosajax){
      $('#numVenta').text(datosajax);
      $('#cliente').select();
    })
    .fail(function(){
    eModal.alert("<p class='text-danger'>Error al conseguir numero de fecha</p>"," ");
    });
  }); 
  $('#cliente').autocomplete({
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
          $('#idcliente').text(cliente.idcliente);
          $('#cliente').val(cliente.razon_social);
      });
    },
// hay que poner appendTo para que el desplegable no se quede debajo del modal y no se vea
    appendTo: $('#nuevaVenta'),
    autoFocus: true
  });
});