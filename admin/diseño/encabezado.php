<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Granos</title>
	
	<link rel="stylesheet" href="./diseño/css/fontawesome-all.min.css">
	<link rel="stylesheet" href="./diseño/css/2.css">
	<link rel="stylesheet" href="./diseño/css/estilo.css">
</head>
<body>
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<a class="navbar-brand" href="../admin/editar_perfil.php">GdO</a>
			</div>
			<div id="navbar" class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
					<li><a href="../admin/listar.php">Productos</a></li>
					<li><a href="../admin/vender.php">Vender</a></li>
					<li><a href="../admin/ventas.php">Ventas</a></li>
					<li><a href="../admin/listar_usuarios.php">Administrar Usuarios</a></li>
					<li><a href="../admin/listar_almacenes.php">Administrar Almacenes</a></li>
					<li><a href="../admin/logout.php">Cerrar Sesion</a></li>
				</ul>
			</div>
		</div>
	</nav>
	
	