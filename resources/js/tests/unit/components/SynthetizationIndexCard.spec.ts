import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import SynthetizationIndexCard from '../../../components/TalentEngineering/SynthetizationIndexCard.vue';

describe('SynthetizationIndexCard', () => {
    it('renders index value', () => {
        const wrapper = mount(SynthetizationIndexCard, {
            props: { index: 42 },
            global: {
                stubs: {
                    'v-card': { template: '<div><slot/></div>' },
                    'v-card-text': { template: '<div><slot/></div>' },
                    'v-progress-circular': { template: '<div><slot/></div>' },
                },
            },
        });

        expect(wrapper.text()).toContain('Índice de Sintetización');
        expect(wrapper.text()).toContain('42%');
    });
});
