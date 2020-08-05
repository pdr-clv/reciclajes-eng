<?php

  function convertir_fecha($fecha) {
    $fecha = date_create($fecha);
    $fecha_devol = date_format($fecha, 'd/m/Y');
    return $fecha_devol;
  }

  function convertir_precio($valor) {
    $precio_devol = number_format($valor,2,",",".");
    
    return $precio_devol;
  }

  function convertir_cantidad($valor) {
    $cantidad_devol = str_replace('.',',',(string)round($valor,3));
    
    return $cantidad_devol;
  }

  function convertir_num_factura($num, $fecha) {
    $num_devol=$num. "/". date("y",strtotime($fecha));
    return $num_devol;
  }

  