<?php

/**
 * Clase encargada de la conexion a la BD
 */
class modSQL{
	
	private static $SQL=null;
    
	private function checkSQL(){
		if(self::$SQL===null){
            require_once('DataBase.php');
			self::$SQL= new mysqli($host,$user,$pass,$bd);
			if(self::$SQL->connect_errno)
				die('Error al conectarse a la BD');			
		}
	}
	
	/**
	 * funcion que te regresa la instancia del SQL 
	 * @deprecated 
	 */
	public function getSQL(){
		$this->checkSQL();
		return self::$SQL;
	}
	
	/**
	 * funcion encargada de recibir un query que no regresará resultados y ejecutarlo
	 * @return boolean Si se ejecutó o muere en caso de fallar.
	 */
	public function ejecutaQuery($query){
		$this->checkSQL();
		$res=self::$SQL->query($query);
		if($res===NULL || $res===FALSE){
			$this->panic();
			//die();
		}
		else
			return TRUE;	
	}
	
	/**
	 * funcion encargaa de recibir un query, ejecutarlo y regresar el resultado de la consulta
	 * @return Resultado de consulta o muere en caso de fallar.
	 */
	public function ejecutaConsulta($query){
		$this->checkSQL();
		
		$res=self::$SQL->query($query);
		if($res===NULL || $res===FALSE){
			$this->panic();
			//die();
		}
		else
			return $res;
	}
	
	private function panic(){
		session_unset();
		session_destroy();
		setcookie(session_name(),'',time()-3600);
		header("Location: ../index.php");
	}
}

?>