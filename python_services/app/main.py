from fastapi import FastAPI, BackgroundTasks, HTTPException
from pydantic import BaseModel, Field
from crewai import Agent, Task, Crew, Process
from langchain_openai import ChatOpenAI
import os
import subprocess
import json
import psycopg2
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

BASE_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), '..'))

app = FastAPI(title="Stratos Intel & ETL Service", version="0.1.0")

DEEPSEEK_BASE_URL = "https://api.deepseek.com"

# Force environment variable for libraries that might check it directly
os.environ["OPENAI_API_BASE"] = os.getenv("OPENAI_API_BASE", DEEPSEEK_BASE_URL)
os.environ["OPENAI_BASE_URL"] = os.getenv("OPENAI_API_BASE", DEEPSEEK_BASE_URL) 
os.environ["OPENAI_API_KEY"] = os.getenv("OPENAI_API_KEY", "")

# Check for API key
if not os.getenv("OPENAI_API_KEY") and os.getenv("STRATOS_MOCK_IA", "false").lower() == "false":
    print("WARNING: OPENAI_API_KEY is not set. The agent will likely fail.")

# --- NÚCLEO DE IDENTIDAD ORGANIZACIONAL ---
# Aquí se cargan la Misión, Visión y Valores. Idealmente esto vendría de la DB.
CULTURE_MANIFESTO = {
    "mission": "Transformar la gestión del talento mediante ingeniería de datos e IA, democratizando el acceso a trayectorias de crecimiento profesional.",
    "vision": "Convertirnos en el sistema operativo del talento para las organizaciones del futuro, donde cada persona alcanza su máximo potencial.",
    "values": [
        "Innovación con Propósito: No creamos tecnología por crearla, sino para resolver problemas humanos.",
        "Transparencia Radical: Creemos en la claridad de datos para la toma de decisiones justa.",
        "Aprendizaje Continuo (Growth Mindset): El potencial es dinámico, no estático.",
        "Excelencia Técnica: Nos apasiona la calidad en cada línea de código y cada insight analítico."
    ],
    "principles": [
        "Las personas primero, el software después.",
        "Datos para empoderar, no para vigilar.",
        "Simplicidad en la complejidad."
    ]
}
# ------------------------------------------

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

@app.get("/")
def read_root():
    return {"status": "online", "message": "Stratos Intelligence Core (Powered by CrewAI & GPT-4o)"}


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
    cultural_fit: float = Field(..., description="Score from 0.0 to 1.0 representing alignment with organizational values")
    success_probability: float = Field(..., description="Probability of success in the target role/context (0.0 to 1.0)")
    summary_report: str
    cultural_analysis: str = Field(..., description="Detailed analysis of alignment with mission, vision, and values")
    team_synergy_preview: str = Field(..., description="Analysis of how this person's profile interacts with a hypothetical or actual team team")
    ai_reasoning_flow: list[str] = Field(default_factory=list, description="The logical steps the AI took to reach the conclusions")
    blind_spots: list[str] = Field(default_factory=list, description="Strengths seen by others but not by the subject, or vice-versa")

class FeedbackItem(BaseModel):
    relationship: str
    question: str | None = None
    content: str | None = None
    skill_context: dict | None = None

class ThreeSixtyAnalysisRequest(BaseModel):
    person_name: str
    interview_history: list[ChatMessage]
    external_feedback: list[FeedbackItem]
    performance_data: list[dict] | None = None
    language: str = "es"

class MatchingRequest(BaseModel):
    candidate_profile: dict = Field(..., description="The psychometric and skills profile of the candidate")
    blueprint: dict = Field(..., description="The requirements and context of the role or scenario blueprint")
    language: str = "es"

class MatchingResponse(BaseModel):
    match_score: float = Field(..., description="Overall resonance score between candidate and blueprint (0.0 to 1.0)")
    resonance_analysis: str = Field(..., description="Detailed explanation of the technical and cultural fit")
    gap_closure_recommendations: list[str] = Field(..., description="Key training or onboarding actions to bridge the gaps")
    success_probability: float = Field(..., description="Predicted probability of success in this specific role (0.0 to 1.0)")
    synergy_prognosis: str = Field(..., description="Prognosis of how this candidate fits into the existing team dynamics")

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
            role='Expert Psychometric Interviewer (DISC & Learning Agility Specialist)',
            goal='Conduct deep, insightful interviews to understand personality, potential, and values through DISC and Learning Agility lenses.',
            backstory="""You are a senior organizational psychologist specializing in DISC profiling and the 4 pillars of Learning Agility. 
            You excel at asking situational follow-up questions that reveal if a person is Dominant (D), Influential (I), Steady (S), or Conscientious (C). 
            You also probe for Mental, People, Change, and Results Agility.
            You are empathetic but thorough, avoiding generic questions to reveal underlying potential.""",
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
            role='Talent Assessment & DISC Analyst',
            goal='Synthesize interview data into a psychometric profile using DISC and Learning Agility frameworks.',
            backstory="""You are an expert in behavioral analysis, DISC profiling, and Learning Agility. 
            You transform interview transcripts into objective data points about potential, categorizing traits within the DISC quadrants and Agility pillars.""",
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
            "cultural_fit": 0.95,
            "success_probability": 0.91,
            "summary_report": "[MOCK] Perfil Unicornio analizado con éxito. El sujeto presenta una alta alineación con el rol de Arquiteco de Soluciones, destacando en competencias transversales de resiliencia y un fit cultural excepcional.",
            "cultural_analysis": "El sujeto personifica el valor de 'Transparencia Radical' al admitir fallos técnicos previos y mostrar una mentalidad de crecimiento (Growth Mindset) alineada con la visión de Stratos.",
            "team_synergy_preview": "Actuará como un acelerador de talento en equipos técnicos, compensando la falta de comunicación de perfiles puramente técnicos con su alta inteligencia emocional y DISC de tipo Influente.",
            "blind_spots": ["Auto-percepción de liderazgo vs realidad del equipo: El sujeto se ve como contribuidor individual, pero el equipo lo percibe como mentor."],
            "ai_reasoning_flow": [
                "Extracción de entidades clave de la transcripción de entrevista.",
                "Correlación de feedback de pares con el Manifiesto de Cultura.",
                "Cruce de datos de DISC con pilares de Learning Agility.",
                "Cálculo de probabilidad de éxito basado en Gaps vs Trayectoria.",
                "Síntesis final de Ingeniería de Talento."
            ]
        }

    try:
        analyst = Agent(
            role='Expert Talent Assessment & DISC Specialist',
            goal='Analyze professional potential, DISC profiling, and Learning Agility from multiple sources.',
            backstory="""You are a world-class organizational psychologist. You excel at finding 
            patterns between what a person says and what others observe, through the lens of DISC (D, I, S, C) and Learning Agility (Mental, People, Change, Results).""",
            llm=DeepSeekLLM(),
            verbose=True,
            allow_delegation=False
        )

        guardian = Agent(
            role='Guardian of Organizational Culture',
            goal='Assess cultural alignment and ethical resonance with the company manifold.',
            backstory=f"""You are the protector of the company's identity. 
            CULTURE MANIFESTO: {json.dumps(CULTURE_MANIFESTO)}
            You look for shared purpose and long-term belonging. You alert on cultural toxins.""",
            llm=DeepSeekLLM(),
            verbose=True,
            allow_delegation=False
        )

        predictor = Agent(
            role='Strategic Success Predictor (Talent ROI Analyst)',
            goal='Predict the probability of success and ROI for this specific talent trajectory.',
            backstory="""You are a data-driven talent economist. You analyze the intersection of 
            Potential, Culture, and Skills Gaps to calculate the success_probability. 
            You look at the 'Time to Peak Performance' and predict team synergy impacts.""",
            llm=DeepSeekLLM(),
            verbose=True,
            allow_delegation=False
        )

        history_str = "\n".join([f"{m.role}: {m.content}" for m in request.interview_history])
        
        def format_feedback(f):
            base = f"[{f.relationship}]"
            if f.skill_context:
                bars_info = f" | BARS Score: {f.skill_context.get('score')} | Skill: {f.skill_context.get('skill')}"
                base += bars_info
            if f.question:
                base += f" | Q: {f.question}"
            base += f" | A: {f.content}"
            return base
            
        feedback_str = "\n".join([format_feedback(f) for f in request.external_feedback])
        performance_str = json.dumps(request.performance_data) if request.performance_data else "No Performance KPI Data"

        task_description = f"""
        EXECUTE HIGH-LEVEL TALENT ENGINEERING ANALYSIS for: {request.person_name}
        
        DATA INPUTS:
        - INTERVIEW: {history_str}
        - 360° FEEDBACK (Includes BARS anchored questions & general feedback): {feedback_str}
        - PERFORMANCE DATA / KPIs: {performance_str}
        
        AGENT DIRECTIVES:
        1. [ANALYST]: Detailed DISC + Learning Agility profiling. (traits, blind_spots). USE BARS SCORES from 360° FEEDBACK to objectively calibrate Technical/Behavioral proficiency levels.
        2. [GUARDIAN]: Deep Cultural Analysis vs Manifest. (cultural_fit, cultural_analysis)
        3. [PREDICTOR]: Calculate success_probability (0-1) and team_synergy_preview. Consider Gaps vs Potential and quantitative KPI Data.
        4. [SYNTHESIS]: Generate objective overall_potential and the final summary_report in {request.language}.
        
        This analysis must be worthy of a Fortune 500 strategic planning board.
        """

        analysis_task = Task(
            description=task_description,
            agent=analyst,
            expected_output="A predictive, multi-agent Talent Engineering JSON report.",
            output_json=AnalysisResponse
        )

        crew = Crew(
            agents=[analyst, guardian, predictor],
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

@app.post("/match-talent", response_model=MatchingResponse)
def match_talent(request: MatchingRequest):
    if os.getenv("STRATOS_MOCK_IA", "false").lower() == "true":
        return {
            "match_score": 0.82,
            "resonance_analysis": "[MOCK] El candidato presenta una resonancia técnica alta (0.9) pero una alineación cultural moderada (0.7) con el valor de 'Transparencia Radical'. Sin embargo, su Learning Agility compensa los gaps estructurales.",
            "gap_closure_recommendations": [
                "Plan de Onboarding centrado en metodologías ágiles propias de la empresa.",
                "Sesión de alineación de valores con el Guardián de Cultura."
            ],
            "success_probability": 0.85,
            "synergy_prognosis": "Fomentará una cultura de feedback constructivo en el equipo receptor."
        }

    try:
        matchmaker = Agent(
            role='Expert Talent Matchmaker & ROI Forecaster',
            goal='Execute high-fidelity resonance analysis between candidate profiles and role blueprints.',
            backstory="""You are an expert in organizational fit and talent engineering. 
            You don't just look for matches; you look for SYNERGY. You analyze the candidate's psychometric 
            traits (DISC/Agility) against the Blueprint requirements (Capabilities/Skills/Composition).
            Your conclusions determine if a hire is a high-ROI strategic investment.""",
            llm=DeepSeekLLM(temperature=0.2),
            verbose=True,
            allow_delegation=False
        )

        task_description = f"""
        PERFORM STRATEGIC MATCHING ANALYSIS:
        
        CANDIDATE PROFILE: {json.dumps(request.candidate_profile)}
        ROLE BLUEPRINT: {json.dumps(request.blueprint)}
        
        YOUR TASK:
        1. Calculate a 'match_score' (0.0-1.0) based on total alignment.
        2. Perform a 'resonance_analysis' (technical and cultural).
        3. Predict 'success_probability' based on historical data patterns and Learning Agility.
        4. Provide 'gap_closure_recommendations' for the first 90 days.
        5. Provide a 'synergy_prognosis' (team fit).
        
        RESPONSE:
        Return ONLY a JSON object matching the MatchingResponse model in {request.language}.
        """

        match_task = Task(
            description=task_description,
            agent=matchmaker,
            expected_output="A structured JSON object with matching scores and resonance analysis.",
            output_json=MatchingResponse
        )

        crew = Crew(
            agents=[matchmaker],
            tasks=[match_task],
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
