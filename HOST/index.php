<?php
/**
 * Index.php de GoldenEagle
 * 
 * @author Pekas
 * @version 1.0
 */

if(isset($_POST['ctrl'])){
	if(preg_match('/^[a-z]+$/',$_POST['ctrl'])===0)
		die("Ctrl Rechazado!");
	else{
		if(strcmp($_POST['ctrl'],'usuario')){
			session_start();
			require_once('ctrl/ctrlUsuario.php');
			$ctrl= new ctrlUsuario();
			$ctrl->ejecutar();
		}
	}
}
else{
	$archivo= file_get_contents('views/inicio.html');
	echo $archivo; 
}
?>