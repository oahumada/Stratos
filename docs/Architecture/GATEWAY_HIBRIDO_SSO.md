# 🚪 Gateway Híbrido: Autenticación Passwordless & SSO

**Status:** ✅ Implementado (Backend & Integración Socialite)  
**Fecha:** 8 de Marzo de 2026  
**Versión:** 1.0

---

## 📋 1. Visión General

El **Gateway Híbrido** de Stratos está diseñado para eliminar la fricción del inicio de sesión tradicional (password) y facilitar el acceso empresarial de forma segura. El concepto es "Híbrido" porque soporta múltiples métodos de entrada según el perfil del usuario:

1.  **SSO (Single Sign-On):** Para usuarios corporativos (Google Business, Microsoft Azure/Office 365).
2.  **Magic Links:** Para usuarios externos, consultores o invitados sin SSO corporativo.
3.  **Legacy Password:** (Opcional/Backup) Para acceso administrativo de emergencia.

---

## 🏗️ 2. Arquitectura de Autenticación

### A. Flujo SSO (Oauth2)

Utilizamos **Laravel Socialite** para gestionar los apretones de manos con los proveedores externos.

- **Endpoint Redirect:** `/auth/{provider}/redirect`
- **Endpoint Callback:** `/auth/{provider}/callback`
- **Lógica de Vinculación:** El sistema busca al usuario por `provider_id`. Si no lo encuentra, busca por `email`. Si el email coincide, "vincula" la cuenta SSO al perfil existente en Stratos. Esto evita la duplicidad de cuentas.

### B. Flujo Magic Link (Firmado)

Para usuarios sin SSO, Stratos genera una URL firmada temporal (TTL: 15-30 min) que se envía por email.

- **Seguridad:** Utiliza `URL::temporarySignedRoute`, lo que garantiza que el enlace no puede ser manipulado ni reutilizado tras su expiración.
- **Controller:** `MagicLinkController.php`

---

## 🛠️ 3. Implementación Técnica

### Nuevos Campos en `users`

| Campo           | Tipo        | Propósito                                         |
| :-------------- | :---------- | :------------------------------------------------ |
| `provider_name` | `string`    | Nombre del proveedor (ej: 'google', 'microsoft'). |
| `provider_id`   | `string`    | ID único retornado por el proveedor.              |
| `last_login_at` | `timestamp` | Auditoría de último acceso.                       |

### Orquestador: `SsoController.php`

Este controlador maneja la creación proactiva de usuarios si el SSO es exitoso pero el usuario no existe (configurable según la política de la organización).

---

## 🎨 4. Integración en UI (Frontend)

El componente de Login debe presentar:

- **Botones Principales:** "Continuar con Google", "Continuar con Microsoft".
- **Fallback:** "O envíame un enlace de acceso por email" (Input de email + Botón Magic Link).
- **Administrador:** Link discreto para acceso tradicional (si está habilitado).

---

## 🚀 5. Configuración Requerida (.env)

Para habilitar los servicios, se deben configurar las siguientes variables:

```env
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI=https://app.stratos.ai/auth/google/callback

MICROSOFT_CLIENT_ID=...
MICROSOFT_CLIENT_SECRET=...
MICROSOFT_REDIRECT_URI=https://app.stratos.ai/auth/microsoft/callback
```

---

> _"Seguridad no es añadir barreras, sino simplificar el acceso legítimo."_
