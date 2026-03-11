# 🌧️/☀️ Plan de Revisión: Escenarios Soleados y Lluviosos por Rol

Este documento establece la metodología de pruebas de **"Día Soleado"** (camino ideal) y **"Día Lluvioso"** (manejo de errores y casos borde) para asegurar que Stratos sea resiliente y confiable antes del lanzamiento.

---

## 🎭 1. El Actor: CEO (Dirección Estratégica)

### Caso de Uso: Simulación de Escenarios de Crecimiento (Scenario IQ)

| Tipo            | Escenario                     | Descripción del Flujo                                                                                   | Resultado Esperado                                                                                                                                                  |
| :-------------- | :---------------------------- | :------------------------------------------------------------------------------------------------------ | :------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| **☀️ Soleado**  | **Expansión Exitosa**         | El CEO crea un escenario de expansión del 20% en 12 meses con datos completos de mercado.               | La IA proyecta los gaps de skills al instante. Los gráficos Glass se animan suavemente. El reporte es coherente.                                                    |
| **🌧️ Lluvioso** | **Contexto Vacío**            | El CEO intenta simular un crecimiento en un área que no tiene roles ni empleados asignados en TechCorp. | El sistema no explota. Envía una alerta: _"No hay datos base suficientes para proyectar en esta área. Por favor, asegúrese de tener roles configurados"_.           |
| **🌧️ Lluvioso** | **Fallo de IA (Scenario IQ)** | El microservicio de Python o la API de DeepSeek están caídos durante la simulación.                     | El sistema activa el **Fail-Safe**. Utiliza la lógica determinística de la BD para dar una proyección base y avisa: _"Modo optimizado activo (IA fuera de línea)"_. |

---

## 📊 2. El Actor: CFO (Gestión de Capital)

### Caso de Uso: Calculadora de ROI (Build vs Buy vs Borrow)

| Tipo            | Escenario                | Descripción del Flujo                                                        | Resultado Esperado                                                                                                                         |
| :-------------- | :----------------------- | :--------------------------------------------------------------------------- | :----------------------------------------------------------------------------------------------------------------------------------------- |
| **☀️ Soleado**  | **Decisión Justificada** | El CFO ingresa salarios y costos de capacitación. Compara las 3 estrategias. | El sistema genera un gráfico de "Break-even" claro. Indica que "Build" ahorra un 35% en 2 años.                                            |
| **🌧️ Lluvioso** | **Valores Extremos**     | El usuario ingresa un costo de capacitación de $0 o salarios negativos.      | La validación de FormSchema bloquea la acción. Mensaje: _"Los valores financieros deben ser superiores a cero"_ (evita división por cero). |
| **🌧️ Lluvioso** | **Falta de Benchmarks**  | No hay datos de mercado para el rol de "Arquitecto Cuántico".                | El sistema permite ingresar los costos manualmente y muestra un aviso: _"Sin benchmarks globales. Valores calculados por el usuario"_.     |

---

## 🧬 3. El Actor: CHRO (Gobernanza de Talento)

### Caso de Uso: Asignador de Estrategias y Gestión de Gaps

| Tipo            | Escenario                     | Descripción del Flujo                                                                   | Resultado Esperado                                                                                                                                  |
| :-------------- | :---------------------------- | :-------------------------------------------------------------------------------------- | :-------------------------------------------------------------------------------------------------------------------------------------------------- |
| **☀️ Soleado**  | **Cierre de Brechas**         | El CHRO ve 50 gaps críticos. Selecciona "Build" (Capacitar).                            | El sistema propone automáticamente a los 5 empleados con mayor compatibilidad para iniciar un Learning Path.                                        |
| **🌧️ Lluvioso** | **Sin Candidatos Internos**   | El CHRO intenta cerrar una brecha pero no hay nadie en la empresa con skills similares. | El sistema sugiere cambiar la estrategia a **"Buy"** (Contratar fuera) o **"Borrow"** (Freelance) con una alerta de "Dificultad de Reversión Alta". |
| **🌧️ Lluvioso** | **Conflicto de Roles (RBAC)** | Un usuario sin permisos de CHRO intenta modificar el portafolio de estrategias.         | Middleware de Laravel lanza un 403. La UI muestra una página de "Acceso Denegado" con estética Glass.                                               |

---

## 🚀 4. El Actor: Colaborador (Desarrollo y PX)

### Caso de Uso: Talent Pass y Mapa de Carrera

| Tipo            | Escenario                     | Descripción del Flujo                                                                              | Resultado Esperado                                                                                                                           |
| :-------------- | :---------------------------- | :------------------------------------------------------------------------------------------------- | :------------------------------------------------------------------------------------------------------------------------------------------- |
| **☀️ Soleado**  | **Evolución del DNA**         | El empleado abre su perfil y ve su historial de skills (DNA Timeline) balanceado.                  | Visualización fluida de los hexágonos de skills. Las insignias (badges) brillan con el efecto Glass.                                         |
| **🌧️ Lluvioso** | **Skills Expiradas**          | El empleado tiene certificaciones de hace 5 años que ya no son válidas.                            | El "DNA Timeline" muestra la skill en gris o con un ícono de advertencia: _"Skill en riesgo de obsolescencia. Se recomienda actualización"_. |
| **🌧️ Lluvioso** | **Camino sin Salida (Neo4j)** | El empleado quiere ser "Director de Innovación" pero no hay ruta configurada en el grafo de roles. | El motor de grafos muestra la ruta más cercana y avisa: _"Ruta personalizada en construcción. Consulta con tu experto de RRHH"_.             |

---

## 📋 Resumen de la Estrategia de Revisión

1.  **Ejecutar primero todos los "Soleados"** para validar que el producto entrega el valor prometido.
2.  **Ejecutar los "Lluviosos"** para validar que el sistema es "irrompible" y la experiencia del usuario no se arruina ante errores comunes.
3.  **Audit de Consola:** En ningún escenario deben aparecer "errores rojos" en la consola del navegador.

---

> [!TIP]
> **Mecanismo de Verificación:**  
> Te recomiendo que mientras haces la revisión, mantengas el **Quality Hub** abierto. Cada vez que un escenario "Lluvioso" rompa el sistema en lugar de manejarlo bien, es un ticket de alta prioridad.
