<?php

// Guardo la salida en un buffer(en memoria)
// No se envia al navegador
$auto = $_SERVER['PHP_SELF'];
ob_start();
// FORMULARIO DE ALTA DE USUARIOS


?>
<div id='aviso'><b><?= (isset($msg))?$msg:"" ?></b></div>
<form name='MODIFICAR' method="POST" action="index.php?orden=Modificar&id=<?=$id?>">
<div class="center">
	<h1>Modificar Usuario</h1>
</div>
Identificador: <?=$usuarios->login?> <br>
Nombre:<input name="nombre" type="text" value="<?=$usuarios->nombre ?>" required><br>
<?=(isset($_GET['error']) && $_GET['error']=="nombre")?"<p>El nombre no cumple las normas<p>": "" ?>
Correo electronico: <input name="correo" type="text" value="<?=$usuarios->correo ?>" required><br>
<?=(isset($_GET['error']) && $_GET['error']=="correo")?"<p>El correo no cumple las normas<p>": "" ?>
Contraseña:<input name="clave" type="password" value="<?=$usuarios->password ?>" size=20>	<br>
<?=(isset($_GET['error']) && $_GET['error']=="clave")?"<p>La clave no cumple las normas<p>": "" ?>
Estado :<br>
<?=(isset($_GET['error']) && $_GET['error']=="estado")?"<p>Error en el estado<p>": "" ?>
<select name="estado" size="2">
	<option value="A" <?=($usuarios->estado=="A")?"selected":"" ?>>Activo</option>	
	<option value="B"  <?=($usuarios->estado=="B")?"selected":"" ?>>Bloqueado</option>
	<option value="I"  <?=($usuarios->estado=="I")?"selected":"" ?>>Desactivado</option>
</select> <br> 
Plan :<br>
<?=(isset($_GET['error']) && $_GET['error']=="plan")?"<p>Error en el plan<p>": "" ?>
<select name="plan" size="2">
	<option value="0" <?=($usuarios->plan==0)?"selected":"" ?>>Basico</option>
	<option value="1" <?=($usuarios->plan==1)?"selected":"" ?>>Profesional</option>
	<option value="2" <?=($usuarios->plan==2)?"selected":"" ?>>Premium</option>
</select><br>
	<input type="submit" value="Modificar Usuario">			
	<button><a href="<?= $auto?>?orden=VerUsuarios">cancelar</a></button>
</form>



<?php 
// Vacio el bufer y lo copio a contenido
// Para que se muestre en div de contenido
$contenido = ob_get_clean();
include_once "principal.php";

?>