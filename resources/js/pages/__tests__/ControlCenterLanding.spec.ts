// @vitest-environment jsdom
import { shallowMount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vitest';

vi.mock('vue-i18n', () => ({
    useI18n: () => ({
        t: (key: string) =>
            ({
                'landings.control_center.title': 'Centro de Control',
                'landings.control_center.slogan':
                    'Gobierno, seguridad y calidad — Supervisa y administra la plataforma.',
                'landings.control_center.modules.rbac_manager.title':
                    'Gestor RBAC',
                'landings.control_center.modules.rbac_manager.description':
                    'Gestión de roles y permisos.',
                'landings.control_center.modules.ai_agent_supervisor.title':
                    'Supervisor de Agentes IA',
                'landings.control_center.modules.ai_agent_supervisor.description':
                    'Supervisión de agentes LLM.',
                'landings.control_center.modules.quality_sentinel.title':
                    'Centinela de Calidad',
                'landings.control_center.modules.quality_sentinel.description':
                    'Monitoreo de fiabilidad y RAGAS.',
                'landings.control_center.modules.stratos_compliance.title':
                    'Stratos Compliance',
                'landings.control_center.modules.stratos_compliance.description':
                    'Auditoría, ISO, GDPR y evidencia verificable externamente.',
                'landings.control_center.modules.ragas_neural_dash.title':
                    'Panel Neural RAGAS',
                'landings.control_center.modules.ragas_neural_dash.description':
                    'Analítica de salidas LLM.',
                'landings.control_center.modules.comando_360.title':
                    'Comando 360',
                'landings.control_center.modules.comando_360.description':
                    'Orquestación de ciclos de evaluación y políticas.',
                'landings.control_center.modules.comando_px.title':
                    'Comando PX',
                'landings.control_center.modules.comando_px.description':
                    'Tablero de burnout y clima.',
                'landings.control_center.modules.cultural_blueprint.title':
                    'Stratos Identity',
                'landings.control_center.modules.cultural_blueprint.description':
                    'Gestión de la Constitución Organizacional.',
            }[key] ?? key),
    }),
}));

import ControlCenterLanding from '../ControlCenter/Landing.vue';

describe('ControlCenter Landing', () => {
    it('includes Stratos Compliance card pointing to compliance audit dashboard', () => {
        const wrapper = shallowMount(ControlCenterLanding, {
            global: {
                stubs: {
                    GroupHeader: true,
                    ModuleCard: {
                        props: ['title', 'description', 'href', 'icon', 'iconColor'],
                        template:
                            '<div class="module-card" :data-title="title" :data-href="href">{{ title }}</div>',
                    },
                },
            },
        });

        const cards = wrapper.findAll('.module-card');
        const complianceCard = cards.find(
            (card) => card.attributes('data-title') === 'Stratos Compliance',
        );

        expect(complianceCard).toBeTruthy();
        expect(complianceCard?.attributes('data-href')).toBe(
            '/quality/compliance-audit',
        );
    });
});