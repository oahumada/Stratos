import { mount } from '@vue/test-utils'
import { nextTick } from 'vue'
import { describe, it, expect, vi, beforeEach } from 'vitest'
import ChangeSetModal from '@/components/StrategicPlanningScenarios/ChangeSetModal.vue'

// Mock the store used by the component
const mockPreview = { ops: [{ type: 'create_role', payload: { name: 'Role A' } }] }

vi.mock('@/stores/changeSetStore', () => ({
  useChangeSetStore: () => ({
    previewChangeSet: vi.fn().mockResolvedValue(mockPreview),
    canApplyChangeSet: vi.fn().mockResolvedValue({ can_apply: true }),
    applyChangeSet: vi.fn().mockResolvedValue({}),
    approveChangeSet: vi.fn().mockResolvedValue({}),
    rejectChangeSet: vi.fn().mockResolvedValue({}),
  }),
}))

describe('ChangeSetModal.vue', () => {
  beforeEach(() => {
    vi.resetAllMocks()
  })

  it('renders ops and allows revert (UI-only)', async () => {
    const wrapper = mount(ChangeSetModal, {
      props: { id: 1, title: 'Test CS' },
      global: { stubs: { transition: false } },
    })

    // wait for preview to load
    await nextTick()
    await new Promise((r) => setTimeout(r, 0))

    expect(wrapper.text()).toContain('Operaciones')
    expect(wrapper.text()).toContain('create_role')

    // find revert button and click
    const revertBtn = wrapper.find('button:contains("Revertir")')
    // fallback: search by text manually if CSS selector not supported
    let button = revertBtn
    if (!button.exists()) {
      const buttons = wrapper.findAll('button')
      button = buttons.find((b) => b.text().includes('Revertir')) || buttons[0]
    }

    await button.trigger('click')
    await nextTick()

    // After revert, the op should be removed from view (preview.ops becomes empty)
    expect(wrapper.text()).not.toContain('create_role')
  })
})
