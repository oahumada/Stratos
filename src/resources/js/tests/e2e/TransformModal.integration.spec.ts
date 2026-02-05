import { render, fireEvent, waitFor } from '@testing-library/vue'
import TransformModal from '@/Pages/Scenario/TransformModal.vue'
import { describe, it, expect, vi, beforeEach } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'

vi.mock('axios', () => ({
  default: {
    post: vi.fn(),
    get: vi.fn(),
  }
}))

import axios from 'axios'

describe('TransformModal integration', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    // reset mocks
    ;(axios.post as any).mockReset()
    ;(axios.get as any).mockReset()
  })

  it('loads versions, allows editing BARS and submits transform', async () => {
    // initial GET for versions on mount
    ;(axios.get as any).mockResolvedValueOnce({ data: { data: [] } })

    const { getByLabelText, getByText, getByTestId, emitted } = render(TransformModal as any, {
      props: { competencyId: 123 }
    })

    // fill form fields
    const nameInput = getByLabelText('Nombre') as HTMLInputElement
    await fireEvent.update(nameInput, 'Transform Test')

    const descInput = getByLabelText('Descripción') as HTMLTextAreaElement
    await fireEvent.update(descInput, 'Descripción prueba')

    // add a skill via BarsEditor structured UI
    const addSkills = await waitFor(() => getByTestId('add-skills'))
    await fireEvent.click(addSkills)
    const skillInput = getByTestId('skills-0') as HTMLInputElement
    await fireEvent.update(skillInput, 'Skill A')

    // prepare mocks: POST returns created data, GET afterwards returns versions list
    ;(axios.post as any).mockResolvedValueOnce({ data: { data: { id: 999, name: 'v1' } } })
    ;(axios.get as any).mockResolvedValueOnce({ data: { data: [{ id: 999 }] } })

    const submitBtn = getByText('Transformar')
    await fireEvent.click(submitBtn)

    await waitFor(() => {
      expect((axios.post as any)).toHaveBeenCalledWith('/api/competencies/123/transform', expect.objectContaining({ name: 'Transform Test' }))
      const ev = emitted()['transformed']
      expect(ev).toBeTruthy()
      expect(ev[0][0]).toEqual({ id: 999, name: 'v1' })
    })
  })
})
