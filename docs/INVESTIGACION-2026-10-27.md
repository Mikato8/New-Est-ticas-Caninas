# Estética canina en México: recursos abiertos, marco legal y contenido técnico

**Consulta:** 27-10-2026. Enlaces verificados por HTTP; lo no confirmado se señala.

---

## 1. Repositorios y recursos abiertos útiles

| Nombre | URL | Licencia | Uso | Nota |
|---|---|---|---|---|
| OpenSalon | https://github.com/clawnify/OpenSalon | MIT (confirmada) | Agenda/citas multi-giro; incluye pet groomers. Preact + Hono + SQLite | Reutilizable; **no es PHP**; 63★ |
| Groomwise | https://github.com/russkyc/groomwise | GPL-3.0 (confirmada) | CRM de peluquería canina a domicilio para Windows (C#) | Copyleft: obliga a liberar modificaciones |
| Pet-Shop-Grooming-Appointment-System | https://github.com/humb1e1989/Pet-Shop-Grooming-Appointment-System- | Apache-2.0 (confirmada) | Citas de grooming vía web (proyecto académico) | Reutilizable con aviso de licencia |
| MaoMaoLand | https://github.com/RichyChenYJ/MaoMaoLand | **Sin licencia** | Gestión de estética con agenda por *time-slot bitmap* y bloqueo optimista | Sin licencia: **no reutilizable**, solo referencia |
| Yosemite Crew | https://github.com/YosemiteCrew/Yosemite-Crew | "Other" (`License.txt`) | Sistema integral para salud animal (TS); 2,021★ | Revisar `License.txt` antes de usarlo |
| OpenVPM | https://github.com/evangauer/openvpm | AGPL-3.0 (confirmada) | PIMS veterinario API-first, autohospedable | AGPL: publicar código si se ofrece como servicio |
| PetCare | https://github.com/geosem42/PetCare | MIT (confirmada) | PMS veterinario Laravel + Inertia + Vue | Modelo útil de historial de mascota |
| fci-breeds | https://github.com/paiv/fci-breeds | MIT (confirmada) | Lista de razas reconocidas por la FCI | Datos para tu base de razas |
| Dog-Breeds-Dataset | https://github.com/AtharvaTaras/Dog-Breeds-Dataset | CC-BY-4.0 (confirmada) | Imágenes de razas FCI | Atribución obligatoria |
| Dog-Breed-120 (HF) | https://huggingface.co/prithivMLmods/Dog-Breed-120 | apache-2.0 (confirmada) | Clasificador de 120 razas | Sin ONNX: exportable, no listo para transformers.js |
| Qwen2.5-0.5B-Instruct ONNX (HF) | https://huggingface.co/onnx-community/Qwen2.5-0.5B-Instruct | Tarjeta **sin licencia declarada**; base Qwen2.5 es Apache-2.0 | Asistente local en navegador (ONNX int8/q4) | Verificar licencia antes de producción |
| SmolLM2-360M-Instruct (HF) | https://huggingface.co/HuggingFaceTB/SmolLM2-360M-Instruct | apache-2.0 (confirmada) | ONNX; asistente pequeño | Español limitado; respuestas cortas |
| pet-health-symptoms-dataset (HF) | https://huggingface.co/datasets/karenwky/pet-health-symptoms-dataset | MIT (confirmada) | Síntomas de mascotas para Q&A/contenido | No es consejo veterinario; solo material educativo |

**Sin resultados:** no existe repo de *calculadora de dilución de shampoo* para mascotas (búsqueda: 0 resultados) ni base de datos abierta específica de historial de mascotas para estética; lo más cercano son los PMS veterinarios.

---

## 2. Marco legal y sanitario en México

**No existe NOM específica de estética canina.** La más citada, **NOM-042-SSA2-2006** (DOF 06-11-2008), regula *centros de atención canina* antirrábicos (captura, sacrificio, rabia) y **no aplica**. **NOM-033-SAG/ZOO-2014** regula *métodos para dar muerte*, no estética. **NOM-087-ECOL-SSA1-2002** aplica a atención médica; una estética que solo baña y corta pelo no genera RPBI.

### Federal

- **Ley Federal de Protección al Consumidor** (última reforma DOF 12-12-2025) — https://www.diputados.gob.mx/LeyesBiblio/pdf/LFPC.pdf
  Arts. 32 (publicidad veraz), 42 (cumplir lo ofrecido), 78-79 (póliza de garantía escrita), 81-82 (vicios ocultos), 92 (bonificación).
- **Ley Federal de Protección de Datos Personales en Posesión de los Particulares** — nueva ley DOF **20-03-2025**, última reforma 14-11-2025 — https://www.diputados.gob.mx/LeyesBiblio/pdf/LFPDPPP.pdf
  Aviso de privacidad, consentimiento para datos sensibles (art. 8) y derechos ARCO (arts. 27-29). **La autoridad ya no es el INAI**: es la **Secretaría Anticorrupción y Buen Gobierno** (https://www.gob.mx/buengobierno).
- **Ley Federal de Sanidad Animal** (última reforma 21-05-2024) — https://www.diputados.gob.mx/LeyesBiblio/pdf/LFSA.pdf
  Procura el bienestar animal (arts. 1-2) y regula el sacrificio humanitario (art. 23), pero **no la operación de estéticas**. No localicé ley general federal de bienestar animal publicada (dictamen en proceso, SIL sept-2026): verifícalo antes de citarla.
- **NOM-012-ZOO-1993** (DOF 17-01-1995) — https://dof.gob.mx/nota_detalle.php?codigo=4867004&fecha=17/01/1995
  Regula producción, importación y envasado de productos químicos/farmacéuticos para uso animal. Relevante **solo si fabricas o envasas** shampoo; registro: https://www.gob.mx/senasica/acciones-y-programas/registro-de-productos-de-uso-veterinario
- **LGPGIR** (última reforma 19-01-2026) — https://www.diputados.gob.mx/LeyesBiblio/pdf/LGPGIR.pdf — residuos; el pelo y aguas de baño no son peligrosos, pero el registro como generador aplica si manejas residuos peligrosos.
- **Ley General de Protección Civil** (última reforma 21-12-2023), art. 40 — https://www.diputados.gob.mx/LeyesBiblio/pdf/LGPC.pdf — Programa Interno para inmuebles de empresas "a que se refiere el Reglamento"; su obligatoriedad depende del giro, tamaño y normativa estatal/municipal.

### Estatal (ejemplo: Ciudad de México)

- **Ley de Protección y Bienestar de los Animales de la CDMX** (2002; última reforma GOCDMX 07-06-2024) — https://paot.org.mx/centro/leyes-normas-htmls/ley-de-protecci-n-489.html
  Su definición de **"persona prestadora de servicios"** incluye expresamente **estética y spa** (art. 4), así que sí alcanza a tu giro. Cada estado tiene su propia ley; no hay una federal supletoria.

### Municipal

No existe licencia federal. Cada ayuntamiento exige, según su reglamento de unidades económicas/establecimientos mercantiles: **licencia o aviso de funcionamiento**, **constancia/dictamen de uso de suelo o zonificación**, **Programa Interno de Protección Civil**, permiso de anuncios y, en su caso, verificación administrativa. El giro suele clasificarse como servicios personales o "estética/baño de mascotas"; el catálogo exacto está en el reglamento de tu municipio.

**No localicé una obligación federal de "aviso de funcionamiento" aplicable a estéticas caninas.** El aviso sanitario que regula COFEPRIS corresponde a establecimientos de atención médica, no a una estética; confirma con la autoridad sanitaria estatal.

| Trámite típico | Nivel | Dónde se verifica |
|---|---|---|
| Licencia/aviso de funcionamiento | Municipal | Portal de trámites municipal (ej. https://tramites.guadalajara.gob.mx/; CDMX: https://www.sedeco.cdmx.gob.mx/tramites) |
| Uso de suelo / zonificación | Municipal | Dirección de desarrollo urbano municipal |
| Programa Interno de Protección Civil | Municipal/estatal | Protección Civil municipal |
| Aviso de privacidad | Federal (obligación, no trámite) | https://www.diputados.gob.mx/LeyesBiblio/pdf/LFPDPPP.pdf |
| Registro sanitario de producto | Federal | SENASICA (solo si fabricas/envasas) |
| Registro de generador de residuos peligrosos | Federal | SEMARNAT (solo si aplica) |

**Lo que sí debes verificar en tu municipio:** (1) si tu giro requiere licencia o basta aviso; (2) la zonificación permitida para servicios con animales; (3) si exigen PIPC y con qué alcance; (4) permiso de anuncio/fachada; (5) horarios y límites de ruido; (6) manejo de aguas residuales y basura; (7) si hay inspección de sanidad estatal para animales de compañía. No supongas trámites: pídelo por escrito en la ventanilla.

---

## 3. Contenido técnico (recomendaciones con fuente)

1. **Baño y dilución.** Agua tibia, sin mojar ojos ni oídos, enjuague hasta que salga clara; shampoo formulado para la especie ([ASPCA](https://www.aspca.org/pet-care/dog-care/dog-grooming-tips)). **La dilución no tiene norma ni proporción universal**: manda la ficha del fabricante (4:1 o 10:1 son ejemplos comerciales). Mito: "más espuma = más limpieza"; el exceso irrita y deja residuo.
2. **Secado.** Toalla primero; secadora tibia, boquilla en movimiento y supervisión continua. No hay cifra oficial de distancia/temperatura (consenso profesional). **Riesgo:** dejar al perro solo en jaula de secado puede causar sobrecalentamiento y muerte.
3. **Uñas.** Recorta cuando las uñas toquen el suelo o se oigan al caminar; incrementos de 1-2 mm, detente antes de la *quick*; ten estíptico a mano ([ASPCA](https://www.aspca.org/pet-care/dog-care/dog-grooming-tips), [AVMA](https://www.avma.org/resources-tools/pet-owners/petcare/dog-nail-trimming)). En uñas oscuras, corta menos y con luz.
4. **Oídos.** Limpia solo el pabellón con gasa; **nada de cotonetes en el canal** ni alcohol/agua oxigenada. Con dolor, olor, secreción o cabeza ladeada, deriva al veterinario ([AVMA](https://www.avma.org/resources-tools/pet-owners/petcare/dog-ear-care)).
5. **Glándulas anales.** **No se vacían de rutina.** Solo con signos (se arrastra, se lame, dolor al sentarse) o indicación veterinaria; forzarla puede traumatizar e infectar. Razas pequeñas predispuestas ([Merck](https://www.merckvetmanual.com/es-us/veterinary/propietarios-de-perros/trastornos-digestivos-de-los-perros/trastornos-del-recto-y-el-ano-en-los-perros)).
6. **Deslanado.** Cepilla según manto y usa herramienta de deslanado; **nunca rasures doble capa**: el manto aísla del calor y del sol ([ASPCA](https://www.aspca.org/pet-care/general-pet-care/hot-weather-safety-tips): "never shave your dog"). Mito: "rasurar lo refresca" — puede causar alopecia y quemaduras.
7. **Perros ansiosos o agresivos.** Aprende señales de ansiedad/miedo, manejo de bajo estrés, bozal si hace falta y **no fuerces**: si no tolera el manejo, se detiene y se deriva ([AAHA](https://www.aaha.org/resources/2015-aaha-canine-and-feline-behavior-management-guidelines/)).
8. **Higiene del equipo.** Desinfecta tijeras, máquinas y mesas entre perros, respeta dilución y tiempo de contacto, e higiene de manos entre pacientes ([AAHA](https://www.aaha.org/resources/2018-aaha-infection-control-prevention-and-biosecurity-guidelines/)).
9. **Golpe de calor.** Signos: jadeo intenso, saliva espesa, encías rojas, debilidad, tambaleo. Enfría con agua templada (no hielo), ventila y traslada al veterinario ([AVMA](https://www.avma.org/resources-tools/pet-owners/petcare/heatstroke-dogs)). Riesgo: braquicéfalos, obesos, geriátricos, cardiópatas.
10. **Mitos y prácticas de riesgo.** (a) Rasurar doble capa: no. (b) **Anestesia para estética**: es acto veterinario, con riesgo anestésico; nunca la aplique un estilista. (c) **Lazo al cuello** para sujetar: riesgo de asfixia y lesión cervical; usa arnés y sujeción de dos puntos. (d) **Talco/perfumes** en aerosol: irritan vías respiratorias. (e) "Corte sanitario" o extracción de glándulas como rutina: no son procedimientos cosméticos.

---

## 4. Diez conclusiones accionables para el sitio (PHP/Mikato)

1. **Calculadora de dilución** con la ficha de cada producto Mikato (producto → ratio → ml/L), no ratios genéricos: es el hueco que ningún repo cubre.
2. **Agenda de citas** con duración por servicio y raza (inspirada en OpenSalon/Groomwise).
3. **Historial de mascota** (peso, manto, alergias, incidencias) con consentimiento y aviso de privacidad LFPDPPP 2025.
4. **Buscador de razas** con datos FCI (`paiv/fci-breeds`, MIT) y tipo de manto.
5. **Guía legal por municipio** (licencia, uso de suelo, PIPC) con enlaces oficiales y fecha visible.
6. **Generador de aviso de privacidad** con la autoridad correcta (SABG, no INAI).
7. **Checklist de bioseguridad** descargable basada en AAHA.
8. **Quiz de golpe de calor** y protocolo de emergencia de una página.
9. **Asistente local en español** (Qwen2.5-0.5B ONNX en navegador) con disclaimer y sin datos de clientes.
10. Evita prometer "anestesia para estética", "rasurado refrescante" o "vaciado de glándulas incluido": son riesgo legal y reputacional.
