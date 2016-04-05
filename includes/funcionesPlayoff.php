<?php

/**
 * @author www.intercambiosvirtuales.org
 * @copyright 2013
 */
date_default_timezone_set('America/Buenos_Aires');

class ServiciosPlayOff {
	
/* PARA PlayOff */

function insertarPlayOff($refequipo,$reftorneo,$refzona,$fechacreacion) {
$sql = "insert into dbplayoff(idplayoff,refequipo,reftorneo,refzona,fechacreacion)
values ('',".$refequipo.",".$reftorneo.",".$refzona.",'".utf8_decode($fechacreacion)."')";
$res = $this->query($sql,1);
return $res;
}


function modificarPlayOff($id,$refequipo,$reftorneo,$refzona,$fechacreacion) {
$sql = "update dbplayoff
set
refequipo = ".$refequipo.",reftorneo = ".$reftorneo.",refzona = ".$refzona.",fechacreacion = '".utf8_decode($fechacreacion)."'
where idplayoff =".$id;
$res = $this->query($sql,0);
return $res;
}


function eliminarPlayOff($id) {
$sql = "delete from dbplayoff where idplayoff =".$id;
$res = $this->query($sql,0);
return $res;
}


function traerPlayOff() {
$sql = "select idplayoff,e.nombre,t.nombre,g.nombre,p.fechacreacion,refequipo,reftorneo,refzona 
		from dbplayoff p
		inner join dbequipos e on e.idequipo = p.refequipo
		inner join dbgrupos g on g.idgrupo = p.refzona
		inner join dbtorneos t on t.idtorneo = p.reftorneo
		where t.reftipotorneo = ".$_SESSION['idtorneo_predio']." and t.activo = 1
		order by 1";
$res = $this->query($sql,0);
return $res;
}


function traerPlayOffTorneoZona() {
$sql = "select t.idtorneo,g.idgrupo,t.nombre,g.nombre
		from dbplayoff p
		inner join dbequipos e on e.idequipo = p.refequipo
		inner join dbgrupos g on g.idgrupo = p.refzona
		inner join dbtorneos t on t.idtorneo = p.reftorneo
		where t.reftipotorneo = ".$_SESSION['idtorneo_predio']." and t.activo = 1
		group by t.nombre,g.nombre,t.idtorneo,g.idgrupo
		order by 1";
$res = $this->query($sql,0);
return $res;
}

function traerPlayOffPorTorneoZona($idTorneo, $idZona) {
$sql = "select idplayoff,e.nombre,t.nombre,g.nombre,p.fechacreacion,refequipo,reftorneo,refzona 
		from dbplayoff p
		inner join dbequipos e on e.idequipo = p.refequipo
		inner join dbgrupos g on g.idgrupo = p.refzona
		inner join dbtorneos t on t.idtorneo = p.reftorneo
		where t.idtorneo = ".$idTorneo." and p.refzona = ".$idZona."
		order by 1";
$res = $this->query($sql,0);
return $res;
}


function traerPlayOffPorId($id) {
$sql = "select idplayoff,refequipo,reftorneo,refzona,fechacreacion from dbplayoff where idplayoff =".$id;
$res = $this->query($sql,0);
return $res;
}

/* Fin */


/* PARA Etapas */

function insertarEtapas($descripcion,$valor) {
$sql = "insert into tbetapas(idetapa,descripcion,valor)
values ('','".utf8_decode($descripcion)."',".$valor.")";
$res = $this->query($sql,1);
return $res;
}


function modificarEtapas($id,$descripcion,$valor) {
$sql = "update tbetapas
set
descripcion = '".utf8_decode($descripcion)."',valor = ".$valor."
where idetapa =".$id;
$res = $this->query($sql,0);
return $res;
}


function eliminarEtapas($id) {
$sql = "delete from tbetapas where idetapa =".$id;
$res = $this->query($sql,0);
return $res;
}


function traerEtapas() {
$sql = "select idetapa,descripcion,valor from tbetapas order by 1";
$res = $this->query($sql,0);
return $res;
}


function traerEtapasPorId($id) {
$sql = "select idetapa,descripcion,valor from tbetapas where idetapa =".$id;
$res = $this->query($sql,0);
return $res;
}

/* Fin */


/* PARA ArmarPlayOff */


function insertarArmarPlayOff($refplayoffequipo_a,$refplayoffresultado_a,$refplayoffequipo_b,$refplayoffresultado_b,$fechajuego,$hora,$refcancha,$chequeado,$refetapa,$penalesa,$penalesb,$refzona) {

if ($hora != '') {
$sqlH = "select
				h.idhorario,h.horario
				from tbhorarios h
				where		h.idhorario = ".$hora;
		$resH = mysql_result($this-> query($sqlH,0),0,1);
		$horario = $resH;
}
			
$sql = "insert into tbplayoff(idplayoff,refplayoffequipo_a,refplayoffresultado_a,refplayoffequipo_b,refplayoffresultado_b,fechajuego,hora,refcancha,chequeado,refetapa,penalesa,penalesb,refzona)
values ('',".$refplayoffequipo_a.",".$refplayoffresultado_a.",".$refplayoffequipo_b.",".$refplayoffresultado_b.",'".utf8_decode($fechajuego)."','".$horario."',".$refcancha.",".$chequeado.",".$refetapa.", ".($penalesa == '' ? 'null' : $penalesa).", ".($penalesb == '' ? 'null' : $penalesb).",".$refzona.")";
//return $sql;
$res = $this->query($sql,1);
return $res;
}


function modificarArmarPlayOff($id,$refplayoffequipo_a,$refplayoffresultado_a,$refplayoffequipo_b,$refplayoffresultado_b,$fechajuego,$hora,$refcancha,$chequeado,$refetapa,$penalesa,$penalesb,$refzona) {
$sql = "update tbplayoff
set
refplayoffequipo_a = ".$refplayoffequipo_a.",refplayoffresultado_a = ".$refplayoffresultado_a.",refplayoffequipo_b = ".$refplayoffequipo_b.",refplayoffresultado_b = ".$refplayoffresultado_b.",fechajuego = '".utf8_decode($fechajuego)."',hora = ".$hora.",refcancha = ".$refcancha.",chequeado = ".$chequeado.",refetapa = ".$refetapa.", penalesa = ".($penalesa == '' ? 'null' : $penalesa).", penalesb = ".($penalesb == '' ? 'null' : $penalesb).",refzona = ".$refzona."
where idplayoff =".$id;
$res = $this->query($sql,0);
return $res;
}


function eliminarArmarPlayOff($id) {
$sql = "delete from tbplayoff where idplayoff =".$id;
$res = $this->query($sql,0);
return $res;
}


function traerArmarPlayOff($idTorneo, $idZona) {
$sql = "select 
			p.idplayoff,
			(select 
					eq.nombre
				from
					dbequipos eq
						inner join
					dbplayoff pl ON eq.idequipo = pl.refequipo
				where
					pl.idplayoff = p.refplayoffequipo_a) as refplayoffequipo_a,
			refplayoffresultado_a,
			refplayoffresultado_b,
			(select 
					eq.nombre
				from
					dbequipos eq
						inner join
					dbplayoff pl ON eq.idequipo = pl.refequipo
				where
					pl.idplayoff = p.refplayoffequipo_b) as refplayoffequipo_b,
			
			t.nombre,
			g.nombre,
			fechajuego,
			e.descripcion,
			p.hora,
			p.penalesa,
			p.penalesb,
			refcancha,
			chequeado,
			refetapa
		from
			tbplayoff p
				inner join
			dbplayoff pp ON p.refplayoffequipo_a = pp.idplayoff and p.refzona = pp.refzona
				inner join
			dbtorneos t ON t.idtorneo = pp.reftorneo
				inner join
			dbgrupos g ON g.idgrupo = pp.refzona
				inner join
			tbetapas e ON p.refetapa = e.idetapa
				inner join
			tbcanchas c ON p.refcancha = c.idcancha
		where pp.refzona = ".$idZona." and pp.reftorneo = ".$idTorneo." and p.refzona =".$idZona."
		order by 1";
$res = $this->query($sql,0);
return $res;
}


function traerArmarPlayOffPorId($id) {
$sql = "select idplayoff,refplayoffequipo_a,refplayoffresultado_a,refplayoffequipo_b,refplayoffresultado_b,fechajuego,hora,refcancha,chequeado,refetapa from tbplayoff where idplayoff =".$id;
$res = $this->query($sql,0);
return $res;
}

/* Fin */	

function TraerEtaposPorTorneosZonas($idTorneo,$idZona) {
	$zonas4 = $this->traerZonaPorTipoTorneos($idTorneo);
	$cadZon = '';

	while ($rz = mysql_fetch_array($zonas4)) {
	
		$cadZon .= $rz['refgrupo'].",";	
	
	}
	
	$sql = "select 
			e.idetapa, e.descripcion, e.valor
		from
			tbplayoff p
				inner join
			dbplayoff pp ON p.refplayoffequipo_a = pp.idplayoff and p.refzona = pp.refzona
				inner join
			dbtorneos t ON t.idtorneo = pp.reftorneo
				inner join
			dbgrupos g ON g.idgrupo = pp.refzona
				inner join
			tbetapas e ON p.refetapa = e.idetapa
				inner join
			tbcanchas c ON p.refcancha = c.idcancha
		where pp.reftorneo = ".$idTorneo."
		group by e.idetapa, e.descripcion, e.valor
		order by e.idetapa";
	//return $sql;	
	$res = $this->query($sql,0);
	return $res;	
}

function traerArmarPlayOffPorEtapa($idTorneo, $idZona, $idEtapa) {
	$zonas4 = $this->traerZonaPorTipoTorneos($idTorneo);
	$cadZon = '';

	while ($rz = mysql_fetch_array($zonas4)) {
	
		$cadZon .= $rz['refgrupo'].",";	
	
	}
$sql = "select 
			p.idplayoff,
			(select 
					eq.nombre
				from
					dbequipos eq
						inner join
					dbplayoff pl ON eq.idequipo = pl.refequipo
				where
					pl.idplayoff = p.refplayoffequipo_a) as refplayoffequipo_a,
			refplayoffresultado_a,
			refplayoffresultado_b,
			(select 
					eq.nombre
				from
					dbequipos eq
						inner join
					dbplayoff pl ON eq.idequipo = pl.refequipo
				where
					pl.idplayoff = p.refplayoffequipo_b) as refplayoffequipo_b,
			
			t.nombre,
			g.nombre,
			fechajuego,
			e.descripcion,
			p.hora,
			refcancha,
			chequeado,
			refetapa,
			penalesa,
			penalesb
		from
			tbplayoff p
				inner join
			dbplayoff pp ON p.refplayoffequipo_a = pp.idplayoff
				inner join
			dbtorneos t ON t.idtorneo = pp.reftorneo
				inner join
			dbgrupos g ON g.idgrupo = pp.refzona
				inner join
			tbetapas e ON p.refetapa = e.idetapa
				inner join
			tbcanchas c ON p.refcancha = c.idcancha
		where pp.refzona in (".(substr($cadZon,0,strlen($cadZon)-1)).") and pp.reftorneo = ".$idTorneo." and p.refetapa = ".$idEtapa."
		order by 1";
$res = $this->query($sql,0);
return $res;
}


function traerJugadoresPorPlayOffA($idPlayOff,$idTorneo,$idZona) {
	$sql = "select
				j.idjugador,
				concat(j.apellido, ', ', j.nombre) as apyn,
				j.dni,
				ee.nombre,
				ee.idequipo,
			   
				a.goles,
				e.reftorneo
			from		(select
						distinct pp.refequipo, pp.reftorneo
						from		tbplayoff p
						inner join
						dbplayoff pp ON p.refplayoffequipo_a = pp.idplayoff
						where pp.refzona = ".$idZona." and pp.reftorneo = ".$idTorneo." and p.refzona =".$idZona." and p.idplayoff = ".$idPlayOff.") e
				left join		dbjugadores j on  j.idequipo = e.refequipo 
				inner join		dbequipos ee on ee.idequipo = e.refequipo
				left join		dbgolesplayoff a on a.refplayoff = ".$idPlayOff." and a.refjugador = j.idjugador
				order by concat(j.apellido, ', ', j.nombre)";	
	$res = $this->query($sql,0);
	return $res;
}


function traerJugadoresPorPlayOffB($idPlayOff,$idTorneo,$idZona) {
	$sql = "select
				j.idjugador,
				concat(j.apellido, ', ', j.nombre) as apyn,
				j.dni,
				ee.nombre,
				ee.idequipo,
			   
				a.goles,
				e.reftorneo
			from		(select
						distinct pp.refequipo, pp.reftorneo
						from		tbplayoff p
						inner join
						dbplayoff pp ON p.refplayoffequipo_b = pp.idplayoff
						where pp.refzona = ".$idZona." and pp.reftorneo = ".$idTorneo." and p.refzona =".$idZona." and p.idplayoff = ".$idPlayOff.") e
				left join		dbjugadores j on  j.idequipo = e.refequipo 
				inner join		dbequipos ee on ee.idequipo = e.refequipo
				left join		dbgolesplayoff a on a.refplayoff = ".$idPlayOff." and a.refjugador = j.idjugador
				order by concat(j.apellido, ', ', j.nombre)";	
	$res = $this->query($sql,0);
	return $res;
}


function traerZonaPorTipoTorneos($idtorneo) {
		$sql = "select 
                            distinct tge.refgrupo,g.nombre,tp.descripciontorneo
                        from
                            dbtorneos t
                                inner join
                            tbtipotorneo tp ON tp.idtipotorneo = t.reftipotorneo
                                        inner join
                                dbtorneoge tge ON tge.reftorneo = t.idtorneo
                                        inner join
                                dbgrupos g ON g.idgrupo = tge.refgrupo
                        where	t.activo = 1 and t.idtorneo = ".$idtorneo."
                        order by g.idgrupo ";	
		return $this-> query($sql,0);
	}


/* PARA GolesPlayoff */

function insertarGolesPlayoff($refplayoff,$reftorneo,$refzona,$refequipo,$refjugador,$goles) {
$sql = "insert into dbgolesplayoff(idgolesplayoff,refplayoff,reftorneo,refzona,refequipo,refjugador,goles)
values ('',".$refplayoff.",".$reftorneo.",".$refzona.",".$refequipo.",".$refjugador.",".$goles.")";
$res = $this->query($sql,1);
return $res;
}


function modificarGolesPlayoff($id,$refplayoff,$reftorneo,$refzona,$refequipo,$refjugador,$goles) {
$sql = "update dbgolesplayoff
set
refplayoff = ".$refplayoff.",reftorneo = ".$reftorneo.",refzona = ".$refzona.",refequipo = ".$refequipo.",refjugador = ".$refjugador.",goles = ".$goles."
where idgolesplayoff =".$id;
$res = $this->query($sql,0);
return $res;
}


function eliminarGolesPlayoff($id) {
$sql = "delete from dbgolesplayoff where idgolesplayoff =".$id;
$res = $this->query($sql,0);
return $res;
}


function traerGolesPlayoff() {
$sql = "select idgolesplayoff,refplayoff,reftorneo,refzona,refequipo,refjugador,goles from dbgolesplayoff order by 1";
$res = $this->query($sql,0);
return $res;
}


function traerGolesPlayoffPorId($id) {
$sql = "select idgolesplayoff,refplayoff,reftorneo,refzona,refequipo,refjugador,goles from dbgolesplayoff where idgolesplayoff =".$id;
$res = $this->query($sql,0);
return $res;
}

/* Fin */


	function query($sql,$accion) {
		
		require_once 'appconfig.php';

		$appconfig	= new appconfig();
		$datos		= $appconfig->conexion();
		$hostname	= $datos['hostname'];
		$database	= $datos['database'];
		$username	= $datos['username'];
		$password	= $datos['password'];
		
		
		$conex = mysql_connect($hostname,$username,$password) or die ("no se puede conectar".mysql_error());
		
		mysql_select_db($database);
		
		        $error = 0;
		mysql_query("BEGIN");
		$result=mysql_query($sql,$conex);
		if ($accion && $result) {
			$result = mysql_insert_id();
		}
		if(!$result){
			$error=1;
		}
		if($error==1){
			mysql_query("ROLLBACK");
			return false;
		}
		 else{
			mysql_query("COMMIT");
			return $result;
		}
		
	}
	
	
	
	
	} //fin de servicios


?>