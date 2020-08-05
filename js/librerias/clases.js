class LinCompras{
  constructor(idlin,idcompra,idarticulo,cantidad,pvplin){
    this._idlin = idlin;
    this._idcompra = idcompra;
    this._idarticulo = idarticulo;
    this._cantidad = cantidad;
    this._pvplin = pvplin;
    this._url = 'php/ajax_articulos_compra.php';
  }
  
  eliminarLinea(){
    let datosAjax;
    datosAjax = {action:'eliminar_linea',idlin:this._idlin};
    return new Promise((resolve,reject) =>{
      $.ajax({
        url:this._url,
        method:'get',
        data:datosAjax,
        success:data =>resolve(data),
        error:err=>reject(err)
      });
    });
  }
  
  listarLineas(){
    let datosAjax;
    datosAjax = {action:'cargar_lineas',idcompra:this._idcompra};
    return new Promise((resolve,reject) =>{
      $.ajax({
        url:this._url,
        method:'get',
        data:datosAjax,
        success:data =>resolve(data),
        error:err=>reject(err)
      });
    });
  }
  
  cargarEncabezado() {
    let datosAjax;
    datosAjax = {action:'cargar_encabezado',idcompra:this._idcompra};
    return new Promise((resolve,reject) =>{
      $.ajax({
        url:this._url,
        method:'get',
        data:datosAjax,
        success:data =>resolve(data),
        error:err=>reject(err)
      });
    });
  }
  
  
  displayImporte() {
    let datosAjax;
    datosAjax = {action:'display_importe',idcompra:this._idcompra};
    return new Promise((resolve,reject) =>{
      $.ajax({
        url:this._url,
        method:'get',
        data:datosAjax,
        success:data =>resolve(data),
        error:err=>reject(err)
      });
    });
  }
  
  get idlin() {
    return this._idlin;
  }
  get idcompra() {
    return this._idcompra;
  }
  get idarticulo() {
    return this._idarticulo;
  }
  get cantidad() {
    return this._cantidad;
  }
  get pvplin() {
    return this._pvplin;
  }
  set idlin(newIdLin){
    this._idlin = newIdLin;
  }
  set idcompra(newNumCompra) {
    this._idcompra = newNumCompra;
  }
  set idarticulo(newArticulo) {
    this._idarticulo = newArticulo;
  }
  set cantidad(newCantidad) {
    this._cantidad = newCantidad;
  }
  set pvplin(newPvpLin) {
    this._pvplin = newPvpLin;
  }
}

class Proveedores{
  constructor(idproveedor,razon_social,cif, direccion, telf,movil,FAX, codpost, poblacion, provincia, email,webpage){
    this._idproveedor = idproveedor;
    this._razon_social = razon_social;
    this._cif = cif;
    this._direccion = direccion;
    this._telf = telf;
    this._movil = movil;
    this._FAX = FAX;
    this._codpost = codpost;
    this._poblacion = poblacion;
    this._provincia = provincia;
    this._email =email ;
    this._webpage =webpage ;
    this._url='php/ajax_compras.php';
  }
  
  listarAutocomplete(request){
    let datosAjax;
    datosAjax={action:'rellena_combo', q:request};
    return new Promise((resolve,reject) =>{
      $.ajax({
        url:this._url,
        method:'get',
        data:datosAjax,
        success:data =>resolve(data)
        ,
        error:error => reject(error)
      });
    });  
  }
  
  selectAutoComplete(idProv){
    let datosAjax;
    datosAjax={action:'get_idproveedor',id:idProv};
    return new Promise((resolve,reject) =>{
      $.ajax({
        url:this._url,
        method:'get',
        data:datosAjax,
        success:data =>resolve(data)
        ,
        error:error => reject(error)
      });
    });  
  }
}

class Compras {
    constructor(idcompra,numcompra, fecha, idproveedor, iva, sfactura, deducible, notas ) {
      this._idcompra = idcompra;
      this._numcompra = numcompra;
      this._fecha = fecha;
      this._idproveedor = idproveedor;
      this._iva = iva;
      this._sfactura = sfactura;
      this._deducible = deducible;
      this._notas = notas;
// esta constante url será comun a la llamada de todos los ajax.
      this._url = 'php/ajax_compras.php';
    }
  
    comprobarIdCompra(){
      let datosAjax;
      datosAjax = {action:'comprobar_numcompra',numCompra:this._numcompra,fecha:this._fecha,deducible:this._deducible};
      return new Promise((resolve,reject) =>{
        $.ajax({
          url:this._url,
          method:'get',
          data:datosAjax,
          success:data =>{
            console.log(data);
            resolve(data);
          }
        });
      });
    }
    
    insertarCompra() {
// devuelve una promesa, ejecuta el ajax, y cuando está resuelto devuelve true, y después en funciones se procesará según sea true o false.
      let datosAjax;
      datosAjax = {action:'insertar_compra',numCompra:this._numcompra,fecha:this._fecha,idproveedor:this._idproveedor,iva:this._iva,sfactura:this._sfactura,deducible:this._deducible,notas:this._notas};
      return new Promise((resolve,reject) =>{
        $.ajax({
          url:this._url,
          method:'post',
          data:datosAjax,
          success:data =>{
            resolve(data);
          }
        });
      });
    }
    
    cargarNumCompra(){
      let datosAjax;
      let action = 'num_compra';
      datosAjax= {action:action,fecha:this._fecha,deducible:this._deducible};
      return new Promise((resolve,reject) =>{
        $.ajax({
          url:this._url,
          method:'post',
          data:datosAjax,
          success:data =>{
            resolve(data);
          }
        });
      });
    }
    
    editarCompra(){
      let datosAjax;
      let action = 'editar_compra';
      datosAjax = {action:action,id:this._idcompra,numCompra:this._numcompra,fecha:this._fecha,idproveedor:this._idproveedor,iva:this._iva,sfactura:this._sfactura,deducible:this._deducible,notas:this._notas};
      return new Promise((resolve,reject) =>{
        $.ajax({
          url:this._url,
          method:'post',
          data:datosAjax,
          success:data =>{
            resolve(data);
          }
        });
      });
    }
  
    eliminarCompra(){
      let datosAjax;
      let action = 'eliminar_compra';
      datosAjax = {action:action,id:this._idcompra};
      return new Promise((resolve,reject) =>{
        $.ajax({
          url:this._url,
          method:'get',
          data:datosAjax,
          success:data =>{
            resolve(data);
          }
        });
      });
    }
  
  cargarPaginacion(search){
    let datosAjax;
    let action = 'cargar_paginacion2';
    datosAjax = {action:action,deducible:this._deducible, search:search};
    return new Promise((resolve,reject) =>{
      $.ajax({
        url:this._url,
        method:'get',
        data:datosAjax,
        success:data =>{
          resolve(data);
        }
      });
    });
  }

  cargarTablaCompra(search){
    let datosAjax;
    let action = 'cargar_compras';
    datosAjax = {action:action,deducible:this._deducible, search:search};
    return new Promise((resolve,reject) =>{
      $.ajax({
        url:this._url,
        method:'get',
        data:datosAjax,
        success:data =>{
          resolve(data);
        }
      });
    });
  }

  cargarTablaPaginacion(search,pagina){
    let datosAjax;
    let action = 'actualiza_pagina';
    datosAjax = {action:action,deducible:this._deducible, search:search,pagina:pagina};
    return new Promise((resolve,reject) =>{
      $.ajax({
        url:this._url,
        method:'get',
        data:datosAjax,
        success:data =>{
          resolve(data);
        }
      });
    });
  }
  
  get idcompra() {
    return this._idcompra;
  }
  get numcompra() {
    return this._numcompra;
  }
  get fecha() {
    return this._fecha;
  }
  get idproveedor() {
    return this._idproveedor;
  }
  get iva() {
    return this._iva;
  }
  get sfactura() {
    return this._sfactura;
  }
  get deducible() {
    return this._deducible;
  }
  set idcompra(newIdCompra){
    this._idcompra = newIdCompra;
  }
  set numcompra(newNumCompra) {
    this._numcompra = newNumCompra;
  }
  set fecha(newFecha) {
    this._fecha = newFecha;
  }
  set idproveedor(newProveedor) {
    this._idproveedor = newProveedor;
  }
  set iva(newIva) {
    this._iva = newIva;
  }
  set sfactura(newSFactura) {
    this._sfactura = newSFactura;
  }
  set deducible(newDeducible) {
    this._deducible = newDeducible;
  }
  set notas(newNota) {
    this._notas = newNota;
  }
}