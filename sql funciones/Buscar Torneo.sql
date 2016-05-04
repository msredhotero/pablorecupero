select
*
from		dbtorneos t
inner
join		tbtipotorneo tp
on			t.reftipotorneo = tp.idtipotorneo
where		t.Activo = 1 and tp.descripciontorneo like '%5%'