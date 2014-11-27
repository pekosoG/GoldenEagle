<?php
date_default_timezone_set('America/Mexico_City');
if(isset($_GET)!==0){
	if(isset($_GET['accion'])!==0){
		if(preg_match('/^[a-zA-Z]+$/', $_GET['accion'])!==0){
			require_once('ctrl/ctrlUsuario.php');
			$user = new ctrlUsuario();
			$user->elAJAX(); 	
		} 
		else echo 'Error: no regex GET'; 
	}
	else echo 'Error: no mijo';
}
else echo 'Error: no get';

?>