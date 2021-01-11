<?php 
include_once 'config.php';
include_once 'AccesoDatos.php';
/* DATOS DE USUARIO
• Identificador ( 5 a 10 caracteres, no debe existir previamente, solo letras y números)
• Contraseña ( 8 a 15 caracteres, debe ser segura)
• Nombre ( Nombre y apellidos del usuario)
• Correo electrónico ( Valor válido de dirección correo, no debe existir previamente)
• Tipo de Plan (0-Básico |1-Profesional |2- Premium| 3- Máster)
• Estado: (A-Activo | B-Bloqueado |I-Inactivo )
*/
// Inicializo el modelo 
// Cargo los datos del fichero a la session
function modeloUserInit(){
       
    if (! isset ($_SESSION['tusuarios'] )){    
    $_SESSION['tusuarios'] = "";
   }

      
}

// Comprueba usuario y contraseña (boolean)
function modeloOkUser($login,$clave){
    $resu=false;
    $db = AccesoDatos::getModelo();
    $user = $db->getUsuario($login);
    if ($user) {
        $resu= ($user->password==$clave);       
    }
    return $resu;
}

// Devuelve el plan de usuario (String)
function modeloObtenerTipo($login){
    $db = AccesoDatos::getModelo();
    $user = $db->getUsuario($login);    
    return PLANES[$user->plan]; // Máster
}

// Borrar un usuario (boolean)*
function modeloUserDel($login){
    $db = AccesoDatos::getModelo();
    $tuser = $db->borrarUsuario($login);
}
// Añadir un nuevo usuario (boolean)
function modeloUserAdd($user){
    $db = AccesoDatos::getModelo();
    $tuser = $db->addUsuario($user);
}

// Actualizar un nuevo usuario (boolean)
function modeloUserUpdate ($user){  
    $db = AccesoDatos::getModelo();
    $db->modUsuario($user);                                    
   
}

// Tabla de todos los usuarios para visualizar
function modeloUserGetAll (){
    // Genero lo datos para la vista que no muestra la contraseña ni los códigos de estado o plan
    // sino su traducción a texto
    $tuservista=[];
    $db = AccesoDatos::getModelo();
    $tuser = $db->getUsuarios();
    foreach ($tuser as $user) {
        $tuservista[$user->login] = [$user->nombre,
            $user->correo,
            PLANES[$user->plan],
            ESTADOS[$user->estado]
        ];
        
        
    }    
    return $tuservista;
}
// Datos de un usuario para visualizar
function modeloUserGet ($login){
    $db = AccesoDatos::getModelo();
    $user = $db->getUsuario($login);    
    return $user;
}

// Vuelca los datos al fichero
function modeloUserSave(){
    
    $datosjon = json_encode($_SESSION['tusuarios']);
    file_put_contents(FILEUSER, $datosjon) or die ("Error al escribir en el fichero.");
    fclose($fich);
}

/*************************/
//chequeos

function comprobarErroresModificar($user) {    
    if (!comprobarNombre($user->nombre )) {        
        return 'nombre';       
    }
    if (!filter_var($user->correo, FILTER_VALIDATE_EMAIL)) {
        return 'correo';    
    }
    if (!comprobarClave($user->password)) {
        return 'clave'; 
    }
    if (!comprobarEstado($user->estado)) {
        return 'estado';
    }
    if (!comprobarPlan(intval($user->plan ))) {
        return 'plan';
    }
    return false;
}

function comprobarErroresUsuario($user) {
    if (!comprobarIdentificador($user->login )) {
        return 'identificador';
    }    
    if (!comprobarNombre($user->nombre )) {
        return 'nombre';
    }
    if (!filter_var($user->correo, FILTER_VALIDATE_EMAIL)) {
        return 'correo';
    }
    if (!comprobarClave($user->password)) {
        return 'clave';
    }    
    if (!comprobarEstado($user->estado)) {
        return 'estado';
    }
    if (!comprobarPlan(intval($user->plan ))) {
        return 'plan';
    }
    return false;
}
//identificador
function comprobarIdentificador($login) {
    $resultado=false;    
    
    if (strlen($login) >= 5  && strlen($login) <= 10 && ctype_alnum($login)   ) {
        $resultado=true;
    }
    return $resultado;
}
function comprobarIdentificadorExiste($login) {
    $db = AccesoDatos::getModelo();
    $user = $db->getUsuario($login);
    return $user;
}

//nombre
function comprobarNombre($nombre) {
    return ( strlen($nombre) <= 20);  
}
//contraseña 
function comprobarClave($clave) :bool { 
    $resu=false;
    if (( strlen($clave) >= 8  && strlen($clave) <= 15) && hayMayusculas($clave) && hayMinusculas($clave) && hayNoAlfanumerico($clave) ) {
        $resu=true;
    }
    return $resu;
}


// Funciones auxilires contraseña 


function hayMayusculas ($valor){
    for ($i=0; $i<strlen($valor); $i++){
        if ( ctype_upper($valor[$i]) )
            return true;
    }
    return false;
}

function hayMinusculas ($valor){
    for ($i=0; $i<strlen($valor); $i++){
        if ( ctype_lower($valor[$i]))
            return true;
    }
    return false;
}



function hayNoAlfanumerico ($valor){
    for ($i=0; $i<strlen($valor); $i++){
        if ( !ctype_alnum($valor[$i]) )
            return true;
    }
    return false;
}

//correo

function comprobarCorreoId($userid,$correo) {
    $resultado=false;   
        $db = AccesoDatos::getModelo();
        $user = $db->chekCorreo($correo);        
        if ($user) {
            $resultado=true;
            if($user->login==$userid && $user->correo ==$correo){
                $resultado=false;            
            }
        }   
    return $resultado;
}

function comprobarCorreoExiste($correo) {
    $resultado=false;    
        $db = AccesoDatos::getModelo();
        $user = $db->chekCorreo($correo);
        if ($user) {
            $resultado=true;
        }   
    return $resultado;
}

//Estado
function comprobarEstado($param) {
    $resul=false;
    if ($param=="A" || $param=="B" || $param=="I") {
        $resul=true;
    }
    return $resul;
}

//Plan
function comprobarPlan($param) {
    $resul=false;
    if ($param>=0 || $param<=3) {
        $resul=true;
    }
    return $resul;
}


/*
 *  Funciones para limpiar la entreda de posibles inyecciones
 */
function limpiarEntrada(string $entrada):string{
    $salida = trim($entrada); // Elimina espacios antes y después de los datos
    $salida = stripslashes($salida); // Elimina backslashes \
    $salida = htmlspecialchars($salida); // Traduce caracteres especiales en entidades HTML
    return $salida;
}
// Función para limpiar todos elementos de un array
function limpiarArrayEntrada(array &$entrada){
    
    foreach ($entrada as $key => $value ) {
        $entrada[$key] = limpiarEntrada($value);
    }
}
