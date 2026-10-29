-- Esquema de Mikato Estilistas Caninos · PostgreSQL 15+
-- Idempotente: se puede correr varias veces con php bin/migrate.php

create table if not exists usuarios (
  id bigserial primary key,
  nombre text not null,
  usuario text not null,
  correo text not null,
  telefono text,
  whatsapp text,
  password_hash text not null,
  rol text not null default 'cliente',
  estado text not null default 'activo',
  ciudad text,
  estado_geo text,
  bio text,
  avatar text,
  acceso_hasta date,
  creado_en timestamptz not null default now(),
  ultimo_acceso timestamptz,
  constraint usuarios_rol_valido check (rol in ('admin', 'editor', 'estilista', 'cliente')),
  constraint usuarios_estado_valido check (estado in ('activo', 'suspendido', 'pendiente'))
);

create unique index if not exists usuarios_usuario_unico on usuarios (lower(usuario));
create unique index if not exists usuarios_correo_unico on usuarios (lower(correo));
create index if not exists usuarios_rol_idx on usuarios (rol);

alter table usuarios add column if not exists acceso_hasta date;

create table if not exists password_resets (
  id bigserial primary key,
  usuario_id bigint not null references usuarios (id) on delete cascade,
  token_hash text not null unique,
  creado_en timestamptz not null default now(),
  expira_en timestamptz not null,
  usado_en timestamptz,
  ip text,
  user_agent text
);

create index if not exists password_resets_usuario_idx on password_resets (usuario_id, usado_en);
create index if not exists password_resets_expira_idx on password_resets (expira_en);

create table if not exists solicitudes_arco (
  id bigserial primary key,
  folio text not null unique,
  tipo text not null default 'rectificacion',
  nombre_titular text not null,
  numero_cliente text,
  correo text not null,
  telefono text,
  ciudad text not null,
  fecha_solicitud date not null default current_date,
  nombre_representante text,
  tipo_representacion text,
  dato_incorrecto text not null,
  dato_correcto text not null,
  documento_acreditativo text,
  notificacion_medio text not null default 'correo',
  domicilio text,
  direccion_oficina text,
  nombre_firma text not null,
  archivo_identificacion text,
  archivo_representacion text,
  archivo_acreditativo text,
  estado text not null default 'recibida',
  respuesta text,
  creado_en timestamptz not null default now(),
  actualizado_en timestamptz not null default now(),
  ip text,
  user_agent text,
  constraint solicitudes_arco_tipo_valido check (tipo in ('rectificacion')),
  constraint solicitudes_arco_estado_valido check (estado in ('recibida', 'en_revision', 'resuelta', 'rechazada')),
  constraint solicitudes_arco_notificacion_valida check (notificacion_medio in ('correo', 'domicilio', 'oficina'))
);

create index if not exists solicitudes_arco_estado_idx on solicitudes_arco (estado, creado_en desc);
create index if not exists solicitudes_arco_correo_idx on solicitudes_arco (lower(correo));

create table if not exists guia_media (
  id bigserial primary key,
  usuario_id bigint not null references usuarios (id) on delete cascade,
  guia_slug text not null,
  tipo text not null default 'imagen',
  ruta text not null,
  titulo text not null,
  descripcion text,
  estado text not null default 'publicado',
  creado_en timestamptz not null default now(),
  constraint guia_media_tipo_valido check (tipo in ('imagen', 'video')),
  constraint guia_media_estado_valido check (estado in ('publicado', 'oculto'))
);

create index if not exists guia_media_guia_idx on guia_media (guia_slug, estado, creado_en desc);
create index if not exists guia_media_usuario_idx on guia_media (usuario_id, creado_en desc);

create table if not exists clientes (
  id bigserial primary key,
  usuario_id bigint references usuarios (id) on delete set null,
  nombre text not null,
  telefono text,
  correo text,
  direccion text,
  notas text,
  creado_en timestamptz not null default now()
);

create index if not exists clientes_nombre_idx on clientes (lower(nombre));
create index if not exists clientes_usuario_idx on clientes (usuario_id);

create table if not exists mascotas (
  id bigserial primary key,
  cliente_id bigint not null references clientes (id) on delete cascade,
  nombre text not null,
  especie text not null default 'perro',
  raza text,
  tamano text not null default 'mediano',
  sexo text,
  nacimiento date,
  peso numeric(5, 2),
  temperamento text,
  alergias text,
  notas text,
  foto text,
  activo boolean not null default true,
  creado_en timestamptz not null default now(),
  constraint mascotas_especie_valida check (especie in ('perro', 'gato', 'otro')),
  constraint mascotas_tamano_valido check (tamano in ('chico', 'mediano', 'grande', 'gigante'))
);

create index if not exists mascotas_cliente_idx on mascotas (cliente_id);
create index if not exists mascotas_nombre_idx on mascotas (lower(nombre));

create table if not exists servicios (
  id bigserial primary key,
  nombre text not null,
  descripcion text,
  categoria text not null default 'bano',
  tamano text not null default 'todos',
  precio numeric(10, 2) not null default 0,
  duracion_min integer not null default 60,
  activo boolean not null default true,
  orden integer not null default 0,
  creado_en timestamptz not null default now(),
  constraint servicios_tamano_valido check (tamano in ('todos', 'chico', 'mediano', 'grande', 'gigante'))
);

create index if not exists servicios_activo_idx on servicios (activo, orden);

create table if not exists productos (
  id bigserial primary key,
  nombre text not null,
  descripcion text,
  categoria text not null default 'shampoo',
  presentacion text,
  precio numeric(10, 2) not null default 0,
  precio_mayoreo numeric(10, 2),
  costo numeric(10, 2),
  stock integer not null default 0,
  sku text,
  imagen text,
  activo boolean not null default true,
  destacado boolean not null default false,
  creado_en timestamptz not null default now()
);

create index if not exists productos_activo_idx on productos (activo, destacado);

create table if not exists paquetes (
  id bigserial primary key,
  nombre text not null,
  descripcion text,
  precio numeric(10, 2) not null default 0,
  tamano text not null default 'todos',
  activo boolean not null default true,
  creado_en timestamptz not null default now()
);

create table if not exists paquete_servicios (
  paquete_id bigint not null references paquetes (id) on delete cascade,
  servicio_id bigint not null references servicios (id) on delete cascade,
  cantidad integer not null default 1,
  primary key (paquete_id, servicio_id)
);

create table if not exists citas (
  id bigserial primary key,
  codigo text not null,
  cliente_id bigint not null references clientes (id) on delete restrict,
  mascota_id bigint not null references mascotas (id) on delete restrict,
  servicio_id bigint not null references servicios (id) on delete restrict,
  fecha date not null,
  hora time not null,
  estado text not null default 'pendiente',
  precio numeric(10, 2) not null default 0,
  notas text,
  origen text not null default 'web',
  recordatorio boolean not null default false,
  creado_en timestamptz not null default now(),
  actualizado_en timestamptz not null default now(),
  constraint citas_estado_valido check (estado in ('pendiente', 'confirmada', 'en_proceso', 'completada', 'cancelada', 'no_asistio')),
  constraint citas_origen_valido check (origen in ('web', 'telefono', 'mostrador', 'whatsapp'))
);

create unique index if not exists citas_codigo_unico on citas (codigo);
create index if not exists citas_fecha_idx on citas (fecha, hora);
create index if not exists citas_cliente_idx on citas (cliente_id);
create unique index if not exists citas_slot_unico on citas (fecha, hora)
  where estado in ('pendiente', 'confirmada', 'en_proceso');

create table if not exists ventas (
  id bigserial primary key,
  folio text not null,
  cliente_id bigint references clientes (id) on delete set null,
  fecha date not null default current_date,
  subtotal numeric(10, 2) not null default 0,
  descuento numeric(10, 2) not null default 0,
  total numeric(10, 2) not null default 0,
  metodo_pago text not null default 'efectivo',
  estado text not null default 'pagada',
  notas text,
  registrado_por bigint references usuarios (id) on delete set null,
  creado_en timestamptz not null default now(),
  constraint ventas_metodo_valido check (metodo_pago in ('efectivo', 'transferencia', 'tarjeta', 'otro')),
  constraint ventas_estado_valido check (estado in ('pagada', 'pendiente', 'cancelada'))
);

create unique index if not exists ventas_folio_unico on ventas (folio);
create index if not exists ventas_fecha_idx on ventas (fecha);
create index if not exists ventas_cliente_idx on ventas (cliente_id);

create table if not exists venta_items (
  id bigserial primary key,
  venta_id bigint not null references ventas (id) on delete cascade,
  tipo text not null default 'servicio',
  referencia_id bigint,
  descripcion text not null,
  cantidad integer not null default 1,
  precio_unitario numeric(10, 2) not null default 0,
  importe numeric(10, 2) not null default 0,
  constraint venta_items_tipo_valido check (tipo in ('servicio', 'producto', 'paquete', 'otro'))
);

create index if not exists venta_items_venta_idx on venta_items (venta_id);

create table if not exists gastos (
  id bigserial primary key,
  concepto text not null,
  categoria text not null default 'insumos',
  monto numeric(10, 2) not null default 0,
  fecha date not null default current_date,
  metodo_pago text not null default 'efectivo',
  notas text,
  creado_en timestamptz not null default now()
);

create index if not exists gastos_fecha_idx on gastos (fecha);

create table if not exists historial_estetica (
  id bigserial primary key,
  mascota_id bigint not null references mascotas (id) on delete cascade,
  cita_id bigint references citas (id) on delete set null,
  fecha date not null default current_date,
  servicio text not null,
  observaciones text,
  proxima_visita date,
  foto text,
  creado_en timestamptz not null default now()
);

create index if not exists historial_mascota_idx on historial_estetica (mascota_id, fecha desc);

create table if not exists publicaciones (
  id bigserial primary key,
  usuario_id bigint not null references usuarios (id) on delete cascade,
  titulo text not null,
  slug text not null,
  categoria text not null default 'casos',
  resumen text,
  contenido text not null,
  imagen text,
  estado text not null default 'pendiente',
  fijado boolean not null default false,
  vistas integer not null default 0,
  creado_en timestamptz not null default now(),
  actualizado_en timestamptz not null default now(),
  constraint publicaciones_estado_valido check (estado in ('pendiente', 'publicado', 'rechazado'))
);

create unique index if not exists publicaciones_slug_unico on publicaciones (slug);
create index if not exists publicaciones_estado_idx on publicaciones (estado, fijado, creado_en desc);

create table if not exists comentarios (
  id bigserial primary key,
  publicacion_id bigint not null references publicaciones (id) on delete cascade,
  usuario_id bigint not null references usuarios (id) on delete cascade,
  contenido text not null,
  estado text not null default 'pendiente',
  creado_en timestamptz not null default now(),
  constraint comentarios_estado_valido check (estado in ('pendiente', 'aprobado', 'rechazado'))
);

create index if not exists comentarios_publicacion_idx on comentarios (publicacion_id, estado);

create table if not exists negocios (
  id bigserial primary key,
  usuario_id bigint references usuarios (id) on delete set null,
  nombre text not null,
  slug text not null,
  descripcion text,
  ciudad text,
  estado_geo text,
  direccion text,
  telefono text,
  whatsapp text,
  correo text,
  web text,
  servicios text,
  horario text,
  logo text,
  verificado boolean not null default false,
  estado text not null default 'pendiente',
  destacado boolean not null default false,
  es_demo boolean not null default false,
  creado_en timestamptz not null default now(),
  constraint negocios_estado_valido check (estado in ('pendiente', 'aprobado', 'rechazado'))
);

create unique index if not exists negocios_slug_unico on negocios (slug);
create index if not exists negocios_estado_idx on negocios (estado, estado_geo);

create table if not exists contactos (
  id bigserial primary key,
  nombre text not null,
  correo text,
  telefono text,
  asunto text not null default 'general',
  mensaje text not null,
  estado text not null default 'nuevo',
  creado_en timestamptz not null default now(),
  constraint contactos_estado_valido check (estado in ('nuevo', 'atendido'))
);

create index if not exists contactos_estado_idx on contactos (estado, creado_en desc);

create table if not exists boletin (
  id bigserial primary key,
  correo text not null,
  creado_en timestamptz not null default now()
);

create unique index if not exists boletin_correo_unico on boletin (lower(correo));

create table if not exists configuracion (
  clave text primary key,
  valor text,
  actualizado_en timestamptz not null default now()
);

insert into configuracion (clave, valor) values
  ('negocio_nombre', 'Mikato Estética Canina'),
  ('negocio_telefono', ''),
  ('negocio_whatsapp', ''),
  ('negocio_direccion', ''),
  ('negocio_horario', 'Lun a sáb · 9:00 a 19:00'),
  ('capacidad_diaria', '12'),
  ('anticipo_pct', '0')
on conflict (clave) do nothing;
