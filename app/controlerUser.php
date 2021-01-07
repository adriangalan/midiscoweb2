<?php
// ------------------------------------------------
// Controlador que realiza la gestión de usuarios
// ------------------------------------------------
include_once 'config.php';
include_once 'modeloUser.php';
include_once 'AccesoDatos.php';

/*
 * Inicio Muestra o procesa el formulario (POST)
 */

function  ctlUserInicio(){
    $msg = "";
    $user ="";
    $clave ="";
    if ( $_SERVER['REQUEST_METHOD'] == "POST"){
        if (isset($_POST['user']) && isset($_POST['clave'])){
            $user =$_POST['user'];
            $clave=$_POST['clave'];
            if ( modeloOkUser($user,$clave)){
                $_SESSION['user'] = $user;
                $_SESSION['tipouser'] = modeloObtenerTipo($user);
                if ( $_SESSION['tipouser'] == "Máster"){
                    $_SESSION['modo'] = GESTIONUSUARIOS;
                    header('Location:index.php?orden=VerUsuarios');
                }
                else {
                  // Usuario normal;
                  // PRIMERA VERSIÓN SOLO USUARIOS ADMISTRADORES
                  $msg="Error: Acceso solo permitido a usuarios Administradores.";
                  unset($_SESSION['user']);
                  // $_SESSION['modo'] = GESTIONFICHEROS;
                  // Cambio de modo y redireccion a verficheros
                }
            }
            else {
                $msg="Error: usuario y contraseña no válidos.";
           }  
        }
    }
    
    include_once 'plantilla/facceso.php';
}

// Cierra la sesión y vuelva los datos
function ctlUserCerrar(){
    AccesoDatos::closeModelo();
    session_destroy();
    header('Location:index.php');
}

// Muestro la tabla con los usuario 
function ctlUserVerUsuarios (){
    // Obtengo los datos del modelo
    $usuarios = modeloUserGetAll(); 
    // Invoco la vista 
    include_once 'plantilla/verusuariosp.php';
   
}
//Borra un usuario
function ctlUserBorrar() {
    if (isset($_GET['id'])) {
        modeloUserDel($_GET['id']);       
    }
    header('Location:index.php');
}
//modifica un usuario
function ctlUserModificar() {
    if (isset($_POST['nombre']) && isset($_POST['correo']) && isset($_POST['clave']) && isset($_POST['estado']) && isset($_POST['plan'])) {       
        limpiarArrayEntrada($_POST); //Evito la posible inyección de código          
        $user = new Usuario();                
        $user->nombre= $_POST['nombre'];
        $user->login=$_GET['id']; 
        $user->correo =$_POST['correo'];
        $user->password  =$_POST['clave'];
        $user->estado=$_POST['estado'];
        $user->plan =$_POST['plan'];        
        $error=comprobarErroresModificar($user);    
        if (!comprobarCorreoId($user->login,$user->correo)){
            $error='correo';
        }        
        if ($error!=FALSE) {                       
            header('Location:index.php?orden=Modificar&id='.$user->login.'&error='.$error);
            exit();
        }   
        modeloUserUpdate($user);
        header('Location:index.php?orden=VerUsuarios');
        exit;        
    }elseif(isset($_GET['id'])) {        
        $usuarios = modeloUserGet($_GET['id']); 
        $id=$_GET['id'];
    }
    include_once 'plantilla/fmodificar.php';
    
}
//Muestra los detalles de un usuario
function ctlUserDetalles() {
    if (isset($_GET['id'])) {
        $usuarios = modeloUserGet($_GET['id']); 
        $plan=PLANES[$usuarios->plan];
    }
    include_once 'plantilla/detalles.php';
}
//crear usuario
function ctlUserAlta() {
    if (isset($_POST) && isset($_POST['identificador'])) {
        limpiarArrayEntrada($_POST); //Evito la posible inyección de código
        $user = new Usuario(); 
        $user->login=$_POST['identificador'];
        $user->nombre  = $_POST['nombre'];
        $user->correo =$_POST['correo'];
        $user->password  =$_POST['clave'];
        $user->estado=$_POST['estado'];
        $user->plan =$_POST['plan'];
        $error=comprobarErroresUsuario($user);   
        if ($_POST['clave']!=$_POST['claveRepetida']) {
            $error='claveRepetida';
        }
        if (comprobarCorreoExiste($user->correo)){
            $error='correo';
        }
        if (comprobarIdentificadorExiste($user->login)){
            $error='identificador';
        }        
        if ($error!=FALSE) {                       
            header('Location:index.php?orden=Alta&error='.$error);
            exit();
        }        
        modeloUserAdd($user);
        header('Location:index.php?orden=VerUsuarios');        
    }    
    include_once 'plantilla/fnuevo.php';    
}
function ctlUserVerRegistro() {
    if (isset($_POST) && isset($_POST['identificador'])) {
        limpiarArrayEntrada($_POST); //Evito la posible inyección de código
        $user = new Usuario();
        $user->login=$_POST['identificador'];
        $user->nombre  = $_POST['nombre'];
        $user->correo =$_POST['correo'];
        $user->password  =$_POST['clave'];        
        $user->plan =$_POST['plan'];
        $user->estado="B";
        $error=comprobarErroresUsuario($user);
        if ($_POST['clave']!=$_POST['claveRepetida']) {
            $error='claveRepetida';
        }
        if (comprobarCorreoExiste($user->correo)){
            $error='correo';
        }
        if (comprobarIdentificadorExiste($user->login)){
            $error='identificador';
        }
        if ($error!=FALSE) {
            header('Location:index.php?orden=Registro&error='.$error);
            exit();            
        }               
        modeloUserAdd($user);       
        header('Location:index.php?orden=VerUsuarios');
        
    }
    
    include_once 'plantilla/fregistro.php';    
}
