# Stratos Glass Design System 🌌

This document outlines the **Stratos Glass** UX/UI design guidelines and development standards. It serves as the single source of truth for creating and refactoring Vue.js components within the Stratos platform.

## 1. Core Philosophy

Stratos focuses on a **premium, dark-mode-first, glassmorphism aesthetic** that ensures an immersive, modern, and data-dense user experience. Every component should feel like an advanced operating system rather than a standard web page.

- **Look & Feel**: Tech-forward, analytical, deep space colors with vibrant holographic accents.
- **Micro-interactions**: Subtle but deliberate animations provide immediate, satisfying feedback without overwhelming the user.
- **Glassmorphism**: Layering, translucency, and frosted glass effects guide hierarchy and structure.

## 2. Global Design Tokens (Tailwind CSS)

### 2.1 Color Palette

- **Backgrounds (Base Levels)**: Deep slate and indigo (`bg-[#0f172a]`, `bg-slate-900`).
- **Surface Layers (Glass Panels)**: White or colored semi-transparent layers.
    - _Standard_: `bg-white/5 border-white/10`
    - _Elevated/Focused_: `bg-white/10 border-white/20`
- **Accent Colors**:
    - Indigo/Cyan (Primary tech vibe)
    - Emerald/Teal (Success, Synergy, Positive metrics)
    - Pink/Fuchsia (Remediation, AI actions, Sentinel mode)
    - Orange/Amber (Friction, Warnings, Organic Insights)

### 2.2 Typography

- **Headings**: High contrast (`text-white`), often combined with bold or black font weights (`font-black`, `tracking-tight`).
- **Labels & Metatags**: Extremely small but highly readable (`text-[9px]` or `text-[10px]`), uppercase (`uppercase`), widely spaced (`tracking-widest`), and low opacity (`text-white/50`).
- **Body Text**: Muted to reduce eye strain (`text-white/80` or `text-white/70`).

### 2.3 Shadows & Glows

- Instead of traditional hard drop shadows, use colored glows for active or important items.
- Example: `shadow-[0_0_15px_rgba(236,72,153,0.2)]` for a pink button glow.
- Standard panel shadow: `shadow-[0_10px_40px_-10px_rgba(0,0,0,0.5)]`
- **Glow Line (Indicator)**: Use a 1px top or bottom line with a colored glow to indicate active modules or premium sections. 
    - Pattern: `h-px w-full bg-linear-to-r from-transparent via-current to-transparent` + `boxShadow: 0 0 20px [color]`.

## 3. Stratos UI Components

Avoid standard Vuetify components (`v-card`, `v-btn`) wherever possible when building the core "Stratos" experience, and use our custom Vue components instead.

### `StCardGlass.vue`

Use for containers, modals, and panel wrappers.

- _Behavior_: Handles the heavy frosted glass effect `backdrop-blur-xl`, borders, and base background tinting.
- **New Padding Standards (Premium Standard)**:
    - **KPIs / Small Info Cards**: Use `p-12!` or `!p-12` (48px) for maximum breathing room.
    - **Hero / Main Action Panels**: Use `p-16!` or `!p-16` (64px) to create a spacious, luxury feel.
    - **List Items / Sub-cards**: Use `p-8` (32px) to maintain density while remaining airy.
    - **Grid Gaps**: Maintain a minimum of `gap-8` for secondary grids and `gap-12` for primary sections.

### `StButtonGlass.vue`

Use for all primary and secondary actions.

- _Variants_: `primary`, `secondary`, `ghost`, `glass`.
- _Props_: `icon` (Phosphor component), `size` (`sm`, `md`, `lg`), `loading`, `disabled`.
- _Usage_: `<StButtonGlass :icon="PhCube" variant="primary">Action</StButtonGlass>`

### `StBadgeGlass.vue`

Use for statuses, tagging, and small data point indicators.

- _Variants_: `success`, `warning`, `info`, `default`.

## 4. Animation & Transitions

Leverage `tailwindcss-animate` utility classes to inject smooth entries and exits.

- **Component Entry**: `<div class="animate-in fade-in slide-in-from-top-2 duration-500">`
- **Hover States**: Apply `transition-all duration-300` along with opacity or color changes (`hover:bg-white/10`).
- **Active AI Activity**: Use `.animate-pulse` on badges or borders gently to show processing.

## 5. Development Standards & Refactoring Rules

1. **Remove Vuetify dependencies gradually**: Strip out `v-row`, `v-col`, `v-card`, and `v-btn`. Use native HTML tags styled with Tailwind utility classes (`flex`, `grid`, `space-y-4`).
2. **Icons**: Use **Phosphor Icons** (`@phosphor-icons/vue`).
    - _Import_: `import { PhCube, PhUser } from '@phosphor-icons/vue';`
    - _Standard usage_: `<component :is="PhIconName" :size="20" />` or pass as prop to `StButtonGlass`.
    - _Styling_: Wrap in colored glass containers for consistent aesthetic.
3. **i18n Ready**: Hardcoded text is forbidden.
    - All user-facing text must be passed through the vue-i18n function: `{{ $t('key') }}`.
    - For complex components like `FormSchema`, use keys under `form_schema`.
    - Add translations to `resources/js/i18n.ts` in both `en` and `es`.
4. **Composition API**: Use `<script setup lang="ts">`.
5. **Linting Check**: Before committing or concluding a feature, verify variables and exceptions, ensuring zero unused imports or unused `catch` placeholders (`e` -> `_e`).

## 6. Fase de Refinamiento: "Sacar Brillo" 💎

Una vez que el módulo es funcional, entramos en la fase de **Excelencia Refractiva**. El objetivo es eliminar cualquier rastro de "software tradicional" y convertir la interfaz en una experiencia de vanguardia.

### 6.1 Ejes de Refinamiento Técnico

- **Micro-interacciones Proactivas**: 
    - Cada elemento interactivo debe tener un estado `:hover` y `:active` definido con transiciones suaves (`duration-300`).
    - Los botones no solo cambian de color; ganan un sutil resplandor (`glow`) o cambian la opacidad del borde.
- **Iconografía Impecable (100% Phosphor)**: 
    - Prohibido el uso de MDI o iconos genéricos.
    - Uso consistente de pesos (ej. `Regular` vs `Duotone` para estados activos).
- **Feedback Visual Continuo**:
    - **Skeletons Premium**: Usar `LoadingSkeleton.vue` que imite la estructura real de los datos.
    - **Validaciones en Vivo**: El usuario debe recibir feedback antes de intentar enviar un formulario.
- **Narrativa de IA (Human-AI Mix)**:
    - Las acciones realizadas por Agentes deben distinguirse visualmente (uso del color Fuchsia/Pink y efectos de pulso).

### 6.2 Checklist de "Brillo"

- [ ] **Sin Call-to-Actions muertos**: ¿Cada vista guía proactivamente al siguiente paso?
- [ ] **Empty States de Autoridad**: ¿Las listas vacías inspiran acción en lugar de mostrar vacío?
- [ ] **Jerarquía de Capas (Z-Axis)**: ¿Se percibe claramente qué paneles están "encima" de otros mediante `backdrop-blur` y sombras suaves?
- [ ] **Copywriting Estratégico**: ¿Los textos hablan de "Talento" y "Estrategia" o solo de campos técnicos?

## 7. Modal System: Floating Glass 💎

Para asegurar que todas las ventanas emergentes mantengan la estética premium, hemos estandarizado el patrón **Floating Glass**. Este diseño utiliza capas suspendidas con scroll interno y gradientes cinéticos.

Consulte la **[Guía de Implementación de Modales Glass](./MODAL_GLASS_IMPLEMENTATION.md)** para especificaciones técnicas, clases CSS globales y ejemplos de estructura.

---

> [!TIP]
> **"Sacar brillo"** no es decorar; es remover la fricción visual y cognitiva para que el valor estratégico de Stratos brille sin obstáculos.
