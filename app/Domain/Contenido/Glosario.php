<?php

declare(strict_types=1);

namespace App\Domain\Contenido;

final class Glosario
{
    /** @return list<array{termino: string, definicion: string, categoria: string}> */
    public static function todos(): array
    {
        return [
            ['termino' => 'Manto', 'definicion' => 'Conjunto de pelo que cubre al perro. Incluye capa externa y, en muchas razas, subpelo.', 'categoria' => 'manto'],
            ['termino' => 'Subpelo', 'definicion' => 'Capa interna suave y densa que aísla del frío y del calor. Se suelta en la muda.', 'categoria' => 'manto'],
            ['termino' => 'Doble capa', 'definicion' => 'Manto con pelo externo e subpelo. Ejemplos: Husky, Akita, Pastor alemán, Golden retriever.', 'categoria' => 'manto'],
            ['termino' => 'Muda', 'definicion' => 'Periodo en que el perro suelta pelo muerto. En doble capa suele ocurrir dos veces al año.', 'categoria' => 'manto'],
            ['termino' => 'Deslanado', 'definicion' => 'Retiro del subpelo muerto con rastrillo o carda, sin rasurar la capa externa.', 'categoria' => 'manto'],
            ['termino' => 'Stripping', 'definicion' => 'Técnica de arrancado manual del pelo muerto en mantos duros, como Schnauzer o Terrier.', 'categoria' => 'manto'],
            ['termino' => 'Nudo', 'definicion' => 'Entrelazado de pelo que se aprieta contra la piel. No se debe mojar ni arrancar en seco.', 'categoria' => 'manto'],
            ['termino' => 'Capa', 'definicion' => 'Sección de pelo que se separa para cepillar o cortar de forma ordenada.', 'categoria' => 'manto'],
            ['termino' => 'Slicker', 'definicion' => 'Cepillo de cerdas metálicas finas con base plana. Sirve para desenredar y alisar.', 'categoria' => 'herramienta'],
            ['termino' => 'Carda', 'definicion' => 'Cepillo de púas metálicas curvadas para desenredar mantos largos y tupidos.', 'categoria' => 'herramienta'],
            ['termino' => 'Rastrillo', 'definicion' => 'Herramienta con dientes que retira subpelo muerto sin cortar la capa externa.', 'categoria' => 'herramienta'],
            ['termino' => 'Peine de acero', 'definicion' => 'Peine de dientes separados para verificar nudos y ordenar el acabado.', 'categoria' => 'herramienta'],
            ['termino' => 'Hoja', 'definicion' => 'Cuchilla de la máquina. El número indica el largo del corte: menor número, más corto.', 'categoria' => 'herramienta'],
            ['termino' => 'Guarda', 'definicion' => 'Accesorio que se coloca sobre la hoja para dejar un largo uniforme.', 'categoria' => 'herramienta'],
            ['termino' => 'Tijera de entresacar', 'definicion' => 'Tijera con dientes que rebaja volumen sin marcar líneas duras.', 'categoria' => 'herramienta'],
            ['termino' => 'Tijera roma', 'definicion' => 'Tijera de punta redondeada para zonas delicadas como patas, cara y zona sanitaria.', 'categoria' => 'herramienta'],
            ['termino' => 'Vaso', 'definicion' => 'Parte viva de la uña con vasos sanguíneos y nervio. También se le llama *quick*.', 'categoria' => 'salud'],
            ['termino' => 'Espolón', 'definicion' => 'Dedo adicional en la parte interna de la pata. Su uña no se desgasta y hay que revisarla.', 'categoria' => 'salud'],
            ['termino' => 'Otitis externa', 'definicion' => 'Inflamación del conducto auditivo externo. Requiere diagnóstico veterinario.', 'categoria' => 'salud'],
            ['termino' => 'Glándula anal', 'definicion' => 'Glándula que produce secreción para marcar territorio. No debe exprimirse de rutina.', 'categoria' => 'salud'],
            ['termino' => 'Golpe de calor', 'definicion' => 'Elevación peligrosa de la temperatura corporal. Es una urgencia veterinaria.', 'categoria' => 'salud'],
            ['termino' => 'Braquicéfalo', 'definicion' => 'Perro de hocico corto y vías respiratorias estrechas. Tiene mayor riesgo con el calor y el estrés.', 'categoria' => 'salud'],
            ['termino' => 'Dermatitis', 'definicion' => 'Inflamación de la piel por alergia, infección, parásitos o irritación. Requiere valoración.', 'categoria' => 'salud'],
            ['termino' => 'Pioderma', 'definicion' => 'Infección bacteriana de la piel. Se ve como pústulas, costras o enrojecimiento.', 'categoria' => 'salud'],
            ['termino' => 'Hipotermia', 'definicion' => 'Descenso peligroso de la temperatura corporal, frecuente en perros mojados y pequeños.', 'categoria' => 'salud'],
            ['termino' => 'Feromona', 'definicion' => 'Señal química sintética que puede ayudar a reducir el estrés en el ambiente.', 'categoria' => 'manejo'],
            ['termino' => 'Manejo de bajo estrés', 'definicion' => 'Conjunto de técnicas para reducir miedo y forcejeo durante el servicio.', 'categoria' => 'manejo'],
            ['termino' => 'Bozal de cesta', 'definicion' => 'Bozal que permite jadear y beber. Es preferible al bozal de tela ajustado.', 'categoria' => 'manejo'],
            ['termino' => 'Consentimiento informado', 'definicion' => 'Documento donde el tutor autoriza el servicio y reconoce riesgos y cuidados.', 'categoria' => 'negocio'],
            ['termino' => 'Ticket promedio', 'definicion' => 'Venta total dividida entre el número de servicios o tickets del periodo.', 'categoria' => 'negocio'],
            ['termino' => 'Punto de equilibrio', 'definicion' => 'Número de servicios necesarios para cubrir los costos fijos del negocio.', 'categoria' => 'negocio'],
            ['termino' => 'Margen', 'definicion' => 'Porcentaje de utilidad que queda después de cubrir costos directos y comisiones.', 'categoria' => 'negocio'],
        ];
    }

    /** @return list<array{termino: string, definicion: string, categoria: string}> */
    public static function porCategoria(string $categoria): array
    {
        return array_values(array_filter(self::todos(), static fn (array $t): bool => $t['categoria'] === $categoria));
    }

    /** @return array<string, string> */
    public static function categorias(): array
    {
        return [
            'manto' => 'Manto y pelo',
            'herramienta' => 'Herramientas',
            'salud' => 'Salud y seguridad',
            'manejo' => 'Manejo',
            'negocio' => 'Negocio',
        ];
    }
}
