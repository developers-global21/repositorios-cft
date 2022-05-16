<?php
$con = mysqli_connect("localhost", "root", "Ruvae200");
if ($con) {
    $idCategoria = $_REQUEST['idcategoria'];
    $sql = "select * from repositorio.subcategoria where categoria_id='" . $idCategoria . "'";
    echo ($sql . "<br>");
    $reg = mysqli_query($con, $sql);
    if ($reg) {
?>
        <script language="javascript" type="text/javascript">
            var x = document.getElementById('subproceso').options.length;
            if (x > 1) {
                document.getElementById('subproceso').length = 1;
            }
            var x = document.getElementById('proceso').options.length;
            if (x > 1) {
                document.getElementById('proceso').length = 1;
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
                02 nombre	varchar(255)
                03 directorio	text
                */
                $id = trim($arreglo[0]);
                $nombre = utf8_encode($arreglo[2]);

        ?>
                <script>
                    document.getElementById('proceso').options[<?php echo (($i + 1)) ?>] = new Option('<?php echo ($nombre) ?>', '<?php echo ($id) ?>', false, true);
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
