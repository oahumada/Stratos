import { render, fireEvent } from '@testing-library/vue'
import BarsEditor from '@/components/BarsEditor.vue'
import { describe, it, expect } from 'vitest'

describe('BarsEditor', () => {
  it('emits update:modelValue when adding and editing skills', async () => {
    const { getByTestId, emitted } = render(BarsEditor as any, { props: { modelValue: {} } })

    const addSkills = getByTestId('add-skills')
    await fireEvent.click(addSkills)

    const skillsInput = getByTestId('skills-0') as HTMLInputElement
    await fireEvent.update(skillsInput, 'Skill A')

    const ev = emitted()['update:modelValue']
    expect(ev).toBeTruthy()
    const last = ev[ev.length - 1][0]
    expect(last.skills).toEqual(['Skill A'])
  })
})
