import BulkPeopleImporter from '@/components/Talent/BulkPeopleImporter.vue';
import { mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import { nextTick } from 'vue';

// Mock API calls
vi.mock('@/apiHelper', () => ({
    post: vi.fn(),
}));

// Mock i18n
vi.mock('vue-i18n', () => ({
    useI18n: () => ({
        t: (key: string) => key,
    }),
}));

import { post } from '@/apiHelper';

describe('BulkPeopleImporter.vue', () => {
    beforeEach(() => {
        vi.resetAllMocks();
    });

    it('renders the initial step when opened', async () => {
        const wrapper = mount(BulkPeopleImporter, {
            props: { isOpen: true },
        });

        expect(wrapper.text()).toContain('Stratos Node Aligner');
        expect(wrapper.text()).toContain('Seleccionar Archivo');
    });

    it('moves to step 2 after file upload', async () => {
        const wrapper = mount(BulkPeopleImporter, {
            props: { isOpen: true },
        });

        // Mock text() on File for JSDOM
        const file = new File(
            [
                'name,last_name,email,department,role\nJohn,Doe,john@test.com,Sales,Manager',
            ],
            'test.csv',
            { type: 'text/csv' },
        );
        file.text = vi
            .fn()
            .mockResolvedValue(
                'name,last_name,email,department,role\nJohn,Doe,john@test.com,Sales,Manager',
            );

        // Find input and trigger change
        const input = wrapper.find('input[type="file"]');
        Object.defineProperty(input.element, 'files', {
            value: [file],
        });

        await input.trigger('change');
        await nextTick();
        // Wait for async f.text()
        await new Promise((resolve) => setTimeout(resolve, 0));
        await nextTick();

        expect(wrapper.text()).toContain('Vista Previa y Mapeo');
        expect(wrapper.text()).toContain('john@test.com');
    });

    it('calls analysis API and moves to step 3', async () => {
        (post as any).mockResolvedValue({
            success: true,
            analysis: {
                detected_departments: [
                    {
                        raw_name: 'Sales',
                        status: 'existing',
                        suggested_name: 'Sales',
                    },
                ],
                detected_roles: [
                    {
                        raw_name: 'Manager',
                        status: 'new',
                        suggested_name: 'Sales Manager',
                    },
                ],
                movements: {
                    hires: [{ name: 'John Doe', email: 'john@test.com' }],
                    transfers: [],
                    exits: [],
                },
            },
        });

        const wrapper = mount(BulkPeopleImporter, {
            props: { isOpen: true },
        });

        // Manually set rawData and step to bypass file upload for this test
        (wrapper.vm as any).rawData = [{ email: 'john@test.com' }];
        (wrapper.vm as any).step = 2;
        await nextTick();

        // En el paso 2, el botón de analizar contiene este texto
        const btn = wrapper
            .findAll('button')
            .find((b) => b.text().includes('Analizar'));
        if (btn) await btn.trigger('click');

        await nextTick();
        await new Promise((resolve) => setTimeout(resolve, 100)); // Give time for analysis
        await nextTick();

        expect(post).toHaveBeenCalled();
        expect(wrapper.text()).toContain('Alineación de Departamentos');
        expect(wrapper.text()).toContain('Sales Manager');
    });
});
