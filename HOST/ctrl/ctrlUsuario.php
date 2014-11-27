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
			if(preg_match('/^[a-zA-Z]+$/', $_POST['act'])===0)
				die('Error: Act Invalido');
			else{
				switch($_POST['act']){
					case 'login':
				        if(!$this->modLogin->login($_POST['user'],$_POST['pass']))
                            $this->modLogin->logout();
						else{
							$this->showPrincipal();
						}
						break;
					case 'principal':
						session_start();
						if($this->modLogin->isLogged())
							$this->showPrincipal();
						else 
							$this->modLogin->logout();
						break;
					case 'users':
						session_start();
						if($this->modLogin->isLogged()){
							if($this->getNivel()>=5){
								$this->users();
							}
							else 
								$this->showPrincipal();
						}
						else 
							$this->modLogin->logout();
						break;
					case 'usrSaveAlta':
						session_start();
						if($this->modLogin->isLogged()){
							if($this->getNivel()>=5){
								$this->usrSaveAlta();
							}
						}
						else 
							$this->modLogin->logout();
						break;
					case 'usrMuere':
						session_start();
						if($this->modLogin->isLogged())
							if($this->getNivel()>=5){
								$this->usrMuere();
							}else echo 'u have no power here';
						else 
							$this->modLogin->logout();
						break;
					case 'historial':
						session_start();
						if($this->modLogin->isLogged())
							$this->historial();
						else 
							$this->modLogin->logout();
						break;
					case 'pedidos':
						session_start();
						if($this->modLogin->isLogged())
							$this->pedidos(1);
						else 
							$this->modLogin->logout();
						break;
					case 'logout':
						$this->modLogin->logout();
						header("Location: ../index.php");
				}
			}
		}
		else
			die('Problema con el ACT');
	}


	/**
	 * Funcion encargada de mostrar la vista de los usuarios
	 */
	private function users(){
		$plantillaPrincipal=file_get_contents('views/principal.html');
		$plantillaPrincipal=str_replace('{Titulo}','Usuarios - Golden Eagle',$plantillaPrincipal);
		$contenido=file_get_contents('views/cont_users.html');
		$plantillaTabla=file_get_contents('views/cont_TablaUsers.html');
		
		$opciones=$this->modUser->obtieneUsuarios($plantillaTabla);
        $contenido=str_replace('{det_tabla}',$opciones,$contenido);
		
		if($this->getNivel()>5)
			$contenido=str_replace('{selAdm}',"<option value=\"full-power\">Administrador</option>", $contenido);
		else 
			$contenido=str_replace('{selAdm}',"", $contenido);
	
		if($this->getNivel()>=5)
			$contenido=str_replace('{selSpr}',"<option value=\"medium-power\">Supervisor</option>", $contenido);
		else 
			$contenido=str_replace('{selSpr}',"", $contenido);
		
		echo str_replace('{contenido}', $contenido, $plantillaPrincipal);	
	}
	
	/**
	 * funcion que se encarga de revisar los datos ingresados, para verificar que
	 * no haya datos maliciosos
	 * @return boolean 
	 */
	private function revisaDatos(){
		$REGEX_user='/^[a-zA-Z0-9_]{5,12}$/';
		$REGEX_nombre='/^[a-zA-Z]+\s*[^0-9]*[a-zA-Z]*$/';
		$REGEX_Correo='/^[a-zA-Z0-9_\.-]+@[\da-zA-Z\.-]+\.[a-zA-Z\.]{2,6}$/';
		$REGEX_b64='/^@(?:[A-Za-z0-9+/]{4})*(?:[A-Za-z0-9+/]{2}==|[A-Za-z0-9+/]{3}=)?$/';
		
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
		
		if(!isset($_POST['usrPwd'])){
			die($ERROR_noRec.'UserPwd');
		}else{
			if(preg_match($REGEX_b64,$_POST['usrPwd'])===0)
				die($ERROR_invalid.'UserPws');
			else
				$okCount++;
		}
		
		if(!isset($_POST['usrMail'])){
			die($ERROR_noRec.'Correo de Usuario');
		}else{
			if(preg_match($REGEX_Correo,$_POST['usrMail'])===0)
				die($ERROR_invalid.'Correo de Usuario');
			else
				$okCount++;
		}
		
		if(!isset($_POST['usrLevel'])){
			die($ERROR_noRec.'Nivel de Usuario');
		}else{
			if(preg_match($REGEX_nombre,$_POST['usrLevel'])===0)
				die($ERROR_invalid.'Nivel de Usuario');
			else{
				if(strcmp($_POST['usrLevel'],"full-power")==0)
					$_POST['usrLevel']=10;
				if(strcmp($_POST['usrLevel'],"medium-power")==0)
					$_POST['usrLevel']=5;
				if(strcmp($_POST['usrLevel'],"no-power")==0)
					$_POST['usrLevel']=1;
				$okCount++;
			}
		}
		
		if($okCount===6)
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
	private function usrSaveAlta(){
		if($this->revisaDatos()){
			if($this->modUser->altaUser(isset($_POST['idUpdate']))){
				$this->users();
				$this->enviaCorreo();
			}
			else
				die('Error al Guardar el User');
		}	
	}
	
	private function enviaCorreo(){
		$plantCorreo=file_get_contents('views/correos/bienvenido.html');
		
		$plantCorreo=str_replace('{usrNombre}',$_POST['usrNombre'],$plantCorreo);
		$plantCorreo=str_replace('{usrName}',$_POST['usrName'],$plantCorreo);
		$plantCorreo=str_replace('{usrPass}',base64_decode($_POST['usrPwd']),$plantCorreo);
		require_once('ctrl/ctrlCorreos.php');
		
		enviaCorreo::enviale($plantCorreo,$_POST['usrMail'],'Bienvenido a GoldenEagle!');
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
				$this->users();
			}
		}
	}
	
	/**
	 * Function que se encarga de mostrar la pagina principal
	 * una vez que ya se loggearon
	 */
	private function showPrincipal(){
		$plantillaPrincipal=file_get_contents('views/principal.html');
		$plantillaPrincipal=str_replace('{Titulo}','Principal - Golden Eagle',$plantillaPrincipal);
		$contenido=file_get_contents('views/cont_principal.html');
		$opciones=$this->modUser->obtieneDivisiones();
        $contenido=str_replace('{Divisiones}',$opciones,$contenido);
        
		echo str_replace('{contenido}', $contenido, $plantillaPrincipal);	
	}
	
	/**
	 * funcion encargda de mostrar el historial del usuario loggeado
	 */
	function historial(){
		$plantillaPrincipal=file_get_contents('views/principal.html');
		$plantillaPrincipal=str_replace('{Titulo}','Historial - Golden Eagle',$plantillaPrincipal);
		$contenido=file_get_contents('views/cont_Historial.html');
		$plantTabla=file_get_contents('views/cont_TablaHistorial.html');
		$userSelect='';
		
		$plantTabla=$this->modUser->getHistorial($plantTabla,$this->modUser->getUserID($_SESSION['user']));
		if($this->getNivel()>=5)
			$userSelect=$this->modUser->getUserSelect();
		
		$contenido=str_replace('{selectUsers}',$userSelect,$contenido);
		$contenido=str_replace('{contTabla}',$plantTabla,$contenido);
		
		echo str_replace('{contenido}', $contenido, $plantillaPrincipal);
	}

	function listar(){
	 	$cve=array();
	 	switch($_GET['filtro']){
			case 'div':
				$cve=$this->modUser->getCVE('Division',$_GET['cve']);
				break;
			case 'zon':
				$cve=$this->modUser->getCVE('zona',$_GET['cve']);
				break;
			case 'cac':
				$cve=$this->modUser->getCVE('agencia',$_GET['cve']);
				break;
	 	}	
		$plantilla=file_get_contents('views/cont_Descargas.html');
		$contenido='';
		
		$contenido=$this->modUser->getListaDescargas($cve,$plantilla);
		
		return $contenido;
	}

	/**
	 * funcion encargada de obtener la vista de los pedidos cargada...
	 * @param $print Bandera para saber si imprimir o regresar
	 */
	function pedidos($print){
		$plantillaPrincipal=file_get_contents('views/principal.html');
		$plantillaPrincipal=str_replace('{Titulo}','Pedidos - Golden Eagle',$plantillaPrincipal);
		$contenido=file_get_contents('views/cont_Pedidos.html');
		
		$contenido=str_replace('{tblPedidos}',$this->listaPedidos('pendiente'),$contenido);
		
		$opciones=$this->modUser->obtieneDivisiones();
        $contenido=str_replace('{Divisiones}',$opciones,$contenido);
		
		$plantillaPrincipal=str_replace('{contenido}',$contenido,$plantillaPrincipal);
		if($print===1)
			echo  $plantillaPrincipal;
		else
			return $plantillaPrincipal;
	}
	
	
	/**
	 * funcion encargada de registrar un pedido solicitado.
	 */
	function altaPedido(){
		
		if($_GET['agencia']!=NULL && $_GET['cant']>0){
			$this->modUser->registraPedido($_GET['agencia'],$_GET['cant']);
		}
		return $this->pedidos(0);
	}
	
	/**
	 * funcion encargada de regresar la lista de pediddos segun status
	 * @param $status el status que se requiere
	 * @return lista de los pedidos
	 */
	function listaPedidos($status){
		$plantTabla=file_get_contents('views/cont_TablaPedidos.html');
		return $this->modUser->getPedidos($status,$plantTabla);
	}
	
	/**
	 * funcion donde se manda el correo para recuperar el password
	 */
	function recuperaPass(){
		$elMail=$_GET['mail'];
		if($this->modUser->existeMail($elMail)){
			$array=$this->modUser->registraRecover($elMail);
			//var_dump($array); 
			 
			
			$plantillaCorreo=file_get_contents('views/correos/correoPassword.html');
			$plantillaCorreo=str_replace('{usrNombre}', $arreglo['nombre'],$plantillaCorreo);
			
			$link='http://goldeneagle.blackoutsystems.com/PassRecovery.php?recoverpass&usrid='.$array['id'].'&key='.$array['llave'];
			$plantillaCorreo=str_replace('{linkPass}',$link,$plantillaCorreo);
			
			require_once('ctrl/ctrlCorreos.php');
			enviaCorreo::enviale($plantillaCorreo,$elMail,'Recuperar Password');
			
			return "ok";
		}
		return 'error...';
	}
	
	function PasswordJobs(){
		if(isset($_GET['mail']) && $_GET['mail']!==''){
			echo $this->recuperaPass();
			return;
		}
		if(isset($_GET['key']) && $_GET['key']!==''){
			$this->modLogin->tmpSession($_GET['key'],$_GET['usrid']);
			$plantilla=file_get_contents('views/resetPass.html');
			echo str_replace('{llave}',$_GET['key'],$plantilla);
			return;	
		}
		if(isset($_GET['newpass']) &&$_GET['newpass']!==''){
			session_start();
			if($this->modLogin->isLogged()){
				if($this->modUser->updatePassword($_GET['newpass'])){
					
					echo 'ok';
				}
				else
					echo 'nope';
			}
			return;
		}
		echo 'none';
	}
	
	/**
	 * Funcion encargada de manejar las solicitudas AJAX del usuario
	 */
	function elAjax(){
	 	if($_GET['padre']!==''){
	 		session_start();
			if(!$this->modLogin->isLogged())
				$this->modLogin->logout();
			else{
				switch($_GET['accion']){
					case 'zonas':
						echo $this->modUser->getZonas($_GET['padre']);
						break;
					case 'cacs':
						echo $this->modUser->getCacs($_GET['padre']);
						break;
					case 'historial':
						echo  $this->getHistoril($_GET['padre']);
						break;
					case 'listar':
						echo $this->listar();
						break;
					case 'finuserbyid':
						echo $this->modUser->getUserByID($_GET['padre']);
						break;
					case 'listar':
						echo $this->listar();
						break;
					case 'pedido':
						echo $this->altaPedido();
						break;
					case 'listapedido':
						echo $this->listaPedidos($_GET['padre']);
						break;
			 	}
			}
		}
	}
}

?>