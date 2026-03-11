# Talent Pass & Sovereign Identity

## 🚀 Concepto General

El ecosistema Stratos introduce dos conceptos revolucionarios para la gestión del talento en el bloque de expansión (Wave 3 / Fase 0): **Talent Pass** y **Sovereign Identity**.

1. **Talent Pass (El Producto / CV 2.0):** Es la evolución natural del currículum tradicional. Una "billetera" (Wallet) digital corporativa donde el empleado consolida sus logros verificados (Skills certificadas, medallas, finalización de entrenamientos, etc.). Su objetivo primordial es la portabilidad y la eliminación de la fricción en procesos de _Background Check_.
2. **Sovereign Identity (La Tecnología / Identidad Soberana):** Representa la tecnología subyacente que empodera al Talent Pass, basándose en credenciales verificables (Verifiable Credentials - W3C) y principios de descentralización (Web3). Permite que la información pertenezca al empleado y pueda ser verificada criptográficamente por terceros.

---

## 🛠️ Arquitectura y Estado Actual (Implementación Fase 1)

Actualmente, el sistema cuenta con el diseño de datos, los modelos base, y un API para la consolidación de logros y firma simulada, preparando el terreno de las estructuras de datos antes de una integración con la Blockchain.

### 1. Modelos de Datos (`VerifiableCredential`)

Se ha implementado el modelo y la migración `verifiable_credentials` para almacenar de forma estandarizada los "Pasaportes" y certificados.

**Campos principales:**

- `people_id`: El "Holder" o dueño de la credencial.
- `type`: El tipo de credencial (e.g. `SkillAssessment`, `QuestCompletion`, `CourseCompletion`).
- `issuer_name` / `issuer_did`: Datos del "Issuer" (Stratos Platform y su identificador descentralizado).
- `credential_data`: Payload JSON con el logro concreto.
- `cryptographic_signature`: Firma digital (actualmente emulada con hashes seguros).
- `status`: Estado de la credencial (`active`, `revoked`, `expired`).

### 2. Controlador de Billetera (`TalentPassController`)

Expone la información del usuario consolidada en su "Talent Pass".

- `GET /api/people/{id}/talent-pass`: Retorna un payload JSON del "Wallet" del empleado.
    - Genera un DID único para propósitos de simulación.
    - Une las `verifiableCredentials` activas (certificados emitidos con firmas).
    - Incluye `unverified_achievements` (Habilidades activas y medallas que aún no han sido "acuñadas" o firmadas criptográficamente como credenciales).
- `POST /api/people/{id}/talent-pass/issue`: Recibe un payload con información del logo (`type`, `payload`) y emite una nueva credencial (usando un hash aleatorio seguro para la `cryptographic_signature` a modo de emulador).

---

## 🏗️ Lo que Queda Pendiente (Fase 2 - W3C & Web3)

La fase futura consistirá en convertir el "Hash" local estático en un sistema validado criptográficamente bajo los estándares mundiales.

1. **Microservicio de Firmas (Go/Rust):** En lugar de que Laravel genere una firma estática local, se deberá crear un microservicio de alto rendimiento especializado en manejar llaves privadas (Private Keys) asimétricas.
2. **Generación W3C Verifiable Credentials:** Modificar el `credential_data` para que cumpla estrictamente con la semántica JSON-LD de las credenciales verificables del W3C.
3. **Smart Contracts (Opcional pero recomendable):** Desplegar un contrato inteligente base (por ejemplo, en Polygon Network) para registrar el esquema de los Issuers de Stratos, o simplemente anclar los roothashes de un Árbol de Merkle diariamente para inmutabilidad del ecosistema corporativo.
4. **UI del "Talent Pass":** Construir en la Single Page Application (Vue/Inertia) la interfaz visual del "Wallet" para que el usuario pueda ver sus "cartas promocionales", su DID, y pueda compartir el código QR a recultadores externos para validación de origen.
