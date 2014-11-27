<?php
/**
 * Index.php de GoldenEagle
 * 
 * @author Pekas
 * @version 1.0
 */

date_default_timezone_set('America/Mexico_City');
if(isset($_POST['ctrl'])){
	if(preg_match('/^[a-z]+$/',$_POST['ctrl'])===0)
		die("Ctrl Rechazado!");
	else{
		if(strcmp($_POST['ctrl'],'usuario')===0){
			require_once('ctrl/ctrlUsuario.php');
			$ctrl= new ctrlUsuario();
			$ctrl->ejecutar();
		}
	}
}
else{
	$archivo= file_get_contents('views/login.html');
	echo $archivo; 
}
?>