import { render, fireEvent } from '@testing-library/vue'
import RoleCompetencyStateModal from '@/components/ScenarioPlanning/Step2/RoleCompetencyStateModal.vue'
import { describe, it, expect } from 'vitest'
import { createPinia } from 'pinia'

describe('RoleCompetencyStateModal', () => {
  it('muestra la leyenda al pulsar el icono de información', async () => {
    const { getByLabelText, getByText } = render(RoleCompetencyStateModal as any, {
      props: {
        visible: true,
        roleId: 1,
        roleName: 'Role X',
        competencyId: 1,
        competencyName: 'Competency Y'
      },
      global: {
        plugins: [createPinia()],
        stubs: {
          TransformModal: true,
          InfoLegend: {
            props: ['modelValue', 'title', 'items', 'icon'],
            template: '<div data-testid="info-legend"><h1>{{ title }}</h1><div v-for="it in items" :key="it.title">{{ it.title }}</div></div>'
          }
        }
      }
    })

    const btn = getByLabelText('Leyenda tipo de asociación')
    await fireEvent.click(btn)

    // verificar que el título de la leyenda aparece
    getByText('Leyenda: Tipos de asociación')
  })
})
