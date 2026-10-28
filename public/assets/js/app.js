/* Mikato Estilistas Caninos · interacciones sin dependencias */
(function () {
  'use strict';

  const $ = (sel, ctx) => (ctx || document).querySelector(sel);
  const $$ = (sel, ctx) => Array.from((ctx || document).querySelectorAll(sel));

  /* Menú móvil público */
  const menuBoton = $('[data-menu]');
  const nav = $('#nav-principal');
  if (menuBoton && nav) {
    menuBoton.addEventListener('click', () => {
      const abierto = nav.classList.toggle('nav--abierta');
      menuBoton.setAttribute('aria-expanded', abierto ? 'true' : 'false');
    });
    $$('a', nav).forEach((a) => a.addEventListener('click', () => {
      nav.classList.remove('nav--abierta');
      menuBoton.setAttribute('aria-expanded', 'false');
    }));
  }

  /* Sombra de cabecera al hacer scroll */
  const cabecera = $('#cabecera');
  if (cabecera) {
    const actualizar = () => cabecera.classList.toggle('cabecera--pegada', window.scrollY > 8);
    actualizar();
    window.addEventListener('scroll', actualizar, { passive: true });
  }

  /* Panel: abrir barra lateral en móvil */
  const panelBoton = $('[data-panel-menu]');
  const panelBarra = $('#panel-barra');
  if (panelBoton && panelBarra) {
    panelBoton.addEventListener('click', () => panelBarra.classList.toggle('panel__barra--abierta'));
    $$('a', panelBarra).forEach((a) => a.addEventListener('click', () => panelBarra.classList.remove('panel__barra--abierta')));
  }

  /* Confirmaciones */
  $$('form[data-confirmar]').forEach((form) => {
    form.addEventListener('submit', (e) => {
      if (!window.confirm(form.getAttribute('data-confirmar') || '¿Continuar?')) e.preventDefault();
    });
  });

  /* Agenda pública: cargar horarios y resumen */
  const agendaForm = $('[data-agenda]');
  if (agendaForm) {
    const fecha = $('[name="fecha"]', agendaForm);
    const servicio = $('[name="servicio_id"]', agendaForm);
    const contenedorHoras = $('[data-horas]', agendaForm);
    const resumen = $('[data-resumen-agenda]');
    const servicioTexto = $('[data-servicio-nombre]');
    const servicioPrecio = $('[data-servicio-precio]');

    const actualizarResumen = () => {
      if (!servicio) return;
      const opt = servicio.options[servicio.selectedIndex];
      if (!opt) return;
      if (servicioTexto) servicioTexto.textContent = opt.dataset.nombre || '';
      if (servicioPrecio) servicioPrecio.textContent = opt.dataset.precio || '';
      if (resumen) resumen.hidden = false;
    };

    const cargarHoras = async () => {
      if (!fecha || !contenedorHoras || !fecha.value) return;
      contenedorHoras.innerHTML = '<p class="texto-tenue" style="grid-column:1/-1;">Buscando horarios…</p>';
      try {
        const res = await fetch('/agenda/disponibilidad?fecha=' + encodeURIComponent(fecha.value), { headers: { Accept: 'application/json' } });
        const data = await res.json();
        if (!data.ok) throw new Error(data.error || 'No disponible');
        if (!data.horas || !data.horas.length) {
          contenedorHoras.innerHTML = '<p class="texto-tenue" style="grid-column:1/-1;">No quedan horarios ese día. Elige otra fecha o escríbenos.</p>';
          return;
        }
        contenedorHoras.innerHTML = data.horas.map((h, i) => {
          const id = 'hora-' + i;
          return '<input type="radio" name="hora" id="' + id + '" value="' + h + '"' + (i === 0 ? ' required' : '') + '>'
            + '<label for="' + id + '">' + h + '</label>';
        }).join('');
      } catch (err) {
        contenedorHoras.innerHTML = '<p class="texto-tenue" style="grid-column:1/-1;">No pudimos cargar los horarios. Intenta de nuevo o escríbenos.</p>';
      }
    };

    if (fecha) fecha.addEventListener('change', cargarHoras);
    if (servicio) servicio.addEventListener('change', actualizarResumen);
    actualizarResumen();
  }

  /* Panel: filtrar mascotas por cliente en la agenda */
  const clienteSelect = $('[data-cliente-select]');
  const mascotaSelect = $('[data-mascota-select]');
  if (clienteSelect && mascotaSelect) {
    const filtrar = () => {
      const cliente = clienteSelect.value;
      Array.from(mascotaSelect.options).forEach((opt) => {
        if (!opt.value) return;
        const coincide = !cliente || opt.dataset.cliente === cliente;
        opt.hidden = !coincide;
        opt.disabled = !coincide;
      });
      const actual = mascotaSelect.selectedOptions[0];
      if (actual && actual.disabled) mascotaSelect.value = '';
    };
    clienteSelect.addEventListener('change', filtrar);
    filtrar();
  }

  /* Panel: nueva venta con conceptos dinámicos */
  const ventaForm = $('[data-venta]');
  if (ventaForm) {
    const cuerpo = $('[data-conceptos]', ventaForm);
    const totalEl = $('[data-total-venta]', ventaForm);
    const descuento = $('[name="descuento"]', ventaForm);
    const plantilla = $('#plantilla-concepto');

    const recalcular = () => {
      let subtotal = 0;
      $$('tr[data-concepto]', cuerpo).forEach((fila) => {
        const cant = parseFloat($('[name="cantidad[]"]', fila).value) || 0;
        const precio = parseFloat($('[name="precio_unitario[]"]', fila).value) || 0;
        const importe = Math.round(cant * precio * 100) / 100;
        $('[data-importe]', fila).textContent = importe.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
        $('[data-importe]', fila).dataset.valor = importe;
        subtotal += importe;
      });
      const desc = Math.min(subtotal, parseFloat(descuento && descuento.value) || 0);
      if (totalEl) totalEl.textContent = (subtotal - desc).toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
      const subEl = $('[data-subtotal-venta]', ventaForm);
      if (subEl) subEl.textContent = subtotal.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
    };

    const agregarFila = (datos) => {
      if (!plantilla) return;
      const nodo = plantilla.content.cloneNode(true);
      const fila = $('tr[data-concepto]', nodo);
      if (datos) {
        $('[name="tipo[]"]', fila).value = datos.tipo || 'servicio';
        $('[name="referencia_id[]"]', fila).value = datos.id || '';
        $('[name="descripcion[]"]', fila).value = datos.nombre || '';
        $('[name="precio_unitario[]"]', fila).value = datos.precio || '';
      }
      cuerpo.appendChild(nodo);
      recalcular();
      const primera = $('[name="descripcion[]"]', cuerpo.lastElementChild);
      if (primera && !datos) primera.focus();
    };

    $$('[data-agregar-servicio]').forEach((sel) => {
      sel.addEventListener('change', () => {
        const opt = sel.options[sel.selectedIndex];
        if (!opt || !opt.value) return;
        agregarFila({ tipo: 'servicio', id: opt.value, nombre: opt.dataset.nombre, precio: opt.dataset.precio });
        sel.value = '';
      });
    });
    $$('[data-agregar-producto]').forEach((sel) => {
      sel.addEventListener('change', () => {
        const opt = sel.options[sel.selectedIndex];
        if (!opt || !opt.value) return;
        agregarFila({ tipo: 'producto', id: opt.value, nombre: opt.dataset.nombre, precio: opt.dataset.precio });
        sel.value = '';
      });
    });
    $$('[data-agregar-paquete]').forEach((sel) => {
      sel.addEventListener('change', () => {
        const opt = sel.options[sel.selectedIndex];
        if (!opt || !opt.value) return;
        agregarFila({ tipo: 'paquete', id: opt.value, nombre: opt.dataset.nombre, precio: opt.dataset.precio });
        sel.value = '';
      });
    });

    cuerpo.addEventListener('input', recalcular);
    if (descuento) descuento.addEventListener('input', recalcular);

    cuerpo.addEventListener('click', (e) => {
      const boton = e.target.closest('[data-quitar]');
      if (!boton) return;
      const fila = boton.closest('tr[data-concepto]');
      if (fila) { fila.remove(); recalcular(); }
    });

    const agregarVacio = $('[data-agregar-linea]');
    if (agregarVacio) agregarVacio.addEventListener('click', () => agregarFila(null));

    if (!$$('tr[data-concepto]', cuerpo).length) agregarFila(null);
    recalcular();
  }

  /* Copiar código de cita */
  $$('[data-copiar]').forEach((boton) => {
    boton.addEventListener('click', async () => {
      const texto = boton.getAttribute('data-copiar');
      try {
        await navigator.clipboard.writeText(texto);
        const original = boton.textContent;
        boton.textContent = 'Copiado';
        setTimeout(() => { boton.textContent = original; }, 1600);
      } catch (e) { /* silencio */ }
    });
  });
})();
