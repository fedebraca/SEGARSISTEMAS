# SEGARSISTEMAS — Instrucciones del Proyecto

## Descripción

Sistema web de **gestión de seguridad laboral y riesgos** para la empresa SEGAR Sistemas. Permite registrar accidentes e incidentes laborales, gestionar riesgos, administrar empresas/clientes/equipos, controlar horas hombre trabajadas y generar indicadores e informes con exportación a Excel.

## Stack Técnico

- **Framework:** Laravel 5.2 (PHP)
- **PHP en producción:** 7.4 (configurado vía .htaccess con `alt-php74`)
- **PHP local:** 7.4.33 instalado en `C:\php74`
- **Base de datos:** MySQL (producción en Bluehost, local en `localhost:3306`)
- **Frontend:** Blade templates + Semantic UI + jQuery
- **Autenticación:** Sentinel/Centaur (`srlabs/centaur`)
- **Exportación Excel:** `box/spout`
- **Build frontend:** Gulp + Laravel Elixir (assets ya compilados en `public/`)

## Estructura del Proyecto

```
app/                    # Modelos, Controllers, Middleware, Providers
├── Http/
│   ├── Controllers/    # AccidenteController, RiesgoController, etc.
│   ├── Middleware/
│   └── routes.php      # Rutas (Route::controllers)
├── Providers/
│   └── AuthServiceProvider.php  # Gates: roles adm, sup; vistas a, r, m
bootstrap/              # Bootstrap de Laravel
config/                 # Configuración (database.php, app.php, etc.)
database/
│   ├── migrations/     # Solo users + password_resets (esquema real está en dump)
│   ├── seeds/          # Vacío
│   └── localhost.sql   # Dump completo de producción (NO commitear datos sensibles)
public/                 # Assets compilados, index.php, Semantic UI
resources/views/        # Vistas Blade por módulo
storage/                # Logs, cache, sessions
tests/                  # PHPUnit
```

## Bases de Datos

El dump (`database/localhost.sql`) contiene 2 bases:

1. **`segarsis_accidente`** (base principal, 17 tablas):
   `accidente`, `cau_basica`, `cau_basica_factor`, `cau_basica_tipo`, `cau_inm`, `cau_inm_tipo`, `cliente`, `empresa`, `equipo`, `horas_hombre`, `incidente`, `lugar`, `migrations`, `password_resets`, `riesgo`, `tipo_accidente`, `users`

2. **`segarsis_obs`** (módulo observaciones, 15 tablas):
   `cliente`, `envio`, `equipamiento`, `equipo`, `generico`, `listado`, `migrations`, `password_resets`, `personal`, `responsable`, `tipo`, `tipo_acto`, `tipo_cond`, `tipo_usu`, `users`

## Roles y Permisos

- **`adm`** — Administrador, acceso total
- **`sup`** — Supervisor, acceso a listados, agregar accidentes/riesgos, descargas
- **Vistas:** `a` (accidentes), `r` (riesgos), `m` (ambos)

## Entorno Local

- **Servidor:** `C:\php74\php.exe artisan serve` → http://localhost:8000
- **MySQL:** Servicio manual: `"C:\Program Files\MySQL\MySQL Server 8.4\bin\mysqld.exe" --datadir=C:\mysql_data --console`
- **MySQL client:** `"C:\Program Files\MySQL\MySQL Server 8.4\bin\mysql.exe" -u root`
- **Composer:** `C:\php74\php.exe C:\composer\composer.phar`
- **SQLyog Community** instalado para gestión visual de la base

## Configuración Local (.env)

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=segarsis_accidente
DB_USERNAME=root
DB_PASSWORD=
```

## Hosting (Producción)

- **Proveedor:** Bluehost (shared hosting)
- **URL:** https://segarsistemas.com
- **Web root:** `/home2/segarsis/public_html`
- **Deploy:** Manual por FTP (no hay CI/CD configurado)
- **PHP:** 7.4 vía cPanel PHP Selector

## Flujo de Trabajo

1. **Nunca editar directamente en `main`** — crear rama `feature/nombre-del-cambio`
2. Un commit = un cambio lógico
3. Push de la rama → Pull Request en GitHub → merge a `main`
4. Deploy manual por FTP de los archivos modificados
5. **No commitear:** `.env`, `vendor/`, `node_modules/`, `storage/logs/`
6. Backup de base de producción antes de cambios grandes

## Notas Importantes

- Las migraciones de Laravel no cubren el esquema real; las tablas se crearon manualmente en MySQL. El esquema real está en `database/localhost.sql`.
- El `.htaccess` raíz tiene la configuración de PHP version y redirect HTTPS.
- La carpeta `observaciones/` en el hosting es un módulo separado que usa la base `segarsis_obs`.
- Los assets frontend ya están compilados; no es necesario correr `npm install` ni `gulp` salvo que se modifiquen estilos.
