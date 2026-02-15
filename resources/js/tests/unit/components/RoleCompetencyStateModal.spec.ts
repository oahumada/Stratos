import RoleCompetencyStateModal from '@/components/ScenarioPlanning/Step2/RoleCompetencyStateModal.vue';
import { fireEvent, render } from '@testing-library/vue';
import { createPinia } from 'pinia';
import { describe, it } from 'vitest';

describe('RoleCompetencyStateModal', () => {
    it('muestra la leyenda al pulsar el icono de información', async () => {
        const { getByLabelText, getByText } = render(
            RoleCompetencyStateModal as any,
            {
                props: {
                    visible: true,
                    roleId: 1,
                    roleName: 'Role X',
                    competencyId: 1,
                    competencyName: 'Competency Y',
                },
                global: {
                    plugins: [createPinia()],
                    stubs: {
                        TransformModal: true,
                        InfoLegend: {
                            props: ['modelValue', 'title', 'items', 'icon'],
                            template:
                                '<div data-testid="info-legend"><h1>{{ title }}</h1><div v-for="it in items" :key="it.title">{{ it.title }}</div></div>',
                        },
                    },
                },
            },
        );

        const btn = getByLabelText('Leyenda tipo de asociación');
        await fireEvent.click(btn);

        // verificar que el título de la leyenda aparece
        getByText('Metodología: Tipos de Asociación Estratégica');
    });
});
