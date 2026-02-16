# Casos de Prueba: Sistema de Coherencia Arquitectónica

**Fecha:** 2026-02-15  
**Sesión:** 1.1 - Validación Exhaustiva  
**Objetivo:** Verificar que el sistema de coherencia arquitectónica funciona correctamente

---

## 📋 Casos de Prueba del Semáforo de Coherencia

### **Caso 1: Rol Estratégico con Nivel Bajo**

**Escenario:** Rol Estratégico (E) con nivel de maestría 3  
**Resultado Esperado:**

- ⚠️ Color: Warning (amarillo/naranja)
- 🔔 Ícono: `mdi-alert-decagram`
- 📝 Título: "Arquitectura Débil"
- 💬 Mensaje: "Un Rol Estratégico suele requerir niveles 4 o 5. El nivel 3 podría ser insuficiente..."

**Pasos:**

1. Abrir matriz de roles-competencias
2. Seleccionar un rol con arquetipo "E" (Estratégico)
3. Abrir modal de edición de una competencia
4. Seleccionar nivel 3
5. Verificar el semáforo

**Estado:** [ ] Pendiente | [ ] Pasó | [ ] Falló  
**Notas:**

---

### **Caso 2: Rol Estratégico con Nivel Alto**

**Escenario:** Rol Estratégico (E) con nivel de maestría 4 o 5  
**Resultado Esperado:**

- ✅ Color: Success (verde)
- ✓ Ícono: `mdi-check-decagram`
- 📝 Título: "Diseño Coherente"
- 💬 Mensaje: "El nivel X es consistente con un Arquetipo Estratégico"

**Pasos:**

1. Mismo rol estratégico
2. Seleccionar nivel 4 o 5
3. Verificar el semáforo

**Estado:** [ ] Pendiente | [ ] Pasó | [ ] Falló  
**Notas:**

---

### **Caso 3: Rol Operacional con Nivel Alto SIN Referente**

**Escenario:** Rol Operacional (O) con nivel 4 o 5, sin marcar como referente  
**Resultado Esperado:**

- ℹ️ Color: Info (azul)
- 💡 Ícono: `mdi-lightbulb-on`
- 📝 Título: "Sobrecarga Técnica"
- 💬 Mensaje: "Nivel X es inusualmente alto para un Rol Operacional. Verifica si no hay un exceso de Job Enrichment, o marca este rol como Referente/Mentor."

**Pasos:**

1. Seleccionar un rol con arquetipo "O" (Operacional)
2. Abrir modal de edición de una competencia
3. Seleccionar nivel 4 o 5
4. NO marcar el checkbox de "Rol de Referencia"
5. Verificar el semáforo

**Estado:** [ ] Pendiente | [ ] Pasó | [ ] Falló  
**Notas:**

---

### **Caso 4: Rol Operacional con Nivel Alto CON Referente**

**Escenario:** Rol Operacional (O) con nivel 4 o 5, marcado como referente  
**Resultado Esperado:**

- ✅ Color: Success (verde)
- ⭐ Ícono: `mdi-account-star`
- 📝 Título: "Rol de Referencia Validado"
- 💬 Mensaje: "Este rol operacional actúa como mentor técnico. El nivel X es coherente con su función de mentoría."

**Pasos:**

1. Mismo rol operacional
2. Seleccionar nivel 4 o 5
3. **Marcar el checkbox de "Rol de Referencia / Mentoría"**
4. Verificar el semáforo

**Estado:** [ ] Pendiente | [ ] Pasó | [ ] Falló  
**Notas:**

---

### **Caso 5: Rol Operacional con Nivel Normal**

**Escenario:** Rol Operacional (O) con nivel 1, 2 o 3  
**Resultado Esperado:**

- ✅ Color: Success (verde)
- ✓ Ícono: `mdi-check-decagram`
- 📝 Título: "Diseño Coherente"
- 💬 Mensaje: "El nivel X es consistente con un Arquetipo Operacional"

**Pasos:**

1. Mismo rol operacional
2. Seleccionar nivel 1, 2 o 3
3. Verificar el semáforo

**Estado:** [ ] Pendiente | [ ] Pasó | [ ] Falló  
**Notas:**

---

### **Caso 6: Rol Táctico con Nivel Bajo**

**Escenario:** Rol Táctico (T) con nivel 1  
**Resultado Esperado:**

- ⚠️ Color: Warning (amarillo/naranja)
- ⚠ Ícono: `mdi-alert-outline`
- 📝 Título: "Nivel Insuficiente"
- 💬 Mensaje: "Un Rol Táctico requiere al menos nivel 2 o 3 para asegurar la coordinación efectiva."

**Pasos:**

1. Seleccionar un rol con arquetipo "T" (Táctico)
2. Abrir modal de edición de una competencia
3. Seleccionar nivel 1
4. Verificar el semáforo

**Estado:** [ ] Pendiente | [ ] Pasó | [ ] Falló  
**Notas:**

---

### **Caso 7: Rol Táctico con Nivel Normal**

**Escenario:** Rol Táctico (T) con nivel 2, 3 o 4  
**Resultado Esperado:**

- ✅ Color: Success (verde)
- ✓ Ícono: `mdi-check-decagram`
- 📝 Título: "Diseño Coherente"
- 💬 Mensaje: "El nivel X es consistente con un Arquetipo Táctico"

**Pasos:**

1. Mismo rol táctico
2. Seleccionar nivel 2, 3 o 4
3. Verificar el semáforo

**Estado:** [ ] Pendiente | [ ] Pasó | [ ] Falló  
**Notas:**

---

### **Caso 8: Rol Táctico con Nivel Alto**

**Escenario:** Rol Táctico (T) con nivel 5, sin marcar como referente  
**Resultado Esperado:**

- ℹ️ Color: Info (azul)
- 💡 Ícono: `mdi-lightbulb-on`
- 📝 Título: "Nivel Inusual"
- 💬 Mensaje: "Nivel 5 es inusualmente alto para un Rol Táctico. Considera si este rol debería ser Estratégico o marcarlo como Referente."

**Pasos:**

1. Mismo rol táctico
2. Seleccionar nivel 5
3. NO marcar el checkbox de referente
4. Verificar el semáforo

**Estado:** [ ] Pendiente | [ ] Pasó | [ ] Falló  
**Notas:**

---

## 📋 Casos de Prueba del Checkbox de Referente

### **Caso 9: Checkbox Visible para Operacional Nivel 4**

**Escenario:** Rol Operacional con nivel 4  
**Resultado Esperado:**

- ✅ Checkbox de "Rol de Referencia / Mentoría" debe ser VISIBLE

**Pasos:**

1. Rol operacional (O)
2. Seleccionar nivel 4
3. Verificar que el checkbox aparece

**Estado:** [ ] Pendiente | [ ] Pasó | [ ] Falló  
**Notas:**

---

### **Caso 10: Checkbox NO Visible para Operacional Nivel 3**

**Escenario:** Rol Operacional con nivel 3  
**Resultado Esperado:**

- ❌ Checkbox de "Rol de Referencia / Mentoría" debe ser INVISIBLE

**Pasos:**

1. Rol operacional (O)
2. Seleccionar nivel 3
3. Verificar que el checkbox NO aparece

**Estado:** [ ] Pendiente | [ ] Pasó | [ ] Falló  
**Notas:**

---

### **Caso 11: Checkbox Visible para Táctico Nivel 5**

**Escenario:** Rol Táctico con nivel 5  
**Resultado Esperado:**

- ✅ Checkbox de "Rol de Referencia / Mentoría" debe ser VISIBLE

**Pasos:**

1. Rol táctico (T)
2. Seleccionar nivel 5
3. Verificar que el checkbox aparece

**Estado:** [ ] Pendiente | [ ] Pasó | [ ] Falló  
**Notas:**

---

### **Caso 12: Checkbox NO Visible para Táctico Nivel 4**

**Escenario:** Rol Táctico con nivel 4  
**Resultado Esperado:**

- ❌ Checkbox de "Rol de Referencia / Mentoría" debe ser INVISIBLE

**Pasos:**

1. Rol táctico (T)
2. Seleccionar nivel 4
3. Verificar que el checkbox NO aparece

**Estado:** [ ] Pendiente | [ ] Pasó | [ ] Falló  
**Notas:**

---

### **Caso 13: Checkbox NUNCA Visible para Estratégico**

**Escenario:** Rol Estratégico con cualquier nivel  
**Resultado Esperado:**

- ❌ Checkbox de "Rol de Referencia / Mentoría" debe ser INVISIBLE

**Pasos:**

1. Rol estratégico (E)
2. Probar con niveles 1, 3, 5
3. Verificar que el checkbox NUNCA aparece

**Estado:** [ ] Pendiente | [ ] Pasó | [ ] Falló  
**Notas:**

---

## 📋 Casos de Prueba de Persistencia

### **Caso 14: Persistencia de is_referent**

**Escenario:** Guardar un mapping con is_referent = true y verificar persistencia  
**Resultado Esperado:**

- ✅ El flag se guarda en la base de datos
- ✅ Al recargar la página, el checkbox sigue marcado
- ✅ Al editar el mapping, el checkbox aparece marcado

**Pasos:**

1. Crear mapping con nivel alto y marcar como referente
2. Guardar
3. Recargar la página
4. Abrir el mismo mapping para editar
5. Verificar que el checkbox está marcado

**Estado:** [ ] Pendiente | [ ] Pasó | [ ] Falló  
**Notas:**

---

### **Caso 15: Verificación en Base de Datos**

**Escenario:** Verificar directamente en la base de datos  
**Resultado Esperado:**

- ✅ Campo `is_referent` existe en tabla `scenario_role_competencies`
- ✅ Valor es `true` (o 1) para el mapping creado

**Pasos:**

1. Crear mapping con is_referent = true
2. Ejecutar query SQL: `SELECT * FROM scenario_role_competencies WHERE is_referent = true`
3. Verificar que el registro existe

**Estado:** [ ] Pendiente | [ ] Pasó | [ ] Falló  
**Notas:**

---

## 📋 Casos de Prueba de Racionales Estratégicos

### **Caso 16: Selector Aparece al Disminuir Nivel**

**Escenario:** Editar un mapping existente y disminuir el nivel  
**Resultado Esperado:**

- ✅ Selector de "Racional de Reducción" debe aparecer
- ✅ Opciones: Efficiency Gain, Reduced Scope, Capacity Loss

**Pasos:**

1. Crear mapping con nivel 4
2. Guardar
3. Editar el mismo mapping
4. Cambiar nivel a 2
5. Verificar que aparece el selector de racional

**Estado:** [ ] Pendiente | [ ] Pasó | [ ] Falló  
**Notas:**

---

### **Caso 17: Selector NO Aparece al Aumentar Nivel**

**Escenario:** Editar un mapping existente y aumentar el nivel  
**Resultado Esperado:**

- ❌ Selector de "Racional de Reducción" NO debe aparecer

**Pasos:**

1. Crear mapping con nivel 2
2. Guardar
3. Editar el mismo mapping
4. Cambiar nivel a 4
5. Verificar que NO aparece el selector de racional

**Estado:** [ ] Pendiente | [ ] Pasó | [ ] Falló  
**Notas:**

---

### **Caso 18: Persistencia de Racional**

**Escenario:** Guardar un racional y verificar persistencia  
**Resultado Esperado:**

- ✅ El racional se guarda correctamente
- ✅ Al editar, el racional seleccionado aparece

**Pasos:**

1. Crear mapping con nivel 4
2. Editar y reducir a nivel 2
3. Seleccionar racional "Efficiency Gain"
4. Guardar
5. Editar de nuevo
6. Verificar que el racional está seleccionado

**Estado:** [ ] Pendiente | [ ] Pasó | [ ] Falló  
**Notas:**

---

## 📊 Resumen de Resultados

**Total de Casos:** 18  
**Pasados:** **_  
**Fallados:** _**  
**Pendientes:** \_\_\_

**Porcentaje de Éxito:** \_\_\_\_%

---

## 🐛 Bugs Encontrados

### Bug #1

**Descripción:**  
**Severidad:** [ ] Crítico | [ ] Alto | [ ] Medio | [ ] Bajo  
**Pasos para Reproducir:**  
**Comportamiento Esperado:**  
**Comportamiento Actual:**

---

## ✅ Conclusiones

**Estado General:** [ ] Aprobado | [ ] Aprobado con Observaciones | [ ] Rechazado

**Observaciones:**

**Próximos Pasos:**
