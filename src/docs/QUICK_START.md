# 🚀 QUICK START - Hoja de Referencia Rápida

**Imprime esto o mantenlo abierto mientras trabajas**

---

## 📍 ¿Dónde Estoy?

```
[ ] Empezando nuevo módulo → Ir a PASO 1
[ ] Es mi primer día del módulo → Ir a PASO 2
[ ] Continuando con el módulo → Ir a PASO 3
[ ] Final del día → Ir a PASO 4
[ ] Final de semana/módulo → Ir a PASO 5
```

---

## 🎯 PASO 1: NUEVO MÓDULO (Una sola vez al inicio)

**Tiempo: ~1 hora**

```bash
# 1. Crea/completa memories.md con estas 6 secciones:
- [ ] 1. Contexto: ¿Cuál es el problema?
- [ ] 2. Usuarios: ¿Quién lo usa?
- [ ] 3. Requisitos: ¿Qué necesita?
- [ ] 4. Casos de uso: ¿Cómo lo usa?
- [ ] 5. Restricciones: ¿Qué limita?
- [ ] 6. Datos/Arquitectura: ¿Cómo se estructura?

# 2. Valida requisitos:
grep -E "^##|^###" memories.md | head -20  # Ver estructura

# 3. Planifica módulo completo:
# ¿Cuántos días? Días 1-X
# ¿Cada día qué? Día 1: BD, Día 2: Services, etc.

# 4. Crea primer PLAN_DIA_1.md:
cp docs/TEMPLATE_DIA_N.md docs/PLAN_DIA_1.md
# Edita y personaliza
```

**Resultado:** ✅ memories.md completo + Plan de todos los días

---

## 🌅 PASO 2: PRIMER DÍA O CUALQUIER DÍA (Haz esto CADA MAÑANA)

**Tiempo: 20-25 minutos**
**Horario: 08:00-08:30**

```bash
# 1. LEE: Echada de Andar
# ... sigue instrucciones de ECHADA_DE_ANDAR.md

# 2. VALIDA CONTEXTO:
grep "^## " memories.md
echo "---"
echo "Responsabilidad hoy: [tu respuesta en 1 frase]"
echo "Esto valida si: [tu respuesta: test/endpoint/página]"

# 3. VALIDA AMBIENTE:
echo "BD:" && php artisan migrate:status | grep pending
echo "Server:" && curl -s http://127.0.0.1:8000/api/health || echo "⚠️ Server DOWN"
echo "Vite:" && curl -s http://127.0.0.1:5173 | head -1 || echo "⚠️ Vite DOWN"

# 4. LEE TU PLAN:
cat docs/PLAN_DIA_[N].md | head -40

# 5. SETUP INICIAL:
git status  # ¿Limpio?
git branch -v  # ¿Rama correcta?
git log --oneline -3  # ¿Commits coherentes?

# 6. EMPIEZA:
echo "✅ Listo. Abro VS Code con mis archivos"
code app/[ruta] routes/[ruta] tests/[ruta]  # Los que voy a editar
```

**Resultado:** ✅ Contexto claro, ambiente validado, plan en mente

---

## 💪 PASO 3: DURANTE EL DÍA (Sigue tu PLAN*DIA*[N].md)

**Bloques de trabajo:**

### Bloque 1 (09:30-12:00)

```
09:30-11:45  Código
11:45-12:00  CHECKPOINT 1
  └─ php artisan test
  └─ npm run lint
  └─ git commit -m "Bloque 1 ✅"
```

### Bloque 2 (13:00-16:00)

```
13:00-15:45  Código
15:45-16:00  CHECKPOINT 2
  └─ php artisan test
  └─ npm run lint
  └─ git commit -m "Bloque 2 ✅"
```

### Validación Final (16:00-17:00)

```
16:00-16:30  Testing completo
  └─ php artisan test (todos deben pasar)
  └─ npm run lint (0 errores)
  └─ npm run build (si hay frontend)
  └─ Prueba endpoints en Postman
  └─ Prueba flujo en navegador

16:30-17:00  DOCUMENTACIÓN
  └─ Actualiza README/doc relevante
  └─ Copia TEMPLATE_DIA_N.md → DIA_[N].md
  └─ Completa resumen del día
  └─ git commit -m "Día [N]: Cierre - [descripción]"
```

**Clave:** Valida CADA 2.5 horas, no solo al final

---

## 📝 PASO 4: FIN DEL DÍA (17:00-18:00)

```bash
# 1. Abre tu resumen:
nano docs/DIA_[N].md

# 2. Completa estas secciones:
# [ ] ✅ Completado
# [ ] 📊 Métricas
# [ ] 🔗 Archivos Generados
# [ ] 📝 Notas
# [ ] 🔴 Incompleto (si hay)
# [ ] 🔗 Conecta con Día [N+1]

# 3. Responde honestamente:
# ¿Cumplí responsabilidad? [ ] SÍ [ ] PARCIAL [ ] NO
# ¿Tests pasan? [ ] SÍ [ ] NO
# ¿0 errores? [ ] SÍ [ ] NO
# ¿Documentado? [ ] SÍ [ ] NO

# 4. Git final:
git add docs/ && \
git commit -m "Día [N] - Documentación final

- Completadas X tareas
- Tests: N/N PASS
- Documentación: Actualizada
- Estado: LISTO PARA DÍA [N+1] / BLOQUEADOR"

# 5. Revisa git log:
git log --oneline -5
# Debe mostrar: Setup → Bloque 1 → Bloque 2 → Documentación
```

**Resultado:** ✅ Día documentado, commits coherentes, listo para mañana

---

## 📊 PASO 5: FIN DE SEMANA (Viernes 17:00)

```bash
# 1. Llena VALIDACION_ESTADO.md COMPLETO
nano docs/VALIDACION_ESTADO.md

# PARTES:
# [ ] Parte 1: Requisitos (entiendo)
# [ ] Parte 2: Estado técnico (BD, tests, frontend)
# [ ] Parte 3: Continuidad (qué falta, bloqueadores)
# [ ] Parte 4: Progresión (% completitud)
# [ ] Parte 5: Documentación (lista y útil)

# 2. Calcula: ¿Cuál es el % completo?
# = (SÍ × 100 + Parcial × 50) / Total requisitos

# 3. Responde:
# [ ] VERDE (todo OK) → Celebra, planifica siguiente
# [ ] AMARILLO (hay cosas) → Ajusta plan lunes
# [ ] ROJO (bloqueadores) → Alinea con líder

# 4. Revisa LECCIONES_APRENDIDAS:
# ¿Apliqué alguna? [ ] SÍ [ ] NO
# ¿Cometí algún error de la lista? [ ] SÍ [ ] NO

# 5. Planifica semana siguiente:
# Days: [ ] BD [ ] Services [ ] API [ ] Frontend [ ] Polish
# Recursos: [ ] Personas [ ] Equipos [ ] Bloqueadores resueltos

# 6. Documenta resumen:
echo "=== RESUMEN SEMANA ===" && \
echo "Completitud: X%" && \
echo "Estado: VERDE/AMARILLO/ROJO" && \
echo "Bloqueadores: [si hay]" && \
echo "Próximos pasos: [plan de lunes]"
```

**Resultado:** ✅ Semana validada, progreso claro, plan para lunes

---

## 🎯 CHECKLIST DIARIO (Pega en tu monitor)

```
╔════════════════════════════════════════════════════════════╗
║              ⚡ CHECKLIST DIARIO TalentIA ⚡               ║
╠════════════════════════════════════════════════════════════╣
║                                                            ║
║ MAÑANA (08:00-08:30):                                     ║
║  [ ] Echada de Andar - ECHADA_DE_ANDAR.md                ║
║  [ ] Validé contexto (memories, BD, server)              ║
║  [ ] Tengo plan claro (PLAN_DIA_[N].md)                  ║
║  [ ] 0 bloqueadores CRÍTICOS                             ║
║                                                            ║
║ DURANTE DÍA:                                              ║
║  [ ] Bloque 1: 09:30-12:00 ✅                           ║
║  [ ] Bloque 2: 13:00-16:00 ✅                           ║
║  [ ] Checkpoint cada 2.5 horas                           ║
║      └─ php artisan test                                 ║
║      └─ npm run lint                                     ║
║      └─ git commit                                       ║
║                                                            ║
║ TARDE (16:00-17:00):                                      ║
║  [ ] Testing completo = PASS                             ║
║  [ ] 0 errores sintaxis                                  ║
║  [ ] Documentación actualizada                           ║
║  [ ] Endpoints probados (Postman)                        ║
║  [ ] Frontend probado (navegador)                        ║
║                                                            ║
║ NOCHE (17:00-18:00):                                      ║
║  [ ] DIA_[N].md completado                              ║
║  [ ] Métricas: Archivos, Líneas, Tests                   ║
║  [ ] Honestidad: ¿Cumplí o no?                          ║
║  [ ] Git log: 3-4 commits coherentes                     ║
║                                                            ║
║ RESULTADO:                                                ║
║  [ ] Puedo dormir tranquilo?                             ║
║  [ ] Listo para mañana?                                  ║
║  [ ] Día documentado?                                    ║
║                                                            ║
╚════════════════════════════════════════════════════════════╝
```

---

## 🔗 ACCESO RÁPIDO A DOCUMENTOS

| Necesito...         | Archivo                         |
| ------------------- | ------------------------------- |
| Checklist mañana    | ECHADA_DE_ANDAR.md              |
| Plan de hoy         | PLAN*DIA*[N].md                 |
| Validar estado      | VALIDACION_ESTADO.md            |
| Entender requisitos | memories.md                     |
| Qué pasó ayer       | DIA\_[N-1].md                   |
| Proceso general     | GUIA_DESARROLLO_ESTRUCTURADO.md |
| Errores a evitar    | LECCIONES_APRENDIDAS_DIA1_5.md  |
| Endpoints API       | dia5_api_endpoints.md           |
| Comandos útiles     | CHEATSHEET_COMANDOS.md          |

---

## 🚀 COMANDOS RÁPIDOS

```bash
# MAÑANA:
alias echada="cat docs/ECHADA_DE_ANDAR.md | head -50"

# DURANTE DÍA (validar):
alias valida="php artisan test && npm run lint"

# NOCHE (commit):
alias cierre='git add docs/ && git commit -m "Día $1: Documentación final"'

# FIN SEMANA:
alias semana="cat docs/VALIDACION_ESTADO.md | head -40"

# En ~/.bashrc o ~/.zshrc agrega estas líneas
```

---

## 📞 DECISIONES RÁPIDAS

**¿Duda? Usa este árbol:**

```
¿Qué hago hoy?
├─ No entiendo → memories.md
├─ Entiendo pero no sé cómo → GUIA_DESARROLLO_ESTRUCTURADO.md
├─ Necesito plan → PLAN_DIA_[N].md
├─ Necesito validar → VALIDACION_ESTADO.md
├─ Test falla → git diff + lees error + LECCIONES_APRENDIDAS.md
├─ ¿Voy en tiempo? → Checkpoints cada 2.5 horas
├─ ¿Completé día? → Todos checkpoints PASS + documentación OK
└─ ¿Mañana continúo? → DIA_[N].md muestra qué va siguiente

¿Test falla?
├─ Lee error completo
├─ git diff para ver qué cambió
├─ Compara con LECCIONES (errores a evitar)
└─ Arregla ANTES de continuar (no después)

¿Bloqueador?
├─ Es CRÍTICO → Resuelve ahora, avanza después
├─ Es IMPORTANTE → Documenta, continúa, resuelve hoy
└─ Es BAJO → Documenta para mañana, continúa

¿Completé?
├─ Todos checkpoints PASS → SÍ
├─ Tests fallan → NO
├─ Hay TODO comentarios → PARCIAL
└─ Documentado + Git coherente → LISTO
```

---

## 🎓 LA CLAVE EN 5 FRASES

1. **Mañana (20 min):** Echada de andar → Contexto, ambiente, plan
2. **Durante día (8 horas):** 2 bloques × 2.5 horas + validación cada 2.5h
3. **Tarde (1 hora):** Testing completo = PASS, documentación
4. **Noche (1 hora):** Resumen del día + git coherente
5. **Viernes:** Validar estado general, ajustar plan siguiente

**Resultado:** Consistencia = tranquilidad = éxito

---

## ✨ ÚLTIMA COSA

Esta hoja de referencia rápida es tu **brújula del día**.
Si la sigues, no te pierdes.
Si algo se desvía, ajusta aquí, no durante codificación.

**¿Estás listo?**

```
[ ] Tengo memories.md claro
[ ] Entiendo mi responsabilidad de hoy
[ ] Sé dónde irán mis archivos
[ ] Tengo plan de 2 bloques
[ ] Entiendo cómo validar completitud

🟢 SI TODO ESTÁ MARCADO → EMPIEZA AHORA
```

---

**Buenas suerte. Este esquema funcionó en Días 1-5. Funcionará en futuros módulos si lo respetas.** ✨
