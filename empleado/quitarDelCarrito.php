<?php
if(!isset($_GET["indice"])) return;
$indice = $_GET["indice"];

session_start();
array_splice($_SESSION["carrito1"], $indice, 1);
header("Location: ./vender.php?status=3");
?>