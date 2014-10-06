<?php

class ctrlUsuario {
	
	private $userName;
	private $tipo;
	private $modLogin;
	private $modUser;
	
	/**
	 * Constructor de la clase usuario
	 * REQUIERE que se haya enviado el usuario por POST
	 */
	function __construct(){
		$this->userName=$_POST['user'];
		$this->tipo=1;
		 
		require_once('mod/modLogin.php');
		$this->modLogin = new modLogin();
		
		require_once('mod/modUsuario.php');
		$this->modUser= new modUsuario();
	}
	
	/**
	 * funcion encargada de obtener el nivel con el que el usuario se ha loggeado
	 * @return nivel que tiene el User o FALSE en caso contrario
	 */
	function getNivel(){
		$ok=false;
		if(isset($_SESSION))
			$ok=$_SESSION['nivel'];
		return $ok;
	}
	
	/**
	 * Funcion encargada de ejecutar las peticiones que llegan por medio del POST
	 */
	function ejecutar(){
		if(isset($_POST['act'])){
			if(preg_match('/^[a-z]+$/', $_POST['act'])===0)
				die('Error: Act Invalido');
			else{
				switch($_POST['act']){
					case 'login':
						if(!$this->modLogin->isLogged())
							if(!$this->modLogger->login($_POST['user'],$_POST['pass']))
								die('Bad Login!');
						break;
					case 'usrAlta':
						if($this->modLogin->isLogged()){
							if($this->getNivel()>=5){
								$this->usrAlta();
							}
						}
						break;
					case 'usrCambio':
						if($this->modLogin->isLogged())
							if($this->getNivel()>=5)
								$this->usrCambia(); 
						break;
					case 'usrMuere':
						if($this->modLogin->isLogged())
							if($this->getNivel()>=5)
								$this->usrMuere();
						break;
					case 'logout':
						$this->modLogin->logout();
						header("Location: ../index.html");
				}
			}
		}
		else
			die('Problema con el ACT');
	}
	
	/**
	 * funcion que se encarga de revisar los datos ingresados, para verificar que
	 * no haya datos maliciosos
	 * @return boolean 
	 */
	private function revisaDatos(){
		$REGEX_user='/^[a-zA-Z0-9_]{5,12}$/';
		$REGEX_nombre='/^[a-zA-Z]+\s[^0-9]*[a-zA-Z]$/';
		$REGEX_Correo='/^[a-z0-9_\.-]+@[\da-z\.-]+\.[a-z\.]{2,6}$/';
		
		$ERROR_noRec='No se recibió el parametro: ';
		$ERROR_invalid='Valor invalido para el parametro: ';
		
		$ok=false;
		$okCount=0;
		
		if(!isset($_POST['usrNombre'])){
			die($ERROR_noRec.'Nombre de Usuario');
		}else{
			if(preg_match($REGEX_nombre,$_POST['usrNombre'])===0)
				die($ERROR_invalid.' Nombre de Usuario');
			else
				$okCount++;
		}
		
		if(!isset($_POST['usrApps'])){
			die($ERROR_noRec.'Apellidos de Usuario');
		}else{
			if(preg_match($REGEX_nombre,$_POST['usrApps'])===0)
				die($ERROR_invalid.'Apellidos de Usuario');
			else
				$okCount++;
		}
		
		if(!isset($_POST['usrName'])){
			die($ERROR_noRec.'UserName');
		}else{
			if(preg_match($REGEX_nombre,$_POST['usrName'])===0)
				die($ERROR_invalid.'UserName');
			else
				$okCount++;
		}
		
		if(!isset($_POST['usrMail'])){
			die($ERROR_noRec.'Correo de Usuario');
		}else{
			if(preg_match($REGEX_nombre,$_POST['usrMail'])===0)
				die($ERROR_invalid.'Correo de Usuario');
			else
				$okCount++;
		}
		
		if($okCount===4)
			$ok=true;
		
		return $ok;	
	}
	
	/**
	 * Funcion encargada de registrar usuarios para el uso del sitio web
	 * Los parametros necesarios para registrar son:
	 * <ul>
	 * 	<li>Nombre(s)</li>
	 * 	<li>Apellidos</li>
	 *	<li>Correo</li>
	 * 	<li>UserName</li>
	 * 	<li>password</li>
	 * </ul>
	 */
	private function usrAlta(){
		if($this->revisaDatos()){
			$this->modUser->altaUser();
		}	
	}
	
	/**
	 * Funcion encargada de actualiar la informacion de un usuario ya registrado.
	 * La informacion que se puede actualizar es:
	 * <ul>
	 * 	<li>Nombre(s)</li>
	 * 	<li>Apellidos</li>
	 * 	<li>Correo</li>
	 * </ul>
	 */
	private function usrCambia(){
		if(isset($_POST['usrID'])){
			if(preg_match('/^\d+$/',$_POST['usrID'])!=0){
				if($this->revisaDatos()){
					$this->modUser->cambiaUser();
				}
			}
		}
	}
	
	/**
	 * funcion encargada de dar de baja al usuario seleccionado
	 * La informacion necesaria para esta accion es SOLAMENTE el ID
	 * del usuario a matar.
	 */
	private function usrMuere(){
		if(isset($_POST['usrID'])){
			if(preg_match('/^\d+$/',$_POST['usrID'])!=0){
				$this->modUser->muereUser();
			}
		}
	}
}

?>