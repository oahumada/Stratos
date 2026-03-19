# 🌱 Compliance Demo Seeder - Guía de Instalación

## 📋 Qué Hace Este Seeder

El `ComplianceDemoSeeder` popula tu base de datos con datos realistas para demostrar toda la potencia del **Compliance Audit Dashboard**:

### Datos Que Crea

| Entidad | Cantidad | Propósito |
|---------|----------|----------|
| **Organization** | 1 | Tenant: "Stratos Demo Corporation" |
| **Departments** | 6 | Engineering, Operations, RRHH, Ventas, Finance, Innovation |
| **Skills** | 12 | Técnicas (Cloud, Data) + Transversales (Leadership, Communication) + Gobernanza |
| **Critical Roles** | 24 | 12 con firma vigente, 6 expiradas, 6 sin firma → Muestra riesgo y cumplimiento |
| **People** | ~89 | Distribuidos en roles (3-5 por role) → ~$48.2M costo reemplazo |
| **Role-Skill Assignments** | ~350+ | Cada persona: 4-7 skills con brechas (current_level < required_level) |
| **Event Store (Audit Trail)** | 200+ | 70% en últimas 24h, 30% históricos → Muestra actividad reciente |
| **Verifiable Credentials** | 5 | VC/JSON-LD exportables para roles firmados →  Demuestra auditoría externa |

---

## 🚀 INSTALACIÓN

### Paso 1: Asegurate de estar en `src/`

```bash
cd /home/omar/Stratos/src
```

### Paso 2: (OPCIONAL) Resetear la BD si quieres limpiar

```bash
# ⚠️ SOLO si quieres empezar desde cero
php artisan migrate:refresh --seed
```

### Paso 3: Ejecutar SOLO el ComplianceDemoSeeder

```bash
# Ejecuta solo este seeder (sin resetear nada)
php artisan db:seed --class=ComplianceDemoSeeder
```

### Esperado: Output Success

```
✅ ComplianceDemoSeeder completed successfully!
📊 Organization: Stratos Demo Corporation
👥 People: 89
💼 Roles: 24
📋 Skills: 12
📝 Events: 200+
```

---

## 🎯 VERIFICACIÓN POST-EJECUCIÓN

### 1️⃣ Verificar que los datos se cargaron

```bash
# Desde `src/`

# Contar eventos
php artisan tinker
>>> use App\Models\EventStore;
>>> EventStore::count()
# Resultado esperado: 200+

# Ver organization
>>> use App\Models\Organization;
>>> Organization::where('slug', 'stratos-demo-corp')->first()
# Resultado: "Stratos Demo Corporation"

# Ver roles críticos
>>> use App\Models\Roles;
>>> Roles::where('organization_id', 1)->count()
# Resultado: 24
```

### 2️⃣ Navegar al Dashboard

```url
http://localhost:8000/quality/compliance-audit
```

**Deberías ver:**
- ✅ **Resumen Audit Trail**: ~200 eventos totales, ~140 en últimas 24h
- ✅ **ISO 30414 KPIs**: ~$48.2M costo reemplazo, 8 skills gaps
- ✅ **Internal Audit Wizard**: 24 roles críticos, 87.5% cumplimiento (21 vigentes)
- ✅ **Tabla de Eventos**: Filtrable, eventos recientes en la parte superior
- ✅ **Credencial Export**: Roll ID 1-5 deberían tener credenciales exportables

---

## 🎬 DEMO CHECKLIST CON DATOS REALES

**Después de ejecutar el seeder, puedes demostrar:**

### ✅ Audit Trail Completo
```
Filtro: event_name = "role.updated"
→ Muestra eventos de cambios de roles
→ Ves actor (Admin Omar), timestamp exacto
```

### ✅ Riesgo de Talento Cuantificado
```
KPI: Costo Total = $48.2M (aprox)
KPI: Costo Promedio = $150K/persona
Tabla: Departamentos rankeados por readiness (Engineering 65%, Ops 72%, etc.)
```

### ✅ Gobernanza Verificable
```
Total Roles Críticos: 24
Roles Cumpliendo: 21 (87.5%)
Roles Expired: 6 (CRO, VP Technology, etc.)
Roles Missing: 6 (Sin firma)
→ Demuestra riesgo real + oportunidad de remediación
```

### ✅ Credencial Exportable
```
Role ID: 1 (VP Talento)
→ Exportar VC → Muestra JSON-LD
→ Verificar → ✅ VC Válida (4 checks pass)
```

---

## 🔧 CUSTOMIZACIÓN (Opcional)

### Si quieres cambiar cantidades:

Edita [database/seeders/ComplianceDemoSeeder.php](database/seeders/ComplianceDemoSeeder.php):

```php
// Línea ~180: Cambiar cantidad de people
$assignmentCount = rand(3, 5);  // Cambiar a rand(5, 10) para más

// Línea ~240: Cambiar cantidad de eventos
for ($i = 0; $i < 140; $i++) {  // Cambiar a 500 para más eventos
    // ...
}

// Línea ~255: Cambiar días históricos
$daysAgo = rand(2, 30);  // Cambiar a rand(2, 90) para más histórico
```

---

## ⚠️ IMPORTANTE: SANDBOX/DEMO ONLY

⚠️ **Este seeder es para DEMO/TESTING solamente:**
- Los datos NO son auténticos (IPs fake, firmas mock)
- Los costos de reemplazo son ESTIMADOS
- Use en ambiente de desarrollo/staging, NO en producción

Para producción:
1. Los eventos se generan automáticamente (cada cambio en el sistema)
2. Las firmas se crean mediante `RoleDesignerService::finalizeRoleApproval`
3. Los costos se calculan desde datos reales de salarios

---

## 🎓 PRÓXIMOS PASOS

1. ✅ Ejecutar seeder
2. ✅ Navegar a dashboard
3. ✅ Probar filtros (event_name, aggregate_type)
4. ✅ Exportar credencial (Role ID: 1)
5. ✅ Verificar firma
6. ✅ Usar la **Guía de Interpretación** (docs/) para explicar números

---

## 📞 TROUBLESHOOTING

| Problema | Causa | Solución |
|----------|-------|----------|
| "Class not found" | Seeder no existe | Verifica que archivo está en `database/seeders/` |
| "Foreign key constraint" | Organización no existe | Ejecuta `php artisan migrate` primero |
| "Tabla no existe" | Migraciones no corrieron | `php artisan migrate` |
| "Cero eventos" | Seeder no corrió | Verifica output de `db:seed` |

---

**Última actualización**: 19 de marzo 2026  
**Tested on**: Laravel 12, PHP 8.4  
**Author**: Copilot Agent
