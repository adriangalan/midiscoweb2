<?php

// Guardo la salida en un buffer(en memoria)
// No se envia al navegador
ob_start();
// FORMULARIO DE ALTA DE USUARIOS
?>
<div id='aviso'><b><?= (isset($msg))?$msg:"" ?></b></div>
<di class="center">
<h1 >Detalles de <?=$_GET['id'] ?> </h1>
<p>Nombre:<b><?=$usuarios->nombre ?></b></br>
Correo electronico: <b><?=$usuarios->correo ?></b></br>
Plan: <b><?=$plan ?></b></br>
Numero de ficheros<b></b></br>
Espacio Ocupado<b></b></p>
</di>
<form action='index.php'>
	<input type='hidden' name='orden' value='VerUsuarios'> <input type='submit'
		value='Volver'>
</form>


<?php 
// Vacio el bufer y lo copio a contenido
// Para que se muestre en div de contenido
$contenido = ob_get_clean();
include_once "principal.php";

?>