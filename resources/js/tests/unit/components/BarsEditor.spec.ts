import BarsEditor from '@/components/BarsEditor.vue';
import { fireEvent, render } from '@testing-library/vue';
import { describe, expect, it } from 'vitest';
import { defineComponent } from 'vue';

describe('BarsEditor', () => {
    it('syncs structured skills through v-model updates', async () => {
        const Host = defineComponent({
            components: { BarsEditor },
            data() {
                return {
                    bars: {},
                };
            },
            template:
                '<div><BarsEditor v-model="bars" /><pre data-testid="bars-state">{{ JSON.stringify(bars) }}</pre></div>',
        });

        const { getByTestId } = render(Host as any);

        const addSkills = getByTestId('add-skills');
        await fireEvent.click(addSkills);

        const skillsInput = getByTestId('skills-0') as HTMLInputElement;
        await fireEvent.update(skillsInput, 'Skill A');

        expect(skillsInput.value).toBe('Skill A');
        expect(getByTestId('bars-state').textContent).toContain('Skill A');
    });
});
