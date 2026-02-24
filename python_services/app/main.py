from fastapi import FastAPI, BackgroundTasks
import os
import subprocess
import json
import psycopg2

BASE_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), '..'))

app = FastAPI(title='Stratos Neo4j ETL Service')


def run_etl_process():
    env = os.environ.copy()
    # Ejecutar el PoC ETL existente
    proc = subprocess.run(['python', 'neo4j_etl.py'], cwd=BASE_DIR, env=env, capture_output=True, text=True)
    return {'returncode': proc.returncode, 'stdout': proc.stdout, 'stderr': proc.stderr}


def get_pg_conn():
    dsn = os.getenv('PG_DSN')
    if not dsn:
        raise RuntimeError('PG_DSN no definido')
    return psycopg2.connect(dsn)


@app.get('/health')
def health():
    return {'status': 'ok'}


@app.post('/sync')
def sync(background_tasks: BackgroundTasks):
    """Dispara el ETL en background y devuelve ACK inmediato."""
    background_tasks.add_task(run_etl_process)
    return {'status': 'started'}


@app.get('/status')
def status():
    """Devuelve los últimos checkpoints almacenados en Postgres."""
    conn = get_pg_conn()
    try:
        with conn.cursor() as cur:
            cur.execute("SELECT job_name, entity, organization_id, last_synced_at FROM sync_checkpoints ORDER BY job_name, entity")
            rows = cur.fetchall()
            keys = ['job_name', 'entity', 'organization_id', 'last_synced_at']
            data = [dict(zip(keys, r)) for r in rows]
    finally:
        conn.close()
    return {'checkpoints': data}
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel, Field
from crewai import Agent, Task, Crew, Process
from langchain_openai import ChatOpenAI
import os
import json
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

DEEPSEEK_BASE_URL = "https://api.deepseek.com"

# Force environment variable for libraries that might check it directly
# langchain might use OPENAI_API_BASE, while openai v1 uses OPENAI_BASE_URL
os.environ["OPENAI_API_BASE"] = os.getenv("OPENAI_API_BASE", DEEPSEEK_BASE_URL)
os.environ["OPENAI_BASE_URL"] = os.getenv("OPENAI_API_BASE", DEEPSEEK_BASE_URL) 
os.environ["OPENAI_API_KEY"] = os.getenv("OPENAI_API_KEY", "")

# Check for API key
if not os.getenv("OPENAI_API_KEY") and os.getenv("STRATOS_MOCK_IA", "false").lower() == "false":
    print("WARNING: OPENAI_API_KEY is not set. The agent will likely fail.")

# Define the data models based on DATA_CONTRACT.md
class GapData(BaseModel):
    role_context: dict
    competency_context: dict
    talent_context: dict
    market_context: dict | None = None

class GapAnalysisRequest(BaseModel):
    gap_data: GapData

class StrategyRecommendation(BaseModel):
    strategy: str = Field(..., description="The recommended strategy (Buy, Build, Borrow, Bind, Bot)")
    confidence_score: float = Field(..., description="Confidence level in the recommendation (0.0 to 1.0)")
    reasoning_summary: str = Field(..., description="A concise explanation of why this strategy was chosen")
    action_plan: list[str] = Field(..., description="A list of specific, actionable steps to implement the strategy")

class ChatMessage(BaseModel):
    role: str
    content: str

class ChatSessionRequest(BaseModel):
    person_name: str
    context: str
    history: list[ChatMessage]
    language: str = "es"

class AnalysisResponse(BaseModel):
    traits: list[dict]
    overall_potential: float
    summary_report: str
    blind_spots: list[str] = Field(default_factory=list, description="Strengths seen by others but not by the subject, or vice-versa")
    ai_reasoning_flow: list[str] = Field(default_factory=list, description="The logical steps the AI took to reach the conclusions")

class FeedbackItem(BaseModel):
    relationship: str
    content: str

class ThreeSixtyAnalysisRequest(BaseModel):
    person_name: str
    interview_history: list[ChatMessage]
    external_feedback: list[FeedbackItem]
    language: str = "es"

# Custom LLM Wrapper to force DeepSeek configuration
class DeepSeekLLM(ChatOpenAI):
    """
    Wrapper for DeepSeek API to ensure CrewAI uses the correct Base URL.
    This bypasses potential default OpenAI routing in CrewAI's internal handling.
    """
    def __init__(self, temperature=0.7, **kwargs):
        super().__init__(
            model="deepseek-chat",
            temperature=temperature,
            base_url=DEEPSEEK_BASE_URL,
            api_key=os.getenv("OPENAI_API_KEY"),
            **kwargs
        )

# LLM Factory function
def get_llm(temperature=0.7):
    # Check explicitly for DeepSeek
    api_base = os.getenv("OPENAI_API_BASE", "")
    model_name = os.getenv("OPENAI_MODEL_NAME", "")
    
    if "deepseek" in api_base or "deepseek" in model_name:
        # Use Custom Wrapper for DeepSeek
        return DeepSeekLLM(temperature=temperature)
    
    # Check for Abacus (future expansion)
    elif "abacus" in api_base:
        return ChatOpenAI(
            model="gpt-4", # Abacus maps this
            temperature=temperature,
            base_url=api_base,
            api_key=os.getenv("OPENAI_API_KEY")
        )
    else:
        # Default to Standard OpenAI
        return ChatOpenAI(
            model=model_name or "gpt-4-turbo",
            temperature=temperature,
            api_key=os.getenv("OPENAI_API_KEY")
        )

app = FastAPI(title="Stratos Intel Service", version="0.1.0")

@app.get("/")
def read_root():
    return {"status": "online", "message": "Stratos Intelligence Core (Powered by CrewAI & GPT-4o)"}

@app.post("/analyze-gap", response_model=StrategyRecommendation)
def analyze_gap(request: GapAnalysisRequest):
    # Check if we should use Mock mode
    if os.getenv("STRATOS_MOCK_IA", "false").lower() == "true":
        return StrategyRecommendation(
            strategy="Build",
            confidence_score=0.95,
            reasoning_summary="[MOCK MODE] El agente sugiere 'Build' debido a que el gap es manejable internamente y el costo de contratación es elevado.",
            action_plan=[
                "Asignar mentoría técnica interna.",
                "Suscripción a plataforma de cursos especializada.",
                "Evaluación de progreso en 4 semanas."
            ]
        )

    try:
        analyst = Agent(
            role='Senior Talent Strategy Consultant',
            goal='Analyze competency gaps and recommend the most effective closure strategy (Buy, Build, Borrow) based on business context.',
            backstory="""You are an expert talent strategist with 20 years of experience at top firms like McKinsey and Korn Ferry. 
            You understand that 'hiring' isn't always the answer. You balance cost, speed, and long-term capability building.
            You prefer 'Build' (Training) for smaller gaps in core talent, and 'Buy' (Hiring) for urgent, large gaps in critical new technologies.""",
            verbose=True,
            allow_delegation=False,
            llm=get_llm(temperature=0.7)
        )

        # 2. Define the Task
        # We pass the input data as a formatted string context
        task_description = f"""
        Analyze the following competency gap and recommend a closure strategy:
        
        CONTEXT:
        - Role: {request.gap_data.role_context}
        - Competency Gap: {request.gap_data.competency_context}
        - Talent Status: {request.gap_data.talent_context}
        - Market Data: {request.gap_data.market_context or 'Not provided (assume average difficulty)'}
        
        YOUR OBJECTIVE:
        Determine if we should:
        - BUY (Hire new talent)
        - BUILD (Train existing talent)
        - BORROW (Contractors/Consultants)
        - BIND (Retain critical talent)
        
        Provide a rationale and a concrete 3-step action plan.
        """

        analysis_task = Task(
            description=task_description,
            agent=analyst,
            expected_output="A structured JSON object with strategy, confidence_score, reasoning_summary, and action_plan.",
            output_json=StrategyRecommendation
        )

        # 3. Create the Crew and Execute
        crew = Crew(
            agents=[analyst],
            tasks=[analysis_task],
            verbose=True,
            process=Process.sequential,
            memory=False, # Disable memory to prevent default OpenAI embedding usage
            embedder={
                "provider": "google",
                "config": {"model": "models/embedding-001"}
            } if os.getenv("UseGoogleEmbeddings") else None 
            # Actually just memory=False is enough to stop it from trying to embed
        )

        result = crew.kickoff()
        
        # Handle CrewOutput object properly
        if hasattr(result, 'json_dict') and result.json_dict:
            return result.json_dict
        elif hasattr(result, 'raw'):
            return json.loads(result.raw)
        elif isinstance(result, str):
            return json.loads(result)
        else:
            return result # Fallback, though likely to fail validation if not dict

    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

class ScenarioRequest(BaseModel):
    company_name: str
    instruction: str
    language: str = "es"
    market_context: dict | None = None

@app.post("/generate-scenario")
def generate_scenario(request: ScenarioRequest):
    # Check if we should use Mock mode
    if os.getenv("STRATOS_MOCK_IA", "false").lower() == "true":
        return {
            "scenario_metadata": {"name": "Mock Scenario", "generated_at": "2026-02-18", "confidence_score": 0.9},
            "capabilities": [],
            "suggested_roles": []
        }

    try:
        # 1. Define the Architect Agent
        architect = Agent(
            role='Strategic Talent Architect',
            goal='Design complex organizational competency maps and hybrid talent blueprints.',
            backstory="""You are a world-class organizational designer. 
            You specialize in translating business instructions into structured capability models.
            You understand the synergy between Human and Synthetic (AI) talent.""",
            verbose=True,
            allow_delegation=False,
            llm=get_llm(temperature=0.3)
        )

        # 2. Define the Task
        # We'll use a very structured prompt similar to the one in Laravel
        task_description = f"""
        DESIGN A TALENT ENGINEERING BLUEPRINT for {request.company_name}.
        
        INSTRUCTIONS:
        {request.instruction}
        
        MARKET DATA:
        {request.market_context or 'Not provided (use general knowledge)'}
        
        LANGUAGE: {request.language}
        
        OUTPUT REQUIREMENTS:
        - Return ONLY a single valid JSON object.
        - Must include scenario_metadata, capabilities (with competencies and skills), competencies (flat list), skills (flat list), suggested_roles, impact_analysis, confidence_score, and assumptions.
        - Every suggested role MUST have a talent_composition (human_percentage, synthetic_percentage).
        """

        architect_task = Task(
            description=task_description,
            agent=architect,
            expected_output="A full, complex JSON object matching the Stratos Talent Engineering Blueprint schema."
        )

        # 3. Execution
        crew = Crew(
            agents=[architect],
            tasks=[architect_task],
            verbose=True,
            memory=False, # Prevent default OpenAI embeddings
            process=Process.sequential
        )

        result = crew.kickoff()
        
        # Result from kickoff might be a string if output_json wasn't used, 
        # but let's assume we want to parse it as JSON.
        import json
        try:
            # CrewAI 0.28+ result is a CrewOutput object, raw is result.raw
            raw_output = str(result)
            return json.loads(raw_output)
        except Exception:
            return {"raw_output": str(result), "error": "Could not parse JSON output"}

    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/interview/chat")
def interview_chat(request: ChatSessionRequest):
    if os.getenv("STRATOS_MOCK_IA", "false").lower() == "true":
        return {"role": "assistant", "content": f"[MOCK] Hola {request.person_name}, cuéntame más sobre tu experiencia en {request.context}."}

    try:
        interviewer = Agent(
            role='Expert Psychometric Interviewer',
            goal='Conduct deep, insightful interviews to understand personality, potential, and values.',
            backstory="""You are a senior clinical psychologist and HR assessment expert. 
            You excel at asking follow-up questions that reveal the underlying character and potential of a person. 
            You are empathetic but thorough. You avoid generic questions and focus on high-impact situational queries.""",
            verbose=True,
            allow_delegation=False,
            llm=get_llm(temperature=0.7)
        )

        history_str = "\n".join([f"{m.role}: {m.content}" for m in request.history])

        task_description = f"""
        CONTINUE THE INTERVIEW with {request.person_name} in {request.language}.
        
        CONTEXT: {request.context}
        
        CURRENT CHAT HISTORY:
        {history_str}
        
        YOUR TASK:
        Analyze the history and provide the NEXT response or question to the user.
        If it's the beginning (empty history), introduce yourself and start the interview politely.
        If the interview seems to reach a natural conclusion, wrap it up gracefully.
        
        RESPONSE FORMAT:
        Provide only the text for the next message. No JSON, no preamble.
        """

        chat_task = Task(
            description=task_description,
            agent=interviewer,
            expected_output="The text content for the next message in the interview."
        )

        crew = Crew(
            agents=[interviewer],
            tasks=[chat_task],
            verbose=True,
            memory=False,
            process=Process.sequential
        )

        result = crew.kickoff()
        return {"role": "assistant", "content": str(result)}

    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/interview/analyze")
def interview_analyze(request: ChatSessionRequest):
    if os.getenv("STRATOS_MOCK_IA", "false").lower() == "true":
        return {
            "traits": [
                {"name": "Resilience", "score": 0.85, "rationale": "Shows high adaptability in complex scenarios."},
                {"name": "Leadership", "score": 0.70, "rationale": "Demonstrates clear vision but lacks delegating practice."}
            ],
            "overall_potential": 0.8,
            "summary_report": "Candidate shows strong technical-strategic alignment."
        }

    try:
        analyst = Agent(
            role='Talent Assessment Analyst',
            goal='Synthesize interview data into a psychometric profile.',
            backstory="""You are an expert in behavioral analysis and psychometry. 
            You transform interview transcripts into objective data points about potential and personality.""",
            verbose=True,
            allow_delegation=False,
            llm=get_llm(temperature=0.2)
        )

        history_str = "\n".join([f"{m.role}: {m.content}" for m in request.history])

        task_description = f"""
        ANALYZE THE FULL INTERVIEW with {request.person_name}.
        
        CONTEXT: {request.context}
        
        FULL CHAT HISTORY:
        {history_str}
        
        YOUR TASK:
        Generates a psychometric profile based on the conversation. 
        Identify at least 3 key traits (scores 0.0 to 1.0) with their rationales.
        Calculate an overall potential score.
        Write a concise summary report.
        
        OUTPUT FORMAT:
        Must be a JSON object matching AnalysisResponse model.
        Return ONLY the JSON.
        """

        analysis_task = Task(
            description=task_description,
            agent=analyst,
            expected_output="A structured JSON object with psychometric traits and summary.",
            output_json=AnalysisResponse
        )

        crew = Crew(
            agents=[analyst],
            tasks=[analysis_task],
            verbose=True,
            memory=False,
            process=Process.sequential
        )

        result = crew.kickoff()
        
        if hasattr(result, 'json_dict') and result.json_dict:
            return result.json_dict
        return json.loads(str(result))

    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/interview/analyze-360")
def interview_analyze_360(request: ThreeSixtyAnalysisRequest):
    if os.getenv("STRATOS_MOCK_IA", "false").lower() == "true":
        return {
            "traits": [
                {
                    "name": "Adaptabilidad Avanzada", 
                    "score": 0.92, 
                    "rationale": "Demostró una capacidad excepcional para pivotar entre contextos técnicos y de negocio durante la entrevista, validado por testimonios de sus pares sobre su manejo de crisis."
                },
                {
                    "name": "Liderazgo de Influencia", 
                    "score": 0.78, 
                    "rationale": "Aunque no tiene reportes directos, sus pares lo identifican como el líder informal del equipo en decisiones arquitectónicas."
                }
            ],
            "overall_potential": 0.88,
            "summary_report": "[MOCK] Perfil 360 analizado con éxito. El sujeto presenta una alta alineación con el rol de Arquiteco de Soluciones, destacando en competencias transversales de resiliencia.",
            "blind_spots": ["Auto-percepción de liderazgo vs realidad del equipo: El sujeto se ve como contribuidor individual, pero el equipo lo percibe como mentor."],
            "ai_reasoning_flow": [
                "Extracción de entidades clave de la transcripción de entrevista.",
                "Correlación de feedback de pares con hitos de proyectos en el historial.",
                "Detección de discrepancias en auto-percepción de liderazgo (Nivel 4 de confianza).",
                "Cruce de datos con KPIs de desempeño histórico.",
                "Generación de síntesis de rasgos psicométricos finales."
            ]
        }

    try:
        analyst = Agent(
            role='Expert Talent Assessment Analyst',
            goal='Synthesize multiple sources of information to create a 360-degree psychometric profile.',
            backstory="""You are a world-class organizational psychologist. You excel at finding 
            patterns between what a person says and what others observe. You identify 'blind spots' 
            and 'hidden strengths' with pinpoint accuracy.""",
            llm=DeepSeekLLM(),
            verbose=True,
            allow_delegation=False
        )

        history_str = "\n".join([f"{m.role}: {m.content}" for m in request.interview_history])
        feedback_str = "\n".join([f"[{f.relationship}]: {f.content}" for f in request.external_feedback])

        task_description = f"""
        Analyze the following person: {request.person_name}
        
        SOURCE 1: PSYCHOMETRIC INTERVIEW
        {history_str}
        
        SOURCE 2: EXTERNAL FEEDBACK (PEERS, SUPERVISORS, ETC.)
        {feedback_str}
        
        YOUR TASK:
        1. Synthesize both sources to generate a high-precision psychometric profile.
        2. Identify at least 3 traits with scores and rationales.
        3. Identify specific BLIND SPOTS (differences between self-perception in interview and external feedback).
        4. Calculate an overall potential score.
        5. Document the logical steps taken (ai_reasoning_flow) to reach these conclusions.
        6. Write a comprehensive summary report in {request.language}.
        
        OUTPUT FORMAT:
        Must be a JSON object matching AnalysisResponse model.
        Return ONLY the JSON.
        """

        analysis_task = Task(
            description=task_description,
            agent=analyst,
            expected_output="A structured 360-degree JSON report.",
            output_json=AnalysisResponse
        )

        crew = Crew(
            agents=[analyst],
            tasks=[analysis_task],
            verbose=True,
            memory=False,
            process=Process.sequential
        )

        result = crew.kickoff()
        
        if hasattr(result, 'json_dict') and result.json_dict:
            return result.json_dict
        return json.loads(str(result))

    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)
