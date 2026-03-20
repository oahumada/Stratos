# Protocolo de Aprobación de Roles y Competencias - Stratos AI

Este documento detalla el flujo de firma digital y validación técnica para los componentes de talento en Stratos, incluyendo las mejoras recientes y la distinción de estados según el origen de los datos.

## 1. Visión General del Flujo

Stratos utiliza un sistema de **Sello Digital (HMAC-SHA256)** para garantizar que la configuración de los puestos (Roles) y los estándares técnicos (Competencias) han sido validados por un responsable antes de entrar en los sistemas operativos de la empresa.

### El Mecanismo: "Enlace Mágico"
1. Un diseñador o administrador solicita la aprobación desde el catálogo.
2. El sistema genera un **Token de Seguridad** de un solo uso con fecha de expiración.
3. El responsable recibe una URL dedicada (físicamente preparada para envío por email) que le permite acceder a una interfaz de revisión premium sin necesidad de navegar por toda la plataforma.

---

## 2. Flujo de Aprobación de Roles

Diseñado para formalizar la arquitectura de un puesto de trabajo específico.

- **Trigger:** Botón "Solicitar Aprobación" en el portal de Roles.
- **Acción Automática:** Al aprobar el Rol, el sistema **materializa y firma automáticamente todas las Habilidades (Skills)** vinculadas, y genera la **Versión Oficial V1.0** en el registro histórico.
- **Resultado:**
  - El Rol cambia su estado a `active`.
  - Se genera un `digital_signature` único para el registro del Rol.
  - Se crea un registro en `role_versions` con el snapshot completo del puesto.
  - Las Skills dependientes pasan de `proposed` a `active` y también quedan selladas individualmente.

---

## 3. Flujo de Aprobación de Competencias (Curaduría Técnica)

Diseñado para competencias **transversales, corporativas o institucionales** que no dependen de un solo rol.

- **Autonomía:** Permite que un Curador Técnico valide el estándar de una competencia (descripción y niveles BARS) de forma independiente.
- **Trigger:** Botón de "Validar" en el Catálogo de Competencias.
- **Acción Automática:** Al ser aprobada, todas las habilidades internas del grupo se activan masivamente y se genera la **Versión Oficial V1.0** de la competencia.
- **Resultado:** 
  - La competencia se convierte en un **Estándar Oficial** del catálogo.
  - Se crea un registro en `competency_versions` vinculado a la firma digital.

---

## 4. Distinción de Estados (Capas de Madurez)

Hemos refinado los estados para evitar confusión entre experimentos de IA y definiciones estructurales:

| Estado | Significado | Origen Común |
| :--- | :--- | :--- |
| **`active`** | **Oficial / Firmada** | Ha pasado por el flujo de sello digital. |
| **`pending_approval`** | **En Revisión** | Procede del **AI Role Wizard**. Espera ser enviada o firmada. |
| **`proposed`** | **Sugerido (Draft)** | Importación de planilla o sugerencia inicial de IA. |
| **`in_incubation`** | **Descubrimiento AI** | Proviene de una **Simulación de Escenarios**. Es un "proto-talento". |
| **`pending`** | **En Trámite** | Existe una solicitud de aprobación activa (enlace mágico enviado). |

---

## 5. Mejoras Realizadas en la Arquitectura

1. **Polimorfismo de Firma:** El servicio `RoleDesignerService` ahora maneja de forma inteligente si lo que se está sellando es un Rol o una Competencia, aplicando reglas de validación específicas para cada uno.
2. **Interfaz de Aprobación "Glassmorphism":** Se crearon componentes Vue dedicados (`Roles/Approval` o `Competencies/Approval`) con una estética premium para impresionar al responsable de aprobación (stakeholder).
3. **Optimización de UI de Catálogo:** Se integraron indicadores visuales en tiempo real para saber qué ítems están listos para ser "Materializados" y cuáles están esperando firma.
4. **Resguardo de Trazabilidad:** Cada aprobación registra la fecha (`signed_at`), la versión de la firma y la identidad de la persona que autorizó el cambio en la base de datos de auditoría.

---

## 6. Cumplimiento ISO y Auditoría (Ready-for-Audit)

El sistema ha sido diseñado para satisfacer los requisitos de normas internacionales de calidad (**ISO 9001:2015**) y seguridad de la información (**ISO/IEC 27001**):

- **Trazabilidad Total (Cláusula 8.5.2 ISO 9001):** Cada aprobación dispara un evento en el `EventStore`, un log de auditoría permanente e inmutable que registra quién, cuándo y qué se aprobó.
- **Control de Información Documentada (Cláusula 7.5 ISO 9001):** El sistema de versiones (`RoleVersion`, `CompetencyVersion`) asegura que siempre se pueda recuperar el estado exacto de un diseño en una fecha específica.
- **Integridad de Datos:** El uso de hashes HMAC-SHA256 garantiza que los datos no han sido alterados después de la firma. Si un administrador intentara modificar un rol firmado sin pasar por el flujo de aprobación, el sello digital se rompería, alertando sobre la falta de integridad.

---

## 7. Marketing & Diferenciación: El Certificado de Autenticidad

Para potenciar el valor de Stratos como herramienta de **Gobernanza Corporativa**, cada diseño oficial cuenta con un **Certificado de Validez Técnica** accesible desde la interfaz:

- **Sello "ISO 9001 VALIDATED":** Aparece automáticamente en el catálogo para roles y competencias firmadas.
- **Visualización Premium:** Al hacer clic en el sello, se despliega un certificado con efecto "cristal" (glassmorphism) que muestra el estándar de auditoría, el hash de integridad y los datos del firmante.
- **Argumento de Venta:** Esta funcionalidad transforma un simple repositorio de cargos en un **Sistema de Confianza**, ideal para presentar ante comités de auditoría o directivos como prueba de que el talento está alineado con los estándares de calidad del grupo.

---
> [!TIP]
> **Práctica Recomendada:** Use la aprobación de roles para puestos específicos y la aprobación de competencias para definir el "DNA" de la organización que será compartido por múltiples departamentos.
