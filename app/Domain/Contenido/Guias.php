<?php

declare(strict_types=1);

namespace App\Domain\Contenido;

/**
 * Guías de oficio para estilistas caninos.
 * El contenido vive en PHP para versionarse con git y no depender de la base.
 * No sustituye la valoración de un médico veterinario.
 */
final class Guias
{
    /** @return list<array<string, mixed>> */
    public static function catalogo(): array
    {
        return [
            [
                'slug' => 'bano-profesional-paso-a-paso',
                'titulo' => 'Baño profesional paso a paso',
                'resumen' => 'Del cepillado previo al secado final: temperatura, dilución, dos pasadas de shampoo y los errores que más resecan la piel.',
                'categoria' => 'bano',
                'nivel' => 'Básico',
                'minutos' => 9,
                'icono' => 'bano',
                'destacada' => true,
                'actualizado' => '2026-10-27',
                'secciones' => [
                    ['titulo' => 'Antes de mojar: la revisión que evita problemas', 'md' => <<<'MD'
El baño empieza en seco. Antes de abrir la regadera, revisa al perro de nariz a cola y anota lo que encuentres en su ficha:

- **Piel:** enrojecimiento, costras, pulgas, garrapatas, zonas calvas o humedad en pliegues.
- **Oídos:** olor, secreción, enrojecimiento o dolor al tocar. Si hay dolor, no limpies a fondo: deriva al veterinario.
- **Uñas y almohadillas:** uñas largas, cortes, objetos clavados, grietas.
- **Nudos:** si hay nudos cerrados contra la piel, no los arranques mojados. Desenreda en seco o rasura con cuidado.
- **Temperamento:** ¿se deja tocar?, ¿gruñe?, ¿se esconde? Ajusta el manejo antes de mojar.

> Un perro mojado resbala, se sacude y muerde más fácil. La revisión en seco es tu mejor herramienta de seguridad.

Protege los oídos con algodón **en la entrada** del conducto, nunca dentro. Si el perro tiene ojos saltones o úlceras, usa protección ocular y consulta al veterinario.
MD],
                    ['titulo' => 'Agua, jabón y temperatura', 'md' => <<<'MD'
Usa agua tibia entre **35 y 38 °C**. El agua caliente reseca la piel, aumenta el riesgo de quemadura y estresa al perro. Prueba con tu muñeca: debe sentirse tibia, no caliente.

**Nunca apliques shampoo concentrado directo sobre el pelo.** Dilúyelo en agua tibia y agita suavemente. La dilución permite cubrir todo el cuerpo, enjuagar mejor y gastar menos producto.

No existe una dilución universal: **manda la ficha del fabricante**. La siguiente tabla es un punto de partida profesional; si tu producto indica otra proporción, usa la del fabricante.

| Tipo de pelo | Dilución de trabajo | Cuándo usarla |
|---|---|---|
| Corto | 1:10 | Mantenimiento sano |
| Medio | 1:10 a 1:15 | Baño normal |
| Largo | 1:15 | Pelo fino que se enreda |
| Doble capa | 1:10 | Llegar a la piel sin saturar |
| Suciedad pesada | 1:10 primera pasada, 1:15 segunda | Barro, grasa, calle |

La dosis de mezcla por tamaño es una guía: un perro chico consume alrededor de 120 ml de mezcla, un mediano 200 ml, un grande 320 ml y un gigante 450 ml. Ajusta según el largo y la suciedad. Calcula tu mezcla exacta en la [calculadora de dilución](/calculadoras/dilucion).
MD],
                    ['titulo' => 'Las dos pasadas que sí limpian', 'md' => <<<'MD'
1. **Primera pasada:** moja todo el cuerpo, aplica la mezcla y masajea con la yema de los dedos. Empieza por el lomo, sigue con patas, abdomen y cola. La cabeza y la cara van al final, con una esponja y mucho cuidado.
2. **Enjuaga** hasta que el agua salga clara. Este paso toma más tiempo del que parece.
3. **Segunda pasada:** repite con menos producto. Sirve para arrastrar el residuo de la primera y tratar zonas grasas como el cuello, la cola y las patas.
4. **Enjuague final:** revisa axilas, ingles, entre los dedos y detrás de las orejas. Ahí se esconde el shampoo que causa comezón.

Deja actuar el producto entre **3 y 5 minutos** si la ficha del fabricante lo indica. No dejes al perro sentado con jabón por 15 minutos: irrita la piel y enfría al animal.
MD],
                    ['titulo' => 'Cara, oídos y zonas delicadas', 'md' => <<<'MD'
- **Cara:** usa una esponja húmeda y evita ojos, nariz y boca. En razas chatas, limpia los pliegues con gasa y seca bien.
- **Oídos:** no metas agua a presión. Limpia solo la parte visible con una gasa y un limpiador veterinario. Seca la entrada del conducto.
- **Glándulas anales:** no las exprimas de rutina. Si el perro se arrastra, se lame o huele fuerte, valora con el veterinario.
- **Patas:** revisa entre los dedos, seca bien y corta el pelo que arrastra suciedad si el perro lo tolera.
MD],
                    ['titulo' => 'Secado y acabado', 'md' => <<<'MD'
El baño no termina cuando sales de la regadera. Un perro mal secado desarrolla hongos, olores y nudos. Sigue la guía de [secado seguro](/guias/secado-seguro): temperatura tibia, boquilla en movimiento y pausas.

Al final:

- Revisa que no quede humedad en axilas, ingle, entre dedos y detrás de orejas.
- Cepilla en la dirección del pelo para levantar la capa.
- Limpia la cara y los ojos con gasa húmeda.
- Anota en la ficha el shampoo usado, la dilución y cualquier hallazgo de piel.

> Si el perro salió del baño con comezón intensa, enrojecimiento o sacudidas de cabeza, no es "normal". Revisa el producto y deriva.
MD],
                ],
                'faq' => [
                    ['p' => '¿Cuántos baños al mes necesita un perro?', 'r' => 'Depende del estilo de vida y del pelo. Un perro de casa suele necesitar cada 3 a 4 semanas; uno que va al parque o tiene piel grasa, cada 2 a 3. Más baños no siempre es mejor: resecan la piel.'],
                    ['p' => '¿Puedo usar shampoo de humano?', 'r' => 'No de forma habitual. El pH de la piel del perro es diferente y los shampoos humanos pueden irritar. Usa productos formulados para mascotas y respeta la dilución del fabricante.'],
                    ['p' => '¿Qué hago si el perro se sacude con shampoo?', 'r' => 'Enjuaga los ojos con solución fisiológica o agua limpia y revisa si hay enrojecimiento. Si hay dolor, secreción o el perro se talla, acude al veterinario.'],
                ],
                'fuentes' => [
                    ['titulo' => 'ASPCA · Dog grooming tips', 'url' => 'https://www.aspca.org/pet-care/dog-care/dog-grooming-tips'],
                    ['titulo' => 'AVMA · Pet care', 'url' => 'https://www.avma.org/resources-tools/pet-owners/petcare'],
                ],
            ],
            [
                'slug' => 'corte-de-unas-seguro',
                'titulo' => 'Corte de uñas seguro: hasta dónde cortar',
                'resumen' => 'Cómo leer la uña, dónde está el vaso sanguíneo, qué hacer si sangra y por qué el limado cambia el resultado.',
                'categoria' => 'corte',
                'nivel' => 'Básico',
                'minutos' => 7,
                'icono' => 'una',
                'destacada' => true,
                'actualizado' => '2026-10-27',
                'secciones' => [
                    ['titulo' => 'Anatomía rápida de la uña', 'md' => <<<'MD'
La uña del perro tiene una parte dura (queratina) y una parte viva con vasos y nervio, conocida como **vaso** o *quick*. En uñas claras se ve rosado; en uñas negras no se ve y hay que ir poco a poco.

- La uña debe quedar a ras de la almohadilla o un poco por delante, sin tocar el piso al caminar.
- En perros que caminan mucho en asfalto, el desgaste natural puede ser suficiente; en perros de casa, no.
- Las uñas delanteras suelen crecer más que las traseras.
- Los **espolones** (dewclaws) no se desgastan y se encarnan si no se cortan.

> Regla de oro: **2 milímetros por corte** y mira la cara del corte. Si ves un punto rosado o una dona oscura, detente.
MD],
                    ['titulo' => 'Herramienta y técnica', 'md' => <<<'MD'
Usa un cortaúñas afilado, del tamaño del perro. Un cortaúñas chico para un perro grande aplasta la uña y duele.

1. Sujeta la pata con firmeza, sin apretar la articulación.
2. Corta perpendicular a la uña, no en diagonal.
3. Haz cortes pequeños y revisa la cara interna después de cada uno.
4. En uñas negras, busca el centro oscuro y detente antes de llegar.
5. Lima el borde para quitar la rebaba y redondear.

Si el perro tiene miedo, pide ayuda a otra persona para sostener el cuerpo, no el cuello. Trabaja una pata por sesión y premia.
MD],
                    ['titulo' => 'Si se corta el vaso', 'md' => <<<'MD'
Pasa. La clave es no entrar en pánico:

- Aplica presión directa con gasa limpia durante **3 a 5 minutos** sin levantar para revisar.
- Usa polvo hemostático si lo tienes; sigue las instrucciones del producto.
- No uses harina, café ni ceniza: contaminan la herida.
- Si el sangrado no para en 10 minutos, acude al veterinario.
- Limpia la zona y evita que el perro lama en exceso.

> Un corte en el vaso no es una emergencia si controlas el sangrado, pero sí es una señal de que necesitas más luz, mejor herramienta y menos prisa.
MD],
                    ['titulo' => 'Frecuencia y señales de alerta', 'md' => <<<'MD'
Revisa las uñas cada **3 a 4 semanas**. Señales de que ya toca:

- Se escucha clic al caminar en piso duro.
- La uña se curva hacia el piso o hacia los dedos.
- El perro cojea, se lame o evita que le toquen la pata.
- Hay uña encarnada, enrojecimiento o mal olor.

**No cortes** si hay una herida abierta, hongo, fractura o dolor intenso. Deriva al veterinario.

En perros geriátricos o con artritis, usa una base antideslizante y sesiones cortas. En cachorros, empieza con la lima y el manejo antes de cortar.
MD],
                ],
                'faq' => [
                    ['p' => '¿Debo cortar el vaso para que la uña se retraiga?', 'r' => 'No. El vaso no se "entrena" cortándolo; se retrae con cortes frecuentes y mínimos. Cortar de más causa dolor, sangrado y miedo a la mesa.'],
                    ['p' => '¿Sirve limar en lugar de cortar?', 'r' => 'Sí, sobre todo en uñas negras y perros nerviosos. La lima da control, pero genera calor: úsala en pasadas cortas.'],
                    ['p' => '¿Qué hago con los espolones?', 'r' => 'Revísalos en cada visita. Si están muy largos, se enredan y se encarnan. Córtalos con la misma técnica, con mucho cuidado.'],
                ],
                'fuentes' => [
                    ['titulo' => 'ASPCA · Dog grooming tips', 'url' => 'https://www.aspca.org/pet-care/dog-care/dog-grooming-tips'],
                    ['titulo' => 'Merck Veterinary Manual · Claw disorders in dogs', 'url' => 'https://www.merckvetmanual.com/dog-owners/claw-and-claw-bed-disorders/claw-disorders-in-dogs'],
                ],
            ],
            [
                'slug' => 'limpieza-de-oidos-y-pliegues',
                'titulo' => 'Oídos y pliegues: limpiar sin lastimar',
                'resumen' => 'La diferencia entre limpieza de mantenimiento e infección, cómo usar el limpiador y por qué el hisopo profundo es mala idea.',
                'categoria' => 'salud',
                'nivel' => 'Básico',
                'minutos' => 6,
                'icono' => 'oido',
                'destacada' => false,
                'actualizado' => '2026-10-27',
                'secciones' => [
                    ['titulo' => 'Oído sano vs. oído que necesita veterinario', 'md' => <<<'MD'
Un oído sano se ve rosado pálido, sin olor fuerte, sin secreción y sin enrojecimiento. El perro no debería quejarse al tocarlo.

Señales de alerta:

- Olor dulce, ácido o a levadura.
- Secreción café, amarilla o negra.
- Enrojecimiento, hinchazón o calor.
- Sacudidas de cabeza frecuentes.
- Se rasca, se lame o camina en círculos.
- Dolor al abrir la oreja.

Si ves cualquiera de estas señales, **no limpies a fondo**: deriva al veterinario. Limpiar una infección puede empujar la suciedad más adentro y romper el tímpano.
MD],
                    ['titulo' => 'Limpieza de mantenimiento', 'md' => <<<'MD'
1. Usa un limpiador ótico veterinario, no agua oxigenada, alcohol ni vinagre.
2. Aplica unas gotas en el conducto visible y masajea la base de la oreja.
3. Deja que el perro se sacuda.
4. Limpia la parte externa y los pliegues con gasa o algodón.
5. **Nunca introduzcas un hisopo en el conducto.** Solo limpia lo que ves.
6. Seca bien la entrada del oído.

Frecuencia: cada 1 a 2 semanas en perros sanos; más seguido solo si el veterinario lo indica. En perros con orejas caídas, revisa cada semana.
MD],
                    ['titulo' => 'Pliegues faciales y corporales', 'md' => <<<'MD'
Bulldogs, pugs, shar peis y otras razas con pliegues necesitan limpieza diaria o cada tercer día:

- Limpia con gasa húmeda y un producto suave indicado por el veterinario.
- Seca **muy bien** con gasa seca. La humedad es el caldo de cultivo de hongos y bacterias.
- Revisa si hay enrojecimiento, mal olor, secreción o dolor.
- No uses talco perfumado dentro del pliegue.
- Si el pliegue está húmedo todo el tiempo, deriva: puede necesitar tratamiento.

En verano, revisa también la zona entre los dedos y la ingle.
MD],
                    ['titulo' => 'Después del baño', 'md' => <<<'MD'
El agua que entra al oído durante el baño es una causa común de otitis externa. Protege con algodón en la entrada (nunca dentro), inclina la cabeza al enjuagar y seca al final.

Si el perro sale del baño sacudiendo la cabeza o rascándose la oreja, revisa antes de irte. No lo mandes a casa "a ver si se le pasa".
MD],
                ],
                'faq' => [
                    ['p' => '¿Puedo usar aceite o vinagre?', 'r' => 'No. El vinagre y el alcohol irritan el conducto y alteran la flora. El aceite atrapa suciedad. Usa limpiadores óticos veterinarios.'],
                    ['p' => '¿Cada cuánto limpio los oídos?', 'r' => 'En un perro sano, cada 1 a 2 semanas. Si hay infección, el veterinario indica el tratamiento y la frecuencia.'],
                    ['p' => '¿El algodón dentro del oído es seguro?', 'r' => 'No. El hisopo puede empujar la suciedad y lastimar el tímpano. Limpia solo la parte visible.'],
                ],
                'fuentes' => [
                    ['titulo' => 'Merck Veterinary Manual · Ear infections in dogs', 'url' => 'https://www.merckvetmanual.com/dog-owners/ear-disorders-of-dogs/ear-infections-in-dogs'],
                    ['titulo' => 'AVMA · Dog ear care', 'url' => 'https://www.avma.org/resources-tools/pet-owners/petcare/dog-ear-care'],
                ],
            ],
            [
                'slug' => 'corte-higienico-y-sanitario',
                'titulo' => 'Corte higiénico: patas, abdomen y zona sanitaria',
                'resumen' => 'Qué rasurar, qué no, cómo usar la máquina sin quemar la piel y cómo dejar el corte limpio sin irritar.',
                'categoria' => 'corte',
                'nivel' => 'Intermedio',
                'minutos' => 8,
                'icono' => 'tijeras',
                'destacada' => false,
                'actualizado' => '2026-10-27',
                'secciones' => [
                    ['titulo' => 'Para qué sirve el corte higiénico', 'md' => <<<'MD'
El corte higiénico mantiene limpia la zona donde se acumula orina, heces y humedad. No es un corte estético: es salud e higiene.

Zonas habituales:

- **Abdomen y entrepierna:** pelo corto para evitar humedad y nudos.
- **Zona perianal:** alrededor del ano, para que las heces no se peguen.
- **Almohadillas:** pelo entre los dedos que arrastra suciedad y favorece hongos.
- **Base de la cola:** si el pelo es muy largo.
- **Orejas:** pelo del conducto solo si el veterinario lo indica; nunca arranques pelo de dentro sin formación.

No rasures el lomo, la cola ni el cuerpo completo "por calor". En perros de doble capa, rasurar puede dañar el folículo y alterar la termorregulación.
MD],
                    ['titulo' => 'Máquina, guía y dirección', 'md' => <<<'MD'
- Usa máquina con hoja afilada y limpia. Una hoja mellada jala el pelo y quema.
- Sujeta la piel tensa con la mano libre para que la hoja no se hunda.
- Trabaja **en la dirección del pelo** en zonas sensibles; a contra pelo solo en el cuerpo si el patrón lo pide.
- Haz pasadas cortas y levanta la hoja con frecuencia. El calor de la hoja es el principal enemigo.
- En pliegues y axilas, usa la hoja más larga o tijera con punta roma.
- Nunca uses la máquina sobre verrugas, lunares, heridas o piel irritada.

> Si la piel se ve roja o el perro se queja, para. El corte no vale una quemadura.
MD],
                    ['titulo' => 'Paso a paso de la zona sanitaria', 'md' => <<<'MD'
1. Baña y seca **antes** de rasurar la zona sanitaria. El pelo limpio y seco se corta mejor.
2. Coloca al perro de pie o recostado sobre una base antideslizante.
3. Sujeta la cola hacia arriba con suavidad, sin torcer.
4. Rasura alrededor del ano en pasadas cortas, de adentro hacia afuera.
5. Revisa si hay irritación, humedad, parásitos o heridas.
6. Limpia la hoja y desinfecta entre perros.

En perros mayores o con sobrepeso, la zona puede ser difícil de alcanzar. Pide ayuda y usa un cabestrillo si es necesario.
MD],
                    ['titulo' => 'Cuándo no rasurar', 'md' => <<<'MD'
- Piel con dermatitis, hongos, heridas o infección: deriva.
- Perros con doble capa y manto sano: evita rasurar todo el cuerpo.
- Perros nerviosos que no toleran la máquina: trabaja por sesiones o usa tijera.
- Zonas con verrugas o tumores: no las toques con la hoja.

Documenta con foto antes y después. Si el perro llega con nudos severos, explica al tutor que el resultado será un corte corto y que el pelo puede tardar meses en recuperarse.
MD],
                ],
                'faq' => [
                    ['p' => '¿Rasurar ayuda con el calor?', 'r' => 'En la mayoría de los casos, no. El pelo actúa como aislante y protege del sol. Rasurar un manto doble puede dejar la piel expuesta y afectar la regulación térmica.'],
                    ['p' => '¿Qué hoja uso para la zona sanitaria?', 'r' => 'Una hoja de 10 o 7F es habitual, pero depende del grosor del pelo y de la tolerancia del perro. En piel sensible, usa tijera o una hoja más larga.'],
                    ['p' => '¿Debo rasurar entre los dedos?', 'r' => 'Solo el pelo que arrastra suciedad o forma nudos. Rasurar al ras puede irritar; recorta con tijera roma y revisa la piel.'],
                ],
                'fuentes' => [
                    ['titulo' => 'ASPCA · Dog grooming tips', 'url' => 'https://www.aspca.org/pet-care/dog-care/dog-grooming-tips'],
                    ['titulo' => 'AVMA · Pet care', 'url' => 'https://www.avma.org/resources-tools/pet-owners/petcare'],
                ],
            ],
            [
                'slug' => 'cepillado-y-deslanado',
                'titulo' => 'Cepillado y deslanado por tipo de pelo',
                'resumen' => 'Herramientas correctas, orden de trabajo y por qué rasurar un manto doble no es la solución al pelo muerto.',
                'categoria' => 'bano',
                'nivel' => 'Intermedio',
                'minutos' => 8,
                'icono' => 'cepillo',
                'destacada' => true,
                'actualizado' => '2026-10-27',
                'secciones' => [
                    ['titulo' => 'Identifica el manto', 'md' => <<<'MD'
No todos los pelos se trabajan igual:

| Manto | Ejemplos | Herramienta base | Cuidado |
|---|---|---|---|
| Corto liso | Chihuahua, Beagle | Cepillo de goma o guante | Poco mantenimiento |
| Medio | Golden retriever joven, mestizos | Cepillo slicker + peine | Nudos detrás de orejas |
| Largo | Shih tzu, Maltés, Yorkie | Slicker, peine de acero, acondicionador | Nudos y ojos |
| Rizado | Poodle, Bichón | Cepillo slicker + carda | Nudos apretados |
| Doble capa | Husky, Akita, Pastor alemán | Rastrillo de deslanado + slicker | Nunca rasurar completo |

Si no sabes qué manto tiene, mira la raíz: si hay dos capas claras (subpelo suave y pelo externo), es doble.
MD],
                    ['titulo' => 'Orden de trabajo', 'md' => <<<'MD'
1. **Desenreda en seco** con los dedos y un acondicionador en spray. No tires del nudo: separa en capas.
2. **Cepilla a favor del pelo** con movimientos largos y suaves.
3. **Trabaja por zonas:** patas, axilas, abdomen, detrás de orejas, cuello y cola.
4. **Deslanado:** con rastrillo, pasadas cortas en la dirección del pelo, sin presionar. No lo uses en piel irritada.
5. **Peina** al final para verificar que no quedan nudos.
6. **Revisa** la piel: enrojecimiento, pulgas, costras.

En perros de doble capa, el deslanado se hace cuando el pelo muerto está suelto (muda). Fuera de temporada, un cepillado regular es suficiente.
MD],
                    ['titulo' => 'Nudos: la regla de la paciencia', 'md' => <<<'MD'
- Un nudo pequeño se deshace con los dedos y un peine de púas.
- Un nudo compacto contra la piel no se deshace sin dolor: se rasura con cuidado o se deriva.
- Nunca uses tijeras de punta recta cerca de la piel para "abrir" un nudo: es la causa más común de cortes.
- No mojes un nudo severo: el agua lo aprieta.

Explica al tutor que el deslanado toma tiempo y que el resultado no es inmediato. Cobra por tiempo y complejidad, no solo por "baño".
MD],
                    ['titulo' => 'El mito de rasurar para que no suelte pelo', 'md' => <<<'MD'
Rasurar no detiene la muda. El pelo crece igual y, al cortarlo, puede volver más áspero o crecer en parches. En mantos dobles, rasurar puede provocar:

- Alopecia y crecimiento irregular.
- Pérdida de aislamiento térmico.
- Quemaduras solares en piel clara.
- Irritación y foliculitis.

La solución es **deslanado + cepillado frecuente**, no la máquina. Si el tutor insiste, documenta la recomendación y ofrece un plan de mantenimiento.
MD],
                ],
                'faq' => [
                    ['p' => '¿Cada cuánto cepillo a un perro de pelo largo?', 'r' => 'De 2 a 3 veces por semana en mantenimiento y a diario si tiene nudos. El baño no sustituye el cepillado.'],
                    ['p' => '¿El deslanado duele?', 'r' => 'Bien hecho, no. Si el rastrillo se clava, presiona o pasa sobre piel irritada, sí. Usa pasadas suaves y revisa la piel.'],
                    ['p' => '¿Puedo rasurar a un perro de doble capa en verano?', 'r' => 'No es recomendable. El manto aísla del calor y protege del sol. Consulta al veterinario si hay una razón médica.'],
                ],
                'fuentes' => [
                    ['titulo' => 'ASPCA · Dog grooming tips', 'url' => 'https://www.aspca.org/pet-care/dog-care/dog-grooming-tips'],
                    ['titulo' => 'AAHA · Behavior management guidelines', 'url' => 'https://www.aaha.org/resources/2015-aaha-canine-and-feline-behavior-management-guidelines/'],
                ],
            ],
            [
                'slug' => 'manejo-de-perros-nerviosos',
                'titulo' => 'Manejo de perros nerviosos, miedosos o reactivos',
                'resumen' => 'Lee el lenguaje corporal, baja el estrés de la mesa y aprende cuándo el mejor servicio es derivar.',
                'categoria' => 'manejo',
                'nivel' => 'Intermedio',
                'minutos' => 9,
                'icono' => 'corazon',
                'destacada' => true,
                'actualizado' => '2026-10-27',
                'secciones' => [
                    ['titulo' => 'El lenguaje corporal que sí importa', 'md' => <<<'MD'
Un perro estresado rara vez muerde sin avisar. Las señales son pequeñas y rápidas:

- Bosteza, se lame la nariz o traga sin comida.
- Orejas hacia atrás, cola baja o rígida.
- Jadea sin calor, tiembla o se queda "congelado".
- Se lame los labios, se rasca o se sacude en seco.
- Evita la mirada, gira la cabeza, se esconde.
- Gruñe, enseña los dientes, se eriza o hace *snap* al aire.

> El gruñido es información, no desafío. Si lo castigas, el perro aprende a morder sin avisar.

Si el perro muestra varias señales al mismo tiempo, para. No hay corte que valga una mordida.
MD],
                    ['titulo' => 'Manejo de bajo estrés en la mesa', 'md' => <<<'MD'
- Cita en un horario tranquilo y con menos perros en la sala.
- Deja que el perro explore la mesa y el equipo antes de sujetarlo.
- Usa toalla o cabestrillo en lugar de lazo al cuello. Nunca lo cuelgues.
- Feromonas, música suave y voz baja ayudan; no sustituyen el manejo.
- Trabaja en sesiones cortas y premia con comida de alto valor si el tutor lo permite.
- Si el perro no tolera la pata, trabaja otra zona y vuelve después.
- Pide al tutor que se retire si su presencia altera al perro.

No uses "alpha roll", golpes, sacudidas ni anestesia. La sedación es una decisión veterinaria, no del estilista.
MD],
                    ['titulo' => 'Bozal: seguridad, no castigo', 'md' => <<<'MD'
Usa bozal si el perro tiene historial de mordidas, dolor o reactividad intensa. Debe permitir jadear y beber; un bozal demasiado ajustado es peligroso.

Ideal: el perro llega entrenado para usarlo. Si no, colócalo con calma, sin forcejeo, y trabaja solo lo esencial. No lo dejes puesto más tiempo del necesario.

Un perro con bozal sigue necesitando manejo suave: el bozal evita una mordida, no el estrés.
MD],
                    ['titulo' => 'Cuándo decir que no', 'md' => <<<'MD'
Deriva al veterinario o a un especialista en conducta si:

- Hay dolor al tacto, cojera o herida.
- El perro intenta morder de forma repetida y no se deja manejar ni con bozal.
- Tiene problemas respiratorios, convulsiones o se desmaya.
- El tutor pide sedación o "anestesia para bañarlo".
- El perro es geriátrico, cardiópata o braquicéfalo y se estresa mucho.

Documenta lo que pasó, con hora y testigos. Un servicio negado a tiempo es mejor que una urgencia.
MD],
                ],
                'faq' => [
                    ['p' => '¿Puedo bañar a un perro que gruñe?', 'r' => 'Solo si puedes hacerlo con seguridad y sin forzar. Si el gruñido escala o el perro intenta morder, detente y deriva.'],
                    ['p' => '¿El bozal es maltrato?', 'r' => 'No, si está bien ajustado y se usa el tiempo necesario. Es una herramienta de seguridad. El maltrato es sujetar, golpear o forzar.'],
                    ['p' => '¿Sirve darle un sedante?', 'r' => 'Solo un veterinario puede prescribirlo. La sedación mal indicada puede complicar la respiración y la termorregulación.'],
                ],
                'fuentes' => [
                    ['titulo' => 'Fear Free Pets', 'url' => 'https://fearfreepets.com/'],
                    ['titulo' => 'AVMA · Pet care', 'url' => 'https://www.avma.org/resources-tools/pet-owners/petcare'],
                ],
            ],
            [
                'slug' => 'secado-seguro',
                'titulo' => 'Secado seguro: temperatura, distancia y golpe de calor',
                'resumen' => 'Cómo secar sin quemar la piel ni cocinar al perro por dentro, y qué hacer ante los primeros signos de golpe de calor.',
                'categoria' => 'salud',
                'nivel' => 'Básico',
                'minutos' => 8,
                'icono' => 'secadora',
                'destacada' => true,
                'actualizado' => '2026-10-27',
                'secciones' => [
                    ['titulo' => 'El secado es la parte de mayor riesgo', 'md' => <<<'MD'
El agua y el calor pueden elevar la temperatura del perro más rápido de lo que parece, sobre todo en razas chatas, geriátricos, cachorros y perros de doble capa. Un secado mal hecho causa:

- Quemaduras de piel y estrés térmico.
- Golpe de calor, que puede ser mortal.
- Hongos y olores por humedad atrapada.
- Miedo al secador para toda la vida.

La regla es simple: **temperatura tibia, boquilla en movimiento, distancia de 20 a 30 cm y pausas**.
MD],
                    ['titulo' => 'Técnica segura', 'md' => <<<'MD'
1. Retira el exceso de agua con toalla de microfibra. Cuanto menos agua, menos tiempo de secado.
2. Prueba el aire en tu muñeca: debe sentirse tibio, nunca caliente.
3. Mantén la boquilla en movimiento constante; no la dejes fija en una zona.
4. Empieza por el cuerpo y las patas; cara y orejas al final, con aire tibio y poca presión.
5. Protege ojos y oídos; nunca apuntes al conducto auditivo.
6. Revisa axilas, ingle, entre dedos y detrás de orejas al final.
7. Si el perro jadea, se echa o intenta escapar, apaga y deja que se recupere.

En doble capa, seca primero la capa interna. No uses secadora de jaula con el perro mojado y sin supervisión.
MD],
                    ['titulo' => 'Golpe de calor: señales y acción', 'md' => <<<'MD'
Señales:

- Jadeo intenso y ruidoso, salivación espesa.
- Encías rojas u oscuras, lengua morada.
- Tambaleo, debilidad, vómito o diarrea.
- Convulsiones, pérdida de conciencia.
- Temperatura rectal mayor a 40 °C (105 °F).

Qué hacer:

1. Llama al veterinario de inmediato.
2. Lleva al perro a un lugar fresco y sombreado.
3. Moja el cuerpo con agua **templada**, no helada, y usa ventilación suave.
4. Ofrece agua para beber si está consciente, sin forzar.
5. No uses hielo, no lo cubras con toallas mojadas frías y no lo dejes solo.
6. Transporta al veterinario mientras sigues enfriando.

> El golpe de calor es una emergencia. No esperes a que "se le pase".
MD],
                    ['titulo' => 'Equipo y mantenimiento', 'md' => <<<'MD'
- Limpia el filtro de la secadora cada semana; un filtro sucio recalienta el aire.
- Revisa el cable y la boquilla antes de cada uso.
- No compartas boquillas entre perros con problemas de piel sin desinfectar.
- Calibra el termostato si tu equipo lo permite; no confíes solo en la perilla.
- Deja enfriar el equipo entre perros.
MD],
                ],
                'faq' => [
                    ['p' => '¿Puedo usar secadora de jaula?', 'r' => 'Solo con supervisión directa, temperatura controlada y perros que toleran el encierro. Nunca con doble capa mojada, braquicéfalos o perros que jadean.'],
                    ['p' => '¿Cuánto debe durar el secado?', 'r' => 'Depende del tamaño y manto. Usa la calculadora de secado como guía y añade pausas. Si pasa de 45 minutos, divide la sesión.'],
                    ['p' => '¿Qué temperatura es segura?', 'r' => 'El aire debe sentirse tibio en tu muñeca, no caliente. La piel del perro no debe superar los 40 °C. Ante la duda, baja la temperatura.'],
                ],
                'fuentes' => [
                    ['titulo' => 'AVMA · Heatstroke in dogs', 'url' => 'https://www.avma.org/resources-tools/pet-owners/petcare/heatstroke-dogs'],
                    ['titulo' => 'ASPCA · Hot weather safety tips', 'url' => 'https://www.aspca.org/pet-care/general-pet-care/hot-weather-safety-tips'],
                ],
            ],
            [
                'slug' => 'primeros-auxilios-en-la-estetica',
                'titulo' => 'Primeros auxilios en la estética canina',
                'resumen' => 'Botiquín, cortes, uñas, ojos, quemaduras y reacciones alérgicas: qué hacer en los primeros cinco minutos.',
                'categoria' => 'salud',
                'nivel' => 'Intermedio',
                'minutos' => 9,
                'icono' => 'escudo',
                'destacada' => true,
                'actualizado' => '2026-10-27',
                'secciones' => [
                    ['titulo' => 'Botiquín mínimo del salón', 'md' => <<<'MD'
- Gasas estériles, vendas y cinta médica.
- Solución fisiológica y limpiador de heridas indicado por el veterinario.
- Polvo hemostático para uñas.
- Guantes de nitrilo, tijeras de punta roma y pinzas.
- Manta térmica y base antideslizante.
- Termómetro digital y lubricante.
- Bozal de tamaño adecuado.
- Teléfono del veterinario y del tutor, y hoja de consentimiento.

Revisa caducidades cada mes. Un botiquín sin gasa no sirve.
MD],
                    ['titulo' => 'Cortes y uñas', 'md' => <<<'MD'
**Corte de piel:** presión directa con gasa 3 a 5 minutos. Si es profundo, no retires el objeto, cubre y traslada al veterinario.

**Uña:** presión y polvo hemostático. Si no para en 10 minutos, veterinario. No uses harina, café ni ceniza.

**Mordida o rasguño del personal:** lava con agua y jabón 15 minutos, cubre y acude a valoración médica. Reporta el incidente.
MD],
                    ['titulo' => 'Ojos, oídos y quemaduras', 'md' => <<<'MD'
**Shampoo en los ojos:** enjuaga con solución fisiológica o agua limpia de 10 a 15 minutos. No uses gotas humanas. Si hay enrojecimiento, dolor o el perro se talla, veterinario.

**Quemadura:** agua templada 10 a 15 minutos, cubre con gasa limpia. No uses hielo, pasta, aceite ni crema. Veterinario.

**Producto en la boca:** enjuaga la boca, no induzcas el vómito y llama al veterinario o a toxicología.
MD],
                    ['titulo' => 'Reacciones alérgicas y emergencias', 'md' => <<<'MD'
Signos de anafilaxia: urticaria, hinchazón de cara u hocico, vómito, dificultad para respirar, debilidad. Es una urgencia: traslada de inmediato.

Golpe de calor: sigue la guía de [secado seguro](/guias/secado-seguro). No esperes.

Registra siempre: hora, qué pasó, producto implicado, quién atendió y qué se hizo. Avisa al tutor con claridad y sin culpar. La documentación protege al perro y al negocio.
MD],
                ],
                'faq' => [
                    ['p' => '¿Puedo darle un analgésico humano?', 'r' => 'No. Ibuprofeno, paracetamol y aspirina pueden intoxicar o matar a un perro. Solo un veterinario indica medicamentos.'],
                    ['p' => '¿Debo firmar un consentimiento?', 'r' => 'Sí. Un consentimiento informado con datos del perro, alergias, autorización de atención y contacto de emergencia reduce riesgos y protege a ambos.'],
                    ['p' => '¿Qué hago si el perro muerde a un empleado?', 'r' => 'Lava la herida, busca atención médica y documenta el incidente. Revisa el protocolo de manejo y la necesidad de bozal en futuras visitas.'],
                ],
                'fuentes' => [
                    ['titulo' => 'ASPCA Animal Poison Control', 'url' => 'https://www.aspca.org/pet-care/animal-poison-control'],
                    ['titulo' => 'AVMA · Pet care', 'url' => 'https://www.avma.org/resources-tools/pet-owners/petcare'],
                ],
            ],
            [
                'slug' => 'como-cotizar-servicios',
                'titulo' => 'Cómo cotizar un servicio sin regalar tu trabajo',
                'resumen' => 'Costo fijo, insumos, mano de obra, comisión y margen: la cuenta que convierte un baño en un negocio rentable.',
                'categoria' => 'negocio',
                'nivel' => 'Básico',
                'minutos' => 8,
                'icono' => 'dinero',
                'destacada' => true,
                'actualizado' => '2026-10-27',
                'secciones' => [
                    ['titulo' => 'La cuenta que casi nadie hace', 'md' => <<<'MD'
Un precio no se inventa comparando con la estética de la esquina. Se construye:

1. **Costos fijos mensuales:** renta, luz, agua, internet, sueldos, software, publicidad, mantenimiento. Súmalos.
2. **Servicios al mes:** sé realista. Si atiendes 12 perros por semana, son ~48 al mes, no 200.
3. **Costo fijo por servicio:** fijos ÷ servicios.
4. **Insumos por servicio:** shampoo diluido, acondicionador, toallas, aromas, bolsas, electricidad.
5. **Mano de obra:** tu hora de trabajo. No es "lo que sobra".
6. **Comisión:** si otra persona atiende, inclúyela.
7. **Margen:** lo que necesita el negocio para crecer y reponer equipo.

La fórmula: **precio = costo directo ÷ (1 − margen − comisión)**. Después suma IVA si aplica.
MD],
                    ['titulo' => 'Ejemplo con números', 'md' => <<<'MD'
- Costos fijos: $15,000 al mes.
- Servicios: 120 al mes.
- Costo fijo por servicio: $125.
- Insumos: $45.
- Mano de obra: 1.5 h × $120 = $180.
- Costo directo: $350.
- Margen 35 %, comisión 0 %: $350 ÷ 0.65 = **$538**.
- Con IVA 16 %: **$624**.

Si cobras $350, trabajas gratis y el negocio no repone equipo. Si cobras $450, el margen es 22 %, no 35 %.

Usa la [calculadora de precio](/calculadoras/precio) con tus propios números.
MD],
                    ['titulo' => 'Cómo estructurar la lista de precios', 'md' => <<<'MD'
- **Por tamaño y tiempo**, no solo por raza. Un oso de pelo largo y un labrador no cuestan lo mismo.
- **Nudos y deslanado se cobran aparte** por tiempo y complejidad. Avísalo antes.
- **Paquetes:** baño + corte + uñas con descuento real, no con margen negativo.
- **Membresías:** 4 baños al mes con descuento y pago anticipado. Mejora flujo de efectivo.
- **Anticipo:** 20 a 50 % en servicios largos o razas difíciles.
- **Política de cancelación:** 24 horas. Escríbela y hazla firmar.
- **Precios exhibidos:** el cliente debe saber qué incluye y qué no.

> El cliente que solo busca precio no es tu cliente. El que valora el trato, la seguridad y el resultado, sí.
MD],
                    ['titulo' => 'Subir precios sin perder clientes', 'md' => <<<'MD'
1. Revisa costos cada 3 meses.
2. Avisa con 30 días de anticipación por escrito y en el local.
3. Explica el valor: productos, tiempo, seguridad, instalaciones.
4. Conserva precios de lealtad o paquetes para clientes frecuentes.
5. No pidas perdón por cobrar. La transparencia genera confianza.
6. Si un servicio no es rentable, súbelo o elimínalo.
MD],
                ],
                'faq' => [
                    ['p' => '¿Debo cobrar IVA?', 'r' => 'Depende de tu régimen fiscal y de si estás obligado. Consulta a tu contador. El precio al público debe incluir impuestos y mostrarse con claridad.'],
                    ['p' => '¿Cuánto cobro por un nudo?', 'r' => 'Por tiempo. Calcula los minutos extra, el producto y el riesgo. Un nudo severo puede costar más que el corte completo.'],
                    ['p' => '¿Cómo compito con precios bajos?', 'r' => 'No compitas por precio. Compite por seguridad, puntualidad, trato, resultados y conveniencia. Bajar el precio sin bajar costos es una salida rápida del negocio.'],
                ],
                'fuentes' => [
                    ['titulo' => 'PROFECO · Derechos del consumidor', 'url' => 'https://www.profeco.gob.mx/'],
                    ['titulo' => 'Calculadora de precio de Mikato', 'url' => '/calculadoras/precio'],
                ],
            ],
            [
                'slug' => 'abrir-estetica-en-mexico',
                'titulo' => 'Abrir una estética canina en México: lo que sí debes verificar',
                'resumen' => 'Licencias, protección civil, datos personales, residuos y bienestar animal. Sin inventar trámites ni prometer que una NOM no existe.',
                'categoria' => 'negocio',
                'nivel' => 'Intermedio',
                'minutos' => 10,
                'icono' => 'directorio',
                'destacada' => false,
                'actualizado' => '2026-10-27',
                'secciones' => [
                    ['titulo' => 'Primero: no existe una NOM federal exclusiva de estética canina', 'md' => <<<'MD'
Hasta la fecha de esta guía **no existe una Norma Oficial Mexicana (NOM) federal que regule específicamente los servicios de estética canina**. Desconfía de quien te venda un "certificado NOM" para peluquería canina.

Lo que sí aplica:

- **Regulación municipal y estatal** de giros comerciales, uso de suelo, protección civil, anuncios y residuos.
- **Ley Federal de Protección al Consumidor (LFPC)** para precios, garantías, publicidad y quejas.
- **Ley Federal de Protección de Datos Personales en Posesión de los Particulares (LFPDPPP)** para los datos de tus clientes.
- **Ley Federal de Sanidad Animal** y leyes estatales de protección animal para trato digno y bienestar.
- **Reglas fiscales del SAT** según tu régimen.

Verifica cada punto en tu municipio y con un contador o abogado. Esta guía es orientativa, no una licencia para operar.
MD],
                    ['titulo' => 'Trámites que suelen pedirte', 'md' => <<<'MD'
| Trámite | Dónde | Nota |
|---|---|---|
| Licencia de funcionamiento | Municipio / alcaldía | Varía el nombre y requisitos |
| Uso de suelo | Municipio | Confirma que el giro esté permitido |
| Aviso de funcionamiento | Según giro y entidad | No todos los giros lo requieren |
| Protección civil | Municipio / estado | Extintores, señalización, rutas |
| Anuncio o rótulo | Municipio | Medidas y diseño |
| Manejo de residuos | Municipio / estado | Pelo, químicos, sanitarios |
| RFC y régimen fiscal | SAT | Facturación y obligaciones |
| Aviso de privacidad | Tu negocio | Datos de clientes y mascotas |

No copies la lista de otro estado: los requisitos cambian por municipio. Pregunta directamente en la ventanilla o en la página oficial de tu alcaldía.
MD],
                    ['titulo' => 'Datos personales: el aviso de privacidad no es opcional', 'md' => <<<'MD'
Recabas nombre, teléfono, correo, dirección, datos de la mascota y a veces datos de salud. Eso es tratamiento de datos personales.

Buenas prácticas:

- Aviso de privacidad visible y accesible, con finalidades y derechos ARCO.
- Consentimiento para foto/video del antes y después, y para marketing.
- No publiques fotos de clientes sin autorización.
- Guarda los datos en un lugar seguro y limita el acceso del personal.
- Define un responsable de datos y un correo de contacto.

Consulta la [LFPDPPP](https://www.diputados.gob.mx/LeyesBiblio/pdf/LFPDPPP.pdf) (nueva ley, DOF 20-03-2025). La autoridad de datos personales ya no es el INAI: es la [Secretaría Anticorrupción y Buen Gobierno](https://www.gob.mx/buengobierno).
MD],
                    ['titulo' => 'Bienestar animal y responsabilidad', 'md' => <<<'MD'
- Trato digno, sin golpes, sin "alpha roll" y sin anestesia para estética.
- Agua limpia, ventilación y descansos.
- No aceptes perros enfermos, con dolor o sin vacunas cuando haya riesgo para otros.
- Ten un protocolo de emergencia y un veterinario de referencia.
- Contrata seguro de responsabilidad civil si está disponible en tu zona.
- Documenta incidentes y conserva consentimientos.

La **NOM-033-SAG/ZOO-2014** regula métodos de sacrificio humanitario, no la estética. No la uses como si fuera tu norma de operación.
MD],
                    ['titulo' => 'Productos y proveedores', 'md' => <<<'MD'
- Compra a proveedores que te den ficha técnica, registro y hoja de seguridad.
- No reenvases productos en envases de alimentos o limpieza.
- Respeta diluciones y caducidades.
- Para productos de uso veterinario, pregunta al proveedor y al veterinario; no automediques.
- Guarda las fichas técnicas y facturas.

Si tu proveedor no puede explicarte qué contiene el producto, busca otro.
MD],
                ],
                'faq' => [
                    ['p' => '¿Necesito cédula o certificación para ser estilista canino?', 'r' => 'No existe una cédula profesional federal obligatoria para estética canina. Hay cursos y certificaciones privadas; su valor depende del programa y del prestigio del instructor.'],
                    ['p' => '¿La estética canina es un servicio de salud?', 'r' => 'No. Es un servicio de bienestar e higiene. Los procedimientos médicos, sedación y diagnóstico corresponden a un médico veterinario.'],
                    ['p' => '¿Qué pasa si un perro se lastima?', 'r' => 'Atiende la urgencia, avisa al tutor y documenta. Ten un protocolo y un seguro. La transparencia reduce el conflicto.'],
                ],
                'fuentes' => [
                    ['titulo' => 'LFPDPPP · Cámara de Diputados', 'url' => 'https://www.diputados.gob.mx/LeyesBiblio/pdf/LFPDPPP.pdf'],
                    ['titulo' => 'PROFECO', 'url' => 'https://www.profeco.gob.mx/'],
                    ['titulo' => 'Secretaría Anticorrupción y Buen Gobierno', 'url' => 'https://www.gob.mx/buengobierno'],
                    ['titulo' => 'DOF · NOM-033-SAG/ZOO-2014', 'url' => 'https://www.dof.gob.mx/nota_detalle.php?codigo=5354889&fecha=04/08/2014'],
                ],
            ],
            [
                'slug' => 'higiene-del-equipo-entre-perros',
                'titulo' => 'Higiene del equipo entre perros: el protocolo que evita contagios',
                'resumen' => 'Mesa, tina, tijeras, máquina, toallas y secadora: qué limpiar, con qué y en qué orden entre un perro y otro.',
                'categoria' => 'salud',
                'nivel' => 'Básico',
                'minutos' => 7,
                'icono' => 'escudo',
                'destacada' => false,
                'actualizado' => '2026-10-27',
                'secciones' => [
                    ['titulo' => 'Por qué importa', 'md' => <<<'MD'
En una estética pasan perros de distintos hogares, edades y estados de vacunación. Sin un protocolo, el equipo se convierte en un puente para hongos, bacterias, pulgas y virus.

La limpieza tiene dos pasos distintos:

- **Limpiar:** quitar pelo, grasa y materia orgánica con agua y detergente.
- **Desinfectar:** aplicar un producto con tiempo de contacto suficiente para matar microorganismos.

Si solo limpias, no desinfectas. Si desinfectas sobre pelo y grasa, el producto no funciona.
MD],
                    ['titulo' => 'Protocolo entre perros', 'md' => <<<'MD'
1. Retira el pelo de mesa, tina y piso con escoba y aspiradora.
2. Lava la superficie con agua y detergente.
3. Aplica desinfectante (amonio cuaternario, peróxido de hidrógeno acelerado o el indicado por el fabricante) y respeta el **tiempo de contacto**.
4. Seca la superficie. La humedad residual también contamina.
5. Cambia toallas y fundas por cada perro.
6. Limpia y desinfecta tijeras, peines y máquinas.
7. Deja ventilar el área.

Para parvovirus en superficies duras, el hipoclorito de sodio diluido (aproximadamente 1:32) requiere 10 minutos de contacto. Es corrosivo: úsalo con guantes y ventilación, y solo donde esté indicado.
MD],
                    ['titulo' => 'Herramientas y máquinas', 'md' => <<<'MD'
- **Tijeras:** limpia con paño y desinfectante; aceita el tornillo. No las sumerjas.
- **Máquina:** cepilla la hoja, usa spray limpiador de hojas, retira el pelo y aceita. Guarda sin pelo.
- **Peines y cepillos:** retira el pelo, lava con agua y jabón y desinfecta.
- **Secadora:** limpia filtros y boquillas; no compartas boquillas con perros con hongos.
- **Tinas:** enjuaga y desinfecta después de cada perro.

Ten dos juegos de tijeras y hojas para rotar mientras se limpian.
MD],
                    ['titulo' => 'Parásitos y perros enfermos', 'md' => <<<'MD'
- Si ves pulgas o garrapatas, aísla al perro y trata el área. No lo devuelvas al área común.
- Si hay hongos, secreción o heridas, usa equipo exclusivo y desinfecta al final.
- No aceptes perros con enfermedades contagiosas activas sin indicación veterinaria.
- Lava toallas a temperatura alta y sécalas completamente.
- Lávate las manos entre perros y después de retirar guantes.

Documenta la limpieza en una hoja diaria. Un protocolo escrito se puede entrenar y auditar.
MD],
                ],
                'faq' => [
                    ['p' => '¿Cada cuánto desinfecto la tina?', 'r' => 'Entre cada perro, como mínimo. Al final del día, limpieza profunda de tina, piso y desagüe.'],
                    ['p' => '¿La secadora de jaula se desinfecta?', 'r' => 'Sí, la rejilla y la superficie interior. Cambia o limpia la funda entre perros y revisa el filtro.'],
                    ['p' => '¿Puedo usar cloro en todo?', 'r' => 'No. El cloro es corrosivo para metales y tejidos, y pierde efecto con materia orgánica. Úsalo solo donde esté indicado y con la dilución correcta.'],
                ],
                'fuentes' => [
                    ['titulo' => 'ASPCA · Pet care', 'url' => 'https://www.aspca.org/pet-care'],
                    ['titulo' => 'AAHA · Infection control guidelines', 'url' => 'https://www.aaha.org/resources/2018-aaha-infection-control-prevention-and-biosecurity-guidelines/'],
                ],
            ],
            [
                'slug' => 'razas-populares-en-mexico',
                'titulo' => 'Razas populares en México y su mantenimiento',
                'resumen' => 'Chihuahua, Xoloitzcuintle, Poodle, Schnauzer, Shih Tzu, Husky y criollo: manto, corte típico y cuidados reales.',
                'categoria' => 'bano',
                'nivel' => 'Intermedio',
                'minutos' => 9,
                'icono' => 'perro',
                'destacada' => false,
                'actualizado' => '2026-10-27',
                'secciones' => [
                    ['titulo' => 'Tabla rápida por raza', 'md' => <<<'MD'
| Raza | Manto | Frecuencia típica | Cuidado clave |
|---|---|---|---|
| Chihuahua | Corto o largo | 3 a 4 semanas | Frío, dientes, ojos |
| Xoloitzcuintle | Sin pelo o pelo corto | 3 a 4 semanas | Piel, sol, humectación |
| Poodle | Rizado | 3 a 5 semanas | Nudos, oídos, corte de cara |
| Schnauzer | Duro | 4 a 6 semanas | Barba, cejas, patas |
| Shih Tzu | Largo | 2 a 4 semanas | Nudos, ojos, pliegues |
| Maltés | Largo fino | 2 a 4 semanas | Nudos, lágrimas |
| Yorkshire | Largo fino | 3 a 5 semanas | Nudos, orejas |
| Golden retriever | Doble capa | 3 a 4 semanas | Deslanado, oídos |
| Husky / Akita | Doble capa | 3 a 5 semanas | Nunca rasurar completo |
| Bulldog francés | Corto | 3 a 4 semanas | Pliegues, calor, respiración |
| Criollo / mestizo | Variable | Cada 3 a 6 semanas | Evaluar manto y piel |

La frecuencia es orientativa. El estilo de vida, la edad y la salud cambian todo.
MD],
                    ['titulo' => 'Xoloitzcuintle: piel, no pelo', 'md' => <<<'MD'
El Xolo puede ser sin pelo o con pelo corto. La piel es su órgano más expuesto:

- Baña con shampoo suave y agua tibia; no talles fuerte.
- Hidrata con producto indicado para piel canina.
- Protege del sol con protector pet-safe; evita las horas de mayor radiación.
- Revisa granitos, resequedad y quemaduras.
- En clima frío, abriga.
- No uses productos de humano perfumados.

Es una raza mexicana emblemática: trátala con orgullo y sin mitos.
MD],
                    ['titulo' => 'Poodle, Schnauzer y Shih Tzu', 'md' => <<<'MD'
- **Poodle:** cepilla a diario si está largo; nudos en axilas y patas. Corte de cara y patas con cuidado de ojos.
- **Schnauzer:** el manto duro se trabaja con stripping o máquina según el estándar del cliente. Barba y cejas se limpian y peinan.
- **Shih Tzu:** nudos detrás de orejas y en la cola. Ojos grandes: limpia lágrimas y mantén el pelo fuera de los ojos.

En razas de pelo largo, el baño sin cepillado previo aprieta los nudos. Cepilla antes de mojar.
MD],
                    ['titulo' => 'Doble capa y criollos', 'md' => <<<'MD'
Husky, Akita, Pastor alemán y Golden tienen subpelo. El trabajo es **deslanado**, no rasurado. En época de muda, cepilla 3 a 4 veces por semana.

Los criollos y mestizos son la mayoría de los perros en México. No los trates "como cualquier perro": evalúa manto, piel, edad y tolerancia. Muchos tienen doble capa o pelo corto con subpelo.

> Ninguna raza justifica forcejeo. Si el perro no tolera el procedimiento, ajusta el plan o deriva.
MD],
                ],
                'faq' => [
                    ['p' => '¿El Xolo necesita baño frecuente?', 'r' => 'Cada 3 a 4 semanas con shampoo suave. Más seguido puede resecar la piel. Hidrata y protege del sol.'],
                    ['p' => '¿Rasuro a un Husky en verano?', 'r' => 'No. El manto doble aísla del calor y protege del sol. Deslanar y cepillar es la mejor opción.'],
                    ['p' => '¿Cómo cobro un criollo?', 'r' => 'Por tamaño, manto, tiempo y complejidad, igual que una raza. No hay tarifa única.'],
                ],
                'fuentes' => [
                    ['titulo' => 'ASPCA · Dog grooming tips', 'url' => 'https://www.aspca.org/pet-care/dog-care/dog-grooming-tips'],
                    ['titulo' => 'AVMA · Pet care', 'url' => 'https://www.avma.org/resources-tools/pet-owners/petcare'],
                ],
            ],
        ];
    }

    /** @return array<string, mixed>|null */
    public static function porSlug(string $slug): ?array
    {
        foreach (self::catalogo() as $guia) {
            if ($guia['slug'] === $slug) {
                return $guia;
            }
        }
        return null;
    }

    /** @return list<array<string, mixed>> */
    public static function destacadas(int $limite = 4): array
    {
        $salida = [];
        foreach (self::catalogo() as $guia) {
            if ($guia['destacada']) {
                $salida[] = $guia;
            }
            if (count($salida) >= $limite) {
                break;
            }
        }
        return $salida;
    }

    /** @return list<array<string, mixed>> */
    public static function porCategoria(string $categoria): array
    {
        if ($categoria === '') {
            return self::catalogo();
        }
        return array_values(array_filter(
            self::catalogo(),
            static fn (array $guia): bool => $guia['categoria'] === $categoria
        ));
    }

    /** @return array<string, string> */
    public static function categorias(): array
    {
        return [
            'bano' => 'Baño y manto',
            'corte' => 'Corte y uñas',
            'salud' => 'Salud y seguridad',
            'manejo' => 'Manejo y conducta',
            'negocio' => 'Negocio y precios',
        ];
    }

    /** @return array<string, string> */
    public static function nivelClase(): array
    {
        return ['Básico' => 'basico', 'Intermedio' => 'intermedio', 'Avanzado' => 'avanzado'];
    }

    public static function total(): int
    {
        return count(self::catalogo());
    }
}
