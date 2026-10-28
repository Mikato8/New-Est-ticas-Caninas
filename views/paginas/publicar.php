<?php
declare(strict_types=1);
use App\Core\Auth;
Auth::requiereLogin();
$errores = errores();
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span><a href="<?= url('/comunidad') ?>">Comunidad</a><span>/</span>Publicar</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Comparte tu oficio</span>
        <h1>Publica un caso, una técnica o una duda.</h1>
        <p>Revisamos cada publicación antes de que aparezca. No incluyas datos personales de clientes ni fotos sin permiso.</p>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="grid grid--2" style="align-items:start;">
            <form class="tarjeta tarjeta__cuerpo formulario" method="post" action="<?= url('/comunidad') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div style="position:absolute;left:-9999px;" aria-hidden="true"><label for="sitio_web">No llenar</label><input id="sitio_web" type="text" name="sitio_web" tabindex="-1" autocomplete="off"></div>
                <div class="campo<?= isset($errores['titulo']) ? ' campo--error' : '' ?>">
                    <label for="titulo">Título *</label>
                    <input id="titulo" name="titulo" type="text" required minlength="8" maxlength="140" value="<?= e(viejo('titulo')) ?>" placeholder="Ej. Cómo desenredé un manto doble sin rasurar">
                    <?php if (isset($errores['titulo'])): ?><span class="error"><?= e($errores['titulo']) ?></span><?php endif; ?>
                </div>
                <div class="campo">
                    <label for="categoria">Categoría *</label>
                    <select id="categoria" name="categoria" required>
                        <?php foreach ($categorias as $clave => $etiqueta): ?>
                            <option value="<?= e($clave) ?>" <?= viejo('categoria') === $clave ? 'selected' : '' ?>><?= e($etiqueta) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="campo">
                    <label for="resumen">Resumen corto</label>
                    <input id="resumen" name="resumen" type="text" maxlength="280" value="<?= e(viejo('resumen')) ?>" placeholder="Una línea que invite a leer.">
                </div>
                <div class="campo<?= isset($errores['contenido']) ? ' campo--error' : '' ?>">
                    <label for="contenido">Contenido *</label>
                    <textarea id="contenido" name="contenido" required minlength="40" maxlength="20000" style="min-height:240px;" placeholder="Cuéntanos el caso: raza, manto, herramienta, proceso, resultado y qué aprendiste. Puedes usar Markdown básico."><?= e(viejo('contenido')) ?></textarea>
                    <?php if (isset($errores['contenido'])): ?><span class="error"><?= e($errores['contenido']) ?></span><?php endif; ?>
                </div>
                <div class="campo">
                    <label for="imagen">Imagen (opcional)</label>
                    <input id="imagen" name="imagen" type="file" accept="image/jpeg,image/png,image/webp" class="archivo">
                    <span class="ayuda">JPG, PNG o WebP. Máximo 3 MB. Sin caras de clientes sin permiso.</span>
                </div>
                <div class="formulario__pie">
                    <button class="boton" type="submit">Enviar a revisión</button>
                    <a class="boton boton--contorno" href="<?= url('/comunidad') ?>">Cancelar</a>
                </div>
            </form>

            <aside class="aside-sticky">
                <div class="aside-caja">
                    <h3>Qué publicar</h3>
                    <ul>
                        <li>Casos con antes y después y explicación del proceso.</li>
                        <li>Técnicas que te funcionaron con un manto difícil.</li>
                        <li>Dudas concretas con contexto.</li>
                        <li>Herramientas o productos que probaste.</li>
                    </ul>
                </div>
                <div class="aside-caja">
                    <h3>Qué no publicar</h3>
                    <ul>
                        <li>Datos personales de clientes.</li>
                        <li>Fotos sin autorización.</li>
                        <li>Diagnósticos o tratamientos veterinarios.</li>
                        <li>Promociones agresivas o enlaces de afiliado.</li>
                        <li>Rasurados de doble capa como “solución”.</li>
                    </ul>
                </div>
                <div class="aside-caja" style="background:var(--oro-tenue);border-color:rgba(184,137,47,.3);">
                    <h3>Revisión</h3>
                    <p style="font-size:.87rem;color:#6b4c12;">La revisión puede tomar hasta 48 horas hábiles. Si tu publicación se rechaza, te decimos por qué y puedes corregirla.</p>
                </div>
            </aside>
        </div>
    </div>
</section>
