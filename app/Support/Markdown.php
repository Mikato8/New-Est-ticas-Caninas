<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Renderizador Markdown mínimo y seguro: primero escapa todo el HTML y luego
 * aplica un subconjunto de sintaxis. No permite HTML crudo.
 */
final class Markdown
{
    public static function toHtml(string $texto): string
    {
        $texto = str_replace(["\r\n", "\r"], "\n", trim($texto));
        $codigos = [];
        $texto = preg_replace_callback('/```([a-zA-Z0-9_-]*)\n(.*?)```/s', static function (array $m) use (&$codigos): string {
            $clave = '%%CODIGO' . count($codigos) . '%%';
            $codigos[$clave] = '<pre class="codigo"><code>' . htmlspecialchars($m[2], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</code></pre>';
            return "\n" . $clave . "\n";
        }, $texto) ?? $texto;

        $lineas = explode("\n", $texto);
        $html = [];
        $parrafo = [];
        $lista = null;
        $cita = [];
        $total = count($lineas);

        $cerrarParrafo = static function () use (&$parrafo, &$html): void {
            if ($parrafo !== []) {
                $html[] = '<p>' . self::enLinea(implode(' ', $parrafo)) . '</p>';
                $parrafo = [];
            }
        };
        $cerrarLista = static function () use (&$lista, &$html): void {
            if ($lista !== null) {
                $html[] = '</' . $lista . '>';
                $lista = null;
            }
        };
        $cerrarCita = static function () use (&$cita, &$html): void {
            if ($cita !== []) {
                $html[] = '<blockquote><p>' . self::enLinea(implode(' ', $cita)) . '</p></blockquote>';
                $cita = [];
            }
        };

        for ($i = 0; $i < $total; $i++) {
            $linea = rtrim($lineas[$i]);
            $limpia = trim($linea);

            if ($limpia === '') {
                $cerrarParrafo();
                $cerrarLista();
                $cerrarCita();
                continue;
            }

            if (preg_match('/^#{1,4}\s+(.+)$/', $limpia, $m)) {
                $cerrarParrafo();
                $cerrarLista();
                $cerrarCita();
                $nivel = strlen((string) strstr($limpia, ' ', true));
                $html[] = '<h' . $nivel . '>' . self::enLinea(trim($m[1])) . '</h' . $nivel . '>';
                continue;
            }

            if (preg_match('/^(?:---+|\*\*\*+)$/', $limpia)) {
                $cerrarParrafo();
                $cerrarLista();
                $cerrarCita();
                $html[] = '<hr>';
                continue;
            }

            if (str_starts_with($limpia, '>')) {
                $cerrarParrafo();
                $cerrarLista();
                $cita[] = trim(substr($limpia, 1));
                continue;
            }

            if (preg_match('/^[-*]\s+(.+)$/', $limpia, $m)) {
                $cerrarParrafo();
                $cerrarCita();
                if ($lista !== 'ul') {
                    $cerrarLista();
                    $html[] = '<ul>';
                    $lista = 'ul';
                }
                $html[] = '<li>' . self::enLinea($m[1]) . '</li>';
                continue;
            }

            if (preg_match('/^\d+\.\s+(.+)$/', $limpia, $m)) {
                $cerrarParrafo();
                $cerrarCita();
                if ($lista !== 'ol') {
                    $cerrarLista();
                    $html[] = '<ol>';
                    $lista = 'ol';
                }
                $html[] = '<li>' . self::enLinea($m[1]) . '</li>';
                continue;
            }

            if (str_contains($limpia, '|') && $i + 1 < $total && preg_match('/^\|?[\s:-]+\|[\s:|-]*$/', trim($lineas[$i + 1]))) {
                $cerrarParrafo();
                $cerrarLista();
                $cerrarCita();
                $encabezados = self::celdas($limpia);
                $i += 2;
                $filas = [];
                while ($i < $total && str_contains($lineas[$i], '|') && trim($lineas[$i]) !== '') {
                    $filas[] = self::celdas($lineas[$i]);
                    $i++;
                }
                $i--;
                $html[] = '<div class="tabla-scroll"><table><thead><tr>';
                foreach ($encabezados as $celda) {
                    $html[] = '<th>' . self::enLinea($celda) . '</th>';
                }
                $html[] = '</tr></thead><tbody>';
                foreach ($filas as $fila) {
                    $html[] = '<tr>';
                    foreach ($fila as $celda) {
                        $html[] = '<td>' . self::enLinea($celda) . '</td>';
                    }
                    $html[] = '</tr>';
                }
                $html[] = '</tbody></table></div>';
                continue;
            }

            $parrafo[] = $limpia;
        }

        $cerrarParrafo();
        $cerrarLista();
        $cerrarCita();

        $salida = implode("\n", $html);
        foreach ($codigos as $clave => $bloque) {
            $salida = str_replace('<p>' . $clave . '</p>', $bloque, $salida);
            $salida = str_replace($clave, $bloque, $salida);
        }

        return $salida;
    }

    /** @return list<string> */
    private static function celdas(string $linea): array
    {
        $linea = trim($linea);
        $linea = trim($linea, '|');
        return array_map('trim', explode('|', $linea));
    }

    private static function enLinea(string $texto): string
    {
        $texto = htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $texto = preg_replace_callback('/\[([^\]]+)\]\((https?:\/\/[^\s)]+|\/[^\s)]*)\)/', static function (array $m): string {
            $url = $m[2];
            return '<a href="' . $url . '" rel="noopener noreferrer"' . (str_starts_with($url, 'http') ? ' target="_blank"' : '') . '>' . $m[1] . '</a>';
        }, $texto) ?? $texto;

        $texto = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $texto) ?? $texto;
        $texto = preg_replace('/(?<!\*)\*([^*]+)\*(?!\*)/', '<em>$1</em>', $texto) ?? $texto;
        $texto = preg_replace('/`([^`]+)`/', '<code>$1</code>', $texto) ?? $texto;

        return $texto;
    }

    public static function resumen(string $texto, int $limite = 180): string
    {
        $plano = preg_replace('/[#>*_`\[\]()]/', ' ', $texto) ?? $texto;
        $plano = preg_replace('/\s+/', ' ', trim($plano)) ?? $plano;
        return mb_strlen($plano) > $limite ? mb_substr($plano, 0, $limite - 1) . '…' : $plano;
    }
}
