(function () {
  const inputBuscar = document.getElementById('input-buscar-tareas');
  const selectCurso = document.getElementById('select-filtro-curso');
  const formBuscar = document.getElementById('form-buscar-tareas');
  const formFiltro = document.getElementById('form-filtro-curso');
  const tbody = document.getElementById('tareas-tbody');
  const card = document.getElementById('card-tareas');

  if (!tbody || !card) return; 

  const rol = card.dataset.rol;
  const esDocente = card.dataset.esDocente === '1';

  let controladorFetch = null;
  let temporizador = null;

  function buscar() {
    const q = inputBuscar ? inputBuscar.value.trim() : '';
    const curso = selectCurso ? selectCurso.value : '';

    if (controladorFetch) controladorFetch.abort();
    controladorFetch = new AbortController();

    const params = new URLSearchParams({ accion: 'buscarTareasJson', q: q, curso: curso });

    fetch('index.php?' + params.toString(), { signal: controladorFetch.signal })
      .then((res) => res.json())
      .then((data) => {
        if (!data.ok) return;
        pintarTabla(data.tareas, data.q);
        actualizarEstadisticas(data.tareas);
        actualizarUrl(q, curso);
      })
      .catch((err) => {
        if (err.name !== 'AbortError') console.error('Error buscando tareas:', err);
      });
  }

  function actualizarUrl(q, curso) {
    const params = new URLSearchParams({ accion: 'tareas' });
    if (q) params.set('q', q);
    if (curso) params.set('curso', curso);
    history.replaceState(null, '', 'index.php?' + params.toString());
  }

  function badgeEstado(t) {
    if (rol === 'estudiante') {
      if (t.estado_entrega === 'calificada') return '<span class="badge badge-green">Calificada</span>';
      if (t.id_entrega) return '<span class="badge badge-blue">Entregada</span>';
      if (t.vencida) return '<span class="badge badge-muted">Vencida</span>';
      return '<span class="badge badge-gold">Pendiente</span>';
    }
    return '<span class="badge badge-blue">' + (t.vencida ? 'Cerrada' : 'Abierta') + '</span>';
  }

  function celdaNotaOEntregas(t) {
    if (rol === 'estudiante') {
      return t.nota !== null
        ? '<strong style="color:var(--success);">' + t.nota + '</strong>'
        : '—';
    }
    return String(t.total_entregas);
  }

  function crearAccionesEstudiante(t, td) {
    if (!t.id_entrega && !t.vencida) {
      const btn = botón('btn btn-primary btn-sm', '📤 Entregar');
      btn.addEventListener('click', () => abrirEntregar(t.id_tarea, t.titulo, t.curso_nombre));
      td.appendChild(btn);
      return;
    }
    if (t.id_entrega && t.estado_entrega !== 'calificada') {
      const btnEditar = botón('btn btn-ghost btn-sm', '✎ Editar');
      btnEditar.addEventListener('click', () => abrirEntregar(t.id_tarea, t.titulo, t.curso_nombre));
      td.appendChild(btnEditar);

      const form = document.createElement('form');
      form.method = 'POST';
      form.action = 'index.php?accion=eliminarEntrega';
      form.style.display = 'inline';
      form.addEventListener('submit', (e) => {
        if (!confirm('¿Retirar tu entrega?')) e.preventDefault();
      });
      form.innerHTML = '<input type="hidden" name="id_entrega" value="' + t.id_entrega + '" />'
        + '<button class="btn btn-ghost btn-sm" type="submit">🗑</button>';
      td.appendChild(form);
      return;
    }
    const span = document.createElement('span');
    span.style.cssText = 'font-size:0.78rem;color:var(--text-muted);';
    span.textContent = '—';
    td.appendChild(span);
  }

  function crearAccionesDocente(t, td) {
    const link = document.createElement('a');
    link.href = 'index.php?accion=tareas&ver=' + t.id_tarea;
    link.innerHTML = '<button class="btn btn-ghost btn-sm">👁️ Entregas</button>';
    td.appendChild(link);

    const btnEditar = botón('btn btn-ghost btn-sm', '✎');
    btnEditar.addEventListener('click', () => abrirEditar({
      id_tarea: t.id_tarea,
      titulo: t.titulo,
      instrucciones: t.instrucciones,
      ponderacion: t.ponderacion,
      fecha_limite: t.fecha_limite,
    }));
    td.appendChild(btnEditar);

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'index.php?accion=eliminarTarea';
    form.style.display = 'inline';
    form.addEventListener('submit', (e) => {
      if (!confirm('¿Eliminar esta tarea? Se eliminarán también sus entregas.')) e.preventDefault();
    });
    form.innerHTML = '<input type="hidden" name="id_tarea" value="' + t.id_tarea + '" />'
      + '<button class="btn btn-danger btn-sm" type="submit">🗑</button>';
    td.appendChild(form);
  }

  function botón(clase, texto) {
    const b = document.createElement('button');
    b.type = 'button';
    b.className = clase;
    b.textContent = texto;
    return b;
  }

  function pintarTabla(tareas, q) {
    tbody.innerHTML = '';

    if (tareas.length === 0) {
      const tr = document.createElement('tr');
      const td = document.createElement('td');
      td.colSpan = 7;
      td.style.cssText = 'text-align:center;color:var(--text-muted);padding:24px;';
      td.textContent = q ? `No se encontraron tareas para "${q}".` : 'No se encontraron tareas.';
      tr.appendChild(td);
      tbody.appendChild(tr);
      return;
    }

    tareas.forEach((t) => {
      const tr = document.createElement('tr');

      tr.appendChild(celda(t.titulo, 'td-main'));
      tr.appendChild(celda(t.curso_nombre));
      tr.appendChild(celda(t.fecha_limite_fmt));
      tr.appendChild(celda(t.ponderacion + '%'));

      const tdNota = document.createElement('td');
      tdNota.innerHTML = celdaNotaOEntregas(t);
      tr.appendChild(tdNota);

      const tdEstado = document.createElement('td');
      tdEstado.innerHTML = badgeEstado(t);
      tr.appendChild(tdEstado);

      const tdAcciones = document.createElement('td');
      const wrap = document.createElement('div');
      wrap.className = 'table-actions';
      if (rol === 'estudiante') crearAccionesEstudiante(t, wrap);
      else crearAccionesDocente(t, wrap);
      tdAcciones.appendChild(wrap);
      tr.appendChild(tdAcciones);

      tbody.appendChild(tr);
    });
  }

  function celda(texto, clase) {
    const td = document.createElement('td');
    if (clase) td.className = clase;
    td.textContent = texto;
    return td;
  }

  function actualizarEstadisticas(tareas) {
    if (rol !== 'estudiante') return;
    const elPendientes = document.getElementById('stat-pendientes');
    const elRevision = document.getElementById('stat-revision');
    const elEntregadas = document.getElementById('stat-entregadas');
    const elVencidas = document.getElementById('stat-vencidas');
    if (!elPendientes) return;

    let pendientes = 0, revision = 0, entregadas = 0, vencidas = 0;
    tareas.forEach((t) => {
      if (t.id_entrega) {
        entregadas++;
        if (t.estado_entrega === 'entregada') revision++;
      } else if (t.vencida) {
        vencidas++;
      } else {
        pendientes++;
      }
    });

    elPendientes.textContent = pendientes;
    elRevision.textContent = revision;
    elEntregadas.textContent = entregadas;
    elVencidas.textContent = vencidas;
  }

  // --- Eventos ---
  if (inputBuscar) {
    inputBuscar.addEventListener('input', () => {
      clearTimeout(temporizador);
      temporizador = setTimeout(buscar, 300); // debounce
    });
  }
  if (selectCurso) {
    selectCurso.addEventListener('change', buscar);
  }
  if (formBuscar) {
    formBuscar.addEventListener('submit', (e) => { e.preventDefault(); clearTimeout(temporizador); buscar(); });
  }
  if (formFiltro) {
    formFiltro.addEventListener('submit', (e) => { e.preventDefault(); buscar(); });
  }
})();
