<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

$ruta = __DIR__;
$ruta_base = $_SERVER['DOCUMENT_ROOT'];
$puerto = $_SERVER['SERVER_PORT'];
$server_name = $_SERVER['SERVER_NAME'];
switch ($puerto) {
	case "80":
		$ruta_servidor = "http://" . $server_name . "/";
		break;
	case "8000":
		$ruta_servidor = "http://" . $server_name . ":" . $puerto . "/";
		break;
	case "443":
		$ruta_servidor = "https://" . $server_name .  "/";
		break;
}

$idCategoria = $_REQUEST['idcategoria'];

if (isset($_REQUEST['idproceso'])) {
	if ($_REQUEST['idproceso'] != '-99') {
		$idProceso = $_REQUEST['idproceso'];
	} else {
		$idProceso = '0';
	}
} else {
	$idProceso = '0';
}

if (isset($_REQUEST['idsubproceso'])) {
	if ($_REQUEST['idsubproceso'] != '-99') {
		$idSubproceso = $_REQUEST['idsubproceso'];
	} else {
		$idSubproceso = '0';
	}
} else {
	$idSubproceso = '0';
}

if (isset($_REQUEST['idperiodo'])) {
	if ($_REQUEST['idperiodo'] != '-99') {
		$idPeriodo = $_REQUEST['idperiodo'];
	} else {
		$idPeriodo = '0';
	}
} else {
	$idPeriodo = '0';
}
require_once 'conexion.php';

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


	<script language='javascript' type='text/javascript'>
		function regresa() {
			document.location = 'https://registroscft.global21.cl/dashboard/';
		}

		function filtrar() {
			$('#myModal').modal()
			var idcategoria = document.getElementById('categoria').value;
			var idproceso = document.getElementById('proceso').value;
			var idsubproceso = document.getElementById('subproceso').value;
			var idperiodo = document.getElementById('periodo').value;
			window.location.href = "index.php?idcategoria=" + idcategoria + "&idproceso=" + idproceso + "&idsubproceso=" + idsubproceso + "&idperiodo=" + idperiodo;
		}

		function Limpiar(idcategoria) {
			window.location.href = "index.php?idcategoria=" + idcategoria;
		}

		function cambio1() {
			var idcategoria = document.getElementById('categoria').value;
			window.$("#div001").load("busca_procesos.php?idcategoria=" + idcategoria);
		}

		function cambio() {
			var idcategoria = document.getElementById('categoria').value;
			var idproceso = document.getElementById('proceso').value;
			window.$("#div001").load("busca_subprocesos.php?idcategoria=" + idcategoria + "&idproceso=" + idproceso);
		}
	</script>
</head>

<body>
	<div class='container-fluid'>
		<div class='row' style="background-color:#14626f !important"><img src="../../../assets/images/logo2.png" class="img-thumbnail" width="100px" height="auto">
			&nbsp;<h3 class='text-white'><a href='javascript:regresa()'>Regresar</a></h3>
		</div>
		<div class="card">
			<div class="card-header text-center">
				<h3>Documentos de Calidad</h3>
			</div>
			<div class="card-body">
				<div class='row'>
					<div class='col-12 col-md-3 col-lg-3 text-center'>
						<div class='input-group'>
							<label class='input-group-text'>Categoría</label>
							<select class='form-control' id='categoria' name='categoria' onChange="javascript:cambio1()">
								<option value='-99'>Seleccione</option>
								<?php
								if ($con) {
									$sql = "select * from repositorio.categoria";
									$reg = mysqli_query($con, $sql);
									if ($reg) {
										$filas = mysqli_num_rows($reg);
										if ($filas > 0) {
											while ($registro = mysqli_fetch_array($reg)) {
												$idcategoria = $registro['id'];
												$nombrecategoria = utf8_encode($registro['nombre']);
												if ($idcategoria == $idCategoria) {
													echo "<option value='$idcategoria' selected>$nombrecategoria</option>";
												} else {
													echo "<option value='$idcategoria'>$nombrecategoria</option>";
												}
											}
										} else {
											echo "<option value='0'>No hay Categorías en la BD</option>";
										}
									} else {
										echo "<option value='-99'>No se conectó al servidor de datos</option>";
									}
								}
								?>
							</select>
						</div>
					</div>

					<div class='col-12 col-md-3 col-lg-3 text-center'>
						<div class='input-group'>
							<label class='input-group-text'>Proceso</label>
							<select class='form-control' id='proceso' name='proceso' onChange="javascript:cambio()">
								<option value='-99'>Seleccione</option>
								<?php
								if ($con) {
									$sql = "select * from repositorio.subcategoria where categoria_id=$idCategoria";
									$reg = mysqli_query($con, $sql);
									if ($reg) {
										$filas = mysqli_num_rows($reg);
										if ($filas > 0) {
											while ($registro = mysqli_fetch_array($reg)) {
												$idproceso = $registro['id'];
												$nombreproceso = utf8_encode($registro['nombre']);
												if ($idproceso == $idProceso) {
													echo "<option value='$idproceso' selected>$nombreproceso</option>";
												} else {
													echo "<option value='$idproceso'>$nombreproceso</option>";
												}
											}
										} else {
											echo "<option value='0'>No hay Procesos en la BD</option>";
										}
									} else {
										echo "<option value='-99'>No se conectó al servidor de datos</option>";
									}
								}
								?>
							</select>
						</div>
					</div>

					<div class='col-12 col-md-2 col-lg-2 text-center'>
						<div class='input-group'>
							<label class='input-group-text'>Subproceso</label>
							<select class='form-control' id='subproceso' name='subproceso'>
								<option value='-99'>Seleccione</option>
							</select>
						</div>
					</div>

					<div class='col-12 col-md-2 col-lg-2 text-center'>
						<div class='input-group'>
							<label class='input-group-text'>Periodo</label>
							<select class='form-control' id='periodo' name='periodo'>
								<option value='-99'>Seleccione</option>
								<?php
								if ($con) {
									$sql = "select * from repositorio.periodo ";
									$reg = mysqli_query($con, $sql);
									if ($reg) {
										$filas = mysqli_num_rows($reg);
										if ($filas > 0) {
											while ($registro = mysqli_fetch_array($reg)) {
												$idperiodo = $registro['id'];
												$nombreperiodo = utf8_encode($registro['nombre']);
												if ($idperiodo == $idPeriodo) {
													echo "<option value='$idperiodo' selected>$nombreperiodo</option>";
												} else {
													echo "<option value='$idperiodo'>$nombreperiodo</option>";
												}
											}
										} else {
											echo "<option value='0'>No hay Periodos en la BD</option>";
										}
									} else {
										echo "<option value='-99'>No se conectó al servidor de datos</option>";
									}
								}
								?>
							</select>
						</div>
					</div>
					<div class='col-12 col-md-2 col-lg-2 text-center'>
						<div class='input-group'>
							<input type='button' class='btn btn-primary' value='Filtrar' onclick='javascript:filtrar()'>
							<input type='button' class='btn btn-secondary' value='Limpiar' onclick="javascript:Limpiar('<?php echo ($idCategoria) ?>')">
						</div>
					</div>

				</div>
			</div>

			<div class='row'>
				<div class='col-12 col-md-12 col-lg-12 text-left'>
					<table class='table table-bordered table-striped'>
						<thead class='text-center'>
							<th>Nombre del Documento</th>
							<th class='  d-none  d-md-none  d-lg-none d-xl-table-cell'>Categoria</th>
							<th class='  d-none  d-md-none  d-lg-none d-xl-table-cell'>Proceso</th>
							<th class='  d-none  d-md-none  d-lg-none d-xl-table-cell'>Subproceso</th>
							<th>Periodo</th>
							<th class='  d-none  d-md-none  d-lg-none d-xl-table-cell'>HASH (SHA256)</th>
						</thead>
						<tbody>
							<?php
							if ($con) {
								$sql = "select * from repositorio.consulta_registros where categoria_id='" . $idCategoria . "'";
								if ($idProceso != '0') {
									$sql .= " and subcategoria_id='" . $idProceso . "'";
								}
								if ($idSubproceso != '0') {
									$sql .= " and subproceso_id='" . $idSubproceso . "'";
								}
								if ($idPeriodo != '0') {
									$sql .= " and periodo_id='" . $idPeriodo . "'";
								}
								//echo ($sql . "<br>");
								$reg = mysqli_query($con, $sql);
								if ($reg) {
									$filas = mysqli_num_rows($reg);
									if ($filas > 0) {
										while ($registro = mysqli_fetch_array($reg)) {
											/*
												id	int(11)
												nombre_registro	varchar(255)
												categoria_id	int(11)
												subcategoria_id	int(11)
												subproceso_id	int(11)
												periodo_id	int(11)
												url	text
												nombre_categoria	varchar(255)
												nombre_proceso	varchar(255)
												nombre_subproceso	varchar(255)
												nombre_periodo	varchar(255)
											*/
											$iddocumento = $registro['id'];
											$nombredocumento = utf8_encode($registro['nombre_registro']);
											$categoria = utf8_encode($registro['nombre_categoria']);
											$proceso = utf8_encode($registro['nombre_proceso']);
											$subproceso = utf8_encode($registro['nombre_subproceso']);
											$periodo = utf8_encode($registro['nombre_periodo']);
											$hash = '';
											$link = $ruta_servidor . $registro['url'];
											$directorio = $ruta_base .  $registro['url'];
											if (file_exists($directorio)) {
												$hash = hash_file('sha256', $directorio);
											}
											echo "<tr>";
											echo "<td><a href='$link' target='_blank'>$nombredocumento</a></td>";
											echo "<td class='  d-none  d-md-none  d-lg-none d-xl-table-cell'>$categoria</td>";
											echo "<td class='  d-none  d-md-none  d-lg-none d-xl-table-cell'>$proceso</td>";
											echo "<td class='  d-none  d-md-none  d-lg-none d-xl-table-cell'>$subproceso</td>";
											echo "<td class='text-center'>$periodo</td>";
											echo "<td class='  d-none  d-md-none  d-lg-none d-xl-table-cell'>$hash</td>";
											echo "</tr>";
										}
									} else {
										echo "<tr><td colspan='6' class='text-left'>&nbsp;</tr>";
									}
								} else {
									echo "<tr><td colspan='6' class='text-left text-danger>No se pudo ejecutar la consulta</td></tr>";
								}
							} else {
							?>
								<tr>
									<td colspan='6' class='text-danger'>No se pudo conectar a la BD</td>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>

				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="myModal" role="dialog">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 id="title_modal" class="modal-title">Espere por favor</h5>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<p id="content_modal" align="center"><img src="<?php echo ($ruta_servidor . "/assets/images/") ?>wait2.gif" width="50" height="50"></p>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="myModal2" role="dialog">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 id="title_modal" class="modal-title">Atención</h5>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<p id="content_modal" align="left" class='text-danger'>No se pudo conectar a la Base de Datos, por favoir inténtelo luego</p>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="myModal3" role="dialog">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 id="title_modal" class="modal-title">Atención</h5>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<p id="content_modal" align="left" class='text-danger'>No se pudo consultar la Base de Datos por favor intentelo luego</p>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="myModal4" role="dialog">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 id="title_modal" class="modal-title">Atención</h5>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<p id="content_modal" align="left">No se consiguieron Subprocesos asociados a este Proceso, por favor cambie la combinación de los filtros</p>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="myModal5" role="dialog">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 id="title_modal" class="modal-title">Atención</h5>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<p id="content_modal" align="left" class='text-danger'>No se consiguieron Procesos asociados a esta Categoria, por favor cambie la combinación de los filtros</p>
				</div>
			</div>
		</div>
	</div>
	<?php
	$ccc = 'hidden'
	?>
	<div id='div001' style='visibility:<?php echo ($ccc) ?>; background-color:aquamarine'>Hola</div>
</body>

</html>