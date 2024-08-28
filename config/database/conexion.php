<?php
	require("../config/config.php");
	
	try {
		$conexion = new PDO("mysql:host=" . BD_HOST . "; dbname=" . BD_NAME, BD_USER, BD_PASS);
		$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$conexion-> exec("SET CHARACTER SET utf8");		

	} catch (Exception $e) {
		//die("Linea de erro ") . $e->getMessage();
		echo "Error de conexión: " . $e->getMessage();
	}