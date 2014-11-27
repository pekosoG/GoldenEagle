<?php

/**
 * Clase encargada de el Login de los usuarios en GoldenEagle
 */
class modLogin{
	
	private $MINS_EXPIRE=5;
	private $modSQL;
	
	function __construct(){
		require_once('modSQL.php');
		$this->modSQL= new modSQL();
	}
	
	/**
	 * funcion encargada del Login del usuario
	 * @param $user , String con el Usuario
	 * @param $pass , String con el Password (Encriptado en Base 64)
	 * @return Boolean, TRUE si se loggea, FALSE si no
	 */
	function login($user,$pass){
		$ok=false;
		$query="SELECT pass,nivel FROM usuarios WHERE username='".$user."'";
		$res=$this->modSQL->ejecutaConsulta($query);
		if($res===NULL || $res===FALSE){
			echo 'NOOOOOPE LOGIN';
			die('Error: Problema de Login'); 
		}
		else{
			while($a = $res->fetch_array(MYSQLI_ASSOC)){
				$passb=$a['pass'];
				$nivel=$a['nivel'];
				if(strcmp($passb,sha1($pass))==0){
					$ok=true;
					if(isset($_SESSION)){
						session_unset();
						$this->logout();
					}
					session_start();
					$_SESSION['user']=$_POST['user'];
					$_SESSION['nivel']=$nivel;
					$_SESSION['start'] = time();
					$_SESSION['expire'] = $_SESSION['start'] + ($this->MINS_EXPIRE * 60);
                    
					$this->modSQL->ejecutaQuery("UPDATE usuarios SET last_login=now() WHERE username='".$user."'");
				}
			}
		}
		return $ok;	
	}
	
	/**
	 * Funcion encargada de verificar si el usuario está loggeado
	 * @return Boolean, True si está loggeado, False si no
	 */
	function isLogged(){
		$ok=false;
		$now = time(); 
		if(isset($_SESSION['expire'])){
        	if ($now < $_SESSION['expire']) {
        		$_SESSION['start'] = time();
				$_SESSION['expire'] = $_SESSION['start'] + ($this->MINS_EXPIRE * 60);
            	$ok=true;
        	}else{
        		//echo "Error: Sesion Expirada";
				$this->logout();
			}
		}
		else{
			$this->logout();
		}
		return $ok;
	}
	
	/**
	 * funcion que hace el Logout del usuario
	 */
	function logout(){
		session_unset();
		session_destroy();
		setcookie(session_name(),'',time()-3600);
		header("Location: ../index.php");
	}
	
	function tmpSession($key,$user){
		session_start();
		$_SESSION['key']=$key;
		$_SESSION['userid']=$user;
		$_SESSION['start'] = time();
		$_SESSION['expire'] = $_SESSION['start'] + ($this->MINS_EXPIRE * 60);
	}
}

?>