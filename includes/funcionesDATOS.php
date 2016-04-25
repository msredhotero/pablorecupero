<?php

/**
 * @author www.intercambiosvirtuales.org
 * @copyright 2013
 */

date_default_timezone_set('America/Buenos_Aires');

class ServiciosDatos {
	
	function TraerFechaPorId($id) {
		$sql = "select * from tbfechas where idfecha =".$id;
		return $this-> query($sql,0);
	}
	
	function traerZonasPorTorneo($idtorneo) {
		$sql = "select tge.refgrupo,g.nombre, t.idtorneo 
				from dbtorneoge tge 
				inner join dbgrupos g on tge.refgrupo = g.idgrupo
				inner 
				        join dbtorneos t
				        on tge.reftorneo = t.idtorneo and t.activo = 1
				inner 
				        join tbtipotorneo tp
				        on t.reftipotorneo = tp.idtipotorneo
				where tp.idtipotorneo =".$idtorneo." 
				group by	refgrupo,nombre, t.idtorneo ";
		return $this->query($sql,0);	
	}
	
	function traerResultadosPorTorneoZonaFecha($idtorneo,$idzona,$idfecha) {
		$sql = "select 

		       (select ea.nombre from dbequipos ea where ea.idequipo = t.equipoa) as equipo1,
		       t.resultadoa,
		       t.resultadob,
		       (select ea.nombre from dbequipos ea where ea.idequipo = t.equipob) as equipo2, 
		       t.fechajuego,
		       t.fecha,
		       t.hora,
			   t.idfixture,
			   t.cancha
		 
				from (
				select 
				fi.idfixture,
				(select e.idequipo 
				        from dbtorneoge tge

				        inner 
				        join dbtorneos t
				        on tge.reftorneo = t.idtorneo and t.activo = 1
				        
						inner 
				        join tbtipotorneo tp
				        on t.reftipotorneo = tp.idtipotorneo

				        inner 
				        join dbequipos e
				        on e.idequipo = tge.refequipo
				        
				        inner 
				        join dbgrupos g
				        on g.idgrupo = tge.refgrupo
				        where tge.idtorneoge = fi.reftorneoge_a 
				        	  and g.idgrupo=".$idzona." 
				        	  and tp.idtipotorneo = ".$idtorneo.") as equipoa,
				
				(case when fi.resultado_a is null then (select
					(case when sum(gg.goles) is null 
						then (case when fi.chequeado = 1 then 0 else null end) 
						else sum(gg.goles) - (select coalesce(sum(aaa.arquero),0) from tbamonestados aaa where aaa.reffixture = fi.idfixture
							and aaa.refequipo = (select 
								tge.refequipo
							from
								dbtorneoge tge
							inner join dbtorneos t ON tge.reftorneo = t.idtorneo
								and t.activo = true
							inner join dbequipos e ON e.idequipo = tge.refequipo
							inner join dbgrupos g ON g.idgrupo = tge.refgrupo
							where
								tge.idtorneoge = fi.reftorneoge_a))
							end)
					from		tbgoleadores gg
					where gg.reffixture = fi.idfixture and gg.refequipo = (select tge.refequipo 
							from dbtorneoge tge
							inner 
							join dbtorneos t
							on tge.reftorneo = t.idtorneo and t.activo = true
							inner 
							join dbequipos e
							on e.idequipo = tge.refequipo
							inner 
							join dbgrupos g
							on g.idgrupo = tge.refgrupo
							where tge.idtorneoge = fi.reftorneoge_a))
				else fi.resultado_a end) as resultadoa,
				
				(select e.idequipo 
				        from dbtorneoge tge

				        inner 
				        join dbtorneos t
				        on tge.reftorneo = t.idtorneo and t.activo = 1
				        
						inner 
				        join tbtipotorneo tp
				        on t.reftipotorneo = tp.idtipotorneo

				        inner 
				        join dbequipos e
				        on e.idequipo = tge.refequipo
				        
				        inner 
				        join dbgrupos g
				        on g.idgrupo = tge.refgrupo
				        where tge.idtorneoge = fi.reftorneoge_b and g.idgrupo=".$idzona." and tp.idtipotorneo = ".$idtorneo.") as equipob,
				
				(case when fi.resultado_b is null then (select
								(case when sum(gg.goles) is null 
									then (case when fi.chequeado = 1 then 0 else null end) 
									else sum(gg.goles) - (select coalesce(sum(aaa.arquero),0) from tbamonestados aaa where aaa.reffixture = fi.idfixture
									and aaa.refequipo = (select 
										tge.refequipo
									from
										dbtorneoge tge
									inner join dbtorneos t ON tge.reftorneo = t.idtorneo
										and t.activo = true
									inner join dbequipos e ON e.idequipo = tge.refequipo
									inner join dbgrupos g ON g.idgrupo = tge.refgrupo
									where
										tge.idtorneoge = fi.reftorneoge_b))
								end)
							from		tbgoleadores gg
							where gg.reffixture = fi.idfixture and gg.refequipo = (select tge.refequipo 
						from dbtorneoge tge
						inner 
						join dbtorneos t
						on tge.reftorneo = t.idtorneo and t.activo = true
						inner 
						join dbequipos e
						on e.idequipo = tge.refequipo
						inner 
						join dbgrupos g
						on g.idgrupo = tge.refgrupo
						where tge.idtorneoge = fi.reftorneoge_b))
							else fi.resultado_b end) as resultadob,
				
				(select g.nombre
				        from dbtorneoge tge

				        inner 
				        join dbtorneos t
				        on tge.reftorneo = t.idtorneo and t.activo = 1
				        
						inner 
				        join tbtipotorneo tp
				        on t.reftipotorneo = tp.idtipotorneo

				        inner 
				        join dbequipos e
				        on e.idequipo = tge.refequipo
				        
				        inner 
				        join dbgrupos g
				        on g.idgrupo = tge.refgrupo
				        where tge.idtorneoge = fi.reftorneoge_b and g.idgrupo=".$idzona." and tp.idtipotorneo = ".$idtorneo.") as zona,
				        
				        
				fi.fechajuego,
				f.idfecha as fecha,
				DATE_FORMAT(fi.hora,'%k:%i') as hora,
				fi.cancha
				
				
				from dbfixture as fi
				        inner 
				        join tbfechas AS f
				        on fi.reffecha = f.idfecha
				
				        inner 
				        join dbtorneoge tge
				        on tge.idtorneoge = fi.reftorneoge_b

				        inner 
				        join dbtorneos t
				        on tge.reftorneo = t.idtorneo and t.activo = 1
				        
						inner 
				        join tbtipotorneo tp
				        on t.reftipotorneo = tp.idtipotorneo

				        inner 
				        join dbgrupos g
				        on g.idgrupo = tge.refgrupo
				
				where g.idgrupo=".$idzona." and tp.idtipotorneo = ".$idtorneo."
				order by fecha desc
				) as t
				where t.fecha = ".$idfecha;
		$res = $this->query($sql,0);
		return $res;
	}
	
	
	
	function traerPuntosReemplazo($idequipo) {
		$sqlR = "select puntos from dbreemplazo where refequipo = ".$idequipo;
		$resRR = $this->query($sqlR,0);	
		$puntos = 0;
		if (mysql_num_rows($resRR)>0) {
			$puntos = mysql_result($resRR,0,0);
		} else {
			$puntos = 0;	
		}
		return $puntos;
	}
	
	function UltimaFechaPorTorneoZona($idtorneo,$idzona) {
		$sql = "select 
					ff.idfecha, ff.tipofecha
				from
					dbfixture fi
						inner join
					tbfechas ff ON ff.idfecha = fi.reffecha
						inner join
					dbtorneoge tge ON (tge.idtorneoge = fi.reftorneoge_a
						or tge.idtorneoge = fi.reftorneoge_b)
						inner join
					dbtorneos t ON t.idtorneo = tge.reftorneo
				where
					fi.chequeado = 1 and t.reftipotorneo =".$idtorneo."
						and tge.refgrupo in (".$idzona.")
				group by ff.idfecha , ff.tipofecha
				order by ff.idfecha desc
				limit 1";	
		return $this-> query($sql,0);
	}
	
	function TraerFixturePorZonaTorneo($idtorneo,$zona,$idfecha) {
		
		$sqlTorneo = "select idtorneo from dbtorneos where reftipotorneo = ".$idtorneo." and activo = 1";
		$refTorneo = mysql_result($this->query($sqlTorneo,0),0,0);
		
		$idfecha = $this->UltimaFechaPorTorneoZona($idtorneo,$zona);
		
		if (mysql_num_rows($idfecha)>0) {
		
			$idfecha = mysql_result($idfecha,0,0);	
		
		} else {
		
			$idfecha = 23;	
		
		}
		
		$sql = '
			select
			fix.nombre,
			fix.partidos,
			fix.ganados,
			fix.empatados,
			fix.perdidos,
			COALESCE(fix.golesafavor,0) as golesafavor,
			COALESCE((case when rr.idreemplazo is null then fix.golesencontra + COALESCE(rrr.golesencontra,0) else fix.golesencontra + rr.golesencontra end),0) as golesencontra,
			COALESCE(fix.golesafavor - (case when rr.idreemplazo is null then fix.golesencontra + COALESCE(rrr.golesencontra,0) else fix.golesencontra + rr.golesencontra end),0) as diferencia,
			((case when rr.idreemplazo is null then fix.pts + COALESCE(rrr.puntos,0) else fix.pts + rr.puntos end)
			-
			COALESCE((case 	when fix.puntos >= 15 and fix.puntos< 20 then 1
					when fix.puntos >= 20 and fix.puntos < 25 then 2
					when fix.puntos >= 25 then 3
			end),0)
			) + fix.bonus as pts,
			fix.idequipo,
			fix.puntos,
			fix.equipoactivo,
			cast((fix.golesafavor / fix.partidos) as decimal(4,2)) as porcentajegoles,
			round((fix.pts * 100) / (fix.partidos * 3)) as efectividad,
			/*(select count(*) from tbsuspendidos where refequipo = fix.idequipo and (motivos = "Roja Directa" or motivos = "Doble Amarilla")) as rojas,*/
			coalesce(ro.rojas,0) as rojas,
			/*(select sum(amarillas) from tbamonestados where refequipo = fix.idequipo and amarillas <> 2) as amarillas,*/
			coalesce(aaa.amarillas,0) as amarillas,
			(case when rr.idreemplazo is null then 0 else 1 end) as reemplzado,
(case when rrr.idreemplazo is null then 0 else 1 end) as volvio,
				fix.bonus,
	(case
        when rv.idreemplazovolvio is null then 0
        else 1
    end) as reemplzadovolvio
			from
			(
				select 
		       r.nombre,
		       sum(case when r.resultado_a is null then 0 else 1 end) as partidos,
		       sum(case when r.resultado_a > r.resultado_b then 1 else 0 end) as ganados, 
		       sum(case when r.resultado_a = r.resultado_b then 1 else 0 end) as empatados,
		       sum(case when r.resultado_a < r.resultado_b then 1 else 0 end) as perdidos,
		       sum(r.resultado_a) as golesafavor,
		       sum(r.resultado_b) as golesencontra,
		       (sum(r.resultado_a) - sum(r.resultado_b)) as diferencia,
		       ((sum(case when r.resultado_a > r.resultado_b then 1 else 0 end) * 2) +
		        (sum(case when r.resultado_a = r.resultado_b then 1 else 0 end) * 1)) as pts,
		        r.idequipo,
				max(case
                when r.resultado_a is null then 0
                else fp.puntos
           		 end) as puntos,
				(case when r.equipoactivo = 0 then false else true end) as equipoactivo,
		r.idtorneo, sum(r.bonus) as bonus
		
				from (
				SELECT
				e.idequipo,
				e.nombre,
				t.activo,
				t.idtorneo,
				f.tipofecha,
				fi.hora,
				(case when fi.resultado_a is null then (select
							(case when sum(gg.goles) is null 
								then (case when fi.chequeado = 1 then 0 else null end) 
								else sum(gg.goles) - (select coalesce(sum(aaa.arquero),0) from tbamonestados aaa where aaa.reffixture = fi.idfixture
								and aaa.refequipo = (select 
									tge.refequipo
								from
									dbtorneoge tge
								inner join dbtorneos t ON tge.reftorneo = t.idtorneo
									and t.activo = true
								inner join dbequipos e ON e.idequipo = tge.refequipo
								inner join dbgrupos g ON g.idgrupo = tge.refgrupo
								where
									tge.idtorneoge = fi.reftorneoge_a))
							end)
						from		tbgoleadores gg
						where gg.reffixture = fi.idfixture and gg.refequipo = (select tge.refequipo 
								from dbtorneoge tge
								inner 
								join dbtorneos t
								on tge.reftorneo = t.idtorneo and t.activo = true
								inner 
								join dbequipos e
								on e.idequipo = tge.refequipo
								inner 
								join dbgrupos g
								on g.idgrupo = tge.refgrupo
								where tge.idtorneoge = fi.reftorneoge_a))
				else fi.resultado_a end) as resultado_a,
				(case when fi.resultado_b is null then (select
									(case when sum(gg.goles) is null 
										then (case when fi.chequeado = 1 then 0 else null end) 
										else sum(gg.goles) - (select coalesce(sum(aaa.arquero),0) from tbamonestados aaa where aaa.reffixture = fi.idfixture
										and aaa.refequipo = (select 
											tge.refequipo
										from
											dbtorneoge tge
										inner join dbtorneos t ON tge.reftorneo = t.idtorneo
											and t.activo = true
										inner join dbequipos e ON e.idequipo = tge.refequipo
										inner join dbgrupos g ON g.idgrupo = tge.refgrupo
										where
											tge.idtorneoge = fi.reftorneoge_b))
									end)
								from		tbgoleadores gg
								where gg.reffixture = fi.idfixture and gg.refequipo = (select tge.refequipo 
						from dbtorneoge tge
						inner 
						join dbtorneos t
						on tge.reftorneo = t.idtorneo and t.activo = true
						inner 
						join dbequipos e
						on e.idequipo = tge.refequipo
						inner 
						join dbgrupos g
						on g.idgrupo = tge.refgrupo
						where tge.idtorneoge = fi.reftorneoge_b))
							else fi.resultado_b end) as resultado_b,
				fi.reffecha,
				tge.refgrupo,
				tge.activo as equipoactivo,
					pe.puntos as bonus
				FROM
				dbtorneoge tge
				Inner Join dbequipos e ON tge.refequipo = e.idequipo
				inner join dbgrupos g on tge.refgrupo = g.idgrupo
				Inner Join dbtorneos t ON t.idtorneo = tge.reftorneo
				Inner Join dbfixture fi ON tge.idtorneoge = fi.reftorneoge_a
				inner join tbtipotorneo tp ON tp.idtipotorneo = t.reftipotorneo
				inner join tbfechas f ON fi.refFecha = f.idfecha
				inner join tbpuntosequipos pe on pe.refequipo = e.idequipo and pe.reffixture = fi.idfixture and pe.reffecha = f.idfecha and pe.reftorneo = t.idtorneo
				where tge.refgrupo = '.$zona.'
				and tp.idtipotorneo = '.$idtorneo.'
				and fi.reffecha <= '.$idfecha.' 
				and t.activo = 1
				UNION all
				
				SELECT
				e.idequipo,
				e.nombre,
				t.activo,
				t.idtorneo,
				f.tipofecha,
				fi.hora,
				(case when fi.resultado_b is null then (select
										(case when sum(gg.goles) is null 
											then (case when fi.chequeado = 1 then 0 else null end) 
											else sum(gg.goles) - (select coalesce(sum(aaa.arquero),0) from tbamonestados aaa where aaa.reffixture = fi.idfixture
											and aaa.refequipo = (select 
												tge.refequipo
											from
												dbtorneoge tge
											inner join dbtorneos t ON tge.reftorneo = t.idtorneo
												and t.activo = true
											inner join dbequipos e ON e.idequipo = tge.refequipo
											inner join dbgrupos g ON g.idgrupo = tge.refgrupo
											where
												tge.idtorneoge = fi.reftorneoge_b))
										end)
									from		tbgoleadores gg
									where gg.reffixture = fi.idfixture and gg.refequipo = (select tge.refequipo 														
						from dbtorneoge tge
						inner 
						join dbtorneos t
						on tge.reftorneo = t.idtorneo and t.activo = true
						inner 
						join dbequipos e
						on e.idequipo = tge.refequipo
						inner 
						join dbgrupos g
						on g.idgrupo = tge.refgrupo
						where tge.idtorneoge = fi.reftorneoge_b))
							else fi.resultado_b end) as resultado_b,
				(case when fi.resultado_a is null then (select
												(case when sum(gg.goles) is null 
													then (case when fi.chequeado = 1 then 0 else null end) 
													else sum(gg.goles) - (select coalesce(sum(aaa.arquero),0) from tbamonestados aaa where aaa.reffixture = fi.idfixture
																		and aaa.refequipo = (select 
																			tge.refequipo
																		from
																			dbtorneoge tge
																		inner join dbtorneos t ON tge.reftorneo = t.idtorneo
																			and t.activo = true
																		inner join dbequipos e ON e.idequipo = tge.refequipo
																		inner join dbgrupos g ON g.idgrupo = tge.refgrupo
																		where
																			tge.idtorneoge = fi.reftorneoge_a))
												end)
												from		tbgoleadores gg
												where gg.reffixture = fi.idfixture and gg.refequipo = (select tge.refequipo 
																										from dbtorneoge tge
																										inner 
																										join dbtorneos t
																										on tge.reftorneo = t.idtorneo and t.activo = true
																										inner 
																										join dbequipos e
																										on e.idequipo = tge.refequipo
																										inner 
																										join dbgrupos g
																										on g.idgrupo = tge.refgrupo
																										where tge.idtorneoge = fi.reftorneoge_a))
				else fi.resultado_a end) as resultado_a,
				fi.reffecha,
				tge.refgrupo,
				tge.activo as equipoactivo,
				pe.puntos as bonus	
				FROM
				dbtorneoge tge
				Inner Join dbequipos e ON tge.refequipo = e.idequipo
				inner join dbgrupos g on tge.refgrupo = g.idgrupo
				Inner Join dbtorneos t ON t.idtorneo = tge.reftorneo
				Inner Join dbfixture fi ON tge.idtorneoge = fi.reftorneoge_b
				inner join tbtipotorneo tp ON tp.idtipotorneo = t.reftipotorneo
				inner join tbfechas f ON fi.refFecha = f.idfecha
				inner join tbpuntosequipos pe on pe.refequipo = e.idequipo and pe.reffixture = fi.idfixture and pe.reffecha = f.idfecha and pe.reftorneo = t.idtorneo
				where tge.refgrupo = '.$zona.'
				and tp.idtipotorneo = '.$idtorneo.'
				and fi.reffecha <= '.$idfecha.' 
				and t.activo = 1
				
				union all
				
				 SELECT 
        e.idequipo,
            e.nombre,
            t.activo,
            t.idtorneo,
            f.tipofecha,
            fi.hora,
            null as resultado_b,
            null as resultado_a,
            fi.reffecha,
            tge.refgrupo,
            tge.activo as equipoactivo,
            0 as bonus

    FROM
        dbtorneoge tge
    Inner Join dbequipos e ON tge.refequipo = e.idequipo
    inner join dbgrupos g ON tge.refgrupo = g.idgrupo
    Inner Join dbtorneos t ON t.idtorneo = tge.reftorneo
    Inner Join dbfixture fi ON tge.idtorneoge = fi.reftorneoge_b or tge.idtorneoge = fi.reftorneoge_a
    inner join tbtipotorneo tp ON tp.idtipotorneo = t.reftipotorneo
    inner join tbfechas f ON fi.refFecha = f.idfecha

    where tge.refgrupo = '.$zona.'
				and tp.idtipotorneo = '.$idtorneo.'
				and fi.reffecha <= '.$idfecha.' 
				and t.activo = 1
			and fi.jugo = 0 and fi.chequeado = 0
				
				) as r
				left
				join	(select refequipo,max(puntos) as puntos, reftorneo from tbconducta where reffecha <='.$idfecha.' group by refequipo,reftorneo
				) fp
				on		r.idequipo = fp.refequipo and fp.reftorneo = r.idtorneo
				left join dbtorneos t ON t.idtorneo = fp.reftorneo and t.activo = 1
				group by r.nombre,r.idequipo 
) as fix

left join dbreemplazo rr on rr.refequiporeemplazado = fix.idequipo and rr.reffecha <= '.$idfecha.' and rr.reftorneo = fix.idtorneo
left join dbreemplazo rrr on rrr.refequipo = fix.idequipo and rrr.reffecha <= '.$idfecha.' and rrr.reftorneo = fix.idtorneo
left join
	dbreemplazovolvio rv ON rv.refreemplazo = rrr.idreemplazo and rv.refzona = '.$zona.'
	
	left join
	(select

	sa.nombre,
	sum(sa.puntos) as amarillas,
	sa.idequipo
from (
select 
					f.tipofecha,
						e.nombre,
						count(a.amarillas) as puntos,
						f.idfecha,
						e.idequipo
				from
					tbamonestados a
				inner join dbequipos e ON e.idequipo = a.refequipo
				inner join dbfixture fix ON fix.idfixture = a.reffixture
				inner join tbfechas f ON f.idfecha = fix.reffecha
				inner join dbtorneoge tge ON tge.refequipo = e.idequipo
					and fix.reftorneoge_a = tge.idtorneoge
				where
					a.amarillas = 1 and tge.reftorneo = '.$refTorneo.'
						and fix.reffecha <= '.$idfecha.'
				group by f.tipofecha , e.nombre , f.idfecha , e.idequipo 
				
				union all 
				
				select 
					f.tipofecha,
						e.nombre,
						count(a.amarillas) as puntos,
						f.idfecha,
						e.idequipo
				from
					tbamonestados a
				inner join dbequipos e ON e.idequipo = a.refequipo
				inner join dbfixture fix ON fix.idfixture = a.reffixture
				inner join tbfechas f ON f.idfecha = fix.reffecha
				inner join dbtorneoge tge ON tge.refequipo = e.idequipo
					and fix.reftorneoge_b = tge.idtorneoge 
				where
					a.amarillas = 1 and tge.reftorneo = '.$refTorneo.'
						and fix.reffecha <= '.$idfecha.'
				group by f.tipofecha , e.nombre , f.idfecha , e.idequipo) sa
			group by 
			sa.nombre,
			sa.idequipo) aaa ON aaa.idequipo = fix.idequipo
			
			
	left join
	(select

	sa.nombre,
	sum(sa.puntos) as rojas,
	sa.idequipo
from (
select 
					f.tipofecha,
						e.nombre,
						sum(1) as puntos,
						f.idfecha,
						e.idequipo
				from
					tbsuspendidos a
				inner join dbequipos e ON e.idequipo = a.refequipo
				inner join (select 
					refsuspendido, min(reffecha) as idfecha
				from
					dbsuspendidosfechas
				group by refsuspendido) sp ON sp.refsuspendido = a.idsuspendido
				inner join tbfechas f ON f.idfecha = sp.idfecha - 1
				inner join dbfixture fix ON fix.idfixture = a.reffixture
				inner join dbtorneoge tge ON tge.refequipo = e.idequipo
					and fix.reftorneoge_a = tge.idtorneoge
				where
					tge.reftorneo = '.$refTorneo.' and fix.reffecha <= '.$idfecha.' and (a.motivos like "%Roja Directa%" or a.motivos like "%Doble Amarilla%") 
				group by f.tipofecha , e.nombre , f.idfecha , e.idequipo 
				
				union all 
				
				select 
					f.tipofecha,
						e.nombre,
						sum(1) as puntos,
						f.idfecha,
						e.idequipo
				from
					tbsuspendidos a
				inner join dbequipos e ON e.idequipo = a.refequipo
				inner join (select 
					refsuspendido, min(reffecha) as idfecha
				from
					dbsuspendidosfechas
				group by refsuspendido) sp ON sp.refsuspendido = a.idsuspendido
				inner join tbfechas f ON f.idfecha = sp.idfecha - 1
				inner join dbfixture fix ON fix.idfixture = a.reffixture
				inner join dbtorneoge tge ON tge.refequipo = e.idequipo
					and fix.reftorneoge_b = tge.idtorneoge
				where
					tge.reftorneo = '.$refTorneo.' and fix.reffecha <= '.$idfecha.' and (a.motivos like "%Roja Directa%" or a.motivos like "%Doble Amarilla%") 
				group by f.tipofecha , e.nombre , f.idfecha , e.idequipo) sa
			group by 
			sa.nombre,
			sa.idequipo) ro ON ro.idequipo = fix.idequipo
	
	
				order by (case when rr.idreemplazo is null then fix.pts + fix.bonus + COALESCE(rrr.puntos,0) else fix.pts + rr.puntos end) desc, 
	  fix.golesafavor - (case when rr.idreemplazo is null then fix.golesencontra + COALESCE(rrr.golesencontra,0) else fix.golesencontra + rr.golesencontra end) desc,fix.puntos,
	  fix.golesafavor desc,
	  (case when rr.idreemplazo is null then fix.golesencontra + COALESCE(rrr.golesencontra,0) else fix.golesencontra + rr.golesencontra end),
	  fix.ganados desc';
		$res = $this->query($sql,0);
		return $res;	
		//return $sql;	
	}
	
	
	
	function Goleadores($idtorneo,$zona,$idfecha) {
		
		$sqlTorneo = "select idtorneo from dbtorneos where reftipotorneo = ".$idtorneo." and activo = 1";
		$refTorneo = mysql_result($this->query($sqlTorneo,0),0,0);
		
		$idfecha = $this->UltimaFechaPorTorneoZona($idtorneo,$zona);
		
		if (mysql_num_rows($idfecha)>0) {
		
			$idfecha = mysql_result($idfecha,0,0);	
		
		} else {
		
			$idfecha = 23;	
		
		}
		
		$sql = 'select
				t.apyn,t.nombre,t.cantidad,t.reemplzado, t.volvio, t.refequipo, t.refjugador, t.reemplzadovolvio
				from
				(
				select
				r.apyn, r.nombre, sum(r.goles) as cantidad,r.reemplzado, r.volvio,r.refequipo, r.refjugador,r.reemplzadovolvio
				from
				(
					select
					concat(j.apellido,", ",j.nombre) as apyn, e.nombre, a.goles, 
					(case when rr.idreemplazo is null then 0 else 1 end) as reemplzado,
					(case when rrr.idreemplazo is null then 0 else 1 end) as volvio,
					a.refequipo,
					a.refjugador,
					(case
						when rv.idreemplazovolvio is null then 0
						else 1
					end) as reemplzadovolvio
					from	tbgoleadores a
					inner
					join	dbfixture fi
					on		a.reffixture = fi.Idfixture
					inner
					join	dbjugadores j
					on		j.idjugador = a.refjugador
					inner
					join	dbequipos e
					on		e.idequipo = a.refequipo
					inner 
					join dbtorneoge tge
					on tge.idtorneoge = fi.reftorneoge_b
				
					inner 
					join dbtorneos t
					on tge.reftorneo = t.idtorneo and t.activo = 1
					
					inner 
					join tbtipotorneo tp
					on t.reftipotorneo = tp.idtipotorneo
					
left join dbreemplazo rr on rr.refequiporeemplazado = a.refequipo and rr.reffecha <= '.$idfecha.' and rr.reftorneo = t.idtorneo
left join dbreemplazo rrr on rrr.refequipo = a.refequipo and rrr.reffecha <= '.$idfecha.' and rrr.reftorneo = t.idtorneo
left join
	dbreemplazovolvio rv ON rv.refreemplazo = rrr.idreemplazo and rv.refzona in ('.$zona.')			
					where	tp.idtipotorneo = '.$idtorneo.' and tge.refgrupo in ('.$zona.') and fi.reffecha <= '.$idfecha.'
					
					union all
					
					SELECT 
						CONCAT(j.apellido, ", ", j.nombre) AS apyn,
						e.nombre,
						gp.goles,
						0 AS reemplzado,
						0 AS volvio,
						gp.refequipo,
						gp.refjugador,
						0 AS reemplzadovolvio
					FROM
						dbgolesplayoff gp
							INNER JOIN
						dbjugadores j ON gp.refjugador = j.idjugador
							inner join
						dbequipos e ON e.idequipo = gp.refequipo
					WHERE
						gp.reftorneo = '.$refTorneo.'
							
				) r
				group by r.nombre,r.apyn ,r.reemplzado, r.volvio,r.refequipo, r.refjugador, r.reemplzadovolvio
				) t
				order by t.cantidad desc,t.nombre desc';
			return $this-> query($sql,0);
	}
	
	
	
	function Suspendidos($idtorneo,$zona) {
		$sql = 'select
				t.apyn,t.nombre, t.motivos,t.cantidad,t.reffecha, t.refjugador, t.refequipo
				, t.equipoactivo
				, t.volvio, r.reemplzadovolvio
				from
				(
				select
				r.apyn, r.nombre, r.motivos, r.cantidadfechas as cantidad,r.reffecha, r.refjugador, r.refequipo
				, r.equipoactivo
				, r.volvio,
	(case
        when rv.idreemplazovolvio is null then 0
        else 1
    end) as reemplzadovolvio
				from
				(
					select
					concat(j.apellido,", ",j.nombre) as apyn, e.nombre, a.motivos, a.cantidadfechas,fi.reffecha, a.refjugador, a.refequipo ,
(case when rr.idreemplazo is null then 0 else 1 end) as reemplzado,
(case when rrr.idreemplazo is null then 0 else 1 end) as volvio

					from	tbsuspendidos a
					inner
					join	dbfixture fi
					on		a.reffixture = fi.Idfixture
					inner
					join	dbjugadores j
					on		j.idjugador = a.refjugador
					inner
					join	dbequipos e
					on		e.idequipo = a.refequipo
					inner 
					join dbtorneoge tge
					on tge.idtorneoge = fi.reftorneoge_b
				
					inner 
					join dbtorneos t
					on tge.reftorneo = t.idtorneo and t.activo = 1
					
					inner
					join dbsuspendidosfechas sf
					on sf.refjugador = a.refjugador and sf.refequipo = a.refequipo

					inner 
					join tbtipotorneo tp
					on t.reftipotorneo = tp.idtipotorneo

left join dbreemplazo rr on rr.refequiporeemplazado = fix.idequipo and rr.reffecha <= '.$idfecha.' and rr.reftorneo = t.idtorneo
left join dbreemplazo rrr on rrr.refequipo = fix.idequipo and rrr.reffecha <= '.$idfecha.' and rrr.reftorneo = '.$idtorneo.' and rrr.reftorneo = t.idtorneo
left join
	dbreemplazovolvio rv ON rv.refreemplazo = rrr.idreemplazo and rv.refzona = '.$zona.'					
					where	tp.idtipotorneo = '.$idtorneo.' and tge.refgrupo = '.$zona.'

				) r
				group by r.apyn, r.nombre, r.motivos,r.reffecha,r.cantidadfechas, r.refjugador, r.refequipo, r.equipoactivo
				) t
				order by t.cantidad desc';
			return $this-> query($sql,0);
	}
	
	
	function SuspendidosNuevo($idtorneo,$zona,$reffecha) {
		
				$sql = 'select
				r.apyn, r.nombre, r.motivos, r.cantidadfechas as cantidad,r.reffecha, r.refjugador, r.refequipo
				, r.refsuspendido ,r.reemplzado , r.volvio, r.reemplzadovolvio

				from
				(
				select
				concat(j.apellido,", ",j.nombre) as apyn, e.nombre, ss.motivos, ss.cantidadfechas,min(sp.reffecha) - 1 as reffecha, ss.refjugador, ss.refequipo,sp.refsuspendido,
(case when rr.idreemplazo is null then 0 else 1 end) as reemplzado,
(case when rrr.idreemplazo is null then 0 else 1 end) as volvio,
	(case
        when rv.idreemplazovolvio is null then 0
        else 1
    end) as reemplzadovolvio
				from		tbsuspendidos ss
				inner
				join		dbsuspendidosfechas sp
				on			ss.refjugador = sp.refjugador and ss.refequipo = sp.refequipo and ss.idsuspendido = sp.refsuspendido
				inner
				join	dbjugadores j
				on		j.idjugador = ss.refjugador and j.expulsado <> 1
				inner
				join	dbequipos e
				on		e.idequipo = ss.refequipo
				inner join (select distinct ff.Idfixture,t.idtorneo from dbfixture ff
				inner join dbtorneoge tge ON tge.idtorneoge = ff.reftorneoge_a or tge.idtorneoge = ff.reftorneoge_b
				inner join dbtorneos t ON tge.reftorneo = t.idtorneo
				inner join tbtipotorneo tp ON t.reftipotorneo = tp.idtipotorneo
				where t.activo = 1 and t.reftipotorneo = '.$idtorneo.' and tge.refgrupo in ('.$zona.')) d
				on			d.idfixture = ss.reffixture
									
left join dbreemplazo rr on rr.refequiporeemplazado = e.idequipo and rr.reffecha <= '.$reffecha.' and rr.reftorneo = d.idtorneo
left join dbreemplazo rrr on rrr.refequipo = e.idequipo and rrr.reffecha <= '.$reffecha.' and rrr.reftorneo = d.idtorneo								
left join
	dbreemplazovolvio rv ON rv.refreemplazo = rrr.idreemplazo and rv.refzona in ('.$zona.')
										
				where	sp.reffecha <= '.$reffecha.' +1
				group by j.apellido, j.nombre, e.nombre, ss.motivos, ss.cantidadfechas, ss.refjugador, ss.refequipo,sp.refsuspendido,j.expulsado
				) r
				
				order by r.nombre, r.apyn,r.cantidadfechas desc';
			return $this-> query($sql,0);
			//return $sql;
	}
	
	
	function SuspendidosUltimaFecha($idtorneo,$zona,$reffecha) {
		$resCantDeEquipos = $this->traerResultadosPorTorneoZonaFecha($idtorneo,$zona,$reffecha);
		
		$cantEquipos = (mysql_num_rows($resCantDeEquipos)*4) + 20;
		
		
				$sql = 'select
				r.apyn, r.nombre, r.motivos, r.cantidadfechas as cantidad,r.reffecha, r.refjugador, r.refequipo
				, r.refsuspendido ,r.reemplzado , r.volvio, r.reemplzadovolvio

				from
				(
				select
				concat(j.apellido,", ",j.nombre) as apyn, e.nombre, ss.motivos, ss.cantidadfechas,min(sp.reffecha) as reffecha, ss.refjugador, ss.refequipo,sp.refsuspendido,
(case when rr.idreemplazo is null then 0 else 1 end) as reemplzado,
(case when rrr.idreemplazo is null then 0 else 1 end) as volvio,
	(case
        when rv.idreemplazovolvio is null then 0
        else 1
    end) as reemplzadovolvio
				from		tbsuspendidos ss
				inner
				join		dbsuspendidosfechas sp
				on			ss.refjugador = sp.refjugador and ss.refequipo = sp.refequipo and ss.idsuspendido = sp.refsuspendido
				inner
				join	dbjugadores j
				on		j.idjugador = ss.refjugador and j.expulsado <> 1
				inner
				join	dbequipos e
				on		e.idequipo = ss.refequipo
				inner join (select distinct ff.Idfixture,t.idtorneo, ff.reffecha from dbfixture ff
				inner join dbtorneoge tge ON tge.idtorneoge = ff.reftorneoge_a or tge.idtorneoge = ff.reftorneoge_b
				inner join dbtorneos t ON tge.reftorneo = t.idtorneo
				inner join tbtipotorneo tp ON t.reftipotorneo = tp.idtipotorneo
				where t.activo = 1 and t.reftipotorneo = '.$idtorneo.' and tge.refgrupo = '.$zona.') d
				on			d.idfixture = ss.reffixture
									
left join dbreemplazo rr on rr.refequiporeemplazado = e.idequipo and rr.reffecha <= '.$reffecha.' and rr.reftorneo = d.idtorneo
left join dbreemplazo rrr on rrr.refequipo = e.idequipo and rrr.reffecha <= '.$reffecha.' and rrr.reftorneo = d.idtorneo								
left join
	dbreemplazovolvio rv ON rv.refreemplazo = rrr.idreemplazo and rv.refzona = '.$zona.' 
										
				where	d.reffecha = '.$cantEquipos.'
				group by j.apellido,j.nombre, e.nombre, ss.motivos, ss.cantidadfechas, ss.refjugador, ss.refequipo,sp.refsuspendido,j.expulsado
				) r
				
				order by r.nombre, r.apyn,r.cantidadfechas desc';
			return $this-> query($sql,0);
			//return $sql;
	}
	
	function SuspendidosPorSiempre($idtorneo,$zona,$reffecha) {
		
			$sql = 'select
				r.apyn, r.nombre, r.motivos, r.cantidadfechas as cantidad,r.reffecha, r.refjugador, r.refequipo
				, r.refsuspendido ,r.reemplzado , r.volvio, r.reemplzadovolvio

				from
				(
				select
				concat(j.apellido,", ",j.nombre) as apyn, e.nombre, ss.motivos, ss.cantidadfechas,min(sp.reffecha) - 1 as reffecha, ss.refjugador, ss.refequipo,sp.refsuspendido,
(case when rr.idreemplazo is null then 0 else 1 end) as reemplzado,
(case when rrr.idreemplazo is null then 0 else 1 end) as volvio,
	(case
        when rv.idreemplazovolvio is null then 0
        else 1
    end) as reemplzadovolvio
				from		tbsuspendidos ss
				inner
				join		dbsuspendidosfechas sp
				on			ss.refjugador = sp.refjugador and ss.refequipo = sp.refequipo and ss.idsuspendido = sp.refsuspendido
				inner
				join	dbjugadores j
				on		j.idjugador = ss.refjugador and j.expulsado = 1
				inner
				join	dbequipos e
				on		e.idequipo = ss.refequipo
				inner join (select distinct ff.Idfixture,t.idtorneo from dbfixture ff
				inner join dbtorneoge tge ON tge.idtorneoge = ff.reftorneoge_a or tge.idtorneoge = ff.reftorneoge_b
				inner join dbtorneos t ON tge.reftorneo = t.idtorneo
				inner join tbtipotorneo tp ON t.reftipotorneo = tp.idtipotorneo
				where t.activo = 1 and t.reftipotorneo = '.$idtorneo.' and tge.refgrupo = '.$zona.') d
				on			d.idfixture = ss.reffixture
									
left join dbreemplazo rr on rr.refequiporeemplazado = e.idequipo and rr.reffecha <= '.$reffecha.' and rr.reftorneo = d.idtorneo
left join dbreemplazo rrr on rrr.refequipo = e.idequipo and rrr.reffecha <= '.$reffecha.' and rrr.reftorneo = d.idtorneo								
left join
	dbreemplazovolvio rv ON rv.refreemplazo = rrr.idreemplazo and rv.refzona = '.$zona.' 
										
				where	sp.reffecha <= '.$reffecha.' +1
				group by j.apellido,j.nombre, e.nombre, ss.motivos, ss.cantidadfechas, ss.refjugador, ss.refequipo,sp.refsuspendido,j.expulsado
				) r
				
				order by r.nombre, r.apyn,r.cantidadfechas desc';
			return $this-> query($sql,0);
	}
	
	function traerFechasRestantes($reffecha,$idjugador,$idequipo,$refsuspendido) {
		$sqlRest = "SELECT count(*) FROM dbsuspendidosfechas where reffecha <= ".$reffecha." and refequipo =".$idequipo." and refjugador =".$idjugador." and refsuspendido =".$refsuspendido;
		//return $sqlRest;
		$resRest = $this-> query($sqlRest,0);
		$restan = 0;
		if (mysql_num_rows($resRest)>0) {
			$restan = mysql_result($resRest,0,0);	
		}
		return $restan;
	}
	
	
	function TraerJugadoresFixtureA($idfixture) {
		$sql = "
				select
				j.idjugador as idjugadorA,
				(case when ss.idsuspendido is not null then '1' else null end) as RojasA,
				a.amarillas as amarillasA,
				g.goles as golesA,
				j.apyn as apynA
				from		dbjugadores j
				inner
				join		dbequipos e
				on			j.idequipo = e.idequipo
				inner
				join		dbtorneoge tge
				on			tge.refequipo = e.idequipo
				inner
				join		dbfixture fi
				on			fi.reftorneoge_a = tge.idtorneoge 
				
				left
				join		tbgoleadores g
				on			g.refjugador = j.idjugador and g.refequipo = j.idequipo and g.reffixture = fi.idfixture
				
				left
				join		tbamonestados a
				on			a.refjugador = j.idjugador and a.refequipo = j.idequipo and a.reffixture = fi.idfixture and a.amarillas <> 2
				
				left
				join		tbsuspendidos ss
				on			ss.refjugador = j.idjugador and ss.refequipo = j.idequipo and ss.reffixture = fi.idfixture and (ss.motivos like '%Roja Directa%' or ss.motivos like '%Doble Amarilla%')
				
				where		fi.idfixture = ".$idfixture;
		return $this-> query($sql,0);	
		
	}
	
	function TraerJugadoresFixtureB($idfixture) {
		$sql = "
				select
				j.idjugador as idjugadorB,
				(case when ss.idsuspendido is not null then '1' else null end) as RojasB,
				a.amarillas as amarillasB,
				g.goles as golesB,
				j.apyn as apynB
				from		dbjugadores j
				inner
				join		dbequipos e
				on			j.idequipo = e.idequipo
				inner
				join		dbtorneoge tge
				on			tge.refequipo = e.idequipo
				inner
				join		dbfixture fi
				on			fi.reftorneoge_b = tge.idtorneoge 
				
				left
				join		tbgoleadores g
				on			g.refjugador = j.idjugador and g.refequipo = j.idequipo and g.reffixture = fi.idfixture
				
				left
				join		tbamonestados a
				on			a.refjugador = j.idjugador and a.refequipo = j.idequipo and a.reffixture = fi.idfixture and a.amarillas <> 2
				
				left
				join		tbsuspendidos ss
				on			ss.refjugador = j.idjugador and ss.refequipo = j.idequipo and ss.reffixture = fi.idfixture and (ss.motivos like '%Roja Directa%' or ss.motivos like '%Doble Amarilla%')
				
				where		fi.idfixture = ".$idfixture;
		return $this-> query($sql,0);	
		
	}
	
	
	
	
	/* funciones para los suspendidos */
	
	function traerSuspendidosEnFechasActualPorAmarillas($idtipoTorneo,$fecha) {
		$sql = "select
				t.refequipo, t.nombre, t.apyn, t.dni, (case when t.cantidad > 3 then mod(t.cantidad,3) else t.cantidad end) as cantidad,ultimafecha
				from
				(
				select
					a.refequipo, e.nombre, concat(j.apellido,', ',j.nombre) as apyn, j.dni, count(a.amarillas) as cantidad,max(fi.reffecha) as ultimafecha
					from		tbamonestados a
					inner
					join		dbequipos e
					on			e.idequipo = a.refequipo
					inner
					join		dbjugadores j
					on			j.idjugador = a.refjugador
					inner
					join		dbfixture fi
					on			fi.idfixture = a.reffixture
					where	refequipo in (select
											distinct e.idequipo
											from		dbtorneoge tge
											inner
											join		dbequipos e
											on			e.idequipo = tge.refequipo
											inner
											join		dbfixture fix
											on			fix.reftorneoge_a = tge.idtorneoge or fix.reftorneoge_b = tge.idtorneoge
											inner
											join		dbtorneos t
											on			t.idtorneo = tge.reftorneo
											inner
											join		tbtipotorneo tp
											on			tp.idtipotorneo = t.reftipotorneo
											where		tp.idtipotorneo = ".$idtipoTorneo.")
					and a.amarillas <> 2
					group by a.refequipo, e.nombre, j.apellido,j.nombre, j.dni
				) t
				where ultimafecha = ".$fecha." and cantidad = 3
					
					order by t.nombre, t.apyn";	
		
		return $this-> query($sql,0);		
	}
	
	
	
	function traerSuspendidosEnFechasActualPorZonasAmarillas($idtipoTorneo,$idzona,$fecha) {
		$sql = "select
				t.refequipo, t.nombre, t.apyn, t.dni, (case when t.cantidad > 3 then mod(t.cantidad,3) else t.cantidad end) as cantidad,ultimafecha
				from
				(
				select
					a.refequipo, e.nombre, concat(j.apellido,', ',j.nombre) as apyn, j.dni, count(a.amarillas) as cantidad,max(fi.reffecha) as ultimafecha
					from		tbamonestados a
					inner
					join		dbequipos e
					on			e.idequipo = a.refequipo
					inner
					join		dbjugadores j
					on			j.idjugador = a.refjugador
					inner
					join		dbfixture fi
					on			fi.idfixture = a.reffixture
					where	refequipo in (select
											distinct e.idequipo
											from		dbtorneoge tge
											inner
											join		dbequipos e
											on			e.idequipo = tge.refequipo
											inner
											join		dbfixture fix
											on			fix.reftorneoge_a = tge.idtorneoge or fix.reftorneoge_b = tge.idtorneoge
											inner
											join		dbtorneos t
											on			t.idtorneo = tge.reftorneo
											inner
											join		tbtipotorneo tp
											on			tp.idtipotorneo = t.reftipotorneo
											where		tp.idtipotorneo = ".$idtipoTorneo." and tge.refgrupo = ".$idzona.")
					and a.amarillas <> 2
					group by a.refequipo, e.nombre, j.apellido,j.nombre, j.dni
				) t
				where ultimafecha = ".$fecha." and cantidad = 3
					
					order by t.nombre, t.apyn";	
		
		return $this-> query($sql,0);		
	}
	
	
	
	
	function traerSuspendidosEnFechasActualPorZonasEquiposAmarillas($idtipoTorneo,$idzona,$idequipo,$fecha) {
		$sql = "select
				t.refequipo, t.nombre, t.apyn, t.dni, (case when t.cantidad > 3 then mod(t.cantidad,3) else t.cantidad end) as cantidad,ultimafecha
				from
				(
				select
					a.refequipo, e.nombre, concat(j.apellido,', ',j.nombre) as apyn, j.dni, count(a.amarillas) as cantidad,max(fi.reffecha) as ultimafecha
					from		tbamonestados a
					inner
					join		dbequipos e
					on			e.idequipo = a.refequipo
					inner
					join		dbjugadores j
					on			j.idjugador = a.refjugador
					inner
					join		dbfixture fi
					on			fi.idfixture = a.reffixture
					where	refequipo in (select
											distinct e.idequipo
											from		dbtorneoge tge
											inner
											join		dbequipos e
											on			e.idequipo = tge.refequipo
											inner
											join		dbfixture fix
											on			fix.reftorneoge_a = tge.idtorneoge or fix.reftorneoge_b = tge.idtorneoge
											inner
											join		dbtorneos t
											on			t.idtorneo = tge.reftorneo
											inner
											join		tbtipotorneo tp
											on			tp.idtipotorneo = t.reftipotorneo
											where		tp.idtipotorneo = ".$idtipoTorneo." and tge.refgrupo = ".$idzona." and e.idequipo = ".$idequipo.")
					and a.amarillas <> 2
					group by a.refequipo, e.nombre, j.apellido,j.nombre, j.dni
				) t
				where ultimafecha = ".$fecha." and cantidad = 3
					
					order by t.nombre, t.apyn";	
		
		return $this-> query($sql,0);		
	}
	
	
	function traerAmarillasPorTorneo($idtipotorneo) {
		$sql = "select
				t.refequipo, t.nombre, sum(t.cantidad) as cantidad
				from
				(
					select
					a.refequipo, e.nombre, count(a.amarillas) as cantidad
					from		tbamonestados a
					inner
					join		dbequipos e
					on			e.idequipo = a.refequipo
					where	refequipo in (select
											distinct e.idequipo
											from		dbtorneoge tge
											inner
											join		dbequipos e
											on			e.idequipo = tge.refequipo
											inner
											join		dbfixture fix
											on			fix.reftorneoge_a = tge.idtorneoge or fix.reftorneoge_b = tge.idtorneoge
											inner
											join		dbtorneos t
											on			t.idtorneo = tge.reftorneo
											inner
											join		tbtipotorneo tp
											on			tp.idtipotorneo = t.reftipotorneo
											where		tp.idtipotorneo = ".$idtipotorneo.")
					and a.amarillas <> 2
					group by a.refequipo, e.nombre
				
					union all
				
				
					select
					a.refequipo, e.nombre, count(a.amarillas)*3 as cantidad
					from		tbamonestados a
					inner
					join		dbequipos e
					on			e.idequipo = a.refequipo
					where	refequipo in (select
											distinct e.idequipo
											from		dbtorneoge tge
											inner
											join		dbequipos e
											on			e.idequipo = tge.refequipo
											inner
											join		dbfixture fix
											on			fix.reftorneoge_a = tge.idtorneoge or fix.reftorneoge_b = tge.idtorneoge
											inner
											join		dbtorneos t
											on			t.idtorneo = tge.reftorneo
											inner
											join		tbtipotorneo tp
											on			tp.idtipotorneo = t.reftipotorneo
											where		tp.idtipotorneo = ".$idtipotorneo.")
					and a.amarillas = 2
					group by a.refequipo, e.nombre
				) t
				group by t.refequipo, t.nombre
				order by t.nombre";
				
		return $this-> query($sql,0);
		
	}
	
	
	
	function traerAmarillasPorTorneoZona($idtipotorneo,$idzona) {
		$sql = "select
				t.refequipo, t.nombre, sum(t.cantidad) as cantidad
				from
				(
					select
					a.refequipo, e.nombre, count(a.amarillas) as cantidad
					from		tbamonestados a
					inner
					join		dbequipos e
					on			e.idequipo = a.refequipo
					where	refequipo in (select
											distinct e.idequipo
											from		dbtorneoge tge
											inner
											join		dbequipos e
											on			e.idequipo = tge.refequipo
											inner
											join		dbfixture fix
											on			fix.reftorneoge_a = tge.idtorneoge or fix.reftorneoge_b = tge.idtorneoge
											inner
											join		dbtorneos t
											on			t.idtorneo = tge.reftorneo
											inner
											join		tbtipotorneo tp
											on			tp.idtipotorneo = t.reftipotorneo
											where		tp.idtipotorneo = ".$idtipotorneo." and tge.refgrupo = ".$idzona.")
					and a.amarillas <> 2
					group by a.refequipo, e.nombre
				
					union all
				
				
					select
					a.refequipo, e.nombre, count(a.amarillas)*3 as cantidad
					from		tbamonestados a
					inner
					join		dbequipos e
					on			e.idequipo = a.refequipo
					where	refequipo in (select
											distinct e.idequipo
											from		dbtorneoge tge
											inner
											join		dbequipos e
											on			e.idequipo = tge.refequipo
											inner
											join		dbfixture fix
											on			fix.reftorneoge_a = tge.idtorneoge or fix.reftorneoge_b = tge.idtorneoge
											inner
											join		dbtorneos t
											on			t.idtorneo = tge.reftorneo
											inner
											join		tbtipotorneo tp
											on			tp.idtipotorneo = t.reftipotorneo
											where		tp.idtipotorneo = ".$idtipotorneo." and tge.refgrupo = ".$idzona.")
					and a.amarillas = 2
					group by a.refequipo, e.nombre
				) t
				group by t.refequipo, t.nombre
				order by t.nombre";
				
		return $this-> query($sql,0);
		
	}
	
	
	
	function traerAmarillasPorTorneoZonaEquipo($idtipotorneo,$idzona,$idequipo) {
		$sql = "select
				t.refequipo, t.nombre, sum(t.cantidad) as cantidad
				from
				(
					select
					a.refequipo, e.nombre, count(a.amarillas) as cantidad
					from		tbamonestados a
					inner
					join		dbequipos e
					on			e.idequipo = a.refequipo
					where	refequipo in (select
											distinct e.idequipo
											from		dbtorneoge tge
											inner
											join		dbequipos e
											on			e.idequipo = tge.refequipo
											inner
											join		dbfixture fix
											on			fix.reftorneoge_a = tge.idtorneoge or fix.reftorneoge_b = tge.idtorneoge
											inner
											join		dbtorneos t
											on			t.idtorneo = tge.reftorneo
											inner
											join		tbtipotorneo tp
											on			tp.idtipotorneo = t.reftipotorneo
											where		tp.idtipotorneo = ".$idtipotorneo." and tge.refgrupo = ".$idzona." and e.idequipo = ".$idequipo.")
					and a.amarillas <> 2
					group by a.refequipo, e.nombre
				
					union all
				
				
					select
					a.refequipo, e.nombre, count(a.amarillas)*3 as cantidad
					from		tbamonestados a
					inner
					join		dbequipos e
					on			e.idequipo = a.refequipo
					where	refequipo in (select
											distinct e.idequipo
											from		dbtorneoge tge
											inner
											join		dbequipos e
											on			e.idequipo = tge.refequipo
											inner
											join		dbfixture fix
											on			fix.reftorneoge_a = tge.idtorneoge or fix.reftorneoge_b = tge.idtorneoge
											inner
											join		dbtorneos t
											on			t.idtorneo = tge.reftorneo
											inner
											join		tbtipotorneo tp
											on			tp.idtipotorneo = t.reftipotorneo
											where		tp.idtipotorneo = ".$idtipotorneo." and tge.refgrupo = ".$idzona." and e.idequipo = ".$idequipo.")
					and a.amarillas = 2
					group by a.refequipo, e.nombre
				) t
				group by t.refequipo, t.nombre
				order by t.nombre";
				
		return $this-> query($sql,0);
		
	}
	
	
	function traerAcumuladosAmarillasPorTorneo($idtipoTorneo,$idfecha) {
		$sql = "select
				t.refequipo, t.nombre, t.apyn, t.dni, (case when t.cantidad > 3 then mod(t.cantidad,3) else t.cantidad end) as cantidad,ultimafecha,fecha
				from
				(
				select
					a.refequipo, e.nombre, concat(j.apellido,', ',j.nombre) as apyn, j.dni, count(a.amarillas) as cantidad,max(fi.reffecha) as ultimafecha, max(ff.tipofecha) as fecha
					from		tbamonestados a
					inner
					join		dbequipos e
					on			e.idequipo = a.refequipo
					inner
					join		dbjugadores j
					on			j.idjugador = a.refjugador
					inner
					join		dbfixture fi
					on			fi.idfixture = a.reffixture
					inner
					join		tbfechas ff
					on			ff.idfecha = fi.reffecha
					where	refequipo in (select
											distinct e.idequipo
											from		dbtorneoge tge
											inner
											join		dbequipos e
											on			e.idequipo = tge.refequipo
											inner
											join		dbfixture fix
											on			fix.reftorneoge_a = tge.idtorneoge or fix.reftorneoge_b = tge.idtorneoge
											inner
											join		dbtorneos t
											on			t.idtorneo = tge.reftorneo
											inner
											join		tbtipotorneo tp
											on			tp.idtipotorneo = t.reftipotorneo
											where		tp.idtipotorneo =".$idtipoTorneo.")
					and a.amarillas <> 2
					group by a.refequipo, e.nombre, j.apellido,j.nombre, j.dni
				) t
					where (cantidad <> 3 and ultimafecha < ".$idfecha.") or (cantidad = 3 and ultimafecha = ".$idfecha.") or (cantidad < 3 and ultimafecha = ".$idfecha.")
					
					order by t.nombre, t.apyn";	
		return $this-> query($sql,0);
	}
	
	//amarillas de la pagina
	function traerAcumuladosAmarillasPorTorneoZona($idtipoTorneo,$idzona,$idfecha) {
		
		$idfecha = $this->UltimaFechaPorTorneoZona($idtipoTorneo,$idzona);
		
		if (mysql_num_rows($idfecha)>0) {
		
			$idfecha = mysql_result($idfecha,0,0);	
		
		} else {
		
			$idfecha = 23;	
		
		}
		
		$sql = "select
				t.refequipo, t.nombre, t.apyn, t.dni, 
				COALESCE(t.cantidad,0) as cantidad,
				COALESCE(t.cantidadazules,0) as cantidadazules,
				COALESCE(t.cantidadrojas,0) as cantidadrojas,
				ultimafecha,
				fecha,t.reemplzado, t.volvio, t.refjugador, t.reemplzadovolvio
				from
				(
				select
					a.refequipo, e.nombre, concat(j.apellido,', ',j.nombre) as apyn, 
					j.dni, 
					count(a.amarillas) as cantidad,
					count(a.azul) as cantidadazules,
					count(a.rojas) as cantidadrojas,
					max(fi.reffecha) as ultimafecha, 
					max(ff.tipofecha) as fecha
					, (case when rr.idreemplazo is null then false else true end) as reemplzado
					, (case when rrr.idreemplazo is null then 0 else 1 end) as volvio,
					a.refjugador,
					(case
						when rv.idreemplazovolvio is null then 0
						else 1
					end) as reemplzadovolvio
					from		tbamonestados a
					inner
					join		dbequipos e
					on			e.idequipo = a.refequipo
					inner
					join		dbjugadores j
					on			j.idjugador = a.refjugador
					/*inner
					join		dbfixture fi
					on			fi.idfixture = a.reffixture*/
					inner 
					join 		(select fix.idfixture,fix.reffecha,tt.idtorneo from dbfixture fix
									inner join dbtorneoge tge ON fix.reftorneoge_a = tge.idtorneoge
									or fix.reftorneoge_b = tge.idtorneoge
									inner join dbtorneos tt ON tt.idtorneo = tge.reftorneo
									and tt.reftipotorneo in (".$idtipoTorneo.")
									and tt.activo = 1
									group by idfixture,reffecha) fi
					on			fi.idfixture = a.reffixture
					inner
					join		tbfechas ff
					on			ff.idfecha = fi.reffecha
left join dbreemplazo rr on rr.refequiporeemplazado = e.idequipo and rr.reffecha <= ".$idfecha." and rr.reftorneo = fi.idtorneo
left join dbreemplazo rrr on rrr.refequipo = e.idequipo and rrr.reffecha <= ".$idfecha." and rrr.reftorneo = fi.idtorneo
left join
	dbreemplazovolvio rv ON rv.refreemplazo = rrr.idreemplazo and rv.refzona in (".$idzona.")
					
					where	a.refequipo in (select
											distinct e.idequipo
											from		dbtorneoge tge
											inner
											join		dbequipos e
											on			e.idequipo = tge.refequipo
											inner
											join		dbfixture fix
											on			fix.reftorneoge_a = tge.idtorneoge or fix.reftorneoge_b = tge.idtorneoge
											inner
											join		dbtorneos t
											on			t.idtorneo = tge.reftorneo and t.activo = 1
											inner
											join		tbtipotorneo tp
											on			tp.idtipotorneo = t.reftipotorneo
											where		tp.idtipotorneo in (".$idtipoTorneo.") and tge.refgrupo in (".$idzona."))
					and (a.amarillas is not null or a.azul is not null or a.rojas is not null)
					and fi.reffecha <= ".$idfecha."
					group by a.refequipo, e.nombre, j.apellido,j.nombre, j.dni, a.refjugador
				) t
					
					
					order by (case when t.cantidad > 3 then mod(t.cantidad,3) else t.cantidad end) desc,t.nombre, t.apyn";	
		return $this-> query($sql,0);
	}
	
	
	
	function traerAcumuladosAmarillasPorTorneoZonaJugador($idtipoTorneo,$idzona,$idfecha,$idjugador) {
		$sql = "select
				
				t.refequipo, t.nombre, t.apyn, t.dni, (case when t.cantidad > 3 then mod(t.cantidad,3) else t.cantidad end) as cantidad,ultimafecha,fecha
				from
				(
				select
					a.refequipo, e.nombre, concat(j.apellido,', ',j.nombre) as apyn, j.dni, count(a.amarillas) as cantidad,max(fi.reffecha) as ultimafecha, max(ff.tipofecha) as fecha
					, (case when rr.idreemplazo is null then false else true end) as reemplzado
					, (case when rrr.idreemplazo is null then 0 else 1 end) as volvio
					from		tbamonestados a
					inner
					join		dbequipos e
					on			e.idequipo = a.refequipo
					inner
					join		dbjugadores j
					on			j.idjugador = a.refjugador
					/*inner
					join		dbfixture fi
					on			fi.idfixture = a.reffixture*/
					inner 
					join 		(select idfixture,reffecha from dbfixture fix
									inner join dbtorneoge tge ON fix.reftorneoge_a = tge.idtorneoge
									or fix.reftorneoge_b = tge.idtorneoge
									inner join dbtorneos tt ON tt.idtorneo = tge.reftorneo
									and tt.reftipotorneo in (".$idtipoTorneo.")
									and tt.activo = 1
									group by idfixture,reffecha) fi
					on			fi.idfixture = a.reffixture
					inner
					join		tbfechas ff
					on			ff.idfecha = fi.reffecha
					
left join dbreemplazo rr on rr.refequiporeemplazado = e.idequipo and rr.reffecha <= ".$idfecha."
left join dbreemplazo rrr on rrr.refequipo = e.idequipo and rrr.reffecha <= ".$idfecha." and rrr.reftorneo = ".$idtipoTorneo."
					
					where	a.refequipo in (select
											distinct e.idequipo
											from		dbtorneoge tge
											inner
											join		dbequipos e
											on			e.idequipo = tge.refequipo
											inner
											join		dbfixture fix
											on			fix.reftorneoge_a = tge.idtorneoge or fix.reftorneoge_b = tge.idtorneoge
											inner
											join		dbtorneos t
											on			t.idtorneo = tge.reftorneo and t.activo = 1
											inner
											join		tbtipotorneo tp
											on			tp.idtipotorneo = t.reftipotorneo
											where		tp.idtipotorneo in (".$idtipoTorneo.") and tge.refgrupo = ".$idzona.")
					and a.amarillas <> 2
					and a.refjugador = ".$idjugador."
					and fi.reffecha <= ".$idfecha."
					group by a.refequipo, e.nombre, j.apellido,j.nombre, j.dni
				) t
					where (cantidad <> 3 and ultimafecha < ".$idfecha.") or (cantidad = 3 and ultimafecha = ".$idfecha.") or (cantidad < 3 and ultimafecha = ".$idfecha.") or (cantidad > 3 and ultimafecha = ".$idfecha.") or (cantidad = 3 and ultimafecha = ".($idfecha-1).")
					
					order by (case when t.cantidad > 3 then mod(t.cantidad,3) else t.cantidad end) desc,t.nombre, t.apyn";	
		$res = $this-> query($sql,0);
		if (mysql_num_rows($res)>0) {
			return mysql_result($res,0,'cantidad');
		}
		return 0;
	}
	
	
	
	
	function traerAcumuladosAmarillasPorTorneoZonaJugador2($idtipoTorneo,$idzona,$idfecha,$idjugador) {
		$sql = "select
				t.refequipo, t.nombre, t.apyn, t.dni, (case when t.cantidad > 3 then mod(t.cantidad,3) else t.cantidad end) as cantidad,ultimafecha,fecha
				from
				(
				select
					a.refequipo, e.nombre, concat(j.apellido,', ',j.nombre) as apyn, j.dni, count(a.amarillas) as cantidad,max(fi.reffecha) as ultimafecha, max(ff.tipofecha) as fecha
					from		tbamonestados a
					inner
					join		dbequipos e
					on			e.idequipo = a.refequipo
	
					inner
					join		dbjugadores j
					on			j.idjugador = a.refjugador
					inner 
					join 		(select idfixture,reffecha from dbfixture fix
									inner join dbtorneoge tge ON fix.reftorneoge_a = tge.idtorneoge
									or fix.reftorneoge_b = tge.idtorneoge
									inner join dbtorneos tt ON tt.idtorneo = tge.reftorneo
									and tt.reftipotorneo in (".$idtipoTorneo.")
									and tt.activo = 1
									group by idfixture,reffecha) fi
					on			fi.idfixture = a.reffixture
					inner
					join		tbfechas ff
					on			ff.idfecha = fi.reffecha
					where	a.refequipo in (select
											distinct e.idequipo
											from		dbtorneoge tge
											inner
											join		dbequipos e
											on			e.idequipo = tge.refequipo
											inner
											join		dbfixture fix
											on			fix.reftorneoge_a = tge.idtorneoge or fix.reftorneoge_b = tge.idtorneoge
											inner
											join		dbtorneos t
											on			t.idtorneo = tge.reftorneo and t.activo = 1
											inner
											join		tbtipotorneo tp
											on			tp.idtipotorneo = t.reftipotorneo
											where		tp.idtipotorneo in (".$idtipoTorneo.") and tge.refgrupo = ".$idzona.")		
					and j.idjugador = ".$idjugador."
					and a.amarillas <> 2
					and fi.reffecha <= ".$idfecha."
					
					group by a.refequipo, e.nombre, j.apellido,j.nombre, j.dni
					
				) t
					where (cantidad <> 3 and ultimafecha < ".$idfecha.") or (cantidad = 3 and ultimafecha = ".$idfecha.") or (cantidad < 3 and ultimafecha = ".$idfecha.") or (cantidad > 3 and ultimafecha = ".$idfecha.")
					
					order by t.nombre, t.apyn";	
		$res = $this-> query($sql,0);
		if (mysql_num_rows($res)>0) {
			return mysql_result($res,0,'cantidad');
		}
		return 0;
	}
	

	function traerAcumuladosAmarillasPorTorneoZonaEquipo($idtipoTorneo,$idzona,$idfecha,$idequipo) {
		//(case when t.cantidad > 3 then mod(t.cantidad,3) else t.cantidad end) as cantidad,
		// para el acumulado
		$sql = "select
				t.refequipo, t.nombre, 
				COALESCE(t.cantidad,0) as cantidad,
				COALESCE(t.cantidadazules,0) as cantidadazules,
				COALESCE(t.cantidadrojas,0) as cantidadrojas,
				ultimafecha,
				fecha,
				t.reemplzado, 
				t.volvio
				from
				(
				select
					a.refequipo, e.nombre,
					count(a.amarillas) as cantidad,
					count(a.azul) as cantidadazules,
					count(a.rojas) as cantidadrojas,
					max(fi.reffecha) as ultimafecha, 
					max(ff.tipofecha) as fecha
					, (case when rr.idreemplazo is null then false else true end) as reemplzado
					, (case when rrr.idreemplazo is null then 0 else 1 end) as volvio
					from		tbamonestados a
					inner
					join		dbequipos e
					on			e.idequipo = a.refequipo
					inner
					join		dbjugadores j
					on			j.idjugador = a.refjugador
					/*inner
					join		dbfixture fi
					on			fi.idfixture = a.reffixture*/
					inner 
					join 		(select idfixture,reffecha from dbfixture fix
									inner join dbtorneoge tge ON fix.reftorneoge_a = tge.idtorneoge
									or fix.reftorneoge_b = tge.idtorneoge
									inner join dbtorneos tt ON tt.idtorneo = tge.reftorneo
									and tt.reftipotorneo in (".$idtipoTorneo.")
									and tt.activo = 1
									group by idfixture,reffecha) fi
					on			fi.idfixture = a.reffixture
					inner
					join		tbfechas ff
					on			ff.idfecha = fi.reffecha
					
left join dbreemplazo rr on rr.refequiporeemplazado = e.idequipo and rr.reffecha <= ".$idfecha."
left join dbreemplazo rrr on rrr.refequipo = e.idequipo and rrr.reffecha <= ".$idfecha." and rrr.reftorneo = ".$idtipoTorneo."
					
					where	a.refequipo in (select
											distinct e.idequipo
											from		dbtorneoge tge
											inner
											join		dbequipos e
											on			e.idequipo = tge.refequipo
											inner
											join		dbfixture fix
											on			fix.reftorneoge_a = tge.idtorneoge or fix.reftorneoge_b = tge.idtorneoge
											inner
											join		dbtorneos t
											on			t.idtorneo = tge.reftorneo and t.activo = 1
											inner
											join		tbtipotorneo tp
											on			tp.idtipotorneo = t.reftipotorneo
											where		tp.idtipotorneo in (".$idtipoTorneo.") and tge.refgrupo = ".$idzona." and e.idequipo = ".$idequipo.")
					/*and a.amarillas <> 2*/
					and (a.amarillas is not null or a.azul is not null or a.rojas is not null)
					and fi.reffecha <= ".$idfecha."
					group by a.refequipo, e.nombre
				) t
					
					
					order by (case when t.cantidad > 3 then mod(t.cantidad,3) else t.cantidad end) desc,t.nombre";	
		return $this-> query($sql,0);
	}
	
	function fairplay($idtipoTorneo,$idzona,$reffecha) {
		
		$idfecha = $this->UltimaFechaPorTorneoZona($idtipoTorneo,$idzona);
		
		if (mysql_num_rows($idfecha)>0) {
		
			$reffecha = mysql_result($idfecha,0,0);	
		
		} else {
		
			$reffecha = 23;	
		
		}
		/*
		$sql = "select
				e.nombre, ss.puntos, ppe.amarillas, ppe.rojas,ppe.azules, pe.observacion, ss.refequipo
				
				from		tbconducta ss
				
				inner
				join	dbequipos e
				on		e.idequipo = ss.refequipo
				inner
				join	dbtorneoge tge
				on		tge.refequipo = ss.refequipo
				inner 
				join dbtorneos t
				on tge.reftorneo = t.idtorneo and t.activo = 1 and t.idtorneo = ss.reftorneo

				inner 
				join tbtipotorneo tp
				on t.reftipotorneo = tp.idtipotorneo
				
				inner join
				tbpuntosequipos pe 
				ON pe.refequipo = ss.refequipo and ss.reftorneo = pe.reftorneo and ss.reffecha = pe.reffecha

				inner join
				(select sum(COALESCE(amarillas,0)) as amarillas, sum(COALESCE(rojas,0)) as rojas,sum(COALESCE(azules,0)) as azules,refequipo, reftorneo
					from tbpuntosequipos 
					group by refequipo, reftorneo) ppe 
				ON ppe.refequipo = ss.refequipo and ppe.reftorneo = ss.reftorneo and ppe.reftorneo = t.idtorneo
		
				where	tp.idtipotorneo = ".$idtipoTorneo." and tge.refgrupo in (".$idzona.") and ss.reffecha = ".$reffecha."
				group by e.nombre, ss.puntos, ppe.amarillas, ppe.rojas,ppe.azules, pe.observacion, ss.refequipo
				order by ss.puntos desc";
				*/
				
				$sql = "select 
					tt.nombre,
					tt.puntos,
					ppe.amarillas,
					ppe.rojas,
					ppe.azules,
					pe.observacion,
					tt.refequipo
				from
					(select e.nombre,
							COALESCE(max(ss.puntos),0) as puntos,
							ss.refequipo,
							ss.reftorneo,
							max(ss.reffecha) as reffecha
						from dbequipos e
						left join
					tbconducta ss ON e.idequipo = ss.refequipo
						inner join
					dbtorneoge tge ON tge.refequipo = e.idequipo
						inner join
					dbtorneos t ON tge.reftorneo = t.idtorneo
						and t.activo = 1
						and t.idtorneo = ss.reftorneo
						inner join
					tbtipotorneo tp ON t.reftipotorneo = tp.idtipotorneo
						where
							tp.idtipotorneo = ".$idtipoTorneo."
							and tge.refgrupo in (".$idzona.")
							and ss.reffecha <= ".$reffecha."
						group by 	e.nombre,
									ss.refequipo,
									ss.reftorneo) tt
						inner join
					tbpuntosequipos pe ON pe.refequipo = tt.refequipo
						and tt.reftorneo = pe.reftorneo
						and tt.reffecha = pe.reffecha
						inner join
					(select 
						sum(COALESCE(amarillas, 0)) as amarillas,
							sum(COALESCE(rojas, 0)) as rojas,
							sum(COALESCE(azules, 0)) as azules,
							refequipo,
							reftorneo
					from
						tbpuntosequipos
					group by refequipo , reftorneo) ppe ON ppe.refequipo = tt.refequipo
						and ppe.reftorneo = tt.reftorneo
						and ppe.reftorneo = tt.reftorneo
				
				group by tt.nombre , tt.puntos , ppe.amarillas , ppe.rojas , ppe.azules , pe.observacion , tt.refequipo
				order by tt.puntos desc";
		return $this-> query($sql,0);
	}
	
	/* fin de las funciones de los suspendidos */
	

	function mejorJugador($idtipoTorneo,$idzona,$reffecha) {
		$sql = "select 
					a.refjugador,
					a.refequipo,
					e.nombre,
					concat(j.apellido, ', ', j.nombre) as apyn,
					j.dni,
					sum(COALESCE(a.puntos, 0)) as puntos
				from
					tbamonestados a
						inner join
					(select distinct
						ff.Idfixture, ff.reffecha
					from
						dbfixture ff
					inner join dbtorneoge tge ON tge.idtorneoge = ff.reftorneoge_a
						or tge.idtorneoge = ff.reftorneoge_b
					inner join dbtorneos t ON tge.reftorneo = t.idtorneo
					inner join tbtipotorneo tp ON t.reftipotorneo = tp.idtipotorneo
					where
						t.activo = 1 and t.reftipotorneo = ".$idtipoTorneo."
							and tge.refgrupo = ".$idzona.") d ON d.idfixture = a.reffixture
						inner join
					dbequipos e ON e.idequipo = a.refequipo
						inner join
					dbjugadores j ON j.idjugador = a.refjugador
				where d.reffecha <= ".$reffecha."
				group by a.refjugador , a.refequipo , e.nombre , j.apellido , j.nombre , j.dni
				order by sum(COALESCE(a.puntos, 0)) desc, e.nombre,j.apellido , j.nombre";
		return $this-> query($sql,0);		
	}
        
        
        
        function TraerFixturePorZonaTorneoMenosGoles($idtorneo,$zona,$idfecha) {
                $sqlTorneo = "select idtorneo from dbtorneos where reftipotorneo = ".$idtorneo." and activo = 1";
		$refTorneo = mysql_result($this->query($sqlTorneo,0),0,0);
        
		$idfecha = $this->UltimaFechaPorTorneoZona($idtorneo,$zona);
		
		if (mysql_num_rows($idfecha)>0) {
		
			$idfecha = mysql_result($idfecha,0,0);	
		
		} else {
		
			$idfecha = 23;	
		
		}
		
		$sql = '
			select
			fix.nombre,
			fix.partidos,
			fix.ganados,
			fix.empatados,
			fix.perdidos,
			COALESCE(fix.golesafavor,0) as golesafavor,
			COALESCE((case when rr.idreemplazo is null then fix.golesencontra + COALESCE(rrr.golesencontra,0) else fix.golesencontra + rr.golesencontra end),0) as golesencontra,
			COALESCE(fix.golesafavor - (case when rr.idreemplazo is null then fix.golesencontra + COALESCE(rrr.golesencontra,0) else fix.golesencontra + rr.golesencontra end),0) as diferencia,
			((case when rr.idreemplazo is null then fix.pts + COALESCE(rrr.puntos,0) else fix.pts + rr.puntos end)
			-
			COALESCE((case 	when fix.puntos >= 15 and fix.puntos< 20 then 1
					when fix.puntos >= 20 and fix.puntos < 25 then 2
					when fix.puntos >= 25 then 3
			end),0)
			) + fix.bonus as pts,
			fix.idequipo,
			fix.puntos,
			fix.equipoactivo,
			cast((fix.golesafavor / fix.partidos) as decimal(4,2)) as porcentajegoles,
			round((fix.pts * 100) / (fix.partidos * 3)) as efectividad,
			/*(select count(*) from tbsuspendidos where refequipo = fix.idequipo and (motivos = "Roja Directa" or motivos = "Doble Amarilla")) as rojas,*/
			coalesce(ro.rojas,0) as rojas,
			/*(select sum(amarillas) from tbamonestados where refequipo = fix.idequipo and amarillas <> 2) as amarillas,*/
			coalesce(aaa.amarillas,0) as amarillas,
			(case when rr.idreemplazo is null then 0 else 1 end) as reemplzado,
(case when rrr.idreemplazo is null then 0 else 1 end) as volvio,
				fix.bonus,
	(case
        when rv.idreemplazovolvio is null then 0
        else 1
    end) as reemplzadovolvio
			from
			(
				select 
		       r.nombre,
		       sum(case when r.resultado_a is null then 0 else 1 end) as partidos,
		       sum(case when r.resultado_a > r.resultado_b then 1 else 0 end) as ganados, 
		       sum(case when r.resultado_a = r.resultado_b then 1 else 0 end) as empatados,
		       sum(case when r.resultado_a < r.resultado_b then 1 else 0 end) as perdidos,
		       sum(r.resultado_a) as golesafavor,
		       sum(r.resultado_b) as golesencontra,
		       (sum(r.resultado_a) - sum(r.resultado_b)) as diferencia,
		       ((sum(case when r.resultado_a > r.resultado_b then 1 else 0 end) * 2) +
		        (sum(case when r.resultado_a = r.resultado_b then 1 else 0 end) * 1)) as pts,
		        r.idequipo,
				max(case
                when r.resultado_a is null then 0
                else fp.puntos
           		 end) as puntos,
				(case when r.equipoactivo = 0 then false else true end) as equipoactivo,
		r.idtorneo, sum(r.bonus) as bonus
		
				from (
				SELECT
				e.idequipo,
				e.nombre,
				t.activo,
				t.idtorneo,
				f.tipofecha,
				fi.hora,
				(case when fi.resultado_a is null then (select
							(case when sum(gg.goles) is null 
								then (case when fi.chequeado = 1 then 0 else null end) 
								else sum(gg.goles) - (select coalesce(sum(aaa.arquero),0) from tbamonestados aaa where aaa.reffixture = fi.idfixture
								and aaa.refequipo = (select 
									tge.refequipo
								from
									dbtorneoge tge
								inner join dbtorneos t ON tge.reftorneo = t.idtorneo
									and t.activo = true
								inner join dbequipos e ON e.idequipo = tge.refequipo
								inner join dbgrupos g ON g.idgrupo = tge.refgrupo
								where
									tge.idtorneoge = fi.reftorneoge_a))
							end)
						from		tbgoleadores gg
						where gg.reffixture = fi.idfixture and gg.refequipo = (select tge.refequipo 
								from dbtorneoge tge
								inner 
								join dbtorneos t
								on tge.reftorneo = t.idtorneo and t.activo = true
								inner 
								join dbequipos e
								on e.idequipo = tge.refequipo
								inner 
								join dbgrupos g
								on g.idgrupo = tge.refgrupo
								where tge.idtorneoge = fi.reftorneoge_a))
				else fi.resultado_a end) as resultado_a,
				(case when fi.resultado_b is null then (select
									(case when sum(gg.goles) is null 
										then (case when fi.chequeado = 1 then 0 else null end) 
										else sum(gg.goles) - (select coalesce(sum(aaa.arquero),0) from tbamonestados aaa where aaa.reffixture = fi.idfixture
										and aaa.refequipo = (select 
											tge.refequipo
										from
											dbtorneoge tge
										inner join dbtorneos t ON tge.reftorneo = t.idtorneo
											and t.activo = true
										inner join dbequipos e ON e.idequipo = tge.refequipo
										inner join dbgrupos g ON g.idgrupo = tge.refgrupo
										where
											tge.idtorneoge = fi.reftorneoge_b))
									end)
								from		tbgoleadores gg
								where gg.reffixture = fi.idfixture and gg.refequipo = (select tge.refequipo 
						from dbtorneoge tge
						inner 
						join dbtorneos t
						on tge.reftorneo = t.idtorneo and t.activo = true
						inner 
						join dbequipos e
						on e.idequipo = tge.refequipo
						inner 
						join dbgrupos g
						on g.idgrupo = tge.refgrupo
						where tge.idtorneoge = fi.reftorneoge_b))
							else fi.resultado_b end) as resultado_b,
				fi.reffecha,
				tge.refgrupo,
				tge.activo as equipoactivo,
					pe.puntos as bonus
				FROM
				dbtorneoge tge
				Inner Join dbequipos e ON tge.refequipo = e.idequipo
				inner join dbgrupos g on tge.refgrupo = g.idgrupo
				Inner Join dbtorneos t ON t.idtorneo = tge.reftorneo
				Inner Join dbfixture fi ON tge.idtorneoge = fi.reftorneoge_a
				inner join tbtipotorneo tp ON tp.idtipotorneo = t.reftipotorneo
				inner join tbfechas f ON fi.refFecha = f.idfecha
				inner join tbpuntosequipos pe on pe.refequipo = e.idequipo and pe.reffixture = fi.idfixture and pe.reffecha = f.idfecha and pe.reftorneo = t.idtorneo
				where tge.refgrupo in ('.$zona.')
				and tp.idtipotorneo = '.$idtorneo.'
				and fi.reffecha <= '.$idfecha.' 
				and t.activo = 1
				UNION all
				
				SELECT
				e.idequipo,
				e.nombre,
				t.activo,
				t.idtorneo,
				f.tipofecha,
				fi.hora,
				(case when fi.resultado_b is null then (select
										(case when sum(gg.goles) is null 
											then (case when fi.chequeado = 1 then 0 else null end) 
											else sum(gg.goles) - (select coalesce(sum(aaa.arquero),0) from tbamonestados aaa where aaa.reffixture = fi.idfixture
											and aaa.refequipo = (select 
												tge.refequipo
											from
												dbtorneoge tge
											inner join dbtorneos t ON tge.reftorneo = t.idtorneo
												and t.activo = true
											inner join dbequipos e ON e.idequipo = tge.refequipo
											inner join dbgrupos g ON g.idgrupo = tge.refgrupo
											where
												tge.idtorneoge = fi.reftorneoge_b))
										end)
									from		tbgoleadores gg
									where gg.reffixture = fi.idfixture and gg.refequipo = (select tge.refequipo 														
						from dbtorneoge tge
						inner 
						join dbtorneos t
						on tge.reftorneo = t.idtorneo and t.activo = true
						inner 
						join dbequipos e
						on e.idequipo = tge.refequipo
						inner 
						join dbgrupos g
						on g.idgrupo = tge.refgrupo
						where tge.idtorneoge = fi.reftorneoge_b))
							else fi.resultado_b end) as resultado_b,
				(case when fi.resultado_a is null then (select
												(case when sum(gg.goles) is null 
													then (case when fi.chequeado = 1 then 0 else null end) 
													else sum(gg.goles) - (select coalesce(sum(aaa.arquero),0) from tbamonestados aaa where aaa.reffixture = fi.idfixture
																		and aaa.refequipo = (select 
																			tge.refequipo
																		from
																			dbtorneoge tge
																		inner join dbtorneos t ON tge.reftorneo = t.idtorneo
																			and t.activo = true
																		inner join dbequipos e ON e.idequipo = tge.refequipo
																		inner join dbgrupos g ON g.idgrupo = tge.refgrupo
																		where
																			tge.idtorneoge = fi.reftorneoge_a))
												end)
												from		tbgoleadores gg
												where gg.reffixture = fi.idfixture and gg.refequipo = (select tge.refequipo 
																										from dbtorneoge tge
																										inner 
																										join dbtorneos t
																										on tge.reftorneo = t.idtorneo and t.activo = true
																										inner 
																										join dbequipos e
																										on e.idequipo = tge.refequipo
																										inner 
																										join dbgrupos g
																										on g.idgrupo = tge.refgrupo
																										where tge.idtorneoge = fi.reftorneoge_a))
				else fi.resultado_a end) as resultado_a,
				fi.reffecha,
				tge.refgrupo,
				tge.activo as equipoactivo,
				pe.puntos as bonus	
				FROM
				dbtorneoge tge
				Inner Join dbequipos e ON tge.refequipo = e.idequipo
				inner join dbgrupos g on tge.refgrupo = g.idgrupo
				Inner Join dbtorneos t ON t.idtorneo = tge.reftorneo
				Inner Join dbfixture fi ON tge.idtorneoge = fi.reftorneoge_b
				inner join tbtipotorneo tp ON tp.idtipotorneo = t.reftipotorneo
				inner join tbfechas f ON fi.refFecha = f.idfecha
				inner join tbpuntosequipos pe on pe.refequipo = e.idequipo and pe.reffixture = fi.idfixture and pe.reffecha = f.idfecha and pe.reftorneo = t.idtorneo
				where tge.refgrupo in ('.$zona.')
				and tp.idtipotorneo = '.$idtorneo.'
				and fi.reffecha <= '.$idfecha.' 
				and t.activo = 1
				
				union all
				
				 SELECT 
        e.idequipo,
            e.nombre,
            t.activo,
            t.idtorneo,
            f.tipofecha,
            fi.hora,
            null as resultado_b,
            null as resultado_a,
            fi.reffecha,
            tge.refgrupo,
            tge.activo as equipoactivo,
            0 as bonus

    FROM
        dbtorneoge tge
    Inner Join dbequipos e ON tge.refequipo = e.idequipo
    inner join dbgrupos g ON tge.refgrupo = g.idgrupo
    Inner Join dbtorneos t ON t.idtorneo = tge.reftorneo
    Inner Join dbfixture fi ON tge.idtorneoge = fi.reftorneoge_b or tge.idtorneoge = fi.reftorneoge_a
    inner join tbtipotorneo tp ON tp.idtipotorneo = t.reftipotorneo
    inner join tbfechas f ON fi.refFecha = f.idfecha

    where tge.refgrupo in ('.$zona.')
				and tp.idtipotorneo = '.$idtorneo.'
				and fi.reffecha <= '.$idfecha.' 
				and t.activo = 1
			and fi.jugo = 0 and fi.chequeado = 0
				
				) as r
				left
				join	(select refequipo,max(puntos) as puntos, reftorneo from tbconducta where reffecha <='.$idfecha.' group by refequipo,reftorneo
				) fp
				on		r.idequipo = fp.refequipo and fp.reftorneo = r.idtorneo
				left join dbtorneos t ON t.idtorneo = fp.reftorneo and t.activo = 1
				group by r.nombre,r.idequipo 
) as fix

left join dbreemplazo rr on rr.refequiporeemplazado = fix.idequipo and rr.reffecha <= '.$idfecha.' and rr.reftorneo = fix.idtorneo
left join dbreemplazo rrr on rrr.refequipo = fix.idequipo and rrr.reffecha <= '.$idfecha.' and rrr.reftorneo = fix.idtorneo
left join
	dbreemplazovolvio rv ON rv.refreemplazo = rrr.idreemplazo and rv.refzona in ('.$zona.')
	
	left join
	(select

	sa.nombre,
	sum(sa.puntos) as amarillas,
	sa.idequipo
from (
select 
					f.tipofecha,
						e.nombre,
						count(a.amarillas) as puntos,
						f.idfecha,
						e.idequipo
				from
					tbamonestados a
				inner join dbequipos e ON e.idequipo = a.refequipo
				inner join dbfixture fix ON fix.idfixture = a.reffixture
				inner join tbfechas f ON f.idfecha = fix.reffecha
				inner join dbtorneoge tge ON tge.refequipo = e.idequipo
					and fix.reftorneoge_a = tge.idtorneoge
				where
					a.amarillas = 1 and tge.reftorneo = '.$refTorneo.'
						and fix.reffecha <= '.$idfecha.'
				group by f.tipofecha , e.nombre , f.idfecha , e.idequipo 
				
				union all 
				
				select 
					f.tipofecha,
						e.nombre,
						count(a.amarillas) as puntos,
						f.idfecha,
						e.idequipo
				from
					tbamonestados a
				inner join dbequipos e ON e.idequipo = a.refequipo
				inner join dbfixture fix ON fix.idfixture = a.reffixture
				inner join tbfechas f ON f.idfecha = fix.reffecha
				inner join dbtorneoge tge ON tge.refequipo = e.idequipo
					and fix.reftorneoge_b = tge.idtorneoge 
				where
					a.amarillas = 1 and tge.reftorneo = '.$refTorneo.'
						and fix.reffecha <= '.$idfecha.'
				group by f.tipofecha , e.nombre , f.idfecha , e.idequipo) sa
			group by 
			sa.nombre,
			sa.idequipo) aaa ON aaa.idequipo = fix.idequipo
			
			
	left join
	(select

	sa.nombre,
	sum(sa.puntos) as rojas,
	sa.idequipo
from (
select 
					f.tipofecha,
						e.nombre,
						sum(1) as puntos,
						f.idfecha,
						e.idequipo
				from
					tbsuspendidos a
				inner join dbequipos e ON e.idequipo = a.refequipo
				inner join (select 
					refsuspendido, min(reffecha) as idfecha
				from
					dbsuspendidosfechas
				group by refsuspendido) sp ON sp.refsuspendido = a.idsuspendido
				inner join tbfechas f ON f.idfecha = sp.idfecha - 1
				inner join dbfixture fix ON fix.idfixture = a.reffixture
				inner join dbtorneoge tge ON tge.refequipo = e.idequipo
					and fix.reftorneoge_a = tge.idtorneoge
				where
					tge.reftorneo = '.$refTorneo.' and fix.reffecha <= '.$idfecha.' and (a.motivos like "%Roja Directa%" or a.motivos like "%Doble Amarilla%") 
				group by f.tipofecha , e.nombre , f.idfecha , e.idequipo 
				
				union all 
				
				select 
					f.tipofecha,
						e.nombre,
						sum(1) as puntos,
						f.idfecha,
						e.idequipo
				from
					tbsuspendidos a
				inner join dbequipos e ON e.idequipo = a.refequipo
				inner join (select 
					refsuspendido, min(reffecha) as idfecha
				from
					dbsuspendidosfechas
				group by refsuspendido) sp ON sp.refsuspendido = a.idsuspendido
				inner join tbfechas f ON f.idfecha = sp.idfecha - 1
				inner join dbfixture fix ON fix.idfixture = a.reffixture
				inner join dbtorneoge tge ON tge.refequipo = e.idequipo
					and fix.reftorneoge_b = tge.idtorneoge
				where
					tge.reftorneo = '.$refTorneo.' and fix.reffecha <= '.$idfecha.' and (a.motivos like "%Roja Directa%" or a.motivos like "%Doble Amarilla%") 
				group by f.tipofecha , e.nombre , f.idfecha , e.idequipo) sa
			group by 
			sa.nombre,
			sa.idequipo) ro ON ro.idequipo = fix.idequipo
				order by fix.golesencontra';
		$res = $this->query($sql,0);
		return $res;	
	}
        
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
		//mysql_query("SET NAMES 'utf8'");
		$result = mysql_query($sql,$conex);
		if ($accion && $result) {
			$result = mysql_insert_id();
		}
		mysql_close($conex);
		return $result;
		
	}
	
	}
?>