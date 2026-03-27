<?php

ini_set('display_errors', 0); 
error_reporting(E_ALL);

function gestorExcepciones($e) {
   
    $mensaje = $e->getMessage();
    $codigo_sql = $e->getCode(); 
    
   
    $columna_afectada = '';
    $tipo_error = 'general';

   
    if ($codigo_sql == 23000 && strpos($mensaje, '1062') !== false) {
        $tipo_error = '1062';
    }
    
   
    elseif (strpos($mensaje, 'Data truncated') !== false || strpos($mensaje, 'Incorrect value') !== false) {
        $tipo_error = 'valor_invalido';
  
        if (preg_match("/column '(.+?)'/", $mensaje, $coincidencias)) {
            $columna_afectada = $coincidencias[1]; 
        }
    }


    elseif ($codigo_sql == 2002) {
        $tipo_error = 'db_connection';
    }


    $destino = "error.php?error=$tipo_error";
    if (!empty($columna_afectada)) {
        $destino .= "&col=$columna_afectada";
    }
    

    header("Location: $destino");
    exit;
}

set_exception_handler('gestorExcepciones');


$host = 'localhost';
$dbname = 'BaseDatos_LLC';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
  
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES'"); 
} catch (PDOException $e) {
    gestorExcepciones($e);
}
?>