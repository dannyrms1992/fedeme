# FEDEME — Plataforma de Gestión de Eventos
## Archivo de contexto para continuación del desarrollo

> Actualizado: 29 de mayo de 2026 (sesión 7)  
> Uso: Abrir este archivo y decirle a GitHub Copilot: **"Lee CONTEXT.md y continúa el desarrollo del proyecto FEDEME"**

---

## PROMPT ORIGINAL DEL PROYECTO

Actúa como arquitecto senior en Laravel 10+, experto en sistemas SaaS multi-tenant, Clean Architecture y despliegue en VPS Linux de producción.

Estoy desarrollando la plataforma oficial de gestión de eventos para FEDEME (Federación Deportiva Militar del Ecuador).

**DOMINIO PRINCIPAL:** fedeme.app  
**Subdominios dinámicos:** evento1.fedeme.app, evento2.fedeme.app

---

### INFRAESTRUCTURA DE PRODUCCIÓN

VPS dedicada:
- 2 vCPU / 8 GB RAM / 100 GB NVMe / 8 TB ancho de banda
- Ubuntu 22.04 / Nginx / PHP 8.4 / MySQL 8 / Redis / Supervisor / Certbot SSL wildcard
- Git / GitHub / VS Code / Despliegue vía SSH / Preparado para CI/CD futuro

---

### OBJETIVO DEL SISTEMA

Plataforma institucional centralizada donde:
- Cada evento funcione bajo un subdominio dinámico
- Cada evento tenga landing pública independiente
- Cada evento tenga aislamiento de datos
- Se comparta infraestructura y base de código
- El sistema esté preparado para evolucionar a SaaS

Uso inicial: institucional (no comercial externo).

---

### ARQUITECTURA MULTI-TENANT

- Single database (shared schema)
- Todas las entidades dependientes incluyen `event_id` (index + FK)
- Global Scope automático por tenant
- Middleware `TenantResolver` obligatorio
- Resolución: extraer subdominio → buscar evento activo → inyectar tenant en container → aislamiento automático → si no existe → 404 controlado

---

### MÓDULO: CÓDIGO ÚNICO POR EVENTO (ACCESO SIMPLE)

Cada evento puede tener: `access_enabled`, `access_code_hash`, `access_expires_at`  
El código NUNCA se almacena en texto plano (Hash::make / Hash::check)

Flujo:
1. Usuario entra a subdominio
2. TenantResolver identifica evento
3. EventAccessMiddleware verifica si access_enabled
4. Si true → validar sesión: `session()->put('event_access_{event_id}', true)`

Seguridad: Rate limiting (5 intentos/minuto/IP), logs de intentos fallidos, mensajes neutros, expiración opcional.

NO implementar: códigos individuales, registro de participantes, JWT, autenticación compleja.

---

### BACKEND — CLEAN ARCHITECTURE LIGERA

Capas: Domain / Application / Infrastructure / Interfaces  
Reglas: No lógica en controladores, Services para lógica de negocio, Form Requests para validación, Policies para autorización, PSR-12.

---

### ROLES

- SuperAdmin (plataforma total)
- Admin FEDEME
- Organizador
- Usuario público

Permisos aislados por evento.

---

### FASE 1 — FUNCIONALIDAD

1. CRUD de eventos
2. Configuración por evento: name, slug, subdomain, description, logo_path, primary_color, secondary_color, status, access_enabled, access_code_hash, access_expires_at
3. Landing pública modular: Hero, Información, PDF, Contacto, Mapa
4. Activación/desactivación de módulos
5. Panel administrativo con Livewire
6. Gestión de código de acceso desde panel

---

### FRONTEND

- Blade + Livewire (admin) + TailwindCSS
- Layout público / Layout administrativo separados
- Diseño institucional sobrio, responsive, alta legibilidad
- SEO dinámico + OpenGraph por evento
- Personalización: Logo, Colores, Imagen principal

---

### OPTIMIZACIÓN

Redis cache + queue, Supervisor workers, OPcache, indexación adecuada, evitar N+1, cache por evento.

---

### DESPLIEGUE

Nginx wildcard `*.fedeme.app`, SSL wildcard, deploy por Git pull, migraciones automatizadas.

---

## LO QUE YA ESTÁ CONSTRUIDO

### Stack
- **Laravel**: 12.52.0
- **PHP**: 8.4 (Herd)
- **Livewire**: v4.1.4
- **Spatie Laravel Permission**: v7.2.1
- **TailwindCSS**: v4 (via @tailwindcss/vite)
- **Vite**: 7.3.1
- **MySQL 8.0.45** (desarrollo local — `fedeme_local`) ✅ sincronizado con producción

---

### Estructura de directorios

```
app/
├── Domain/
│   ├── Event/
│   │   ├── Models/
│   │   │   ├── Event.php          ← Modelo principal con lógica de acceso + 5 colores
│   │   │   └── EventModule.php    ← Módulos de landing con GlobalScope
│   │   ├── ValueObjects/
│   │   │   └── EventStatus.php    ← Enum: draft | active | inactive
│   │   └── Exceptions/
│   │       └── EventNotFoundException.php
│   └── Tenant/
│       ├── Contracts/
│       │   └── TenantInterface.php
│       └── Exceptions/
│           └── TenantNotFoundException.php
│
├── Application/
│   ├── Event/Commands/
│   │   ├── CreateEventCommand.php         ← Crea evento + 4 módulos por defecto
│   │   └── UpdateAccessCodeCommand.php    ← Actualiza código + invalida caché
│   └── Access/Commands/
│       └── ValidateAccessCodeCommand.php  ← Valida código + rate limiting
│
├── Infrastructure/
│   ├── Tenant/
│   │   └── TenantContext.php      ← Singleton del tenant actual en container
│   ├── Persistence/Scopes/
│   │   └── EventScope.php         ← GlobalScope que filtra por event_id
│   └── Logging/
│       └── AccessAttemptLogger.php ← Log estructurado de intentos de acceso
│
└── Interfaces/Http/
    ├── Middleware/
    │   ├── TenantResolver.php          ← Extrae subdominio → resuelve evento → inyecta TenantContext
    │   └── EventAccessMiddleware.php   ← Guarda el acceso por código
    ├── Controllers/
    │   ├── Public/
    │   │   └── EventLandingController.php
    │   ├── Auth/
    │   │   └── EventAccessController.php
    │   └── Admin/
    │       ├── EventController.php              ← CRUD completo implementado
    │       ├── EventAccessConfigController.php  ← Gestión código de acceso
    │       ├── EventImageController.php         ← Upload logo + carrusel
    │       └── EventModuleController.php        ← Editor de módulos + upload logo emergency
    └── Requests/
        ├── AccessCodeRequest.php
        └── StoreEventRequest.php                ← Incluye validación de 5 colores
```

---

### Migraciones (todas ejecutadas ✅)

| Archivo | Tabla | Batch |
|---|---|---|
| `0001_01_01_000000_create_users_table.php` | `users` | 1 |
| `0001_01_01_000001_create_cache_table.php` | `cache` | 1 |
| `0001_01_01_000002_create_jobs_table.php` | `jobs` | 1 |
| `2026_02_22_020525_create_permission_tables.php` | Tablas Spatie | 1 |
| `2026_02_22_100000_create_events_table.php` | `events` | 1 |
| `2026_02_22_100100_create_event_modules_table.php` | `event_modules` | 1 |
| `2026_02_21_225159_add_images_to_events_table.php` | `events` — logo + carrusel | 2 |
| `2026_02_22_200000_add_color_fields_to_events_table.php` | `events` — 3 colores extra | 3 |

**Campos de color en `events`:**
- `primary_color` (default `#1a4f8a`)
- `secondary_color` (default `#c0392b`)
- `accent_color` (default `#F59E0B`)
- `bg_color` (default `#F8FAFC`)
- `surface_color` (default `#FFFFFF`)

---

### Rutas registradas

**`routes/web.php`** — Dominio raíz `fedeme.test`

| Ruta | Nombre | Descripción |
|---|---|---|
| `GET /` | `home` | Welcome page |
| `GET /dashboard` | `dashboard` | Redirige a admin.dashboard |
| `GET/PATCH/DELETE /profile` | `profile.*` | Perfil Breeze |

**`routes/admin.php`** — Prefijo `/admin`, middleware `auth + verified`

| Ruta | Nombre | Descripción |
|---|---|---|
| `GET /admin/dashboard` | `admin.dashboard` | Panel principal |
| `RESOURCE /admin/events` | `admin.events.*` | CRUD completo de eventos |
| `GET /admin/events/{event}/access` | `admin.events.access.edit` | Configurar código |
| `PATCH /admin/events/{event}/access` | `admin.events.access.update` | Guardar código |
| `POST /admin/events/{event}/logo` | `admin.events.logo.upload` | Upload logo |
| `POST /admin/events/{event}/carousel` | `admin.events.carousel.upload` | Upload imagen carrusel |
| `DELETE /admin/events/{event}/carousel/{index}` | `admin.events.carousel.delete` | Eliminar imagen carrusel |
| `GET /admin/events/{event}/modules` | `admin.events.modules.edit` | Editor de módulos |
| `PATCH /admin/events/{event}/modules/{module}` | `admin.events.modules.update` | Guardar módulo |
| `POST /admin/events/{event}/modules/{module}/reorder` | `admin.events.modules.reorder` | Reordenar módulos |

**`routes/event.php`** — Subdominio dinámico `*.fedeme.test`, middleware `TenantResolver`

| Ruta | Nombre | Descripción |
|---|---|---|
| `GET /access` | `event.access.form` | Formulario código de acceso |
| `POST /access` | `event.access.store` | Validar código (throttle 5/min) |
| `GET /` | `event.landing` | Landing pública (+ EventAccessMiddleware) |

---

### Vistas Blade

```
resources/views/
├── layouts/
│   ├── public.blade.php     ← Layout público: Inter font, 5 CSS vars + 7 derivadas, .glass, footer gradiente diagonal
│   └── admin.blade.php      ← Panel admin con nav
├── public/
│   ├── access.blade.php     ← Formulario de código (minimalista)
│   ├── landing.blade.php    ← Renderiza módulos dinámicamente con sectionIds
│   └── modules/
│       ├── hero.blade.php       ← Carrusel (ver carousel.blade.php), hero-title/hero-subtitle, fallback gradient
│       ├── info.blade.php       ← Glass card + tarjetas Lugar (botón Cómo llegar) + Fecha (rango ES)
│       ├── contact.blade.php    ← Glass wrapper + cards bg-white/60, tel: links
│       ├── pdf.blade.php        ← Embed PDF + CTA accent
│       ├── map.blade.php        ← Google Maps embed + barra browser responsive (decoración oculta en mobile)
│       └── emergency.blade.php  ← Tarjeta centrada: logo upload + botón tel:
└── components/
    └── carousel.blade.php   ← Carrusel Alpine.js + Lightbox fullscreen con thumbnails y teclado
└── admin/
    ├── dashboard.blade.php
    └── events/
        ├── index.blade.php
        ├── create.blade.php   ← 5 color pickers (grid-cols-5)
        ├── show.blade.php
        ├── edit.blade.php     ← 5 color pickers, logo, carrusel
        ├── access.blade.php
        └── modules.blade.php  ← Editor de módulos con Alpine.js, enctype dinámico
```

---

### Sistema de diseño — CSS (`resources/css/app.css`)

**CSS vars por evento** (inline en `<head>` del layout público, valores desde BD):
```css
--color-primary, --color-secondary, --color-accent, --color-bg, --color-surface
```

**CSS vars derivadas** (auto via `color-mix()`):
```css
--color-primary-light, --color-primary-dark, --color-secondary-light,
--color-accent-light, --color-bg-tinted, --glass-bg, --glass-border
```

**Tipografía** (`@layer base`): Inter (Google Fonts), `h1-h6` font-weight:800, `p/li` line-height:1.6

**Clases utilitarias** (`@layer components`):
- `.section-badge` — pill con icono + texto uppercase
- `.section-title` — heading 3xl→5xl font-extrabold
- `.section-description` — texto descriptivo gris
- `.section-accent-bar` — barra gradiente primary→secondary
- `.section-header` — contenedor centrado mb-12
- `.hero-title` — 5xl→7xl font-black
- `.hero-subtitle` — texto ligero opacidad 90%
- `.card-title`, `.card-meta`, `.card-body`
- `.glass` — backdrop-filter blur(16px) saturate(180%)
- `.glass-dark` — variante oscura con color-mix

---

### Módulos de landing (7 tipos)

| Tipo | ID sección | Estado | Descripción |
|---|---|---|---|
| `hero` | `#inicio` | ✅ | Carrusel de imágenes (con lightbox fullscreen) + título + subtítulo |
| `info` | `#informacion` | ✅ | Información general (Markdown) + tarjetas de Lugar y Fecha |
| `contact` | `#contacto` | ✅ | Contactos repetibles (nombre/cargo/email/tel) |
| `pdf` | `#documentos` | ✅ | PDF embebido con CTA de descarga |
| `map` | `#mapa` | ✅ | Google Maps embed + botón "Abrir en Google Maps" (responsive) |
| `emergency` | `#emergencias` | ✅ | Logo (upload) + número de teléfono `tel:` |
| `video_intro` | `#video-intro` | ✅ | Video anuncio overlay al abrir la página (YouTube/Vimeo/MP4, show_once) |

**Módulos por defecto** en nuevos eventos: `hero`, `info`, `contact`, `emergency`  
**Todos los 7 módulos se auto-crean** (inactivos) al entrar al editor de módulos de cualquier evento.

---

### Seeders ejecutados ✅

- `RolesAndPermissionsSeeder` → 4 roles + permisos Spatie
- `DatabaseSeeder` → SuperAdmin creado
- `EmergencyModuleSeeder` → módulo emergency añadido a todos los eventos existentes

**Credenciales SuperAdmin (desarrollo):**
- Email: `admin@fedeme.ec`
- Password: `fedeme2026!`
- ⚠️ Cambiar antes de producción

---

### Build de assets

- Último build: `app-BGiSnBBR.css` (72.86 kB) + `app-CBbTb_k3.js` (83.04 kB)
- `public/build/` **incluido en git** (removido de `.gitignore`) — el servidor no necesita Node.js
- Storage symlink: `public/storage` → `storage/app/public` ✅
- Imágenes sincronizadas desde producción:
  - `storage/app/public/events/1/logo/` — logo evento sudamericano
  - `storage/app/public/events/1/carousel/` — 3 imágenes carrusel
  - `storage/app/public/modules/emergency/` — logo módulo emergency

---

### Configuración

| Parámetro | Valor |
|---|---|
| APP_URL | http://fedeme.test |
| APP_DOMAIN | fedeme.test |
| DB_CONNECTION | mysql |
| DB_DATABASE | fedeme_local |
| DB_USERNAME | root |
| Timezone | America/Guayaquil |
| Locale | es / es_EC |
| Session domain | .fedeme.test |
| Session driver | file |
| Cache | database (local) / Redis (prod) |
| Queue | database (local) / Redis (prod) |
| Log access | storage/logs/access_attempts.log |

### Subdominios locales (`C:\Windows\System32\drivers\etc\hosts`) ✅

```
127.0.0.1 fedeme.test
127.0.0.1 sudamericano.fedeme.test
127.0.0.1 campeonato.fedeme.test
127.0.0.1 mundial.fedeme.test
```

### Eventos en BD local (2)

| ID | Nombre | Subdominio | Estado |
|---|---|---|---|
| 1 | Campeonato Sudamericano de Equitación | `sudamericano` | active |
| 4 | Campeonato Mundial Militar de Equitación | `mundial` | draft |

---

## PENDIENTE — PRÓXIMOS PASOS

### Prioridad Alta

- [x] ~~Configurar subdominios locales~~ — `sudamericano.fedeme.test` y `campeonato.fedeme.test` en hosts ✅
- [x] ~~Módulo video_intro~~ — overlay anuncio al abrir la página, con URL/archivo/YouTube/Vimeo ✅
- [x] ~~Espaciado entre módulos~~ — reducido `py-20` → `py-14` → `py-10`, `section-header mb-8` ✅
- [x] ~~Alpine.js doble instancia~~ — CDN removido de ambos layouts, solo npm + app.js ✅
- [x] ~~Probar flujo completo de emergency~~ — logo sincronizado desde producción ✅
- [x] ~~Deploy producción~~ — commit + push + git pull en VPS (22 mayo 2026) ✅
- [x] ~~Botones adicionales en módulo info~~ — "Ver transmisión en vivo" (reabre overlay video_intro) + "Ver Resultados" (enlace externo configurable) ✅
- [ ] **Gradientes de sección** — actualmente en 5-8% (muy sutiles), subir a 15-30% para hacerlos visibles

### Prioridad Media

- [ ] **Módulos adicionales**: programa/agenda, galería de fotos, resultados/clasificaciones
- [ ] **Política de autorización** — `EventController` aún no usa Spatie policies (solo `auth`)
- [ ] **Tests** — feature tests para CRUD de eventos y flujo de acceso por código
- [ ] **Script de deploy** — crear `deploy.sh` para automatizar `git pull + migrate + cache` en VPS

### Prioridad Baja (Fase 2)

- [ ] Configuración Nginx + Certbot wildcard SSL en VPS Ubuntu 22.04
- [ ] Redis en producción para cache y queue (ya está instalado, activar en .env)
- [ ] GitHub Actions CI/CD — deploy automático por push a `main`
- [ ] Supervisor para queue workers
- [ ] Script de despliegue automatizado (`deploy.sh`)
- [ ] Monitoreo de queue failures

---

## NOTAS TÉCNICAS IMPORTANTES

### Sesiones multi-subdominio
`SESSION_DOMAIN=.fedeme.test` en `.env` es **crítico**. Sin el punto inicial las sesiones no se comparten entre subdominios y el código de acceso no persistirá.

### SSL Wildcard en producción
Certbot wildcard requiere **DNS-01 challenge**. El proveedor DNS de `fedeme.app` debe soportar API automation. Si el DNS es manual, el wildcard se obtiene pero no se renueva automáticamente.

### GlobalScope
`EventScope` aplica a `EventModule` y cualquier modelo futuro que dependa de `event_id`. El modelo `Event` **NO lleva scope propio** — siempre se consulta sin filtro de tenant para poder resolver el tenant desde el subdominio.

### Caché del tenant
`TenantResolver` cachea el evento por subdominio durante 5 minutos (Redis). `UpdateAccessCodeCommand` invalida ese caché al modificar el código de acceso.

### Rate limiting acceso
`ValidateAccessCodeCommand` usa `RateLimiter` de Laravel con clave `event_access:{ip}:{event_id}`. El throttle de la ruta adicionalmente limita a 5 req/min via middleware `throttle:5,1`.

### Sistema de colores CSS (5 vars + 7 derivadas)
Los 5 colores base se inyectan como `<style>` inline en `layouts/public.blade.php` desde los campos de BD del evento. Las 7 vars derivadas se calculan automáticamente con `color-mix()`. Nunca usar valores hardcoded en módulos — siempre `var(--color-primary)` etc.

### Módulo emergency — upload de logo
El formulario admin usa `{!! !!}` (NO `{{ }}`) para renderizar `enctype="multipart/form-data"` condicionalmente, ya que `{{ }}` escapa las comillas. El logo se almacena en `storage/app/public/modules/emergency/` y se accede via `asset('storage/...')`.

### Blade sin escapar para atributos HTML
Al generar atributos HTML dinámicos en Blade que contienen comillas (como `enctype="..."`, `style="..."` con valores variables), siempre usar `{!! !!}` en lugar de `{{ }}`.

### enctype en forms con archivos
Los formularios de módulos `emergency` y `video_intro` llevan `enctype="multipart/form-data"`. La condición es `in_array($module->type, ['emergency', 'video_intro'])` en `modules.blade.php`.

### Módulo info — campos Lugar, Fecha, Transmisión y Resultados
`settings` del módulo `info` soporta los siguientes campos:
- `location` — nombre del lugar visible al visitante
- `location_query` — query para Google Maps dirección (`/maps/dir/?destination=`)
- `date_start` — fecha ISO `YYYY-MM-DD`
- `date_end` — fecha ISO opcional (si difiere de `date_start` se muestra rango)
- `live_stream_button` — bool; activa tarjeta "Transmisión en Vivo" con botón que reabre el overlay de `video_intro`
- `live_stream_label` — texto descriptivo de la tarjeta de transmisión (default: "Video oficial del evento")
- `results_url` — URL opcional; si está configurada muestra tarjeta "Resultados" con enlace externo

Las fechas se formatean en español ("5 al 10 de marzo de 2026"). Si es mismo mes y año → formato corto; si son meses distintos → formato largo con guion. Se calcula y muestra el número de días del evento si es rango.

Las 4 tarjetas se renderizan en un grid `sm:grid-cols-2`: Lugar (`--color-primary`), Fecha (`--color-secondary`), Transmisión (`--color-accent`), Resultados (`--color-primary`). Cada tarjeta solo aparece si su campo está configurado.

### Módulo info ↔ video_intro — comunicación entre componentes
El botón "Ver transmisión en vivo" del módulo `info` dispara un evento nativo del navegador:
```js
window.dispatchEvent(new CustomEvent('open-video-intro'))
```
El módulo `video_intro` escucha ese evento con `@open-video-intro.window="open = true"` en su componente Alpine. El botón **bypasea** el `show_once` (sessionStorage) — siempre reabre el overlay al hacer click explícito. La tarjeta solo se renderiza si `live_stream_button = true` Y el módulo `video_intro` está activo con `video_url` o `video_path` configurado (verificado contra `$modules` ya cargado, sin query extra).

### Carrusel — Lightbox
`resources/views/components/carousel.blade.php` incluye lightbox integrado con Alpine.js:
- Estado `lb: bool` en el mismo `x-data` del carrusel
- URLs de imágenes pasadas al JS via `Js::from()` para evitar re-render
- Al abrir: pausa autoplay, bloquea scroll del body (`document.body.style.overflow = 'hidden'`)
- Al cerrar: restaura scroll, reanuda autoplay
- Navegación: flechas laterales, thumbnails inferiores, teclas ← → ESC
- Click en el backdrop (`@click.self`) cierra el lightbox
- Hint visual "Ver pantalla completa" en esquina superior izquierda del carrusel

### Footer
Gradiente diagonal `135deg` de `color-mix(primary 92%, black)` a `color-mix(secondary 75%, black)` para transición sutil primary→secondary.

### Módulo map — responsive
La barra browser decorativa (puntos rojo/amarillo/verde + barra URL) se oculta en mobile (`hidden sm:flex`). El botón "Abrir en Google Maps" siempre visible, centrado en mobile con mejor contraste (`bg-white/10 border-white/20 text-white font-semibold`).

### Módulo video_intro — overlay de anuncio
`resources/views/public/modules/video_intro.blade.php` — overlay `fixed inset-0 z-50` que aparece al cargar la página.
- **Settings**: `video_url` (YouTube/Vimeo/MP4 directo), `video_path` (archivo subido), `title`, `show_once` (bool)
- `video_url` tiene prioridad sobre `video_path`. YouTube `watch?v=` se convierte a embed. Vimeo ídem.
- `$isEmbed=true` → `<iframe allowfullscreen>`. `$isEmbed=false` → `<video controls playsinline>` (sin muted/autoplay)
- Big play button overlay (Alpine `paused` state) con `pointer-events-auto` centrado sobre el video
- `show_once=true` → usa `sessionStorage` para no repetir en la misma sesión del navegador
- Alpine: `{ open: true, paused: true, init() { check sessionStorage }, close() { set sessionStorage + video.pause() } }`
- Tecla ESC cierra el overlay (`@keydown.escape.window`)
- Archivos de video: `Storage::disk('public')`, path `events/{id}/video/`, max 100MB (mp4/webm)
- En `EventModuleController::edit()` se auto-crean los 7 módulos si no existen (inactivos)

### Acción Eliminar en listado de eventos
`admin/events/index.blade.php` incluye botón Eliminar con `confirm()` nativo. Envía `DELETE /admin/events/{id}` via formulario con `@method('DELETE')`. El controlador `EventController::destroy()` borra el evento e invalida su caché Redis.

### Sincronización BD local ↔ producción

**Producción → Local** (reimportar datos frescos):
```powershell
# 1. Generar dump en servidor (preserva UTF-8)
ssh root@187.77.53.137 "mysqldump -u fedeme_user -pFedemeDB2026! --no-tablespaces --default-character-set=utf8mb4 fedeme_prod > /tmp/fedeme_dump.sql"

# 2. Descargar con SCP (transferencia binaria, NO usar ssh ... > archivo)
scp root@187.77.53.137:/tmp/fedeme_dump.sql C:\Users\rms\Development\fedeme\fedeme_dump.sql

# 3. Importar con cmd (NO PowerShell, para preservar UTF-8)
cmd /c "chcp 65001 > nul && \"C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe\" -u root -pDanito1992. --default-character-set=utf8mb4 fedeme_local < C:\Users\rms\Development\fedeme\fedeme_dump.sql"

# 4. Limpiar
Remove-Item fedeme_dump.sql
```

**Local → Producción** (subir datos locales, SOBREESCRIBE producción):
```powershell
# Solo cuando producción no tenga datos reales que conservar
ssh root@187.77.53.137 "mysqldump ... fedeme_local > /tmp/local_dump.sql"
scp /tmp/local_dump.sql root@187.77.53.137:/tmp/
ssh root@187.77.53.137 "mysql -u fedeme_user -pFedemeDB2026! fedeme_prod < /tmp/local_dump.sql"
```

**⚠️ IMPORTANTE:** Nunca usar `ssh ... | mysql` ni `ssh ... > archivo.sql` desde PowerShell — convierte UTF-8 a CP850 corrompiendo tildes. Siempre: dump en servidor → SCP → importar con `cmd /c chcp 65001`.

### Sincronización imágenes local ↔ producción

```powershell
# Producción → Local (todas las imágenes)
scp -r root@187.77.53.137:/var/www/fedeme/storage/app/public/events C:\Users\rms\Development\fedeme\storage\app\public\
scp -r root@187.77.53.137:/var/www/fedeme/storage/app/public/modules C:\Users\rms\Development\fedeme\storage\app\public\
```

---

## CÓMO RETOMAR EL DESARROLLO

```
1. Abrir VS Code en C:\Users\Usuario\fedeme
2. Iniciar nuevo chat con GitHub Copilot
3. Escribir: "Lee el archivo CONTEXT.md y continúa el desarrollo del proyecto FEDEME"
4. El asistente leerá este archivo y retomará desde los PENDIENTES
```
