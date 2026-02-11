import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import TalentCompositionCard from '../../../components/TalentEngineering/TalentCompositionCard.vue';

describe('TalentCompositionCard', () => {
    it('renders role info and composition', () => {
        const role = {
            name: 'Developer',
            estimated_fte: 2,
            suggested_agent_type: 'Assistant',
            talent_composition: {
                human_percentage: 60,
                synthetic_percentage: 40,
                strategy_suggestion: 'Build',
                logic_justification: 'Automate tests',
            },
        };

        const wrapper = mount(TalentCompositionCard, {
            props: { role },
            global: {
                stubs: {
                    'v-card': { template: '<div><slot/></div>' },
                    'v-card-text': { template: '<div><slot/></div>' },
                    'v-chip': {
                        template: '<div v-bind="$attrs"><slot/></div>',
                    },
                    'v-icon': { template: '<span><slot/></span>' },
                    'v-progress-linear': { template: '<div><slot/></div>' },
                    'v-alert': { template: '<div><slot/></div>' },
                },
            },
        });

        expect(wrapper.text()).toContain('Developer');
        expect(wrapper.text()).toContain('FTE: 2');
        expect(wrapper.text()).toContain('Build');
        expect(wrapper.text()).toContain('Humano (60%)');
        expect(wrapper.text()).toContain('IA (40%)');
        expect(wrapper.text()).toContain('Automate tests');
        // clase testable añadida al componente (Build -> info)
        expect(wrapper.find('.strategy-info').exists()).toBe(true);
    });
});
