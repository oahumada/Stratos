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
  - *Standard*: `bg-white/5 border-white/10`
  - *Elevated/Focused*: `bg-white/10 border-white/20`
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

## 3. Stratos UI Components
Avoid standard Vuetify components (`v-card`, `v-btn`) wherever possible when building the core "Stratos" experience, and use our custom Vue components instead.

### `StCardGlass.vue` 
Use for containers, modals, and panel wrappers. 
- *Behavior*: Handles the heavy frosted glass effect `backdrop-blur-xl`, borders, and base background tinting.

### `StButtonGlass.vue`
Use for all primary and secondary actions.
- *Variants*: `primary`, `secondary`, `ghost`, `danger`.

### `StBadgeGlass.vue`
Use for statuses, tagging, and small data point indicators.
- *Variants*: `success`, `warning`, `info`, `default`.

## 4. Animation & Transitions
Leverage `tailwindcss-animate` utility classes to inject smooth entries and exits.
- **Component Entry**: `<div class="animate-in fade-in slide-in-from-top-2 duration-500">`
- **Hover States**: Apply `transition-all duration-300` along with opacity or color changes (`hover:bg-white/10`).
- **Active AI Activity**: Use `.animate-pulse` on badges or borders gently to show processing. 

## 5. Development Standards & Refactoring Rules

1. **Remove Vuetify dependencies gradually**: Strip out `v-row`, `v-col`, `v-card`, and `v-btn`. Use native HTML tags styled with Tailwind utility classes (`flex`, `grid`, `space-y-4`).
2. **Icons**: Continue using `v-icon` (MDI icons) but wrap them in custom stylized Tailwind containers (e.g., `<div class="h-10 w-10 shrink-0 flex items-center justify-center rounded-xl bg-orange-500/20 border-orange-500/30"> <v-icon color="orange-400"/> </div>`).
3. **i18n Ready**: Hardcoded text is forbidden.
   - All user-facing text must be passed through the vue-i18n function: `{{ $t('key') }}`.
   - Add the translations directly to `resources/js/i18n.ts` under both `es` and `en` blocks.
4. **Composition API**: Use `<script setup lang="ts">`.
5. **Linting Check**: Before committing or concluding a feature, verify variables and exceptions, ensuring zero unused imports or unused `catch` placeholders (`e` -> `_e`).
