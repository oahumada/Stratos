from fastapi.testclient import TestClient
from app.main import app
import json

client = TestClient(app)

def test_gap_analysis():
    # Payload de prueba:
    # Un "Junior Backend Developer" (Rol 101) que tiene una brecha crítica en "AWS Cloud Infrastructure"
    # Necesita Nivel 3 (Intermedio) pero tiene Nivel 1 (Novato).
    payload = {
        "gap_data": {
            "role_context": {
                "role_id": 101,
                "role_name": "Junior Backend Developer",
                "design_purpose": "Mantener microservicios y apoyar en despliegues cloud"
            },
            "competency_context": {
                "competency_name": "AWS Cloud Infrastructure",
                "required_level": 3,
                "current_level": 1,
                "gap_size": 2
            },
            "talent_context": {
                "current_headcount": 1,
                "talent_status": "Active"
            },
            "market_context": {
                "availability": "High demand, scarce talent", 
                "avg_salary": " कॉम्पetitive (Above Market)"
            }
        }
    }

    print("🤖 [TEST] Enviando caso de prueba al Agente Stratos...")
    print(json.dumps(payload, indent=2, ensure_ascii=False))
    
    try:
        response = client.post("/analyze-gap", json=payload)
        
        if response.status_code == 200:
            print("\n✅ ÉXITO! El Agente respondió con la siguiente estrategia:")
            print("-" * 50)
            print(json.dumps(response.json(), indent=2, ensure_ascii=False))
            print("-" * 50)
        else:
            print(f"\n❌ ERROR ({response.status_code}):")
            print(response.text)
            
    except Exception as e:
        print(f"\n❌ ERROR DE EJECUCIÓN: {str(e)}")
        print("Asegúrate de tener las dependencias instaladas y la API Key configurada.")

if __name__ == "__main__":
    print("Iniciando prueba de integración local de Stratos Intel Service...")
    test_gap_analysis()
