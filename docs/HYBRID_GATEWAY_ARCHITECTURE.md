# Gateway Híbrido de Acceso y Sovereign Identity

> Arquitectura de Autenticación Unificada para Stratos (Wave 3 / Fase 0)

Para posicionar a Stratos como una solución verdaderamente _Enterprise_, el proceso de inicio de sesión no puede depender únicamente de contraseñas u OTPs. Debe integrarse naturalmente al ecosistema de identidades del cliente (SSO) y, al mismo tiempo, sentar las bases para la portabilidad del talento hacia la Web3 (Sovereign Identity).

---

## 🏗️ 1. Arquitectura del "Gateway Híbrido"

El Gateway Híbrido consolida múltiples proveedores de identidad bajo un mismo controlador, mitigando la fricción de entrada:

1. **Magic Links (Existente):** Orientado a empresas pequeñas o colaboradores externos que no están en el Directorio Activo. El link se genera vía OTP seguro y ruta firmada de Laravel.
2. **SSO / SAML / OAuth2 (Nuevo):**
    - **Librería de Consolidación:** Laravel Socialite (implementado/requerido). SocialiteProviders (opcional para integraciones raras).
    - **Proveedores de Nivel 1:** Azure AD (Entra ID), Okta, y Google Workspace.
    - **Comportamiento:** Si un usuario pertenece a `miempresa.com`, el sistema intercepta el intento de Magic Link/Password y lo redirige automáticamente al flujo de OIDC (OpenID Connect) del IDP (Identity Provider) correspondiente.

### Flujo de Datos

1. El usuario introduce su correo (`omar@acme.corp`).
2. El sistema busca el Tenant (Organization) vinculado a `@acme.corp`.
3. Revisa la columna JSONB `auth_settings`: `{"provider": "azure", "tenant_id": "xxx"}`.
4. Genera el Challenge PKCE, redirige a Azure AD.
5. Retorno: Laravel Socialite autentica el payload, inyecta/crea el `$user` y asigna el token de Inertia.

---

## 🛡️ 2. Sovereign Identity (Identidad Soberana del Talento)

El concepto más disruptivo del ecosistema Stratos. En el futuro, el "CV" no estará almacenado estáticamente en una base de datos central. Pertenece al empleado.

### Modelo de Control Conceptual (W3C Verifiable Credentials)

1. **El Empleado como "Holder":** Las capacitaciones de _Stratos Grow_ y los logros o niveles de _Stratos 360_ se transforman en "Verifiable Credentials" (VC).
2. **Stratos como "Issuer":** La plataforma emite criptográficamente (bajo estándares W3C) un certificado que avala que el usuario "Juan" posee un _Skill Depth_ nivel 5 en Node.js verificado inter-pares al día de la fecha.
3. **El Futuro Contratante como "Verifier":** Si Juan se va de la empresa, se lleva su "Talent Pass / Wallet". La nueva empresa puede verificar criptográficamente contra el registro inmutable (Ledger) la veracidad instantánea de ese perfil, anulando el costo y tiempo del "Background Check".

### Impacto en Arquitectura Backend (Futuro Módulo D9)

- Se ha implementado la tabla `verifiable_credentials` atada al modelo `People`.
- Se implementó un emulador de firmas criptográficas `cryptographic_signature` y la colección del Talent Pass en base de datos.
- Queda pendiente para el futuro la delegación de firmas a un microservicio en Go/Rust específico para emitir pasaportes a la red Polygon o similar.

---

**Status:** Bases técnicas del Gateway SSO aprobadas (Instalación Socialite exitosa). El diseño del flujo permite construir sobre la base actual de Laravel Sanctum progresivamente.
