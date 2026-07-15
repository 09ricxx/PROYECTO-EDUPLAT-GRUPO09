const TAB_IDS = { contenidos: 'tab-contenidos', tareas: 'tab-tareas', notas: 'tab-notas' };

function showTab(nombre) {
  Object.values(TAB_IDS).forEach(function (id) {
    const el = document.getElementById(id);
    if (el) el.style.display = 'none';
  });
  const activo = document.getElementById(TAB_IDS[nombre]);
  if (activo) activo.style.display = 'block';

  document.querySelectorAll('#detalle-tabs .tab-btn').forEach(function (b) {
    b.classList.toggle('active', b.getAttribute('onclick').includes(nombre));
  });
}
