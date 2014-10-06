<?php

class modUsuario{
	
	private $modSQL;
	
	function __construct(){
		require_once('modSQL.php');
		$this->modSQL=new modSQL();
	}
	
	/**
	 * Funcion encargada de registrar a los usuarios en la BD.
	 * Los datos de la BD corresponden a los siguientes parametros de POST:
		username -> usrName
		pass -> usrPwd
		nombres -> usrNombre
		apellidos -> usrApps
		mail -> usrMail
		nivel -> usrLvl
	 */
	public function altaUser(){
		$query="INSERT INTO usuarios(username,pass,nombres,apellidos,mail,nivel) VALUES('".$_POST['usrName']."',
																				  	sha1('".$_POST['usrPwd']."'),
																					'".$_POST['usrNombre']."',
																					'".$_POST['usrApps']."',
																					'".$_POST['usrMail']."',
																					'".$_POST['usrLvl']."')";
		if(!$this->modSQL->ejecutaQuery($query))
			die('Error al Insertar el usuario');
	}	
	
	/**
	 * funcion encargada de hacer el update a los datos del usuario a cambiar.
	 * Los datos de la BD corresponden a los siguientes parametros de POST
	 	id -> usrID
	 	nombres -> usrNombre
		apellidos -> usrApps
		mail -> usrMail
	 */
	public function cambiaUser(){
		$query="UPDATE usuarios SET nombres='".$_POST['usrNombre']."', apellidos='".$_POST['usrApps']."', 
									mail='".$_POST['usrMail']."' WHERE id=".$_POST['usrID']." ";
									
		if(!$this->modSQL->ejecutaQuery($query))
			die('Error al Actualizar los datos de Usuario ('.$_POST['usrID'].')');
	}
	
	/**
	 * funcion encargada de hacer el update a 0 al campo status del usuario deseado
	 */
	public function muereUser(){
		$query="UPDATE usuarios SET status=0 WHERE id=".$_POST['usrID']." ";
		
		if(!$this->modSQL->ejecutaQuery($query))
			die('Error al Dar de Baja al usuario ('.$_POST['usrID'].')');
	}
}
?>