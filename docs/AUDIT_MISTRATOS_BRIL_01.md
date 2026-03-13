# Auditoría UX/UI: Mi Stratos (Fase de Refinamiento "Sacar Brillo") 💎

**Fecha**: 13 de Marzo, 2026  
**Módulo**: Mi Stratos (Portal Personal del Talento)  
**Documento Fuente**: `resources/js/pages/MiStratos/Index.vue`  
**Score Actual**: **64/100** (Base de referencia)

---

## 1. Personas & Foco

### Persona Foco: Ana (Colaboradora Individual)
- **Objetivo**: Claridad mediata sobre su valor, sus brechas y su camino a seguir.
- **Necesidad**: Una interfaz que no parezca una "consola de administración", sino un "asistente de carrera".

---

## 2. Historias (Storytelling de Éxito)

1. **"El Café de la Mañana"**: Ana entra 2 minutos. Ve su match (85%), ve que su Potencial subió un 2% y ve un botón fucsia que dice "Completa tu Skill de Python". Cierra satisfecha.
2. **"Preparación para el 1:1"**: Ana explora sus evaluaciones. Ve el gráfico de radar y los comentarios de la IA. Llega a la reunión con datos objetivos, no solo sensaciones.

---

## 3. Flujos de Actividad (Revisados para Brillo)

1. **Estado -> Acción**: Dashboard -> KPI Circle -> Click en Brecha -> Sugerencia de Acción. (Debe ser fluido, < 3 clics).
2. **Reconocimiento**: Dashboard -> Widget Logros -> Talent Pass (Visualización de ADN).

---

## 4. Mapa Visual (Jerarquía Actual vs Ideal)

### Above the fold
- **Actual**: Hero grande con foto y chips. KPI circle a la derecha.
- **Ideal (Brillo)**: Hero con efecto `backdrop-blur-xl` más pronunciado. El KPI circle debe tener una animación de entrada (shimmer) y un glow dinámico basado en el color del score.

---

## 5. Checklist de Refinamiento "Sacar Brillo" 💎

### 5.1 Micro-interacciones
- [ ] **Hover en KPIs**: Los cards de KPI (`kpi-card`) tienen transiciones, pero son básicas. Falta escala y elevación 3D.
- [ ] **Botones**: Uso de `v-btn` (Vuetify) en lugar de `StButtonGlass`.
- [ ] **Expansion Panels**: Comportamiento estándar de Vuetify. Falta pulir el "reflejo de luz" en bordes.

### 5.2 Iconografía Phosphor
- [ ] **Limpieza de MDI**: El código está lleno de `mdi-` (mdi-view-dashboard, mdi-badge-account, mdi-target). **CRÍTICO**: Migrar todo a Phosphor Icons.

### 5.3 Cero Call-to-Actions muertos
- [ ] **Navegación Sidebar**: Funciona bien, pero es estática. Podría tener micro-badge de "notificación" en secciones con actividad.
- [ ] **Next Step Card**: Existe un panel de "Siguiente paso", pero visualmente es un `v-card` plano con fondo semi-transparente manual. Debe ser un `StCardGlass`.

### 5.4 Estados de Carga
- [ ] **Carga Inicial**: Usa un `v-progress-circular` central. **MEJORA**: Implementar Skeletons que dibujen la silueta del Hero y los KPIs.

### 5.5 Jerarquía Z-Axis
- [ ] **Fondo**: Se usa un background slate. Falta inyectar las "holographic accents" (manchas de color desenfocadas) para dar profundidad.

---

## 6. Sistema de Puntuación (Auditoría Brillo)

| Eje | Peso | Score (1-5) | Comentario |
| :--- | :---: | :---: | :--- |
| Claridad & narrativa | 15 | 3 | La historia se entiende, pero el diseño no la "empuja" con fuerza. |
| Carga cognitiva | 15 | 3 | 89KB de archivo Index sugiere demasiada lógica y componentes anidados en un solo sitio. |
| Jerarquía visual | 15 | 3 | Correcta, pero se siente "plana" (Vuetify-style). |
| Feedback & Errores | 10 | 3 | Loading básico. |
| **Consistencia Glass** | 15 | **2** | **Grave**: Dependencia masiva de Vuetify. No cumple el estándar Stratos Glass. |
| Accesibilidad | 10 | 3 | Estándar Vuetify. |
| Eficiencia | 10 | 4 | La estructura de navegación es sólida. |
| Delight (Wow Factor) | 10 | 3 | Se queda en "funcional". Le falta el brillo premium. |
| **Total Ponderado** | 100 | **59** | (Bajamos de 64 a 59 al aplicar criterios de "Brillo" más estrictos). |

---

## 7. Plan de Acción: "Operación Espejo" (Sacar Brillo)

### Nivel 1: Limpieza Estética (Inmediato)
1. **Purga MDI**: Reemplazar todos los `mdi-` por sus equivalentes `Ph` (Phosphor).
2. **Inyección Glass**: Reemplazar los `v-card` del Dashboard por `StCardGlass`.
3. **Refactor de Botones**: Cambiar `v-btn` por `StButtonGlass`.

### Nivel 2: Profundidad y Vida
1. **Background Accents**: Añadir las "luces de fondo" al layout de Mi Stratos.
2. **Animación de KPIs**: Añadir `tailwindcss-animate` a los cards de KPI para que entren con un fade-in escalonado.

### Nivel 3: Arquitectura (Sacar Brillo al Código)
1. **Modularización**: El archivo `Index.vue` es inmanejable. Extraer `DashboardSection`, `RoleSection`, `GapsSection` a componentes hijos para mejorar performance y mantenibilidad.

---
> **Estado de la Certificación**: ⚠️ **NO CERTIFICADO (Falta Brillo)**
