<?php

class modUsuario{
	
	private $modSQL;
	
	function __construct(){
		require_once('modSQL.php');
		$this->modSQL=new modSQL();
	}
	
	/**
	 * funcion que regresa el userID de un usuario
	 * @param username del usuario
	 */
	public function getUserID($userName){
		$query="SELECT id FROM usuarios WHERE username='".$userName."'";
		//echo $query;
		$res=$this->modSQL->ejecutaConsulta($query);
		if($res===FALSE || $res===NULL){
			var_dump($this->SQL);
			var_dump($res);
			die('Error: Problema en getUserID');
		}else{
			$a = $res->fetch_array(MYSQLI_ASSOC);
			return $a['id'];
		}
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
	 * @param isUpdate Banera para saber si es update o no
	 */
	public function altaUser($isUpdate){
		if($isUpdate==TRUE){
			echo 'update<br>';
			$query="UPDATE usuarios SET username='".$_POST['usrName']."',
										pass=sha1('".$_POST['usrPwd']."'),
										nombrs='".$_POST['usrNombre']."',
										apellidos='".$_POST['usrApps']."',
										mail='".$_POST['usrMail']."',
										nivel=".$_POST['usrLevel']." WHERE id=".$_POST['idUpdate'];
										
										
		}
		else
			$query="INSERT INTO usuarios(username,pass,nombrs,apellidos,mail,nivel) VALUES('".$_POST['usrName']."',
																				  	sha1('".$_POST['usrPwd']."'),
																					'".$_POST['usrNombre']."',
																					'".$_POST['usrApps']."',
																					'".$_POST['usrMail']."',
																					".$_POST['usrLevel'].")";
		if(!$this->modSQL->ejecutaQuery($query))
			die('Error al Insertar el usuario');
		else
			return true;
	}	
	
	
	/**
	 * funcion encargada de hacer el update a 0 al campo status del usuario deseado
	 */
	public function muereUser(){
		$query="UPDATE usuarios SET status=0 WHERE id=".$_POST['usrID']." ";	
		if(!$this->modSQL->ejecutaQuery($query))
			die('Error al Dar de Baja al usuario ('.$_POST['usrID'].')');
	}
    
	/**
	 * funcion encargada de obtener todas las divisiones y regresarlas en Str
	 */
    public function obtieneDivisiones(){
        $query="SELECT id, nombre,(SELECT count(id) FROM archivos A WHERE A.nombre like CONCAT('%','.',D.cve,'%','_Parte','%')) num FROM division D";
        $res=$this->modSQL->ejecutaConsulta($query);
        if($res===NULL||$res===FALSE){
			var_dump($this->SQL);
			var_dump($res);
			die('Error: Problema en getDivisiones');
		}else{
			$resultado="";//"<option value='*' >Todas (".$todas.")</option>";
			while($a = $res->fetch_array(MYSQLI_ASSOC))
				//if($a['num']>0)
					$resultado.="<option value='".$a['id']."' >".$a['nombre']." (".$a['num'].")</option>";
			return $resultado;
		}
    }
	
	/**
	 * Funcion encargada de regresar todas las zonas segun el id de division otorgado
	 * @param $division ID de division
	 */
	function getZonas($division){
	 	if(strcmp($division,'*')==0)
			die();
	 	$resultado;
		$query="SELECT id, nombre,(SELECT count(id) FROM archivos A WHERE A.nombre like CONCAT('%','.',Z.cve,'%','_Parte','%')) num FROM zona Z WHERE `id_div`=$division";
		//echo $query.'</br>';
		$res=$this->modSQL->ejecutaConsulta($query);
		//var_dump($res);
		if($res===NULL||$res===FALSE){
			var_dump($this->modLogger->SQL);
			die('Error: Problemas getZonas');
		}else{
			$resultado.="<option value='*' >Todas</option>";
			while($a = $res->fetch_array(MYSQLI_ASSOC))
				//if($a['num']>0)
					$resultado.="<option value='".$a['id']."' >".$a['nombre']." (".$a['num'].")</option>";
			return $resultado;
		}
	 }
	
	/**
	 * funcion encargada de regresar los CACS segun el ID de zona otorgado
	 * @param $Zona id de Zona
	 */
	function getCacs($Zona){
	 	$resultado;
		$query="SELECT id, nombre, (SELECT count(id) FROM archivos A WHERE A.nombre like CONCAT('%','.',A.cve,'_Parte','%')) num  FROM `agencia` A WHERE `id_zona`=$Zona";
		$res=$this->modSQL->ejecutaConsulta($query);
		//var_dump($this->modLogger->SQL);
		if($res===NULL||$res===FALSE){
			var_dump($this->modLogger->SQL);
			die('Problemas Cacs');
		}else{
			$resultado.="<option value='*' >Todas</option>";
			while($a = $res->fetch_array(MYSQLI_ASSOC))
				//if($a['num']>0)
					$resultado.="<option value='".$a['id']."' >".$a['nombre']." (".$a['num'].")</option>";
			return $resultado;
		}
	 }
	 
	 /**
	  * funcion que regresa los usuarios registrados que serán mostrados en la plantilla
	  */
	 function obtieneUsuarios($plantilla){
	 	$query="SELECT * FROM usuarios WHERE nivel<=".$_SESSION['nivel']." AND status=1";
        $res=$this->modSQL->ejecutaConsulta($query);
        if($res==NULL || $res==FALSE){
        	var_dump($this->modSQL->SQL);
			die('Problema en ObtieneUsuarios');
        }else{
         	$preCont="";
			while($a = $res->fetch_array(MYSQLI_ASSOC)){
				
				$prePlant=str_replace('{id}',$a['id'],$plantilla);
				$prePlant=str_replace('{nombre_full}',$a['nombrs'].' '.$a['apellidos'],$prePlant);
				$prePlant=str_replace('{userName}',$a['username'],$prePlant);
				$prePlant=str_replace('{mail}',$a['mail'],$prePlant);
				
				if($a['nivel']==10)
					$nivel='Administrador';
				if($a['nivel']==5)
					$nivel='Supervisor';
				if($a['nivel']==1)
					$nivel='Basico';
				$prePlant=str_replace('{level}',$nivel,$prePlant);
				$prePlant=str_replace('{date_login}',$a['last_login'],$prePlant	);
				
				$preCont.=$prePlant;
			}
			return $preCont;
        }
	 }
	 
	 /**
	  * funcion que regresa un JSON con los datos de un usuario segun el ID dado
	  * @param ID id de usuario
	  */
	 function getUserByID($id){
	 	$query="SELECT nombrs,apellidos,userName,mail,nivel FROM usuarios WHERE id=$id";
		$res=$this->modSQL->ejecutaConsulta($query);
		if($res==NULL || $res==FALSE){
        	var_dump($this->modSQL->SQL);
			die('Problema en getUserByID');
        }else{
			$a = $res->fetch_array(MYSQLI_ASSOC);
			return json_encode($a);
		}
	 }
	 
	/**
	 * funcion encargada de obtener los datos del hsitorial del usuario dado
	 * @param $pTabla plantilla Tabla
	 * @param $user id de usuario
	 * @return COntenido de Tabla
	 */
	function getHistorial($pTabla,$user){
		$contenido='';
		$query="SELECT (SELECT username FROM usuarios WHERE id=$user) username,(SELECT nombre from archivos where id=H.archivo_id) Archivo, fecha, accion FROM historial H WHERE user_id=".$user."";
		$res=$this->modSQL->ejecutaConsulta($query);
		if($res===NULL||$res===FALSE){
			var_dump($this->SQL);
			var_dump($res);
			die('Error: Problema en historial');
		}
		
		while($a = $res->fetch_array(MYSQLI_ASSOC)){
			$preCont=str_replace('{Archivo}', $a['Archivo'],$pTabla);
			$preCont=str_replace('{userName}',$a['username'],$preCont);
			$preCont=str_replace('{fecha}',$a['fecha'],$preCont);
			if(strcmp('sube',$a['a'])===0)
				$preCont=str_replace('{accion}','fa-cloud-upload',$preCont);
			else
				$preCont=str_replace('{accion}','fa-download',$preCont);
			
			$contenido.=$preCont;
			$preCont="";
		}
		return $contenido;
	} 
	
	function getUserSelect(){
		$query="SELECT username FROM usuarios WHERE status=1";
		$res=$this->modSQL->ejecutaConsulta($query);
		if($res===NULL||$res===FALSE){
			var_dump($this->SQL);
			var_dump($res);
			die('Error: Problema en getUserSelect');
		}
		$preCont="<select class='centrado menuSeleccion' onchange='getAjax('historial',this.value,'tblCont')'>";
		while($a = $res->fetch_array(MYSQLI_ASSOC)){
			$str="<option>".$a['username']."</option>";
			$preCont.=$str;
		}
		$preCont.="</select>";
		return $preCont;
	}
	
	/**
	 * funcion que regresa la clave de la zona,agencia,division solicidata
	 * @param $tipo (Division,Zona,Agencia)
	 * @param $id de busqueda
	 */
	function getCVE($tipo,$id){
	 	$cve=array();
		if(strlen($id)==0){
			//echo $tipo.' - ';
			$query='SELECT nombre, cve FROM '.$tipo.' WHERE ';
			if(strcmp($tipo,'Division')==0){
				//echo 'in div...';
				$query.='1=1';	
			}
			if(strcmp($tipo,'zona')==0){
				//echo 'in zon...';
				$query.='id_div='.$_GET['granpa'];
			}
			if(strcmp($tipo,'agencias')==0){
				//echo 'in cac...';
				$query.='id_zona='.$_GET['granpa'];
			}
		}
		else
	 		$query='SELECT nombre, cve FROM '.$tipo." WHERE id=".$id;
		//echo $query;
		$res=$this->modSQL->ejecutaConsulta($query);
		if($res===NULL||$res===FALSE){
				var_dump($this->SQL);
				var_dump($res);
				die('Error: Problema en getCVE');
		}
		while($a = $res->fetch_array(MYSQLI_ASSOC))
			$cve[$a['nombre']]=$a['cve'];
		return $cve;
	 }
	
	/**
	 * funcion encargada de obtener la lista de descargas de los paquetes
	 * @param $cve clave de busqueda
	 * @param $plantilla plantilla
	 */
	function getListaDescargas($cve,$plantilla){
		foreach($cve as $key => $value){
			$nombre=$key;
			$query="SELECT nombre,id,online FROM archivos WHERE nombre like '%$value%_Parte%' ";
			$res=$this->modSQL->ejecutaConsulta($query);
			
			if($res===NULL||$res===FALSE){
				var_dump($this->SQL);
				var_dump($res);
				echo $query;
				die('Error: Problema en Listar');
			}
			$contenido='';
			while($a=$res->fetch_array(MYSQLI_ASSOC)){
				$preCont=str_replace('{Origen}', $nombre, $plantilla);
				if($a['online']==1){
					$preCont=str_replace('{Nombre_Archivo}', $a['nombre'], $preCont);
					$preCont=str_replace('{online}','',$preCont);
					$preCont=str_replace('{elLink}','http://goldeneagle.blackoutsystems.com/DF.php?file='.$a['id'].'&user='.$_SESSION['user'], $preCont);
				}
				else{
					$preCont=str_replace('{Nombre_Archivo}', $a['nombre']." <br><i class='fa fa-unlink' title='Offline'></i>", $preCont);
					$preCont=str_replace('{online}','hidden',$preCont);
					$preCont=str_replace('{elLink}',"#", $preCont);
				}
				$contenido.=$preCont; 
				$preCont='';
			}
			return $contenido;
		}
	}

	/**
	 * funcion encargada de obtener los pedidos del usuario segun el status dado
	 * @param $status
	 */
	function getPedidos($status,$plantilla){
		$stat=0;
		if($status==='procesando')
			$stat=1;
		if($status==='hecho')
			$stat=2;
		
		$query="SELECT fecha, cantidad, agencia_id, status FROM pedido WHERE user_id=".$this->getUserID($_SESSION['user']).' AND status='.$stat;
		$res=$this->modSQL->ejecutaConsulta($query);
		
		if($res===NULL || $res===FALSE){
			var_dump($this->SQL);
			var_dump($res);
			echo $query;
			die('Error: Problema en Listar');
		}
		$cont='';
		while($a=$res->fetch_array(MYSQLI_ASSOC)){
			$preCont=str_replace('{fecha}',$a['fecha'],$plantilla);
			$preCont=str_replace('{cant}',$a['cantidad'],$preCont);
			$preCont=str_replace('{ubicacion}',$a['agencia_id'],$preCont);
			$faStat='';
			if($stat===0)
				$faStat='fa-send';
			if($stat===1)
				$faStat='fa-spin fa-spinner';
			if($stat===2)
				$faStat='fa-check';
			$preCont=str_replace('{status}',$faStat,$preCont);
			$preCont=str_replace('{status_str}','  '.$status,$preCont);
			
			$cont.=$preCont;	
		}
		return $cont;
	}
	
	function registraPedido($agencia,$cantidad){
		$query='INSERT INTO pedido(user_id,agencia_id,cantidad,fecha) VALUES('.$this->getUserID($_SESSION['user']).','.$agencia.','.$cantidad.',now())';
		
		if(!$this->modSQL->ejecutaQuery($query)){
			echo 'Error: Problema al Insertar el Pedido';	
		}
	}
	
	/**
	 * funcion que regresa un true un false para ver si existe el mail
	 */
	function existeMail($elMail){
		$query='SELECT username FROM usuarios WHERE mail=\''.$elMail.'\' AND status=1';
		$res=$this->modSQL->ejecutaConsulta($query);
		
		if($res===NULL && $res===FALSE){
			echo 'disque null';
			return false;
		}
		$cont=0;
		while($a=$res->fetch_array(MYSQLI_ASSOC))
			$cont++;
	
		if(cont===0)
			return false;
		else 
			return true;
	}
	
	/**
	 * funcion que registra la solicitud de recover de password
	 * @param $elMail el mail del usuario
	 * @return FALSE si truena, Array con los datos del registro
	 */
	function registraRecover($elMail){
		$query="SELECT old_password(now()) llave, username, id, nombrs FROM usuarios WHERE mail='".$elMail."' AND STATUS=1";
		
		//echo $query;
		$res=$this->modSQL->ejecutaConsulta($query);
		if($res===NULL && $res===FALSE)
			return false;
		$key='';
		$user='';
		$id='';
		$name='';
		while($a=$res->fetch_array(MYSQLI_ASSOC)){
			$key=$a['llave'];
			$user=$a['username'];
			$id=$a['id'];
			$name=$a['nombrs'];
		}
		if($key!==''){
			$query="INSERT INTO passRecover(user_id,expira,llave) VALUES($id,NOW() + INTERVAL 24 HOUR,'$key')";
			if($this->modSQL->ejecutaQuery($query)){
				$arreglo = array();
				$arreglo['llave']=$key;
				$arreglo['id']=$id;
				$arreglo['nombre']=$name;
				$arreglo['mail']=$elMail;
				
				return $arreglo;
			}	
		}
		return false;
	}
	
	/**
	 * Funcion que hace el update del usuario una vez que ya se veiricó que esté loggeado y demas
	 * @param $elPass password en Base64
	 */
	function updatePassword($elPass){
		$query="UPDATE usuarios SET pass=sha1('$elPass') WHERE id=".$_SESSION['userid'];
		return $this->modSQL->ejecutaQuery($query);
	}
	
}
?>