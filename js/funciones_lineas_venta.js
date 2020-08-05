$(document).ready(function(){
// esto se ejecuta cada vez que se abre la página, se rellena el campo idcliente con el nombre de cliente, para que sea mas entendible por el usuario.
  var idClientex = $('#clientex').val();
  var action = 'cargar_cliente';
  //  alert(idCliente);
  $.ajax({
    type: 'POST',
    url: 'php/ajax_lineas_venta.php',
    data: {'action':action,'idcliente': idClientex }
  })
  .done(function(datosajax){
    $('#id_cliente').html(datosajax);
  })
  .fail(function(){
    alert('Error al cargar datos cliente');
  });
});