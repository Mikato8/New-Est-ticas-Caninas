# Contribuir

Gracias por querer mejorar Mikato Estilistas Caninos.

## Antes de abrir un cambio

- Revisa que el cambio tenga que ver con el oficio, la comunidad o la gestión del salón.
- Para cambios grandes, abre un issue con el problema y la propuesta.
- No incluyas datos personales de clientes, contraseñas, `.env` ni respaldos.

## Estilo

- PHP 8.3, `declare(strict_types=1);`, nombres en español para dominio y vistas.
- Sin Composer ni frameworks. Si necesitas una dependencia, justifícala.
- Respeta la separación `Controller → Repository/Domain → View`.
- Escapa siempre con `e()` en las vistas.
- Los POST llevan CSRF y validación de servidor.
- El contenido editorial va en `app/Domain/Contenido/` y las fuentes se citan.

## Pruebas

```bash
php tests/run.php
php -l app/Controllers/MiControlador.php
```

Si cambias una calculadora, actualiza sus pruebas y el corpus:

```bash
php bin/build-ia.php
```

## Contenido

- Español de México, claro y sin alarmismo.
- No prometas curas, anestesia para estética ni rasurados de doble capa.
- Cita fuentes veterinarias u oficiales cuando des un dato de salud o legal.
- Si no hay una cifra oficial, dilo.

## Licencia

Al contribuir aceptas que tu aportación se publique bajo la licencia MIT del proyecto (código) y CC BY-SA 4.0 (contenido editorial).
