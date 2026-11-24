# Formatos para prestadores de servicios estéticos caninos

Documentos originales de Mikato para estéticas caninas. No reproducen el diseño,
los textos ni la identidad de formatos de terceros. Se pueden adaptar al negocio
que los use.

## Archivos

| Archivo | Uso | Páginas |
|---|---|---|
| `ficha-estilo.html` | Ficha de estilo y acuerdo de corte | 2 |
| `checklist-ingreso.html` | Checklist de ingreso y autorización | 2 |
| `guia-prestadores.html` | Guía de obligaciones y responsabilidades | 6 |
| `carta-servicio.html` | Carta de servicio para el cliente | 1 |
| `formatos.css` | Sistema visual compartido (tamaño carta) | — |

Los PDF se publican en `public/descargas/formatos/`.

## Regenerar los PDF

Requiere Chrome o Chromium. Desde esta carpeta:

```bash
CHROME="/Applications/Google Chrome.app/Contents/MacOS/Google Chrome"
for pair in "ficha-estilo:Mikato-Ficha-de-estilo" \
            "checklist-ingreso:Mikato-Checklist-de-ingreso" \
            "guia-prestadores:Mikato-Guia-prestadores" \
            "carta-servicio:Mikato-Carta-de-servicio"; do
  html="${pair%%:*}"; out="${pair##*:}"
  "$CHROME" --headless=new --no-sandbox --disable-gpu --no-pdf-header-footer \
    --virtual-time-budget=5000 \
    --print-to-pdf="$PWD/../public/descargas/formatos/$out.pdf" \
    "file://$PWD/$html.html"
done
```

Verifica el tamaño carta y que no haya contenido cortado:

```bash
pdfinfo ../public/descargas/formatos/Mikato-Ficha-de-estilo.pdf
pdftoppm -png -r 110 ../public/descargas/formatos/Mikato-Ficha-de-estilo.pdf /tmp/ficha
```

## Antes de usarlos como contrato

- Revisa el texto con un abogado de tu estado.
- Ajusta el aviso de privacidad y el plazo de conservación del expediente.
- No elimines la referencia a los derechos de la Ley Federal de Protección al
  Consumidor ni la posibilidad de acudir a PROFECO.
- Confirma los requisitos municipales de giro, salud y bienestar animal.

Base: Ley Federal de Protección al Consumidor (artículos 12 y 32), PROFECO
*Servicios estéticos para mascotas* (2019) y LFPDPPP. Este material es una guía
de operación, no asesoría legal.
