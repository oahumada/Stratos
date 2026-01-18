export function createMockPathFromCapability(capability: any) {
  const gap = capability.gap ?? 1
  const actions: any[] = []

  if (gap >= 1) {
    actions.push({
      id: 1,
      title: 'Formación Teórica (Build)',
      type: 'training',
      status: 'pending',
      order: 1,
      impact_weight: 0.3
    })
  }

  if (gap >= 2) {
    actions.push({
      id: 2,
      title: 'Laboratorio de Aplicación Práctica',
      type: 'practice',
      status: 'pending',
      order: 2,
      impact_weight: 0.3
    })
  }

  if (gap >= 3) {
    actions.push({
      id: 3,
      title: 'Proyecto de Implementación Estratégica',
      type: 'project',
      status: 'pending',
      order: 3,
      impact_weight: 0.4
    })
  }

  return {
    id: capability.id,
    name: `Path: ${capability.name}`,
    capability_id: capability.id,
    actions
  }
}

export type DevelopmentPathMock = ReturnType<typeof createMockPathFromCapability>
