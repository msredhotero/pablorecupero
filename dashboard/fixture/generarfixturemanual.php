<?php
session_start();

if (!isset($_SESSION['usua_predio']))
{
	header('Location: ../../error.php');
} else {


include ('../../includes/funcionesUsuarios.php');
include ('../../includes/funciones.php');
include ('../../includes/funcionesHTML.php');
include ('../../includes/funcionesJugadores.php');
include ('../../includes/funcionesEquipos.php');
include ('../../includes/funcionesGrupos.php');
include ('../../includes/funcionesZonasEquipos.php');
include ('../../includes/funcionesDATOS.php');

$serviciosUsuario 	= new ServiciosUsuarios();
$serviciosHTML 		= new ServiciosHTML();
$serviciosFunciones = new Servicios();
$serviciosJugadores = new ServiciosJ();
$serviciosEquipos	= new ServiciosE();
$serviciosGrupos	= new ServiciosG();
$serviciosZonasEquipos	= new ServiciosZonasEquipos();
$serviciosDatos		= new ServiciosDatos();

$fecha = date('Y-m-d');

$cadEquipoLocal = 'reftorneoge_a';
$cadEquipoVisitante = 'reftorneoge_b';

$cadResultadoLocal = 'resultado_a';
$cadResultadoVisitante = 'resultado_b';

$fecha = 'reffecha';

for ($i=1;$i<=12;$i++) {
    $idEquipoLocal      = $_POST[$cadEquipoLocal.$i];
    $idEquipoVisitante  = $_POST[$cadEquipoVisitante.$i];
    
    $resEquipoLocal     = $_POST[$cadResultadoLocal.$i];
    $resEquipoVisitante = $_POST[$cadResultadoVisitante.$i];
    
    $reffecha              = $_POST[$fecha.$i];
    
    if (($idEquipoLocal != 0) && ($idEquipoVisitante != 0)) {
        if ($idEquipoLocal != $idEquipoVisitante) {
            $serviciosZonasEquipos->insertarFixture($idEquipoLocal, $resEquipoLocal, $idEquipoVisitante, $resEquipoVisitante, $fecha, $reffecha, '', '');

        }
    }
    
    $cadEquipoLocal = 'reftorneoge_a';
    $cadEquipoVisitante = 'reftorneoge_b';

    $cadResultadoLocal = 'resultado_a';
    $cadResultadoVisitante = 'resultado_b';

    $fecha = 'reffecha';
}

header('Location: index.php');

}
?>

