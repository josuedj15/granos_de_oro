<?php

session_start();

unset($_SESSION["carrito1"]);
$_SESSION["carrito1"] = [];

header("Location: ./vender.php?status=2");
?>