export const scenarioMock = {
  id: 1,
  name: 'Adopción de IA',
  description: 'Escenario estratégico: adopción de IA en productos core',
  capabilities: [
    {
      id: 11,
      name: 'System Design',
      importance: 5,
      position_x: 20,
      position_y: 30,
      gap: 2,
      skills: [
        {
          id: 111,
          name: 'Architectural Patterns',
          bars: [
            'Nivel 1: Reconoce patrones básicos.',
            'Nivel 2: Aplica patrones en componentes aislados.',
            'Nivel 3: Diseña subsistemas coherentes.',
            'Nivel 4: Lidera decisiones arquitectónicas.',
            'Nivel 5: Formula y valida estrategias arquitectónicas.'
          ],
          required: 4,
          current: 2
        }
      ]
    },
    {
      id: 12,
      name: 'Data Engineering',
      importance: 4,
      position_x: 65,
      position_y: 20,
      gap: 1,
      skills: [
        {
          id: 121,
          name: 'ETL & Pipelines',
          bars: [
            'Nivel 1: Entiende conceptos ETL.',
            'Nivel 2: Implementa scripts básicos.',
            'Nivel 3: Diseña pipelines eficientes.',
            'Nivel 4: Optimiza y escala pipelines.',
            'Nivel 5: Define estándares de datos y plataforma.'
          ],
          required: 4,
          current: 3
        }
      ]
    },
    {
      id: 13,
      name: 'Product Management',
      importance: 3,
      position_x: 45,
      position_y: 70,
      gap: -1,
      skills: [
        {
          id: 131,
          name: 'Roadmapping',
          bars: [
            'Nivel 1: Participa en sesiones de roadmap.',
            'Nivel 2: Contribuye con prioridades.',
            'Nivel 3: Define entregables de producto.',
            'Nivel 4: Alinea stakeholders y métricas.',
            'Nivel 5: Dirige estrategia de producto integral.'
          ],
          required: 3,
          current: 4
        }
      ]
    }
  ]
}

export type ScenarioMock = typeof scenarioMock
