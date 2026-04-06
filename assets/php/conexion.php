 <!--/**
 * @Estadia numero: 5
 * @Salvador Humberto Cruz Villafuerte - delta9621 Citlali Solano Diaz
 * @date 2025-04-30
 * @version 1.0
 */-->
 
<?php 

    $servidor = "localhost";
    $usuario = "root";
    $password = "delta9621";
    $base_datos = "alivio";

    $conexion = new mysqli($servidor, $usuario, $password, $base_datos);

            if($conexion->connect_error){
                die("Error de conexion: " . $conexion->connect_error);
            }else{
               
            }

    $conexion->set_charset("utf8")
?>
