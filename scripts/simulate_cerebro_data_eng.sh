#!/bin/bash

# Configuration
TOKEN="1|sMfTU39wJAo8XWWzdDZ0H4rAMUKHX7KSvNELdonXc11d2926"
BASE_URL="http://127.0.0.1:8000/api"

echo "=========================================================="
echo "🚀 STRATOS CEREBRO: REAL SIMULATION TEST"
echo "Scenario: AI Data Engineering Team Blueprint"
echo "=========================================================="

# 1. Trigger Generation
echo "Step 1: Initializing Scenario Generation via Cerebro..."
RESPONSE=$(curl -s -X POST "$BASE_URL/strategic-planning/scenarios/generate/intel" \
     -H "Authorization: Bearer $TOKEN" \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{
        "company_name": "Stratos Tech Corp",
        "industry": "Deep Tech / AI",
        "company_size": "50-100",
        "current_challenges": "Escalar la infraestructura de datos para soportar agentes autónomos en tiempo real.",
        "current_capabilities": "Pipelines básicos en Python, bases de datos relacionales estándar.",
        "current_gaps": "Falta de perfiles senior en Data Engineering y Arquitectura de Datos.",
        "strategic_goal": "Establecer un equipo de Data Engineering de clase mundial.",
        "key_initiatives": "Implementar un stack moderno (Snowflake/dbt), contratar nucleo del equipo.",
        "time_horizon": "12 meses",
        "milestones": "Q1: Arquitectura definida, Q2: Primeras 3 contrataciones, Q3: MVP de pipeline.",
        "instruction_language": "es"
     }')

echo "Server Response: $RESPONSE"

GEN_ID=$(echo $RESPONSE | jq -r '.data.id')

if [ "$GEN_ID" == "null" ] || [ -z "$GEN_ID" ]; then
    echo "❌ Error: Failed to start generation."
    exit 1
fi

echo "✅ Generation started with ID: $GEN_ID"
echo ""

# 2. Polling for results
echo "Step 2: Polling for Cerebro'\''s reasoning and results..."
echo "Waiting for agents to kickoff... (this may take 30-60 seconds)"

MAX_RETRIES=20
COUNT=0
STATUS="queued"

while [ "$STATUS" != "complete" ] && [ "$COUNT" -lt "$MAX_RETRIES" ]; do
    sleep 5
    COUNT=$((COUNT+1))
    
    # Check Progress (to see chunks)
    PROGRESS=$(curl -s -X GET "$BASE_URL/strategic-planning/scenarios/generate/$GEN_ID/progress" \
         -H "Authorization: Bearer $TOKEN" \
         -H "Accept: application/json")
    
    # Check overall status
    STATUS_RESP=$(curl -s -X GET "$BASE_URL/strategic-planning/scenarios/generate/$GEN_ID" \
         -H "Authorization: Bearer $TOKEN" \
         -H "Accept: application/json")
    
    STATUS=$(echo $STATUS_RESP | jq -r '.data.status')
    REASONING=$(echo $STATUS_RESP | jq -r '.data.llm_response.ai_reasoning_flow // empty')
    
    echo "[$COUNT] Status: $STATUS | Chunks received: $(echo $PROGRESS | jq -r '.progress.received_chunks // 0')"
    
    if [ ! -z "$REASONING" ]; then
        echo "🧠 Cerebro Reasoning Detected: $REASONING"
    fi
done

if [ "$STATUS" == "complete" ]; then
    echo ""
    echo "=========================================================="
    echo "🎊 SIMULATION COMPLETE!"
    echo "=========================================================="
    # Extract roles to show impact
    ROLES=$(echo $STATUS_RESP | jq '.data.llm_response.suggested_roles // "N/A"')
    echo "📋 Suggested Roles:"
    echo "$ROLES" | jq -r '.[] | "- \(.role_name) (Talent: \(.talent_composition.human_percentage)% Human / \(.talent_composition.synthetic_percentage)% Synthetic)"'
else
    echo "❌ Timeout or error: Simulation took too long."
fi
