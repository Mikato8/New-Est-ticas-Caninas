# Contenido editorial

## Guías (12)

| Guía | Categoría | Nivel |
|---|---|---|
| Baño profesional paso a paso | Baño y manto | Básico |
| Corte de uñas seguro | Corte y uñas | Básico |
| Oídos y pliegues | Salud y seguridad | Básico |
| Corte higiénico y sanitario | Corte y uñas | Intermedio |
| Cepillado y deslanado | Baño y manto | Intermedio |
| Manejo de perros nerviosos | Manejo y conducta | Intermedio |
| Secado seguro | Salud y seguridad | Básico |
| Primeros auxilios en la estética | Salud y seguridad | Intermedio |
| Cómo cotizar servicios | Negocio y precios | Básico |
| Abrir una estética en México | Negocio y precios | Intermedio |
| Higiene del equipo entre perros | Salud y seguridad | Básico |
| Razas populares en México | Baño y manto | Intermedio |

Cada guía incluye secciones en Markdown seguro, FAQ y fuentes. El contenido vive en `app/Domain/Contenido/Guias.php` para versionarse con git.

## Marco legal: lo que sí y lo que no

- **No existe una NOM federal específica de estética canina.**
- **NOM-042-SSA2-2006** regula centros de atención canina antirrábicos; no aplica a una estética.
- **NOM-033-SAG/ZOO-2014** regula métodos para dar muerte a animales; no regula baños ni cortes.
- **NOM-087-ECOL-SSA1-2002** aplica a atención médica; una estética que solo baña y corta pelo no genera RPBI.
- La **LFPDPPP** es una ley nueva (DOF 20-03-2025); la autoridad ya no es el INAI sino la **Secretaría Anticorrupción y Buen Gobierno**.
- Las obligaciones de licencia, uso de suelo y protección civil son **municipales/estatales** y cambian por entidad. Verifica en tu ventanilla.

Fuentes y fecha de consulta en `docs/INVESTIGACION-2026-10-27.md` y en la página `/buenas-practicas`.

## Datos de ejemplo

El seed crea:

- 14 servicios, 10 productos y 3 paquetes con precios MXN de ejemplo.
- 6 clientes, 8 mascotas, 6 citas, 4 registros de historial, 8 ventas y 6 gastos.
- 6 publicaciones editoriales y 4 comentarios aprobados.
- 4 fichas del directorio marcadas como **ejemplo** (`es_demo = true`), con la leyenda visible.
- 2 mensajes de contacto de ejemplo.

Todo se puede editar o borrar desde el panel. No se inventan clientes reales ni se publican fotos sin permiso.

## Estilo editorial

- Español de México, cercano pero técnico.
- No se promete anestesia para estética, rasurado "refrescante" ni vaciado de glándulas de rutina.
- Cuando no hay una cifra oficial (temperatura exacta de secado, distancia), se dice y se usa un criterio conservador.
- Las afirmaciones de salud se contrastan con ASPCA, AVMA, AAHA o Merck.
- Los enlaces externos abren en pestaña nueva y no se reproduce texto normativo íntegro.

## Asistente

`php bin/build-ia.php` genera `public/assets/ia/corpus.json` con fragmentos de guías, FAQ, glosario, calculadoras, servicios y productos. El navegador usa BM25 y muestra la fuente. El modelo generativo es opcional y se ejecuta localmente.
