<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$ruta = __DIR__;

function obtener_estructura_directorios($ruta)
{
    // Se comprueba que realmente sea la ruta de un directorio
    if (is_dir($ruta)) {
        // Abre un gestor de directorios para la ruta indicada
        $gestor = opendir($ruta);
        echo "<ul>";

        // Recorre todos los elementos del directorio
        while (($archivo = readdir($gestor)) !== false) {

            $ruta_completa = $ruta . "/" . $archivo;

            // Se muestran todos los archivos y carpetas excepto "." y ".."
            if ($archivo != "." && $archivo != ".." && $archivo != 'index.php') {
                // Si es un directorio se recorre recursivamente
                if (is_dir($ruta_completa)) {
                    echo "<li>" . $archivo . "</li>";
                    obtener_estructura_directorios($ruta_completa);
                } else {
                    echo "<li><a href='$archivo' target='_blank'>" . $archivo . "</a></li>";
                }
            }
        }

        // Cierra el gestor de directorios
        closedir($gestor);
        echo "</ul>";
    } else {
        echo "No es una ruta de directorio valida<br/>";
    }
}
$idCategoria = $_REQUEST['idcategoria'];
if (is_set($_REQUEST['idproceso'])) {
    $idproceso = $_REQUEST['idproceso'];
} else {
    $idproceso = 0;
}
if (is_set($_REQUEST['idsubproceso'])) {
    $idsubproceso = $_REQUEST['idsubproceso'];
} else {
    $idsubproceso = 0;
}
if (is_set($_REQUEST['idperiodo'])) {
    $idperiodo = $_REQUEST['idperiodo'];
} else {
    $idperiodo = 0;
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Bienvenido
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/fav.png" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
</head>

<body>
    <div class='container-fluid'>
        <div class='row'>&nbsp;</div>
        <div class="card">
            <div class="card-header text-center">
                <h3>Documentos de Calidad</h3>
            </div>
            <div class="card-body">
                <div class='row'>
                    <div class='col-12 col-md-6 col-lg-6 text-left'>
                        <?php obtener_estructura_directorios($ruta); ?>
                    </div>
                </div>
            </div>
        </div>
</body>

</html>