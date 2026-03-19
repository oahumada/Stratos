# 🌍 Guía Operativa de Auditoría Externa de Compliance - Stratos

Esta guía orienta la preparación y ejecución de una auditoría externa para que el proceso sea **ágil, transparente, verificable y con mínima fricción** para auditores, stakeholders y equipo interno.

---

## 1. Objetivo

Asegurar que Stratos pueda demostrar, ante terceros, que sus controles de compliance están implementados, operan de manera consistente y cuentan con evidencia técnica suficiente.

---

## 2. Cuándo usar esta guía

Usar esta guía cuando exista cualquiera de estos escenarios:

- auditoría de cliente enterprise
- due diligence técnica/compliance
- preparación ISO / privacy review
- revisión por partner, asesor o certificador externo
- evaluación de transparencia y gobernanza IA

---

## 3. Principios rectores

La auditoría externa debe ejecutarse bajo cuatro principios:

1. **Expedita**
    - El auditor debe encontrar evidencia sin depender de búsquedas manuales extensas.
2. **Transparente**
    - Cada control debe tener explicación, endpoint y evidencia asociada.
3. **Reproducible**
    - Un tercero debe poder repetir validaciones críticas.
4. **Trazable**
    - Cada conclusión debe conectarse con una fuente verificable.

---

## 4. Paquete de evidencia recomendado

Preparar un `Audit Evidence Pack` con estos bloques:

### A. Marco rector

- [docs/quality_compliance_standards.md](quality_compliance_standards.md)
- descripción del alcance auditado
- período cubierto
- responsables internos

### B. Evidencia funcional

- resumen del dashboard de compliance
- reporte del `Internal Audit Wizard`
- ejemplos de eventos del `EventStore`
- evidencia de consentimiento IA
- evidencia de purga GDPR

### C. Evidencia técnica

- VC/JSON-LD exportada
- DID document público
- metadata pública del verificador
- verificación pública exitosa de una credencial
- verificación pública fallida de un caso tampered

### D. Evidencia de aseguramiento

- resultados de tests de compliance vigentes
- fecha de última revisión interna
- hallazgos abiertos/cerrados

---

## 5. Dueño interno del proceso

| Rol             | Función en auditoría externa                    |
| :-------------- | :---------------------------------------------- |
| Audit Host      | Conduce la sesión y controla tiempos            |
| Technical Owner | Muestra endpoints, payloads y evidencia técnica |
| Compliance Lead | Traduce control → política → evidencia          |
| Scribe / PM     | Registra preguntas, hallazgos y compromisos     |

---

## 6. Preparación previa (T-7 a T-1)

## 6.1 Confirmar alcance con el auditor

Definir por escrito:

- estándar o marco de referencia
- período auditado
- módulos en alcance
- muestra esperada
- formato de evidencia aceptado
- modalidad: remoto/presencial

## 6.2 Preparar entorno de demostración

Idealmente usar:

- entorno estable
- datos auditables consistentes
- accesos verificados
- tenant controlado de demostración

## 6.3 Validar prerrequisitos

Antes del día de auditoría:

- comprobar `/.well-known/did.json`
- comprobar `/api/compliance/public/verifier-metadata`
- comprobar `/api/compliance/public/credentials/verify`
- exportar una VC válida
- verificar una credencial adulterada
- revisar estado del `Internal Audit Wizard`

---

## 7. Secuencia recomendada para la sesión externa

## Paso 1. Apertura (5-10 min)

Presentar:

- objetivo de la revisión
- alcance
- módulos demostrados
- responsables presentes
- agenda estimada

### Mensaje recomendado

> “Vamos a mostrar controles implementados, evidencia observable y mecanismos públicos de verificación para que puedan reproducir los checks sin depender de una interpretación subjetiva del equipo.”

---

## Paso 2. Panorama de controles (10 min)

Usar [docs/quality_compliance_standards.md](quality_compliance_standards.md) como mapa.

Explicar:

- qué cubre Fase 1 a Fase 4
- qué controles son internos
- cuáles son públicos para terceros
- cómo se preserva el scope multi-tenant

---

## Paso 3. Evidencia de trazabilidad (10-15 min)

Mostrar:

- dashboard de auditoría
- eventos relevantes
- filtros por tipo/agregado/fecha
- resumen agregado

### Qué debe concluir el auditor

- existe trazabilidad
- se puede reconstruir una acción
- hay consistencia entre control y evidencia

---

## Paso 4. Evidencia de privacidad (10-15 min)

Demostrar:

- consentimiento IA registrado
- dry-run GDPR
- purga ejecutada
- resultado auditado en eventos

### Qué debe concluir el auditor

- el sistema no solo declara política; ejecuta controles observables

---

## Paso 5. Evidencia de roles críticos (10 min)

Mostrar `Internal Audit Wizard`:

- total de roles críticos
- conformes / no conformes
- firmas vigentes / expirada / faltante
- recomendaciones accionables

### Qué debe concluir el auditor

- existe control preventivo y operativo sobre artefactos críticos

---

## Paso 6. Evidencia verificable por terceros (15-20 min)

Esta es la parte central para una auditoría externa moderna.

### Demostración recomendada

1. Exportar una VC de un rol firmado.
2. Mostrar el payload JSON-LD.
3. Abrir el DID document público.
4. Abrir la metadata pública del verificador.
5. Ejecutar verificación pública de la credencial válida.
6. Alterar `proof.jws` o subject y reintentar.
7. Mostrar que la credencial alterada falla.

### Qué debe concluir el auditor

- la evidencia es verificable por un tercero
- existe discovery técnico
- existe detección explícita de tampering

---

## 8. Evidencia mínima a entregar al auditor

Recomendado entregar:

1. Documento marco de compliance
2. Resumen de hallazgos internos más recientes
3. VC de muestra
4. URL del DID document
5. URL del metadata endpoint
6. URL del public verify endpoint
7. resultado de pruebas relevantes

---

## 9. Preguntas típicas del auditor y respuesta sugerida

### “¿Cómo sé que la firma no fue alterada?”

Respuesta:

- la credencial incluye `proof.jws`
- la validación contrasta ese valor con la firma persistida del rol
- la verificación pública invalida cualquier credencial adulterada

### “¿Cómo descubro al verificador sin acceso interno?”

Respuesta:

- `/.well-known/did.json` publica el issuer y servicios
- `/api/compliance/public/verifier-metadata` publica capacidades y versiones

### “¿Cómo manejan privacidad y eliminación?”

Respuesta:

- consentimiento auditado
- purga con `dry-run` y ejecución controlada
- anonimización + evidencia de evento

### “¿Cómo aseguran transparencia de gobernanza IA?”

Respuesta:

- audit trail
- firma digital
- verificación pública
- controles internos de vigencia

---

## 10. Criterios de éxito de la auditoría externa

La sesión fue exitosa si el auditor puede confirmar que:

- la evidencia existe
- la evidencia es consistente
- la evidencia es verificable
- los controles críticos tienen dueño
- las excepciones están conocidas y tratadas

---

## 11. Manejo de observaciones y no conformidades

Si aparece una observación:

1. registrar la observación textual
2. asociar evidencia ya presentada
3. clasificar severidad
4. definir owner y fecha compromiso
5. enviar respuesta formal posterior si aplica

### No improvisar

Si algo no está disponible en la sesión:

- no inventar respuesta
- reconocer el gap
- comprometer evidencia complementaria posterior
- registrar SLA de entrega

---

## 12. Checklist expedito para el día de auditoría externa

### Antes de iniciar

- [ ] Agenda confirmada
- [ ] Audit pack preparado
- [ ] Enlaces públicos verificados
- [ ] Usuario demo validado
- [ ] VC de muestra preparada
- [ ] Caso tampered preparado

### Durante la sesión

- [ ] Apertura y alcance
- [ ] Dashboard de auditoría mostrado
- [ ] Consentimiento y GDPR mostrados
- [ ] Internal Audit Wizard mostrado
- [ ] DID document mostrado
- [ ] Verifier metadata mostrada
- [ ] VC válida verificada
- [ ] VC adulterada invalidada
- [ ] Preguntas registradas

### Cierre

- [ ] Hallazgos leídos en voz alta
- [ ] Próximos pasos acordados
- [ ] Evidencia compartida
- [ ] Responsable de seguimiento asignado

---

## 13. Recomendaciones para transparencia máxima

- mostrar evidencia en vivo, no solo capturas
- usar endpoints públicos cuando sea posible
- evitar explicaciones abstractas sin payloads
- demostrar tanto caso válido como caso inválido
- documentar cualquier limitación o supuesto

---

## 14. Resultado esperado

Si esta guía se sigue, la auditoría externa debería percibir a Stratos como una plataforma con:

- controles visibles
- evidencia reproducible
- narrativa clara de compliance
- mínima dependencia de confianza implícita
- máxima transparencia operativa
