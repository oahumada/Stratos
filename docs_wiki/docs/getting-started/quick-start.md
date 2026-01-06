# Quick Start - 5 Minutos

Pon en marcha TalentIA en menos de 5 minutos.

---

## 📋 Pre-requisitos

Asegúrate de tener instalado:

- **PHP** >= 8.3 (recomendado 8.4)
- **Composer** >= 2.6
- **Node.js** >= 20
- **npm** o **pnpm**
- **Git**

---

## ⚡ Instalación Rápida

### 1. Clonar el repositorio

```bash
git clone https://github.com/yourusername/TalentIA.git
cd TalentIA
```

### 2. Instalar dependencias Backend

```bash
composer install
```

### 3. Configurar ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurar base de datos

Edita `.env` (SQLite por defecto):

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

O PostgreSQL para producción:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=talentia
DB_USERNAME=postgres
DB_PASSWORD=secret
```

### 5. Ejecutar migraciones y seeders

```bash
# Crear la base de datos
touch database/database.sqlite

# Ejecutar migraciones
php artisan migrate

# Poblar con datos de prueba (opcional)
php artisan db:seed
```

### 6. Instalar dependencias Frontend

```bash
npm install
# o
pnpm install
```

### 7. Levantar servidores

En terminales separadas:

```bash
# Terminal 1: Backend
php artisan serve
# → http://127.0.0.1:8000

# Terminal 2: Frontend
npm run dev
# → http://127.0.0.1:5173
```

---

## ✅ Verificar Instalación

### Probar API

```bash
curl http://127.0.0.1:8000/api/health
```

Respuesta esperada:
```json
{
  "status": "ok",
  "timestamp": "2026-01-05T10:30:00Z"
}
```

### Acceder Frontend

Abre tu navegador en `http://127.0.0.1:5173`

Deberías ver:
- Dashboard con métricas
- Menú lateral con módulos (People, Roles, Skills, etc.)

---

## 🎯 Validar Funcionalidad

### 1. Ver People

1. Navega a **People** en el menú lateral
2. Deberías ver una tabla con personas (si ejecutaste seeders)
3. Prueba:
   - ✅ Búsqueda por nombre
   - ✅ Filtros por departamento
   - ✅ Paginación

### 2. Crear un Role

1. Navega a **Roles**
2. Click en **"Nuevo Role"**
3. Llena el formulario:
   - Name: `QA Engineer`
   - Description: `Quality Assurance Specialist`
4. Click **"Guardar"**
5. ✅ Verifica que aparece en la tabla

### 3. Ejecutar Tests

```bash
# Tests Backend
php artisan test

# Tests específicos
php artisan test --filter=PeopleTest
```

Todos los tests deberían pasar ✅

---

## 🚀 Siguientes Pasos

<div class="grid" markdown>

=== "Desarrollador"
    **Crear tu primer CRUD**
    
    → [Guía: Nuevo CRUD](../development/new-crud-guide.md)
    
    Aprende a crear un módulo completo en 10 minutos.

=== "Arquitecto"
    **Entender la arquitectura**
    
    → [Architecture Overview](../architecture/overview.md)
    
    Profundiza en el diseño del sistema.

=== "QA"
    **Testing Strategy**
    
    → [Testing Guide](../development/testing.md)
    
    Cómo escribir y ejecutar tests.

</div>

---

## 🔧 Troubleshooting

### Error: "Class not found"

```bash
composer dump-autoload
php artisan optimize:clear
```

### Error: "SQLSTATE connection refused"

Verifica que tu `.env` tenga las credenciales correctas:

```bash
php artisan config:clear
php artisan migrate:fresh
```

### Error: Frontend no carga

```bash
# Limpia caché de Vite
rm -rf node_modules/.vite
npm run dev
```

### Puerto 8000 ya en uso

```bash
php artisan serve --port=8001
```

Luego actualiza `VITE_API_BASE_URL` en `.env`:
```env
VITE_API_BASE_URL=http://127.0.0.1:8001
```

---

## 📚 Recursos

- [API Endpoints](../api/endpoints.md) - Documentación completa de la API
- [CRUD Pattern](../development/crud-pattern.md) - Patrón JSON-Driven
- [Commits Semánticos](../development/commits.md) - Convenciones de commits

---

¿Listo? → **[Crea tu primer CRUD →](../development/new-crud-guide.md)**
