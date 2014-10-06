<?php

/**
 * Clase encargada de la conexion a la BD
 */
class modSQL{
	
	private static $SQL=null;
	
	private function checkSQL(){
		if(self::$SQL===null){
			self::$SQL= new mysqli('localhost','usrEagle','pswEagle','blackoutsystems_GEDB');
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
		if($res===NULL || $res===FALSE)
			die("Error al ejecutar el Query: $query");
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
		if($res===NULL || $res===FALSE)
			die("Error al ejecutar el Query: $query");
		else
			return $res;
	}
}

?>