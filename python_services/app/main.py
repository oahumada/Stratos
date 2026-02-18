from fastapi import FastAPI, HTTPException
from pydantic import BaseModel, Field
from crewai import Agent, Task, Crew, Process
from langchain_openai import ChatOpenAI
import os
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

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
        # 1. Define the Agent
        analyst = Agent(
            role='Senior Talent Strategy Consultant',
            goal='Analyze competency gaps and recommend the most effective closure strategy (Buy, Build, Borrow) based on business context.',
            backstory="""You are an expert talent strategist with 20 years of experience at top firms like McKinsey and Korn Ferry. 
            You understand that 'hiring' isn't always the answer. You balance cost, speed, and long-term capability building.
            You prefer 'Build' (Training) for smaller gaps in core talent, and 'Buy' (Hiring) for urgent, large gaps in critical new technologies.""",
            verbose=True,
            allow_delegation=False,
            llm=ChatOpenAI(model_name=os.getenv("OPENAI_MODEL_NAME", "gpt-4o"), temperature=0.7)
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
            process=Process.sequential
        )

        result = crew.kickoff()
        return result

    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)
