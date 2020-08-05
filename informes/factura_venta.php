<?php
// se instancia fpdf y conectar a base de datos
require('fpdf/fpdf.php');
require ('../php/conectar.php');
//se llama al fichero funciones_conversiones, para que le de formato correcto a la fecha, num_factura, precio, cantidad obtenidos de la consulta de base de datos
require('../php/fun_conversiones.php');
define('EURO',chr(128));

// este parametro lo pasamos con un get en el href, para filtrar la venta a imprimir factura.
$ventafiltro = $_GET['idventa'];


// se harán 3 consultas (por no hacer una consulta anidada muy grande) con información de líneas de venta sql_lin, información de encabezado de venta sql_encabezado, y con información del proveedor sql_cliente.
$sql_lin = "SELECT linventas.idlinventa AS idlinventa, linventas.idventa AS idventa, articulos.articulo AS idarticulo,articulos.descripcion AS descripcion,linventas.cantidad AS cantidad,linventas.pvplin AS importe FROM linventas INNER JOIN articulos ON linventas.idarticulo = articulos.idarticulo WHERE linventas.idventa = $ventafiltro";
$resultado_lin = $mysqli->query($sql_lin);
// calculo el total_lineas en la factura, para después hacer calculos de donde hacer el corte para página 2, 3, etc.
$total_lineas =  $resultado_lin ->num_rows;
$sql_encabezado = "SELECT * FROM ventas WHERE idventa =  $ventafiltro";
$resultado_encabezado = $mysqli -> query($sql_encabezado);
$datos_encabezado = $resultado_encabezado -> fetch_assoc();
$idcliente = $datos_encabezado['idcliente'];
$sql_cliente = "SELECT razon_social, cif, direccion, codpost, poblacion, provincia FROM clientes WHERE idcliente =  $idcliente"; 

$resultado_cliente = $mysqli -> query($sql_cliente);
$datos_cliente = $resultado_cliente -> fetch_assoc();

$iva = $datos_encabezado['iva'];
$notas = $datos_encabezado['notas'];
// las siguientes variables se pasaron como globals al encabezado.
$fecha = $datos_encabezado['fecha'];
$numventa = $datos_encabezado['numventa'];
// datos cliente, también se pasará como global al encabezado
$texto_cliente=$datos_cliente['razon_social']."\nCIF: ".$datos_cliente['cif']."\n".$datos_cliente['direccion']."\n".$datos_cliente['poblacion']."\n".$datos_cliente['codpost']." ".$datos_cliente['provincia'];

// llamando desde la clase $GLOBALS['nombre de variable sin dolar'] se pueden utilizar estos datos desde la clase Header()

class PDF extends FPDF
{
function Header() {
  $this->Image('../img/logo.png',15,15,'PNG');
  // después se posiciona en 140,20 y se escribe FACTURA
  $this->SetFont('Arial','BU',20);
  $this->SetXY(140,20);
  $this->SetTextColor(15, 82, 0);
  //Cell(ancho,alto,'texto',0 o 1 si quieres bordes tambien puede ser B T L R donde lo quieres, 0 o 1 espacio del Ln, 'C' disposición del texto, 0 o 1 si quieres que tenga fill)
  $this->Cell(50,10,'FACTURA',0,1,'C');
  $this->Ln();
  $this->SetFont('Arial','BU',9);
  $this->SetX(30);
  $this->Cell(65,5,'FRANCISCO JOSE CATALAN MAS',0,1,'C');
  $this->SetXY(140,40);
  $this->SetFont('Arial','B',10);
  //SetFillColor color de relleno celda
  $this->SetFillColor(27, 139, 2);
  //SetTextColor color del texto
  $this->SetTextColor(255, 255, 255);
  //SetDrawColor color de los bordes
  $this->SetDrawColor(255, 255, 255);
  $this->Cell(25,7,utf8_decode('Factura Nº:'),1,1,'R',1);
  $this->SetXY(165,40);
  $this->SetFillColor(230,  230,  230);
  $this->SetTextColor(0, 0, 0);
  $this->SetFont('Courier','',10);
  // del array asociativo datos_encabezado, nos quedaremos con los valores numventa y fecha, que los pasaremos a las variables $fecha y $numventa. Tambien rescataremo el valor notas para mas adelante escribir notas en la factura.
  $this->Cell(25,7,convertir_num_factura($GLOBALS['numventa'],$GLOBALS['fecha']),1,1,'L',1);
  $this->Ln(-2);
  $this->SetX(30);
  $this->SetTextColor(15, 82, 0);
  $this->SetFont('Arial','B',8);
  $this->Cell(65,5,'CIF:48310352W',0,1,'C');
  $this->SetXY(140,47);
  $this->SetFont('Arial','B',10);
  $this->SetFillColor(27, 139, 2);
  $this->SetTextColor(255, 255, 255);
  $this->Cell(25,7,utf8_decode('Fecha:'),0,1,'R',1);
  $this->SetXY(165,47);
  $this->SetTextColor(0, 0, 0);
  $this->SetFont('Courier','',10);
  $this->SetFillColor(230,  230,  230);
  $this->Cell(25,7,convertir_fecha($GLOBALS['fecha']),0,1,'L',1);
  $this->Ln(-4);
  $this->SetX(30);
  $this->SetFont('Arial','',8);
  $texto_fran="VIA FLAMINIA 6 (POBLA DE VALLBONA)"."\n46185 VALENCIA"."\nTEL: 600452352";
  $this->MultiCell(65,4,$texto_fran,0);
  $this->SetXY(110,55);
  $this->SetFont('Arial','B',10);
  $this->SetTextColor(15, 82, 0);
  $this->SetDrawColor(15, 82, 0);
  $this->SetLineWidth(0.5);
  $this->Cell(88,7,'Cliente:','B',0,'L');
  $this->SetXY(110,62);
  $this->SetLineWidth(0.2);
  $this->Ln(20);
  $this->SetTextColor(0, 0, 0);
  $this->SetXY(110,62);
  $this->SetFont('Courier','',9);
  $this->MultiCell(88,5,$GLOBALS['texto_cliente'],0);
  $this->Ln();
  //damos un pequeño setX para que no quede muy pegado a la izquierda, ya que estas facturas pueden ser archivadas, y siempre necesitan un pequeño margen en la izquierda
  $this->SetX(15);
  //tabla ventas, encabezados.
  $this->SetFillColor(27, 139, 2);
  $this->SetTextColor(255, 255, 255);
  $this->SetFont('Arial','B',10);
  $this->Cell(21,10,'Articulo',0,0,'C',1);
  $this->Cell(95,10,'Descripcion',0,0,'C',1);
  $this->Cell(20,10,'Cantidad',0,0,'C',1);
  $this->Cell(23,10,'Precio',0,0,'C',1);
  $this->Cell(25,10,'Importe',0,0,'C',1);
  $this->Ln(); 
  }
  function Footer()
{
    $this->SetY(-15);
    $this->SetFont('Arial','I',8);
    $this->Cell(0,10,'Continua .....'.utf8_decode('Página ').$this->PageNo().' de {nb}',0,0,'R');
// si se hace así, la última página, coge color blanco el caracter, y este mensaje no sale para la última hoja.
}
}
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Courier','',8);
$subtotal = 0;
$contador_lineas = 0;
//$limite_lineas es contador de si solo caben 24 lineas + subtotales, si existe campo de notas, se deja que sólo entren 19 lineas por página
$limite_lineas = ($notas) ? 19 : 23;
// $min_lineas_next_page son las líneas minimas que se dejarán para que quepa el subtotal y el campo notas/observaciones en la siguiente página, si no quedan estas lineas, se hace corte, si quedan mas, no se hace corte, y se llegará hasta 29 lineas.
$min_lineas_next_page = ($notas) ? 11 : 6;
// el numero de lineas restantes de la factura, se va descontando cada vez que hace un salto de página, es util para ver donde se hará el corte de página. el valor inicial, obviamente es $total_lineas, calculado ya anteriormente.
$lineas_restantes = $total_lineas;
//recorremos todas las lineas que hay en la factura, hemos creado las variables subtotal y num_lineas, para luego imprimir y configurar el alto de la página
if ($resultado_lin) {
  while ($datos_lin = $resultado_lin -> fetch_assoc()){
    
// al comienzo de este archivo, se ha ejecutado un sql que da el resultado de las lineas, se llama resultado_lin, de aquí se obtendrá un array asociativo llamado datos_lin.
    
    $articulo = $datos_lin['idarticulo'];
    //descripcion admite 52 caracteres, hay que capar esto en la generación de referencias
    $descripcion = $datos_lin['descripcion'];
    $cantidad = $datos_lin['cantidad'];
    $precio = $datos_lin['importe']/$datos_lin['cantidad'];
    $importe = $datos_lin['importe'];
    
    $subtotal = $subtotal+$importe;
    $contador_lineas = $contador_lineas +1;
    
    // si $contador_lineas=19 (con campo observaciones (4 lineas y con los subtotales 5 lineas)) llega al tope de $limite_lineas, miramos a ver cuantas lineas quedan para entrar en la siguiente hoja. Si son mas lineas que 5 que son las que se dejan para que quepa los datos de los subtotales, o si son mas de 5+4 = 9 contando que exise observaciones, se puede llegar hasta el final de la página a 29
    if ($contador_lineas == $limite_lineas) {
      if ($limite_lineas == 29){
        $pdf->AddPage();
        $lineas_restantes = $lineas_restantes - 1 - $min_lineas_next_page;
        $contador_lineas = 0;
        $limite_lineas = ($notas) ? 19 : 23;
      } else {
        $lineas_restantes = $lineas_restantes - $contador_lineas;
        if ($lineas_restantes < $min_lineas_next_page) {
          $pdf->AddPage();
          $contador_lineas = 0;
        } else {
          $limite_lineas = 29;
        }
      }
    }
    
    $pdf->SetX(15);
    $pdf->Cell(21,6,$articulo,'B',0,'C',0);
    $pdf->Cell(95,6,$descripcion,'B',0,'L',0);
    $pdf->Cell(20,6,convertir_cantidad($cantidad),'B',0,'R',0);
// la constante EURO está definida a principio de archivo, que es char(124), equivalente al simbolo de euro
    $pdf->Cell(23,6,convertir_precio($precio).EURO,'B',0,'R',0);
    $pdf->Cell(25,6,convertir_precio($importe).EURO,'B',0,'R',0);
    $pdf->Ln();
  } 
}
if ($notas) {
  $pdf->Ln(2);
  $pdf->SetX(45);
  $pdf->Cell(5,4,'Observaciones: ',0,0,'R',0);
  $pdf->MultiCell(85,5,utf8_decode($notas),1,'C',0);
  }

//calculamos cuantas lineas se han añadido, y cuanto hay que bajar para añadir los subtotales como mínimo al centro de la página. Si es mas de 10 líneas, los subtotales se desplazaran hacia abajo.
$desplazar_lineas = 10 - $contador_lineas;
if ($desplazar_lineas > 0) {
  $distacia = $desplazar_lineas*5;
  $pdf->Ln($distacia);
}
$pdf->Ln(2);
$pdf->SetDrawColor(15, 82, 0);
$pdf->SetLineWidth(0.7);
$pdf->SetX(15);
$pdf->Cell(184,2,'','BT',1,'L');
$pdf->SetLineWidth(0.2);
$pdf->SetTextColor(15, 82, 0);
$pdf->SetFont('Arial','',9);
$pdf->SetFillColor(230,  230,  230);
$pdf->Ln();
// el ancho es darle SetX15, y 184
//linea subtotal
$pdf->SetX(30);
// se define x e y, para que después de hacer el MultiCell, se haga un setXY a continuación, el motivo es que después de hacer un MultiCell, es que hace un Ln automatico, y no es posible escribir a continuación.
$x = $pdf -> GetX();
$y = $pdf -> GetY();
$pdf->MultiCell(80,8,utf8_decode('NOTA: SUJETO PASIVO DEL IMPUESTO EL
DESTINATARIO DE LA OPERACIÓN'),1,'C',0);
$pdf->SetXY($x + 80, $y);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(61,8,'Subtotal Factura:',0,0,'R');
$pdf->SetFont('Arial','',10);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(28,8,convertir_precio($subtotal).EURO,0,0,'R',1);
$pdf->Ln();
//linea iva
$pdf->SetTextColor(15, 82, 0);
$pdf->SetFont('Arial','B',10);
$pdf->SetX(15);
$pdf->Cell(144,8,'Iva:',0,0,'R');
$pdf->SetFont('Arial','',10);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(10,8,$iva.'%',0,0,'R',1);
$pdf->Cell(2,8,'',0,0,'R'); // este es un miniespacio
$pdf->Cell(28,8,'0,00'.EURO,0,0,'R',1);
$pdf->Ln(10);
$pdf->SetX(15);
$pdf->SetFillColor(27, 139, 2);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetLineWidth(0);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(156,10,'Total factura:',0,0,'R',1);
$pdf->Cell(28,10,convertir_precio($subtotal).EURO,0,0,'R',1);


$pdf->Output();
?>