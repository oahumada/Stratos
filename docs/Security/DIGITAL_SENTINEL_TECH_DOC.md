# 🛡️ Stratos Sentinel: Protocolo de Sello Digital de Autenticidad

## 1. Introducción y Propósito

El **Stratos Sentinel Signature (SSS)** es un mecanismo de seguridad arquitectónica diseñado para garantizar la procedencia e integridad de los reportes y activos de ingeniería de talento generados por la plataforma.

En un entorno donde la IA genera valor intelectual crítico, el SSS actúa como un "Certificado de Autenticidad" que asegura que un activo no ha sido alterado manualmente y que fue efectivamente originado por los motores oficiales de Stratos (**Cerbero Engine**).

## 2. Arquitectura del Sistema

El sistema se basa en criptografía simétrica de alto rendimiento (HMAC-SHA256) vinculada a la identidad secreta del servidor.

### Componentes Core:

1.  **`StratosSignatureService`**: Orquestador central encargado de generar y verificar firmas.
2.  **`HasDigitalSeal` (Trait)**: Interfaz de implementación que dota a los modelos de Eloquent de capacidades de sellado.
3.  **Persistencia**: Campos `digital_signature`, `signed_at` y `signature_version` integrados en el esquema de base de datos.
4.  **UI de Verificación**: Componente Vue dinámico para feedback visual en tiempo real.

---

## 3. Funcionamiento Técnico (Deep Dive)

### A. Extracción del "ADN Digital"

Para que una firma sea válida, el sistema debe ser capaz de recrearla exactamente. Sin embargo, no todos los campos de una tabla son relevantes (ej. IDs automáticos o fechas de actualización).

El servicio filtra los atributos del modelo para quedarse solo con la **lógica de negocio**:

- Configuración de roles y FTEs.
- Porcentajes de apalancamiento sintético vs humano.
- Justificaciones estratégicas y competencias clave.

```php
// Ejemplo simplificado de filtrado en StratosSignatureService
$excluded = ['id', 'digital_signature', 'signed_at', 'updated_at', ...];
$payload = array_diff_key($attributes, array_flip($excluded));
ksort($payload); // Ordenamiento estable
```

### B. El Proceso de Sellado (Signing)

Cuando un activo alcanza un estado final (ej. `approved` o `complete`), se invoca `$model->seal()`.

1.  Se serializa el payload filtrado a JSON.
2.  Se aplica un `hash_hmac` usando el algoritmo `sha256` y el `APP_KEY` del entorno.
3.  Se guarda el hash resultante junto con un timestamp de firma.

### C. Verificación de Integridad

Cada vez que se consulta un modelo con el trait `HasDigitalSeal`, se puede llamar a `$model->isVerified()`.

- Si alguien edita una celda en la base de datos (ej. cambia un 20% por un 30% manualmente), el hash recalculado no coincidirá con la firma guardada originalmente.
- El sistema detectará la discrepancia inmediatamente.

---

### 4. Core Entities under Protocol

| Category              | Model                 | Trigger Event                   |
| :-------------------- | :-------------------- | :------------------------------ |
| **Talent Strategy**   | `TalentBlueprint`     | On logic finalization           |
| **Scenario Planning** | `Scenario`            | On logic finalization           |
| **AI Raw Output**     | `ScenarioGeneration`  | On LLM response persistence     |
| **Selection**         | `Application`         | On candidate application        |
| **Selection**         | `JobOpening`          | On vacancy creation             |
| **Assesment 360**     | `Evaluation`          | On score calculation            |
| **Assesment 360**     | `LLMEvaluation`       | On RAGAS metrics completion     |
| **Assesment 360**     | `AssessmentSession`   | On session analysis             |
| **Assesment 360**     | `AssessmentRequest`   | On external feedback submission |
| **Assesment 360**     | `PsychometricProfile` | On trait generation             |
| **People Experience** | `PxCampaign`          | On automated trigger            |
| **People Experience** | `PulseSurvey`         | On survey creation              |

---

## 5. Implementación en Modelos

Los siguientes modelos están protegidos actualmente por el protocolo:

- **`TalentBlueprint`**: Sella el diseño individual de cada rol sugerido por la IA.
- **`Scenario`**: Sella la estrategia de planificación de fuerza laboral global.
- **`ScenarioGeneration`**: Sella la respuesta JSON cruda recibida del LLM para auditoría.
- **`Application` / `JobOpening`**: Asegura la transparencia en procesos de selección.
- **`Evaluation` / `AssessmentSession`**: Certifica que los resultados de desempeño no fueron manipulados.
- **`PxCampaign`**: Garantiza la procedencia de las estrategias de People Experience.

---

## 6. Visualización en Frontend

Se ha implementado el componente `VerifySealBadge.vue` que ofrece tres estados visuales:

1.  **Verificado (Premium Emerald)**: El activo es auténtico y mantiene su integridad original.
2.  **No Válido (Amber Warning)**: Indica que los datos han sido modificados fuera del flujo oficial o el hash está corrupto.
3.  **No Sellado**: El activo es manual o está en proceso de creación.

---

## 6. Seguridad y Consideraciones

- **Eficiencia**: A diferencia de Blockchain, el costo computacional de este sellado es de <1ms, permitiendo escalar a miles de reportes sin impacto en performance.
- **Protocolo Evolutivo**: El campo `signature_version` permite actualizar algoritmos (ej. pasar a RSA asimétrico) sin invalidar las firmas antiguas.
- **Propiedad Intelectual**: Este sello dificulta la copia no autorizada de la base de datos de Stratos a otras plataformas de talento, ya que la "marca de agua" criptográfica es inherente a la data.

---

_Documentación generada por Antigravity para el equipo de Ingeniería de Stratos (Marzo 2026)._
