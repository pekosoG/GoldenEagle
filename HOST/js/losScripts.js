var isUpdateUser=0;
var idUpdateUser=0;

function logearse() {
	var error = 0;
	elemento=document.getElementById("userName");
	if (elemento.value === ''){
		$('#warningUser').css('display','inline');
		$('#userName').css('width','80%');
		error+=1;
	}
	else{
		$('#warningUser').css('display','none');
		$('#userName').css('width','85%');
	}
	
	elemento=document.getElementById("paswd");
	if(elemento.value==''){
		$('#warningPass').css('display','inline');
		$('#paswd').css('width','80%');
		error+=1;
	}
	else{
		$('#warningPass').css('display','none');
		$('#paswd').css('width','85%');
	}
	if(error!=0)
		return;
	
	var forma= document.createElement("form");
	forma.setAttribute("method","post");
	forma.setAttribute("action","index.php");
	
	var usuario=document.createElement("input");
	usuario.setAttribute("type","hidden");
	usuario.setAttribute("name","user");
	usuario.setAttribute("value",document.getElementById("userName").value);
	forma.appendChild(usuario);
	
	var pass=document.createElement("input");
	pass.setAttribute("type","hidden");
	pass.setAttribute("name","pass");
	pass.setAttribute("value",base64encode(document.getElementById("paswd").value));
	forma.appendChild(pass);
	
	var ctrl=document.createElement("input");
	ctrl.setAttribute("type","hidden");
	ctrl.setAttribute("name","ctrl");
	ctrl.setAttribute("value","usuario");
	forma.appendChild(ctrl);
	
	var act=document.createElement("input");
	act.setAttribute("type","hidden");
	act.setAttribute("name","act");
	act.setAttribute("value","login");
	forma.appendChild(act);
	
	document.body.appendChild(forma);
	forma.submit();
}

/**
 * Funcion que hace una peticion AJAX 
 * @param {Object} mijo Accion (ZONA,CACS,HISTORIAL)
 * @param {Object} padre Parametro de Busqueda
 * @param {Object} id Id de Elemento donde se agrega el resultado
 */
function getAjax(mijo,padre,id){
	if(window.XMLHttpRequest){
		xmlhttp= new XMLHttpRequest();
	}else{
		xmlhttp= new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState==4 && xmlhttp.status==200){
			var str=xmlhttp.responseText;
			if(str.indexOf("Error:")!=-1){
				alert("Sesion Expirada! "+str);
				self.location="index.php";
			}
			else{
				$('#'.concat(id)).html(xmlhttp.responseText);
				//if(id=='zonas' || id=='agencias'){
					$('#'.concat(id)).fadeIn("slow");
					if(id=='zonas')
						$("#btnSolicita").removeAttr("disabled");       
				//}
			}
		}
	};
	xmlhttp.open("GET","elAjax.php?accion="+mijo+"&padre="+padre);
	xmlhttp.send();
}

/**
 * Funcion que hace una peticion AJAX 
 * @param {Object} mijo Accion (ZONA,CACS,HISTORIAL)
 * @param {Object} padre Parametro de Busqueda
 * @param {Object} id Id de Elemento donde se agrega el resultado
 */
function getAjaxJQ(mijo,padre,id){
	$.ajax({
		type:'GET',
		data:"accion="+mijo+"&padre="+padre,
		url: 'elAjax.php',
		dataType:'html',
		success:function(responseText){
			$('#'.concat(id).concat(' :last-child')).html(responseText);
			$('#'.concat(id)).fadeIn("slow");
		}
	});
}

/**
 *funcion encargada de hacer un request POST a la pagina 
 * @param {Object} actividad
 */
function request(actividad){
	var forma= document.createElement('form');
	forma.setAttribute("method","POST");
	forma.setAttribute("method","post");
	forma.setAttribute("action","index.php");
	
	var usuario=document.createElement("input");
	usuario.setAttribute("type","hidden");
	usuario.setAttribute("name","user");
	usuario.setAttribute("value",'nadien_haha');
	forma.appendChild(usuario);
	
	var ctrl=document.createElement("input");
	ctrl.setAttribute("type","hidden");
	ctrl.setAttribute("name","ctrl");
	ctrl.setAttribute("value","usuario");
	forma.appendChild(ctrl);
	
	var act=document.createElement("input");
	act.setAttribute("type","hidden");
	act.setAttribute("name","act");
	act.setAttribute("value",actividad);
	forma.appendChild(act);
	
	document.body.appendChild(forma);
	forma.submit();
}

/**
 *funcion que oculta y muestra elementos para dar de alta un usuario 
 */
function addUser(){
	$('#usrName').val('');
	$('#usrPass').val('');
	$('#usrPass2').val('');
	$('#usrNombre').val('');
	$('#usrApps').val('');
	$('#usrMail').val('');
	$('#usrControls').fadeOut("fast");
	$('#form-user').fadeIn("fast");
}

/**
 *funcion que obtiene los datos del usurio solicitados 
 */
function ajaxUser(valor){
	$.ajax({
		type:'GET',
		data:"accion=finuserbyid&padre="+valor,
		url: 'elAjax.php',
		dataType:'json',
		success:function(jResp){
			addUser();
			$('#usrName').val(jResp.userName);
			$('#usrNombre').val(jResp.nombrs);
			$('#usrApps').val(jResp.apellidos);
			$('#usrMail').val(jResp.mail);
			var lvl='no-power';
			if(jResp.nivel===10)
				lvl='full-power';
			if(jResp.nivel===5)
				lvl='medium-power';	
			$('#selNivelUsr').val(lvl);
		}
	});
}

/**
 *Funcion que se encarga de pedir los datos del usuario para editarlos encaso de ser necesario 
 */
function editUser(idUser){
	if(idUser!=''){
		ajaxUser(idUser);
		isUpdateUser=1;
		idUpdateUser=idUser;
	}
}

/**
 *funcion que se encarga de preguntar y eliminar un usuario 
 */
function muereUser(idUser){
	var dialogo=document.createElement('div');
	dialogo.setAttribute('id','dialogo');
	dialogo.setAttribute('class','dialogo confirma-dead');
	dialogo.setAttribute('title','Pregunta!');
	
	var parrafo=document.createElement('p');
	parrafo.textContent="¿Eliminar al usuario?";
	dialogo.appendChild(parrafo);
	
	//document.body.appendChild(dialogo);
	
	$(dialogo).dialog({
		modal:true,
		resizable:false,
		dialogClass:'warning-dialog',
		buttons:{
			"Eliminar":function(){
				$.post( 
					"index.php", 
					{ ctrl: "usuario", act: "usrMuere", usrID:idUser },
					function(data,status){
						document.write(data);
					}
				);
			},
			"Cancelar":function(){
				$(this).dialog("close");
			}			
		}
	});
}

/**
 *Funcion que se encarga de guardar los datos 
 */
function saveAlta(){
	var usrName=document.getElementById('usrName').value;
	var usrPass=document.getElementById('usrPass').value;
	var usrNombre=document.getElementById('usrNombre').value;
	var usrApps=document.getElementById('usrApps').value;
	var usrMail=document.getElementById('usrMail').value;
	
	if(checkaAlta(usrName,usrPass,usrNombre,usrApps,usrMail) && checaPass()){
		var forma= document.createElement('form');
		forma.setAttribute("method","POST");
		forma.setAttribute("method","post");
		forma.setAttribute("action","index.php");
		
		var ctrl=document.createElement("input");
		ctrl.setAttribute("type","hidden");
		ctrl.setAttribute("name","ctrl");
		ctrl.setAttribute("value","usuario");
		forma.appendChild(ctrl);
		
		var act=document.createElement("input");
		act.setAttribute("type","hidden");
		act.setAttribute("name","act");
		act.setAttribute("value",'usrSaveAlta');
		forma.appendChild(act);
		
		var usrNa=document.createElement("input");
		usrNa.setAttribute("name","usrName");
		usrNa.setAttribute("value",usrName);
		forma.appendChild(usrNa);
		
		var usrPa=document.createElement("input");
		usrPa.setAttribute("name","usrPwd");
		usrPa.setAttribute("value",base64encode(usrPass));
		forma.appendChild(usrPa);
		
		var usrNo=document.createElement("input");
		usrNo.setAttribute("name","usrNombre");
		usrNo.setAttribute("value",usrNombre);
		forma.appendChild(usrNo);
		
		var usrApp=document.createElement("input");
		usrApp.setAttribute("name","usrApps");
		usrApp.setAttribute("value",usrApps);
		forma.appendChild(usrApp);
		
		var usrMa=document.createElement("input");
		usrMa.setAttribute("name","usrMail");
		usrMa.setAttribute("value",usrMail);
		forma.appendChild(usrMa);
		
		var usrLvl=document.createElement("input");
		usrLvl.setAttribute("name","usrLevel");
		usrLvl.setAttribute("value",document.getElementById('selNivelUsr').value);
		forma.appendChild(usrLvl);
		
		if(isUpdateUser==1){
			var isUpdate=document.createElement("input");
			isUpdate.setAttribute("name","idUpdate");
			isUpdate.setAttribute("value",idUpdateUser);
			forma.appendChild(isUpdate);
		}
		
		//document.body.appendChild(forma);
		forma.submit();
		isUpdateUser=0;
		idUpdateUser=0;
	}else{
		alert('Hacen falta campos!');
		return;
	}
}

/**
 *Funcion encargada de verificar que los campos tengan contenido
 * @param usrName
 * @param usrPass
 * @param usrNombre
 * @param usrApps 
 * @param userMail
 * 
 * @return true si están todos los elementos
 */
function checkaAlta(usrNa,usrPa,usrNo,usrAp,usrMa){
	var ok=0;
	
	if(usrNa!='')
		ok++;
	if(usrPa!='')
		ok++;
	if(usrNo!='')
		ok++;
	if(usrAp!='')
		ok++;
	if(usrMa!='')
		ok++;
		
	if(ok==5)
		return true;
	else
		false;
}


/**
 *Funcion encargada de regresarse en caso de haber entrado al Alta 
 */
function cancelAlta(){
	$('#form-user').fadeOut("fast");
	$('#usrControls').fadeIn("fast");
}

function checaPass(){
	var ps1=document.getElementById('usrPass').value;
	var ps2=document.getElementById('usrPass2').value;
	
	if(ps2===ps1)
		return true;
	else{
		alert('Los Password no son iguales!');
		document.getElementById('usrPass2').focus();
		return false;
	}
	
}

/**
 *funcion que se encarga de verificar que es lo que se va a pedir al servidor 
 */
function getListado(isPedido){
	var busqueda="&filtro=";
	var valBusq="";
	var cve="&cve=";
	var valCve="";
	
	valBusq='div';
	valCve='';
	if(document.getElementById("selDivision").value!="*")
		valCve=document.getElementById("selDivision").value;
		
	if(document.getElementById("selZona").value!=""){
		valBusq='zon';
		valCve='';
		if(document.getElementById("selZona").value!="*"){
			valBusq='zon';
			valCve=document.getElementById("selZona").value;
		}
		else
			valCve='&granpa='+document.getElementById("selDivision").value;
	}
	
	if(document.getElementById("selArea").value!=""){
		valBusq='cac';
		valCve='';
		if(document.getElementById("selArea").value!="*"){
			valBusq='cac';
			valCve=document.getElementById("selArea").value;
		}
		else
			valCve='&granpa='+document.getElementById("selZona").value;
	}
	
	busqueda+=valBusq+cve+valCve;
	
	getAjax('listar','1'+busqueda,'contDescargas');
}

function habilitaBtn(valor){
	if(valor!='*')
		$("#btnSolicita").removeAttr("disabled");
		
}

function Solicitale(){
	var agencia=document.getElementById('selArea').value;
	var cant=document.getElementById('cantPedido').value;
	
	$.ajax({
		type:'GET',
		data:"accion=pedido&agencia="+agencia+"&cant="+cant,
		url: 'elAjax.php',
		dataType:'text',
		success:function(Resp){
			document.write('');
			document.write(Resp);
		}
	});
}

function copiale(laString){
	 window.prompt("Copiar al Porta-papeles: Ctrl+C, Enter", laString);
}

function recuperaPass(){
	
	var elMail=document.getElementById('usrMail').value;
	
	if(elMail===''){	
		var dialogo=document.createElement('div');
		dialogo.setAttribute('id','dialogo');
		dialogo.setAttribute('class','dialogo confirma-dead');
		dialogo.setAttribute('title','Error!');
		
		var parrafo=document.createElement('p');
		parrafo.textContent="No se ha ingresado ningun E-Mail";
		dialogo.appendChild(parrafo);
		
		$(dialogo).dialog({
			modal:true,
			resizable:false,
			dialogClass:'warning-dialog',
			buttons:{
				"Ok":function(){
					$(this).dialog("close");
				}			
			}
		});
	}
	
	$.ajax({
		type:'GET',
		data:"recoverpass=&mail="+elMail,
		url: 'PassRecovery.php',
		dataType:'text',
		success:function(Resp){
			if(Resp==='ok'){
				$('#capturaMail').fadeOut('fast');
				$('#mailReponse').fadeIn('slow');
			}
			else
				alert(Resp);
		}
	});
}
