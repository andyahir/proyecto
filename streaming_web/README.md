# StreamVault — Plataforma de Streaming

## Estructura del proyecto

```
streaming_web/
├── index.php              ← Login de administradores
├── dashboard.php          ← Redirige al panel
├── peliculas.php          ← Registrar y gestionar películas
├── catalogo.php           ← Consultar catálogo visual
├── clientes.php           ← Registrar y gestionar clientes
├── usuarios.php           ← Registrar administradores
├── registro_cliente.php   ← Página pública de registro (desde la app)
├── logout.php
├── .htaccess
├── uploads/               ← Imágenes de películas
├── includes/
│   ├── config.php         ← Configuración BD y helpers
│   ├── header.php         ← Navbar compartida
│   └── footer.php
└── api/
    ├── index.php          ← API REST para la app móvil
    └── docs.html          ← Documentación de la API
```

---

## 1. Instalación en XAMPP (local)

1. Copiar la carpeta `streaming_web` a `C:\xampp\htdocs\`
2. Importar `streaming_db.sql` en phpMyAdmin:
   - Abrir `http://localhost/phpmyadmin`
   - Crear base de datos o dejar que el SQL la cree
   - Ir a **Importar** → seleccionar `streaming_db.sql` → **Ejecutar**
3. Verificar `includes/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');        // Sin contraseña XAMPP
   define('DB_NAME', 'streaming_db');
   define('APP_URL', 'http://localhost/streaming_web');
   ```
4. Acceder a: `http://localhost/streaming_web/`

**Credenciales iniciales:**
- Usuario: `admin`
- Contraseña: `password` *(cambiarla después del primer login)*

---

## 2. Subir a Hostinger

1. En Hostinger, crear la base de datos MySQL desde el **Panel hPanel**
2. Importar `streaming_db.sql` vía phpMyAdmin de Hostinger
3. Editar `includes/config.php` con los datos de Hostinger:
   ```php
   define('DB_HOST', 'localhost');      // Hostinger usa localhost
   define('DB_USER', 'u123456_user');   // Tu usuario de BD
   define('DB_PASS', 'tu_contraseña');  // Tu contraseña de BD
   define('DB_NAME', 'u123456_streaming');
   define('APP_URL', 'https://tudominio.com');
   ```
4. Subir todos los archivos de `streaming_web/` a la carpeta `public_html/` vía FTP (FileZilla) o el administrador de archivos de Hostinger
5. Asegurarse de que la carpeta `uploads/` tenga permisos 755

---

## 3. API para la App Móvil

**URL base (local):** `http://localhost/streaming_web/api/index.php`  
**URL base (Hostinger):** `https://tudominio.com/api/index.php`

Ver documentación completa en: `/api/docs.html`

### Endpoints principales

| Método | Endpoint | Auth | Descripción |
|--------|----------|------|-------------|
| POST | `?endpoint=login` | No | Login cliente → devuelve token |
| POST | `?endpoint=registro` | No | Registro de nuevo cliente |
| GET  | `?endpoint=peliculas` | 🔒 | Lista películas activas |
| GET  | `?endpoint=pelicula&id=X` | 🔒 | Detalle de una película |
| GET  | `?endpoint=generos` | No | Lista de géneros |
| POST | `?endpoint=logout` | 🔒 | Cerrar sesión |

### Header de autenticación
```
Authorization: Bearer {token_recibido_en_login}
```

### Enlace de registro desde la App
El botón "Registrarme" de la app debe abrir en el WebView o navegador:
```
https://tudominio.com/registro_cliente.php
```

---

## 4. Notas importantes

- **Clientes** solo tienen acceso a la app móvil, no al panel web
- **Administradores** solo tienen acceso al panel web
- La **clave del cliente** se genera automáticamente vía trigger en MySQL
- La **contraseña** no se muestra en la tabla de clientes por seguridad
- Al presionar **"Ver"** en la app, se debe abrir la URL del tráiler en YouTube (Intent ACTION_VIEW en Android)
- Los tokens expiran en **24 horas** (configurable en `config.php`)
