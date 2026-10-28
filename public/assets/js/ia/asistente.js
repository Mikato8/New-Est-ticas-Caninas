/* Asistente local de Mikato Estilistas Caninos
   Búsqueda BM25 sobre el corpus público + modelo generativo opcional.
   Todo corre en el navegador; el corpus se genera con bin/build-ia.php. */

const CDN = 'https://cdn.jsdelivr.net/npm/@huggingface/transformers@4.3.1';
const MODELO = 'onnx-community/Qwen2.5-0.5B-Instruct';

const DETENER = new Set([
  'para', 'como', 'cual', 'que', 'con', 'por', 'del', 'las', 'los', 'una', 'uno', 'unos', 'unas',
  'este', 'esta', 'estos', 'estas', 'mas', 'muy', 'sin', 'sobre', 'entre', 'cuando', 'donde',
  'porque', 'pero', 'tambien', 'hasta', 'desde', 'todo', 'toda', 'todos', 'todas', 'hay', 'ser',
  'son', 'fue', 'era', 'estan', 'puedo', 'puede', 'debo', 'debe', 'quiero', 'necesito',
  'usar', 'uso', 'hacer', 'hago', 'tengo', 'tiene', 'perro', 'perros', 'canina', 'estetica',
]);

const escapar = (texto) => String(texto).replace(/[&<>"']/g, (c) => ({
  '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
}[c]));

const normalizar = (texto) => String(texto)
  .toLowerCase()
  .normalize('NFD')
  .replace(/[\u0300-\u036f]/g, '')
  .replace(/[^a-z0-9ñ]+/g, ' ')
  .trim();

const tokens = (texto) => normalizar(texto)
  .split(/\s+/)
  .filter((t) => t.length > 2 && !DETENER.has(t));

const SINONIMOS = {
  bano: ['banar', 'shampoo', 'agua', 'jabon', 'lavar'],
  banar: ['bano', 'shampoo'],
  shampoo: ['jabon', 'dilucion', 'bano', 'producto'],
  dilucion: ['diluir', 'mezcla', 'proporcion', 'shampoo'],
  diluir: ['dilucion', 'mezcla'],
  secado: ['secadora', 'secar', 'calor', 'golpe'],
  secadora: ['secado', 'calor'],
  una: ['unas', 'cortar', 'quick', 'vaso'],
  unas: ['una', 'cortar', 'limar'],
  corte: ['cortar', 'maquina', 'tijera', 'manto'],
  tijera: ['corte', 'tijeras'],
  oido: ['oidos', 'limpiar', 'otitis', 'algodon'],
  oidos: ['oido', 'limpiar'],
  precio: ['costo', 'cobrar', 'tarifa', 'margen'],
  cobrar: ['precio', 'tarifa', 'cotizar'],
  costo: ['precio', 'gasto', 'margen'],
  margen: ['utilidad', 'precio', 'rentabilidad'],
  nervioso: ['miedo', 'estres', 'agresivo', 'manejo', 'bozal'],
  miedo: ['nervioso', 'estres', 'manejo'],
  agresivo: ['nervioso', 'mordida', 'bozal'],
  doble: ['manto', 'subpelo', 'deslanar', 'husky'],
  deslanado: ['subpelo', 'rastrillo', 'muda'],
  nudo: ['enredo', 'desenredar', 'manto'],
  golpe: ['calor', 'emergencia', 'secado'],
  calor: ['golpe', 'secado', 'verano'],
  higiene: ['limpieza', 'desinfectar', 'equipo'],
  legal: ['norma', 'nom', 'licencia', 'municipio', 'datos'],
  nom: ['norma', 'legal', 'dof'],
};

export class Buscador {
  constructor(chunks) {
    this.chunks = chunks;
    this.docs = chunks.map((c) => {
      const contenido = tokens(`${c.titulo} ${c.titulo} ${c.texto} ${c.tags || ''}`);
      const tf = new Map();
      contenido.forEach((t) => tf.set(t, (tf.get(t) || 0) + 1));
      return { tf, longitud: contenido.length };
    });
    this.df = new Map();
    this.docs.forEach((doc) => {
      doc.tf.forEach((_, termino) => this.df.set(termino, (this.df.get(termino) || 0) + 1));
    });
    this.promedio = this.docs.reduce((suma, d) => suma + d.longitud, 0) / Math.max(1, this.docs.length);
  }

  buscar(consulta, k = 5) {
    const base = [...new Set(tokens(consulta))];
    const terminos = [...new Set(base.flatMap((t) => [t, ...(SINONIMOS[t] || [])]))];
    if (terminos.length === 0) return [];
    const N = this.docs.length;
    const k1 = 1.5;
    const b = 0.75;

    return this.docs.map((doc, i) => {
      let score = 0;
      terminos.forEach((termino) => {
        const f = doc.tf.get(termino) || 0;
        if (f === 0) return;
        const df = this.df.get(termino) || 0;
        const idf = Math.log(1 + (N - df + 0.5) / (df + 0.5));
        score += idf * ((f * (k1 + 1)) / (f + k1 * (1 - b + b * (doc.longitud / this.promedio))));
      });
      if (this.chunks[i].categoria === 'faq') score *= 1.2;
      if (this.chunks[i].categoria === 'legal') score *= 1.05;
      return { i, score };
    }).filter((r) => r.score > 0.15).sort((a, b2) => b2.score - a.score).slice(0, k)
      .map((r) => ({ ...this.chunks[r.i], score: r.score }));
  }
}

const sugerenciaCalculadora = (consulta) => {
  const q = normalizar(consulta);
  if (/(dilucion|diluir|shampoo|mezcla|agua)/.test(q)) return { titulo: 'Dilución de shampoo', url: '/calculadoras/dilucion' };
  if (/(precio|cobrar|costo|margen|tarifa|rentabilidad)/.test(q)) return { titulo: 'Precio de servicio', url: '/calculadoras/precio' };
  if (/(secado|secadora|tiempo|calor)/.test(q)) return { titulo: 'Tiempo de secado', url: '/calculadoras/secado' };
  if (/(utilidad|rentabilidad|hora|punto)/.test(q)) return { titulo: 'Rentabilidad', url: '/calculadoras/rentabilidad' };
  return null;
};

const respuestaExtractiva = (consulta, hits) => {
  if (hits.length === 0) {
    return {
      texto: 'No encontré una respuesta documentada para esa pregunta. Prueba con otra palabra (por ejemplo “baño”, “uñas”, “secado”, “precio” o “doble capa”). Si el tema no está en el sitio, escríbenos por contacto y lo evaluamos.',
      fuentes: [],
      calculadora: null,
    };
  }
  const terminos = new Set(tokens(consulta));
  const partes = [];
  const fuentes = [];
  hits.slice(0, 3).forEach((hit, indice) => {
    fuentes.push({ titulo: hit.titulo_guia || hit.titulo, url: hit.url });
    const oraciones = String(hit.texto).split(/(?<=[.!?])\s+/).filter((o) => o.length > 30);
    const relevantes = oraciones.filter((o) => tokens(o).some((t) => terminos.has(t))).slice(0, 2);
    const elegidas = relevantes.length > 0 ? relevantes : oraciones.slice(0, 2);
    if (elegidas.length > 0) {
      partes.push(`<strong>[${indice + 1}] ${escapar(hit.titulo)}</strong><br>${elegidas.map((o) => escapar(o)).join(' ')}`);
    }
  });
  return {
    texto: `${partes.join('<br><br>')}<div class="ia-burbuja__nota">Respuesta construida con fragmentos del sitio. Activa el modelo para una redacción más natural.</div>`,
    fuentes,
    calculadora: sugerenciaCalculadora(consulta),
  };
};

const iniciar = async (root) => {
  const log = root.querySelector('[data-ia-log]');
  const form = root.querySelector('[data-ia-form]');
  const input = root.querySelector('[data-ia-pregunta]');
  const estado = root.querySelector('[data-ia-estado]');
  const botonCargar = root.querySelector('[data-ia-cargar]');
  const progreso = root.querySelector('[data-ia-progreso]');
  const estadoModelo = root.querySelector('[data-ia-modelo-estado]');

  let buscador = null;
  let generador = null;
  let cargando = false;

  const burbuja = (texto, tipo = 'sistema') => {
    const div = document.createElement('div');
    div.className = 'ia-burbuja ia-burbuja--' + tipo;
    div.innerHTML = texto;
    log.appendChild(div);
    log.scrollTop = log.scrollHeight;
    return div;
  };

  const fuentesHtml = (fuentes) => {
    if (!fuentes || fuentes.length === 0) return '';
    const enlaces = fuentes.map((f, i) => `<a href="${escapar(f.url)}">[${i + 1}] ${escapar(f.titulo)}</a>`).join(' · ');
    return `<div class="ia-burbuja__fuentes">${enlaces}</div>`;
  };

  const calculadoraHtml = (calc) => {
    if (!calc) return '';
    return `<div class="ia-burbuja__calc"><a class="boton boton--pequeno" href="${escapar(calc.url)}">Abrir ${escapar(calc.titulo)} →</a></div>`;
  };

  const responder = async (pregunta) => {
    if (pregunta.trim().length < 3) return;
    burbuja(escapar(pregunta), 'usuario');
    input.value = '';

    if (!buscador) {
      burbuja('Todavía estoy cargando el índice del sitio. Espera un segundo y vuelve a preguntar.', 'sistema');
      return;
    }

    const hits = buscador.buscar(pregunta);
    const esperando = burbuja('Buscando en las guías…', 'sistema');

    if (!generador) {
      const respuesta = respuestaExtractiva(pregunta, hits);
      esperando.innerHTML = respuesta.texto + fuentesHtml(respuesta.fuentes) + calculadoraHtml(respuesta.calculadora);
      return;
    }

    try {
      esperando.textContent = 'Redactando con el modelo local…';
      const contexto = hits.map((h, i) => `[${i + 1}] ${h.titulo}\n${h.texto}`).join('\n\n').slice(0, 3500);
      const mensajes = [
        {
          role: 'system',
          content: 'Eres un estilista canino mexicano con experiencia en salón. Respondes en español claro, cálido y sin inventar. Usa SOLO el contexto proporcionado. Si el contexto no alcanza, dilo y sugiere consultar a un veterinario o revisar la guía. No inventes normas, medicamentos, dosis ni cifras. Cita las fuentes con [1], [2]. Máximo 220 palabras. No sustituyes la valoración veterinaria.',
        },
        { role: 'user', content: `Contexto:\n${contexto}\n\nPregunta: ${pregunta}` },
      ];

      const salida = await Promise.race([
        generador(mensajes, { max_new_tokens: 220, do_sample: false }),
        new Promise((_, rechazar) => setTimeout(() => rechazar(new Error('Tiempo de espera agotado')), 180000)),
      ]);

      let texto = '';
      const primero = salida && salida[0] ? salida[0].generated_text : '';
      if (Array.isArray(primero)) {
        texto = primero[primero.length - 1]?.content || '';
      } else if (typeof primero === 'string') {
        texto = primero;
      }
      texto = String(texto).trim();
      if (!texto) throw new Error('Respuesta vacía');

      esperando.innerHTML = escapar(texto).replace(/\n/g, '<br>') + fuentesHtml(hits) + calculadoraHtml(sugerenciaCalculadora(pregunta));
    } catch (error) {
      console.error('[IA generativa]', error);
      const respuesta = respuestaExtractiva(pregunta, hits);
      esperando.innerHTML = '<em>No se pudo generar la respuesta con el modelo (memoria o WebGPU). Te dejo la búsqueda guiada:</em><br><br>' + respuesta.texto + fuentesHtml(respuesta.fuentes) + calculadoraHtml(respuesta.calculadora);
    }
  };

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    responder(input.value);
  });

  root.querySelectorAll('.ia-ejemplo').forEach((boton) => {
    boton.addEventListener('click', () => responder(boton.textContent || ''));
  });

  if (botonCargar) {
    botonCargar.addEventListener('click', async () => {
      if (generador || cargando) return;
      cargando = true;
      botonCargar.disabled = true;
      botonCargar.textContent = 'Cargando…';
      estadoModelo.textContent = 'Descargando modelo (~500 MB). La primera vez tarda.';
      try {
        if (!navigator.gpu) throw new Error('Este navegador no expone WebGPU. Usa la búsqueda guiada.');
        const { pipeline, env } = await import(CDN);
        env.allowLocalModels = false;
        env.useBrowserCache = true;
        generador = await pipeline('text-generation', MODELO, {
          device: 'webgpu',
          dtype: 'q4',
          progress_callback: (p) => {
            if (p && p.status === 'progress' && typeof p.progress === 'number') {
              progreso.style.width = Math.max(2, Math.min(100, p.progress)) + '%';
              estadoModelo.textContent = `Descargando ${p.file || 'modelo'} · ${Math.round(p.progress)} %`;
            }
          },
        });
        progreso.style.width = '100%';
        botonCargar.textContent = 'Modelo cargado';
        estadoModelo.textContent = 'Listo. Ahora redacta con las fuentes del sitio.';
        estado.textContent = 'Modelo generativo · Qwen2.5-0.5B';
        burbuja('Modelo cargado en tu navegador. A partir de ahora redacto con las fuentes del sitio. Si tarda, la búsqueda sigue disponible.', 'sistema');
      } catch (error) {
        progreso.style.width = '0%';
        botonCargar.disabled = false;
        botonCargar.textContent = 'Reintentar carga';
        estadoModelo.textContent = 'No disponible: ' + (error.message || 'error de carga') + '. La búsqueda guiada sigue funcionando.';
      } finally {
        cargando = false;
      }
    });
  }

  try {
    const respuesta = await fetch(root.dataset.corpus, { headers: { Accept: 'application/json' } });
    if (!respuesta.ok) throw new Error('No se pudo cargar el corpus');
    const datos = await respuesta.json();
    const chunks = Array.isArray(datos.chunks) ? datos.chunks : [];
    buscador = new Buscador(chunks);
    estado.textContent = `Búsqueda lista · ${chunks.length} fragmentos`;
  } catch (error) {
    estado.textContent = 'Índice no disponible';
    burbuja('No pude cargar el índice del sitio. Revisa tu conexión y recarga la página.', 'sistema');
  }
};

const root = document.querySelector('[data-ia-chat]');
if (root) iniciar(root);
