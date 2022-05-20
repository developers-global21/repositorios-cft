<?php
$con = mysqli_connect("localhost", "root", "Ruvae200");
if ($con) {
    $idCategoria = $_REQUEST['idcategoria'];
    $idProceso = $_REQUEST['idproceso'];
    $sql = "select * from repositorio.subproceso where categoria_id='" . $idCategoria . "' and subcategoria_id='" . $idProceso . "'";
    //echo ($sql . "<br>");
    $reg = mysqli_query($con, $sql);
    if ($reg) {
?>
        <script language="javascript" type="text/javascript">
            var x = document.getElementById('subproceso').options.length;
            if (x > 1) {
                document.getElementById('subproceso').length = 1;
            }
        </script>
        <?php
        $filas = mysqli_num_rows($reg);
        if ($filas > 0) {


            for ($i = 0; $i < $filas; $i++) {
                $arreglo = mysqli_fetch_row($reg);
                /*
                00 id	int(11) AI PK
                01 categoria_id	int(11)
                02 subcategoria_id	int(11)
                03 nombre	varchar(255)
                04 directorio	text
                */
                $id = trim($arreglo[0]);
                $nombre = utf8_encode($arreglo[3]);

        ?>
                <script>
                    document.getElementById('subproceso').options[<?php echo (($i + 1)) ?>] = new Option('<?php echo ($nombre) ?>', '<?php echo ($id) ?>', false, true);
                </script>
            <?php
            }
        } else {
            ?>
            <script language="javascript">
                $('#myModal4').modal()
            </script>
        <?php
        }
    } else {
        ?>
        <script language="javascript">
            $('#myModal3').modal()
        </script>
    <?php
    }
} else {
    ?>
    <script language="javascript">
        $('#myModal2').modal()
    </script>
<?php
}
