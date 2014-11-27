<?php

if(isset($_GET)){
	if(isset($_GET['recoverpass'])){
		require('ctrl/ctrlUsuario.php');
		$ctrUser= new ctrlUsuario();
		$ctrUser->PasswordJobs();
	}
}

?>