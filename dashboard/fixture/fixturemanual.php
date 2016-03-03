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

//$resProductos = $serviciosProductos->traerProductosLimite(6);
$resMenu = $serviciosHTML->menu($_SESSION['nombre_predio'],"Fixture",$_SESSION['refroll_predio'],$_SESSION['torneo_predio']);





/////////////////////// Opciones para la creacion del formulario  /////////////////////
$tabla 			= "dbfixture";

$lblCambio	 	= array("reftorneoge_a","resultado_a","reftorneoge_b","resultado_b","fechajuego","refFecha","cancha");
$lblreemplazo	= array("Zona-Equipo 1","Resultado 1","Zona-Equipo 2","Resultado 2","Fecha Juego","Fecha","Cancha");

$resZonasEquipos 	= $serviciosZonasEquipos->TraerEquiposZonas();

$cadRef = '';
while ($rowTT = mysql_fetch_array($resZonasEquipos)) {
	$cadRef = $cadRef.'<option value="'.$rowTT[0].'">'.$rowTT[1].' - '.$rowTT[2].'</option>';
	
}


$resFechas 	= $serviciosFunciones->TraerFecha();

$cadRef2 = '';
while ($rowZ = mysql_fetch_array($resFechas)) {
	$cadRef2 = $cadRef2.'<option value="'.$rowZ[0].'">'.$rowZ[1].'</option>';
	
}

$resCanchas 	= $serviciosFunciones->TraerCanchas();

$cadRef3 = '';
while ($rowC = mysql_fetch_array($resCanchas)) {
	$cadRef3 = $cadRef3.'<option value="'.$rowC[0].'">'.$rowC[1].'</option>';
	
}


$resHorarios 	= $serviciosFunciones->TraerHorarios($_SESSION['idtorneo_predio']);

$cadRef4 = '';
while ($rowH = mysql_fetch_array($resHorarios)) {
	$cadRef4 = $cadRef4.'<option value="'.$rowH[0].'">'.$rowH[1].'</option>';
	
}


$refdescripcion = array(0 => $cadRef,1=>$cadRef,2=>$cadRef2,3=>$cadRef3,4=>$cadRef4);
$refCampo	 	= array("reftorneoge_a","reftorneoge_b","refFecha","cancha","Hora"); 
//////////////////////////////////////////////  FIN de los opciones //////////////////////////




/////////////////////// Opciones para la creacion del view  /////////////////////
$cabeceras 		= "	<th>Equipo 1</th>
				<th>Resultado 1</th>
				<th>Resultado 2</th>
				<th>Equipo 2</th>
				<th>Zona</th>
				<th>Fecha Juego</th>
				<th>Fecha</th>
				<th>Hora</th>
				<th>Chequeado</th>";

//////////////////////////////////////////////  FIN de los opciones //////////////////////////




$lstCargados 	= $serviciosFunciones->camposTablaView($cabeceras,$serviciosZonasEquipos->TraerTodoFixture(),98);


?>

<!DOCTYPE HTML>
<html>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">



<title>Gestión: Tres Sesenta Fútbol</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">


<link href="../../css/estiloDash.css" rel="stylesheet" type="text/css">
    

    
    <script type="text/javascript" src="../../js/jquery-1.8.3.min.js"></script>
    <link rel="stylesheet" href="../../css/jquery-ui.css">

    <script src="../../js/jquery-ui.js"></script>
    
	<!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css"/>
	<link href='http://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    <!-- Latest compiled and minified JavaScript -->
    <script src="../../bootstrap/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="../../css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="../../css/bootstrap-timepicker.css">
    <script src="../../js/bootstrap-timepicker.min.js"></script>
	<style type="text/css">
		
  
		
	</style>
    
   
   <link href="../../css/perfect-scrollbar.css" rel="stylesheet">
      <!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>-->
      <script src="../../js/jquery.mousewheel.js"></script>
      <script src="../../js/perfect-scrollbar.js"></script>
      <script>
      jQuery(document).ready(function ($) {
        "use strict";
        $('#navigation').perfectScrollbar();
      });
    </script>
</head>

<body>

 
<?php echo $resMenu; ?>

<div id="content">

<h3>Fixture</h3>

    <div class="boxInfoLargo">
        <div id="headBoxInfo">
        	<p style="color: #fff; font-size:18px; height:16px;">Carga del Fixture</p>
        	
        </div>
    	<div class="cuerpoBox">
            <form class="form-inline formulario" role="form" method="POST" action="generarfixturemanual.php">
                    <div class="row" style="margin-left:25px; margin-right:25px;">
                        <?php for ($i=1;$i<=12;$i++) { ?>
                        <div class="form-group col-md-4">
                        <label class="control-label" style="text-align:left" for="lbl">Equipo Local</label>
                            <div class="input-group col-md-12">
                                <select type="text" id="reftorneoge_a<?php echo $i; ?>" name="reftorneoge_a<?php echo $i; ?>" class="form-control"/>
                                    <option value="0">-- Seleccionar --</option>
                                    <?php echo $cadRef; ?>
                            
                                </select>
                            </div>
                        </div>




                        <div class="form-group col-md-1">
                        <label class="control-label" style="text-align:left" for="lbl">Res. L.</label>
                            <div class="input-group col-md-12">
                                <input type="text" id="resultado_a<?php echo $i; ?>" name="resultado_a<?php echo $i; ?>" class="form-control" />
                            </div>
                        </div>





                        <div class="form-group col-md-1">
                        <label class="control-label" style="text-align:left" for="lbl">Res. V.</label>
                            <div class="input-group col-md-12">
                                <input type="text" id="resultado_b<?php echo $i; ?>" name="resultado_b<?php echo $i; ?>" class="form-control"/>
                            </div>
                        </div>




                        <div class="form-group col-md-4">
                        <label class="control-label" style="text-align:left" for="lbl">Equipo Visitante</label>
                            <div class="input-group col-md-12">
                                <select type="text" id="reftorneoge_b<?php echo $i; ?>" name="reftorneoge_b<?php echo $i; ?>" class="form-control" required/>
                                    <option value="0">-- Seleccionar --</option>
                                    <?php echo $cadRef; ?>
                            
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group col-md-2">
                            <label class="control-label" style="text-align:left" for="lbl">Fecha</label>
                            <div class="input-group col-md-12">
                                <select class="form-control" id="reffecha<?php echo $i; ?>" name="reffecha<?php echo $i; ?>">
                                    <?php echo $cadRef2; ?>
                                </select>
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                
            <div class="row" style="margin-left:25px; margin-right:25px;">
                <div class="alert"> </div>
                <div id="load"> </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                <ul class="list-inline" style="margin-top:15px;">
                    <li>
                        <button type="submit" class="btn btn-primary" id="cargar" style="margin-left:0px;">Guardar</button>
                    </li>
                    
                </ul>
                </div>
            </div>
            
            <div class="row" align="center">
            	<ul class="list-inline">
                	<li>
                    	Seleccione una zona para cargar datos de los jugadores y los equipos
                    </li>
                </ul>
            </div>
            
            </form>
    	</div>
    </div>

    <div class="boxInfoLargo">
        <div id="headBoxInfo">
        	<p style="color: #fff; font-size:18px; height:16px;">Fixture Cargados</p>
        	
        </div>
    	<div class="cuerpoBox">
        	<?php echo $lstCargados; ?>
    	</div>
    </div>
    
   
</div>


</div>
<div id="dialog2" title="Eliminar Fixture">
    	<p>
        	<span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
            ¿Esta seguro que desea eliminar el fixture?.<span id="proveedorEli"></span>
        </p>
        <p><strong>Importante: </strong>Si elimina el fixture se perderan todos los datos de este</p>
        <input type="hidden" value="" id="idEliminar" name="idEliminar">
</div>

<script src="../../js/bootstrap-datetimepicker.min.js"></script>
<script src="../../js/bootstrap-datetimepicker.es.js"></script>



<script type="text/javascript">
$(document).ready(function(){
    $('.varborrar').click(function(event){
            usersid =  $(this).attr("id");
            if (!isNaN(usersid)) {
                  $("#idEliminar").val(usersid);
                  $("#dialog2").dialog("open");


                  //url = "../clienteseleccionado/index.php?idcliente=" + usersid;
                  //$(location).attr('href',url);
            } else {
                  alert("Error, vuelva a realizar la acción.");	
            }
  });//fin del boton eliminar
  
  $( "#dialog2" ).dialog({
		 	
			    autoOpen: false,
			 	resizable: false,
				width:600,
				height:240,
				modal: true,
				buttons: {
				    "Eliminar": function() {
	
						$.ajax({
									data:  {id: $('#idEliminar').val(), accion: 'eliminarFixture'},
									url:   '../../ajax/ajax.php',
									type:  'post',
									beforeSend: function () {
											
									},
									success:  function (response) {
											url = "fixturemanual.php";
											$(location).attr('href',url);
											
									}
							});
						$( this ).dialog( "close" );
						$( this ).dialog( "close" );
							$('html, body').animate({
	           					scrollTop: '1000px'
	       					},
	       					1500);
				    },
				    Cancelar: function() {
						$( this ).dialog( "close" );
				    }
				}
		 
		 
	 		}); //fin del dialogo para eliminar

});
</script>
<script type="text/javascript">
$('.form_date').datetimepicker({
	language:  'es',
	weekStart: 1,
	todayBtn:  1,
	autoclose: 1,
	todayHighlight: 1,
	startView: 2,
	minView: 2,
	forceParse: 0,
	format: 'dd/mm/yyyy'
});
</script>


<?php } ?>
</body>
</html>


