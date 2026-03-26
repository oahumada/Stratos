# 🌌 **Talent Pass Frontend - Guía UX/UI diseño Stratos Glass**

Documento de diseño que incorpora los **5 Pilares de Stratos Vanguard** + **Stratos Glass Design System** para la implementación del módulo Talent Pass (CV 2.0).

---

## 📋 **1. Personas & Narrativa**

### 1.1 Persona Principal: **Profesional en Transición**

- **Edad**: 28-45 años
- **Rol**: Dev, Manager, Skills Coach
- **Motivación**: "Quiero que el mundo vea mi talento de forma integral, sin limitarme a un CV plano"
- **Pain Point**: CVs estáticos, desconexión entre habilidades y experiencia real, dificultad para compartir logros no convencionales

### 1.2 Narrativa (Storytelling)

```
🎬 ACTO 1 - EL DESCUBRIMIENTO:
El usuario entra a "Mi Talent Pass" por primera vez y ve un dashboard 
vacío. Una tarjeta glass frente a él dice:
  "Tu perfil profesional es un edificio. Construyámoslo juntos."
  → CTA primario: "Comenzar a construir"

🎬 ACTO 2 - LA CONSTRUCCIÓN:
El usuario agrega skills, experiencias, credenciales. Cada elemento 
aparece con un shimmer effect, como si se "materializara" en tiempo real.
La interfaz muestra el "porcentaje de completitud" de su Talent Pass 
mediante un glow progresivo.

🎬 ACTO 3 - LA REVELACIÓN:
Una vez publicado, el usuario puede generar una URL pública, exportar 
a LinkedIn, o compartir su Talent Pass como "ADN Digital".
```

---

## 🏛️ **2. Los 5 Pilares Aplicados a Talent Pass**

### **Pilar 1: Jerarquía Visual & Profundidad (Glassmorphism 2.0)**

#### Elevación Semántica (Analogía Arquitectónica)

```
┌─────────────────────────────────────────────────────────┐
│                                        ELEVACIÓN MÁXIMA
│  ┌──────────────────────────────────────────────┐
│  │  Talent Pass (Edificio)                      │  p-16 | backdrop-blur-xl
│  │  ├─ Title + Summary (Cimientos visibles)    │  bg-white/10 border-white/20
│  │  │
│  │  ├─ Skills Section (Pisos)                  │  >>> SECUNDARIA
│  │  │  ├─ Skill Card 1 (Ladrillo)             │  >>> TERCIARIA
│  │  │  ├─ Skill Card 2                        │  >>> TERCIARIA
│  │  │
│  │  ├─ Experience Section                      │
│  │  │  ├─ Job Card (Ladrillo activo)          │  with glow-line
│  │  │
│  │  └─ Credential Section                      │
│  │     ├─ Badge Card (Ladrillo certificado)   │
│  └──────────────────────────────────────────────┘
│
└─────────────────────────────────────────────────────────┘
```

**Implementación CSS:**

```vue
<!-- NIVEL 1: Main Card (Hero-level elevation) -->
<StCardGlass class="p-16 bg-white/10 border-t border-white/20">
  <!-- Indicator Light: Línea de luz (firma de Gold-tier) -->
  <div class="h-px w-full bg-gradient-to-r from-transparent via-blue-400 to-transparent 
              opacity-60 shadow-[0_0_20px_rgba(96,165,250,0.3)]" />
</StCardGlass>

<!-- NIVEL 2: Subsection (Secondary glass) -->
<section class="space-y-4">
  <div class="flex items-center gap-2 mb-4">
    <div class="h-px flex-1 bg-white/10" />
    <h3 class="text-sm uppercase tracking-widest font-light text-white/70">Skills</h3>
    <div class="h-px flex-1 bg-white/10" />
  </div>
</section>

<!-- NIVEL 3: List items (glass-lite) -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <StCardGlass class="p-8 bg-white/5 border-white/10">
    <!-- Skill item -->
  </StCardGlass>
</div>
```

### **Pilar 2: Micro-interacciones de Feedback Vivo**

#### Loading States: Shimmer Progressivo

```vue
<!-- Skeleton que imita la forma de SkillCard -->
<div v-if="loading" class="animate-pulse space-y-3">
  <div class="h-6 bg-white/10 rounded-lg animate-shimmer" />
  <div class="h-4 bg-white/5 rounded-lg w-3/4" />
  <div class="flex gap-2">
    <div class="h-6 bg-white/5 rounded-full flex-1" />
    <div class="h-6 bg-white/5 rounded-full flex-1" />
  </div>
</div>
```

#### Hover Glow Inteligente

```vue
<div class="group relative cursor-pointer transition-all duration-300">
  <!-- Skill card que emite glow al hover -->
  <StCardGlass class="p-6 bg-white/5 border-white/10 
                      group-hover:bg-white/10 group-hover:border-white/20
                      group-hover:shadow-[0_0_30px_rgba(99,102,241,0.2)]
                      transition-all duration-300">
    <div :class="`text-sm font-semibold group-hover:text-${skillColor}-300`">
      {{ skill.name }}
    </div>
  </StCardGlass>
</div>
```

### **Pilar 3: Tipografía & Espacio en Blanco**

#### Conciencia Espacial (Luxury Mindset)

```vue
<div class="space-y-12">  <!-- Máximo espaciado entre secciones -->
  
  <!-- HERO SECTION -->
  <section class="p-16">  <!-- p-16 = 64px = espaciado de autoridad -->
    <h1 class="text-4xl font-black tracking-tight text-white mb-4">
      {{ talentPass.title }}
    </h1>
    <p class="text-white/70 leading-relaxed max-w-2xl text-lg">
      {{ talentPass.summary }}
    </p>
  </section>

  <!-- SUBSECTION -->
  <section class="p-12 space-y-6">  <!-- p-12 = 48px para info cards -->
    <div class="flex items-center gap-4 mb-6">
      <component :is="PhStack" class="text-indigo-400" :size="24" />
      <h2 class="text-xl font-bold text-white">Skills</h2>
      <div class="ml-auto text-xs uppercase tracking-widest text-white/50 font-light">
        {{ skillsCount }} / {{ skillsTarget }}
      </div>
    </div>
    <!-- Items -->
  </section>

</div>
```

### **Pilar 4: Visualización del ADN (Human-AI Mix)**

#### Componente: "Completeness Meter" (Visual de Progreso)

```vue
<!-- Muestra el % de completitud con gradiente dinámico -->
<div class="relative h-2 bg-white/10 rounded-full overflow-hidden">
  <!-- Gradient que representa el avance -->
  <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"
       :style="{ width: `${completeness}%` }"
       :class="{ 'animate-pulse': isUpdating }">
    <!-- Shimmer effect para AI processing -->
    <div v-if="isUpdating" class="absolute inset-0 animate-shimmer" />
  </div>
</div>

<!-- Microcopy estratégica -->
<div class="flex justify-between items-center mt-2">
  <span class="text-xs text-white/50">{{ completeness }}% Completitud</span>
  <span class="text-xs text-indigo-300 font-medium">
    {{ completenessLabel }} ← Syncing Neural
  </span>
</div>
```

### **Pilar 5: Narrativa de Diseño (Metáfora Arquitectónica)**

#### Tabla de Estructura Visual

| Elemento         | Metáfora      | Componente UI              | Comportamiento                              |
|------------------|---------------|----------------------------|---------------------------------------------|
| **Talent Pass**  | Edificio      | Main Card (Hero-elevation) | Fijo, visible siempre                       |
| **Sections**     | Pisos         | Secondary Glass Cards      | Expandible, colapsable                      |
| **Skills**       | Ladrillos     | Tertiary Cards + Bullets   | Hover glow, draggable en futuro              |
| **Completeness** | Construcción  | Progress bar con gradiente | Anima al agregar contenido                  |

---

## 🎨 **3. Palette de Colores (Stratos Glass)**

```
BACKGROUNDS:
  Deep: bg-[#0f172a]     (base, rarely used)
  Slate: bg-slate-900    (primary bg)

GLASS SURFACES:
  Standard:  bg-white/5  border-white/10
  Elevated:  bg-white/10 border-white/20
  Focused:   bg-white/15 border-white/30 (on hover/active)

ACCENTS:
  Primary:   Indigo-500   (#6366f1) - Acciones, CTAs
  Success:   Emerald-400  (#10b981) - Skills confirmados
  Warning:   Amber-400    (#f59e0b) - Crédenciales próximas a expirar
  Error:     Rose-400     (#f43f5e) - Validaciones fallidas
  AI/Sync:   Purple-500   (#a855f7) - Procesos de sincronización

TEXT:
  Headings:     text-white           (font-black 900)
  Body:         text-white/80        (font-normal 400)
  Labels:       text-white/50        (font-light 300, uppercase, tracking-widest)
  Muted:        text-white/30        (para secundaria)
  Accented:     text-{color}-300     (estados active/hover)
```

---

## 📐 **4. Breakpoints & Responsive**

```
xs (Mobile):  < 640px    → single-column, full-width cards
sm (Mobile):  640-768px  → 1.5 column experimental
md (Tablet):  768-1024px → 2-column layout
lg (Desktop): 1024-1280px → 3-column grid (skills)
xl (Large):   > 1280px   → 4-column grid + sidebar
```

**Estrategia:**
- Primero diseñar para mobile (cards apiladas)
- Tablet: Grid de 2 columnas para subsecciones
- Desktop: Grid de 3 columnas + viewport lateral (share/export)

---

## 🔧 **5. Componentes Vue 3 + TypeScript (Stratos Glass)**

### 5.1 Componentes Principales

```
resources/js/components/TalentPass/
├── TalentPassCard.vue              (Card principal reutilizable)
├── SkillsManager.vue               (CRUD + Drag-drop de skills)
├── ExperienceManager.vue           (Timeline de experiencias)
├── CredentialManager.vue           (Certificaciones + expiry)
├── CompletenessIndicator.vue       (Barra de progreso con glow)
├── ExportMenu.vue                  (PDF/LinkedIn/JSON con dropdown)
└── ShareDialog.vue                 (Modal de sharing con ULID)
```

### 5.2 Stack de Dependencias

```
✅ Vue 3 + Composition API (<script setup>)
✅ TypeScript (strict mode)
✅ Headless UI / Pinia (state management)
✅ Tailwind CSS v4 (no Vuetify)
✅ Phosphor Icons (@phosphor-icons/vue)
✅ vue-i18n (zero hardcoded text)
✅ ApexCharts (para visualización de trending skills - futuro)
```

---

## 📖 **6. Flujos de Interacción Principales**

### 6.1 Flujo: Crear Nuevo Talent Pass

```
Usuario: Click en "Nuevo Talent Pass"
  ↓
Sistema: Modal Glass aparece con fade-in
  ├─ Input: Título (required)
  ├─ Input: Resumen (optional)
  └─ Select: Visibilidad (private/public)
  ↓
Usuario: Completa form → Click "Crear"
  ↓
Sistema: 
  1. Muestra skeleton loading con shimmer
  2. Llama POST /api/talent-passes
  3. En éxito: Redirige a página de edición con toast de éxito
  4. En error: Toast rojo con mensaje i18n
```

### 6.2 Flujo: Agregar Skill

```
Usuario: Click en "+ Agregar Skill" dentro de SkillsManager
  ↓
Sistema: Inline form aparece (o expando)
  ├─ Autocomplete: Nombre de skill (busca en catálogo)
  ├─ Select: Nivel (beginner, intermediate, expert, master)
  ├─ Input: Años de experiencia (opcional)
  └─ Botones: Agregar | Cancelar
  ↓
Usuario: Llena form → Click "Agregar"
  ↓
Sistema:
  1. Skelton loading
  2. POST /api/talent-passes/{id}/skills
  3. Card aparece con entrada suave (slide-in + fade-in)
  4. Glow inteligente en hover
```

### 6.3 Flujo: Compartir Talent Pass

```
Usuario: Click "Compartir" → ExportMenu
  ↓
Sistema: Dropdown Glass aparece con opciones:
  ├─ 🔗 Copiar enlace público (ULID-based)
  ├─ 📄 Exportar PDF
  ├─ 💼 Exportar LinkedIn JSON
  └─ 📊 Ver estadísticas (views, completeness)
  ↓
Usuario: Selecciona "Copiar enlace"
  ↓
Sistema:
  1. POST /api/talent-passes/{id}/share
  2. URL pública se copia al portapapeles
  3. Toast: "✓ Enlace copiado"
```

---

## ✅ **7. Checklist UX/UI (Stratos Vanguard)**

### Claridad (5 segundos rule)

- [ ] En 5 segundos se entiende: "Este es mi perfil profesional integral"
- [ ] Un CTA inequívoco en dashboard vacío: "+ Comenzar a construir"
- [ ] Breadcrumb o navegación clara si hay subsecciones

### Enfoque

- [ ] Secciones principales (Skills, Experience, Credentials) NO saturan la vista inicial
- [ ] Información secundaria (metadata, timestamps) está en nivel visual tertiary
- [ ] El % de completitud es el "heartbeat" visual que guía la atención

### Consistencia visual (Stratos Glass)

- [ ] Todos los cards usan `StCardGlass` con `backdrop-blur-xl`
- [ ] Botones primarios usan `StButtonGlass` variant="primary"
- [ ] Badges de status usan `StBadgeGlass`
- [ ] 100% Phosphor Icons (¡sin MDI!")
- [ ] Paleta de colores estricta (Indigo/Emerald/Amber/Rose)

### Emoción / Motivación

- [ ] Microcopy estratégica: "Sintetizando tu ADN profesional..."
- [ ] Cada acción completada muestra feedback positivo (toast + glow)
- [ ] Progress meter aumenta visiblemente al agregar contenido

### "Sacar Brillo" 💎

- [ ] Todos los hover states definen transiciones smooth (duration-300)
- [ ] Skeletons respetan la forma del contenido final
- [ ] Iconografía Phosphor en 2-3 tamaños (sm/md/lg) consistentemente
- [ ] Indicador light (glow line) en headers principales

---

## 🎭 **8. Micro-copy Estratégica (Cyborg Style)**

```
Empty State / First Time:
  "Tu perfil profesional es un edificio.\n
   Construyámoslo juntos, ladrillo a ladrillo."

Adding a Skill:
  "Conocimiento adquirido → Sincronizando Neural"

Publishing:
  "Sellando Tu ADN Digital..." → "¡Publicado con éxito!"

Export Success:
  "ADN transpuesto a formato PDF" / "Perfil LinkedIn actualizado"

Error Validation:
  "Ladrillo sin mortero: El título es requerido"
```

---

## 🚀 **9. Próximos Pasos (Implementación)**

### Fase 1: Estructura Base (2 horas)
- [ ] Crear `TalentPassCard.vue` con estructura glass básica
- [ ] Store Pinia con state management
- [ ] Types en `src/types/talentPass.ts`
- [ ] Rutas en `src/Pages/TalentPass/`

### Fase 2: Componentes & Managers (3 horas)
- [ ] SkillsManager + ExperienceManager + CredentialManager
- [ ] Integración con API (/talent-passes endpoints)
- [ ] Formularios con validación inline
- [ ] Toast notifications

### Fase 3: Micro-interacciones & Polish (2 horas)
- [ ] Shimmer loading states
- [ ] Hover glows + transiciones
- [ ] Dark mode support
- [ ] Accessibility (ARIA labels, keyboard nav)

### Fase 4: Testing & Deployment (1.5 horas)
- [ ] Vitest + @vue/test-utils
- [ ] E2E tests con Pest v4 browser tests
- [ ] Performance audit (Lighthouse)
- [ ] Staging deployment

---

**Fecha de Inicio:** Mar 31, 2026  
**Deadline:** Apr 19 (Staging) → Apr 21 (Production)  
**Owner:** Frontend Team  
**Design Review:** [STRATOS_GLASS_DESIGN_SYSTEM.md](./STRATOS_GLASS_DESIGN_SYSTEM.md) + [UX_UI_VANGUARD_PRINCIPLES.md](./DesignSystem/UX_UI_VANGUARD_PRINCIPLES.md)
