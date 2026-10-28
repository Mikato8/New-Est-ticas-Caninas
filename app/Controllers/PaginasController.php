<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Domain\Contenido\Guias;
use App\Repositories\NegocioRepository;
use App\Repositories\PublicacionRepository;

final class PaginasController
{
    public function herramientas(Request $peticion): Response
    {
        $recursos = [
            [
                'nombre' => 'OpenSalon',
                'url' => 'https://github.com/clawnify/OpenSalon',
                'licencia' => 'MIT',
                'tipo' => 'Agenda y citas',
                'uso' => 'Referencia de agenda multi-giro, incluida peluquería canina. Preact + Hono + SQLite.',
                'reutilizable' => true,
            ],
            [
                'nombre' => 'Groomwise',
                'url' => 'https://github.com/russkyc/groomwise',
                'licencia' => 'GPL-3.0',
                'tipo' => 'CRM de peluquería canina',
                'uso' => 'CRM de estética canina a domicilio para Windows. Solo referencia: su copyleft no es compatible con este portal.',
                'reutilizable' => false,
            ],
            [
                'nombre' => 'Pet-Shop-Grooming-Appointment-System',
                'url' => 'https://github.com/humb1e1989/Pet-Shop-Grooming-Appointment-System-',
                'licencia' => 'Apache-2.0',
                'tipo' => 'Citas de grooming',
                'uso' => 'Proyecto académico de citas para estética canina. Referencia de flujo.',
                'reutilizable' => true,
            ],
            [
                'nombre' => 'MaoMaoLand',
                'url' => 'https://github.com/RichyChenYJ/MaoMaoLand',
                'licencia' => 'Sin licencia',
                'tipo' => 'Gestión de estética',
                'uso' => 'Agenda por bloques y bloqueo optimista. Solo referencia: sin licencia no se reutiliza.',
                'reutilizable' => false,
            ],
            [
                'nombre' => 'Yosemite Crew',
                'url' => 'https://github.com/YosemiteCrew/Yosemite-Crew',
                'licencia' => 'Other (revisar License.txt)',
                'tipo' => 'Salud animal',
                'uso' => 'Sistema integral para clínicas veterinarias. Referencia de historial y roles.',
                'reutilizable' => false,
            ],
            [
                'nombre' => 'OpenVPM',
                'url' => 'https://github.com/evangauer/openvpm',
                'licencia' => 'AGPL-3.0',
                'tipo' => 'PIMS veterinario',
                'uso' => 'API-first y autohospedable. Referencia de modelo de datos.',
                'reutilizable' => false,
            ],
            [
                'nombre' => 'PetCare',
                'url' => 'https://github.com/geosem42/PetCare',
                'licencia' => 'MIT',
                'tipo' => 'Historial de mascotas',
                'uso' => 'Modelo útil de historial y expediente. Laravel + Vue.',
                'reutilizable' => true,
            ],
            [
                'nombre' => 'fci-breeds',
                'url' => 'https://github.com/paiv/fci-breeds',
                'licencia' => 'MIT',
                'tipo' => 'Datos de razas',
                'uso' => 'Lista de razas reconocidas por la FCI. Base para un buscador de razas.',
                'reutilizable' => true,
            ],
            [
                'nombre' => 'Dog-Breeds-Dataset',
                'url' => 'https://github.com/AtharvaTaras/Dog-Breeds-Dataset',
                'licencia' => 'CC-BY-4.0',
                'tipo' => 'Imágenes de razas',
                'uso' => 'Imágenes de razas FCI. Requiere atribución si se usan.',
                'reutilizable' => true,
            ],
            [
                'nombre' => 'Dog-Breed-120',
                'url' => 'https://huggingface.co/prithivMLmods/Dog-Breed-120',
                'licencia' => 'Apache-2.0',
                'tipo' => 'Clasificador de razas',
                'uso' => 'Clasificador de 120 razas. No trae ONNX listo para el navegador.',
                'reutilizable' => true,
            ],
            [
                'nombre' => 'Qwen2.5-0.5B-Instruct (ONNX)',
                'url' => 'https://huggingface.co/onnx-community/Qwen2.5-0.5B-Instruct',
                'licencia' => 'Verificar tarjeta (base Apache-2.0)',
                'tipo' => 'Asistente local',
                'uso' => 'Modelo pequeño para el asistente opcional en el navegador. Revisa la licencia antes de producción.',
                'reutilizable' => true,
            ],
            [
                'nombre' => 'SmolLM2-360M-Instruct',
                'url' => 'https://huggingface.co/HuggingFaceTB/SmolLM2-360M-Instruct',
                'licencia' => 'Apache-2.0',
                'tipo' => 'Asistente local',
                'uso' => 'Alternativa más ligera; español limitado, respuestas cortas.',
                'reutilizable' => true,
            ],
            [
                'nombre' => 'pet-health-symptoms-dataset',
                'url' => 'https://huggingface.co/datasets/karenwky/pet-health-symptoms-dataset',
                'licencia' => 'MIT',
                'tipo' => 'Datos de síntomas',
                'uso' => 'Material educativo para contenido de salud. No es consejo veterinario.',
                'reutilizable' => true,
            ],
        ];

        return Response::html(view('paginas/herramientas', [
            'titulo' => 'Herramientas abiertas y fuentes',
            'descripcion' => 'Repos, datasets, modelos y guías veterinarias que inspiran este portal, con su licencia y uso.',
            'recursos' => $recursos,
            'fuentes' => [
                ['nombre' => 'AAHA · Behavior management guidelines', 'url' => 'https://www.aaha.org/resources/2015-aaha-canine-and-feline-behavior-management-guidelines/'],
                ['nombre' => 'AAHA · Infection control guidelines', 'url' => 'https://www.aaha.org/resources/2018-aaha-infection-control-prevention-and-biosecurity-guidelines/'],
                ['nombre' => 'AVMA · Dog nail trimming', 'url' => 'https://www.avma.org/resources-tools/pet-owners/petcare/dog-nail-trimming'],
                ['nombre' => 'AVMA · Ear care', 'url' => 'https://www.avma.org/resources-tools/pet-owners/petcare/dog-ear-care'],
                ['nombre' => 'AVMA · Heatstroke', 'url' => 'https://www.avma.org/resources-tools/pet-owners/petcare/heatstroke-dogs'],
                ['nombre' => 'ASPCA · Dog grooming tips', 'url' => 'https://www.aspca.org/pet-care/dog-care/dog-grooming-tips'],
                ['nombre' => 'ASPCA · Hot weather safety', 'url' => 'https://www.aspca.org/pet-care/general-pet-care/hot-weather-safety-tips'],
                ['nombre' => 'Merck Veterinary Manual', 'url' => 'https://www.merckvetmanual.com/'],
            ],
        ]));
    }

    public function privacidad(Request $peticion): Response
    {
        return Response::html(view('paginas/privacidad', [
            'titulo' => 'Aviso de privacidad',
            'descripcion' => 'Cómo tratamos los datos personales de clientes, tutores y estilistas en Mikato Estilistas Caninos.',
        ]));
    }

    public function terminos(Request $peticion): Response
    {
        return Response::html(view('paginas/terminos', [
            'titulo' => 'Términos de uso',
            'descripcion' => 'Condiciones de uso del portal, la comunidad y la agenda de Mikato Estilistas Caninos.',
        ]));
    }

    public function asistente(Request $peticion): Response
    {
        return Response::html(view('paginas/asistente', [
            'titulo' => 'Asistente de estética canina',
            'descripcion' => 'Pregunta sobre baño, corte, uñas, secado o negocio. Busca en las guías de Mikato sin enviar tus datos a ningún servidor.',
        ]));
    }

    public function sitemap(Request $peticion): Response
    {
        $base = rtrim((string) config('url'), '/');
        $hoy = date('Y-m-d');
        $urls = [
            ['loc' => '/', 'prioridad' => '1.0'],
            ['loc' => '/servicios', 'prioridad' => '0.9'],
            ['loc' => '/productos', 'prioridad' => '0.8'],
            ['loc' => '/guias', 'prioridad' => '0.9'],
            ['loc' => '/comunidad', 'prioridad' => '0.8'],
            ['loc' => '/directorio', 'prioridad' => '0.8'],
            ['loc' => '/calculadoras', 'prioridad' => '0.8'],
            ['loc' => '/agenda', 'prioridad' => '0.9'],
            ['loc' => '/nosotros', 'prioridad' => '0.5'],
            ['loc' => '/contacto', 'prioridad' => '0.5'],
            ['loc' => '/glosario', 'prioridad' => '0.5'],
            ['loc' => '/buenas-practicas', 'prioridad' => '0.6'],
            ['loc' => '/herramientas', 'prioridad' => '0.4'],
            ['loc' => '/privacidad', 'prioridad' => '0.3'],
            ['loc' => '/terminos', 'prioridad' => '0.3'],
        ];
        foreach (['dilucion', 'precio', 'secado', 'rentabilidad'] as $calc) {
            $urls[] = ['loc' => '/calculadoras/' . $calc, 'prioridad' => '0.7'];
        }
        foreach (Guias::catalogo() as $guia) {
            $urls[] = ['loc' => '/guias/' . $guia['slug'], 'prioridad' => '0.7', 'lastmod' => $guia['actualizado']];
        }
        try {
            foreach (PublicacionRepository::publicadas('', '', 100) as $publicacion) {
                $urls[] = ['loc' => '/comunidad/' . $publicacion['slug'], 'prioridad' => '0.6', 'lastmod' => substr((string) $publicacion['creado_en'], 0, 10)];
            }
            foreach (NegocioRepository::aprobados('', '', 100) as $negocio) {
                $urls[] = ['loc' => '/directorio/' . $negocio['slug'], 'prioridad' => '0.6'];
            }
        } catch (\Throwable) {
            // El sitemap debe responder aun si la base no está disponible.
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            $xml .= '  <url><loc>' . htmlspecialchars($base . $url['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</loc>';
            $xml .= '<lastmod>' . ($url['lastmod'] ?? $hoy) . '</lastmod>';
            $xml .= '<priority>' . $url['prioridad'] . '</priority></url>' . "\n";
        }
        $xml .= '</urlset>';

        return Response::texto($xml, 200, 'application/xml; charset=UTF-8');
    }

    public function fuentes(Request $peticion): Response
    {
        return $this->herramientas($peticion);
    }
}
