 <?php

// Create connection
$mysqli = new mysqli("sql7.freemysqlhosting.net", "sql7358389", "QiuasbvbjI", "sql7358389");
// Check connection
if ($mysqli->connect_error) {
    die("Error conectando con base de datos: " . $mysqli->connect_error);
}
/* Comento el printf que es solo para ver el mensaje de que se ha conectado con exito. */
// printf("Conectado con exito al servidor %s\n", $mysqli->server_info); 

?>


