# Guía de Configuración Agnóstica de LLM para Stratos

Stratos ha sido diseñado utilizando un patrón de **Factoría de LLM** (`LLM Factory`) en su servicio de Python (`python_services`), lo que permite cambiar de proveedor de Inteligencia Artificial (OpenAI, DeepSeek, Abacus, etc.) simplemente modificando variables de entorno, sin tocar el código.

Esta guía explica cómo configurar cada proveedor y cómo extender el sistema.

---

## 1. Arquitectura

El núcleo de esta flexibilidad reside en la función `get_llm()` dentro de `python_services/app/main.py`. Esta función actúa como un intermediario inteligente que:

1.  Lee las variables de entorno.
2.  Detecta qué proveedor se quiere usar (basado en la URL o el nombre del modelo).
3.  Instancia y configura la clase correcta (por ejemplo, `DeepSeekLLM` para DeepSeek o `ChatOpenAI` estándar para OpenAI).
4.  Devuelve un objeto compatible con `CrewAI` y `LangChain`.

### Ubicación de la Configuración

Todas las configuraciones se realizan en el archivo:
`python_services/.env`

---

## 2. Configuración por Proveedor

Para cambiar de proveedor, edite el archivo `python_services/.env` y ajuste las siguientes variables.

### A. DeepSeek (Recomendado por Costo/Rendimiento)

DeepSeek ofrece compatibilidad con la API de OpenAI pero requiere ajustes específicos que Stratos maneja automáticamente.

```bash
# Integración DeepSeek en Stratos
OPENAI_API_KEY="sk-xxxxxxxxxxxxxxxxxxxxxxxx"  # Su clave de DeepSeek
OPENAI_MODEL_NAME="deepseek-chat"             # O "deepseek-coder"
OPENAI_API_BASE="https://api.deepseek.com"    # URL Base oficial
```

_Nota: Stratos detectará "deepseek" en la URL o el modelo y activará automáticamente la clase `DeepSeekLLM` que corrige problemas de enrutamiento._

### B. OpenAI (Estándar)

Si desea volver a usar OpenAI (GPT-4o, GPT-4-Turbo), simplemente elimine o vacíe la URL base para que la librería use los valores por defecto de OpenAI.

```bash
# Integración OpenAI Oficial
OPENAI_API_KEY="sk-xxxxxxxxxxxxxxxxxxxxxxxx"  # Su clave de OpenAI
OPENAI_MODEL_NAME="gpt-4-turbo"               # O "gpt-4o"
OPENAI_API_BASE=""                            # DEJAR VACÍO para usar api.openai.com por defecto
```

### C. Abacus AI (Ubicación Futura)

**SÍ, debe cambiar la URL base.**
Para cualquier proveedor que no sea OpenAI oficial (como Abacus, Azure, o modelos locales), `OPENAI_API_BASE` actúa como la dirección de destino.

**Ejemplo para Abacus:**
Si Abacus proporciona un endpoint compatible con OpenAI, la configuración sería:

```bash
# Integración Abacus
OPENAI_API_KEY="su-clave-abacus"
OPENAI_MODEL_NAME="modelo-abacus-mapeado" # El nombre que Abacus espera
OPENAI_API_BASE="https://api.abacus.ai/v0/openai" # [IMPORTANTE] Cambiar esto por la URL real de Abacus
```

**Regla General:**

- **OpenAI:** `OPENAI_API_BASE=""` (Vacío)
- **DeepSeek:** `OPENAI_API_BASE="https://api.deepseek.com"`
- **Abacus:** `OPENAI_API_BASE="https://api.abacus.ai/..."`
- **Local (Ollama):** `OPENAI_API_BASE="http://localhost:11434/v1"`

---

## 3. Guía para Desarrolladores: Agregando Nuevo Proveedor

Si desea agregar un proveedor completamente nuevo (ej. Anthropic vía adaptador, o un modelo local con Ollama), siga estos pasos:

1.  Abra `python_services/app/main.py`.
2.  Localice la función `get_llm(temperature=0.7)`.
3.  Agregue una nueva condición `elif`.

**Ejemplo para Ollama (Local):**

```python
    # En get_llm()...
    elif "localhost" in api_base:
        return ChatOpenAI(
            model="llama3",
            base_url="http://localhost:11434/v1",
            api_key="ollama" # No requerida
        )
```

## 4. Solución de Problemas Comunes

**Error 401 (Unauthorized):**

- Verifique que `OPENAI_API_KEY` corresponde al proveedor configurado en `OPENAI_API_BASE`. Un error común es poner la URL de DeepSeek pero dejar la clave de OpenAI.

**El modelo alucina o falla el formato JSON:**

- Asegúrese de que `OPENAI_MODEL_NAME` sea correcto. Modelos más pequeños pueden tener dificultades con las instrucciones complejas de Stratos. Se recomienda `deepseek-chat` o `gpt-4-turbo` como mínimo.

**CrewAI intenta usar OpenAI por defecto para Embeddings:**

- Stratos ya está configurado con `memory=False` en las instancias de `Crew` para evitar que intente generar embeddings usando OpenAI por defecto, lo cual fallaría si se usa una clave de DeepSeek. Si habilita la memoria, deberá configurar un `embedder` explícito.
