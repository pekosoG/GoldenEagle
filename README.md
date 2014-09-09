GoldenEagle
===========
##*Proyecto de Programacion Web 2014B*

El objetivo de este proyecto es generar un centro de peticiones, distribucion y descargas de archivos requeridos para el proyecto **Eagle**, interactuará en conjunto con un sistema que estará recibiendo las peticiones y generando los archivos.
Los archivos son identificados por una clave que se encuentra en un catalogo que los organiza de la siguiente manera:
* Division 
* Zona
* Agencia

###Usuarios

Este sitio contará con usuarios, estos usuarios son de 3 tipos diferentes:
* Admin : Podrá dar de alta usuarios (Supervisor y Basico). *Nivel 10*
* Supervisor : Podrá dar de alta Usuarios Básicos. *Nivel 5*
* Basico : Uso Limitado de funciones. *Nivel 1*

###Funciones

En el sitio se podrán realizar las siguientes acciones:

Accion | Descripcion | Nivel Minimo
-----------|------------------|------------------
Peticion | El usuario podrá solicitar la generación de archivos de la zona geografica que desee. El sistema avisará por medio de correo electronico cuando la solicitud esté lista. (AJAX) | 5
Busqueda | El usuario, por medio de un **filtro**, buscará archivos de la zona geografica que desee. (AJAX) | 1
Descarga | El usuario descagará el archivo seleccionado | 1
Compartir | El usuario compartirá una URL en la que se podrá descargar el archivo seleccionado. Esta URL expirará en un tiempo determinado | 1
Revisar Historial | El usuario podrá revisar su historial de acciones | 1

Toda accion será registrada en un historial, accesible solamente por el usuario loggeado. Excepto por el Admin, que podrá revisar el historial de cualquier Usuario.

###Datos

**Division**
 Campo | Tipo de Dato
 -----------|---------------
 ID |  int
 nombre | varchar(35)
 cve | varchar(6)
 
 **Zona**
 Campo | Tipo de Dato
 -----------|---------------
 ID |  int
 nombre | varchar(35)
 cve | varchar(6)
 id_div | int
 
 **Agencia**
 Campo | Tipo de Dato
 -----------|---------------
 ID |  int
 nombre | varchar(35)
 cve | varchar(6)
 id_zona | int
 
 **Usuarios**
 Campo | Tipo de Dato
 -----------|---------------
 username | varchar(15)
 pass | varchar(50)  //Estará en SHA1
 nivel | int
 last_logged | datetime
 
 **Archivos**
 Campo | Tipo de Dato
 -----------|---------------
 ID | int
 nombre | varchar(150)
 path | varchar(150)
 ultima_desc | datetime
 
 **Historial**
 Campo | Tipo de Dato
 ------------|---------------
 user | varchar(15)
 archivo | int
 fecha | datetime
 accion | varchar(20)

