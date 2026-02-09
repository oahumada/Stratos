// @vitest-environment jsdom
import { useScenarioGenerationStore } from '@/stores/scenarioGenerationStore';
import axios from 'axios';
import { createPinia, setActivePinia } from 'pinia';
import { beforeEach, describe, expect, it, vi } from 'vitest';

vi.mock('axios');

describe('Demo generation accept+import flow (programmatic)', () => {
    beforeEach(() => {
        setActivePinia(createPinia());
        vi.mocked(axios.post).mockReset?.();
        vi.mocked(axios.get).mockReset?.();
    });

    it('generates, completes and accepts with import flag', async () => {
        const store = useScenarioGenerationStore();
        // prefill demo data
        store.prefillDemo();

        // stub generate POST
        (axios.post as any).mockImplementation(
            async (url: string, payload: any) => {
                if (url.includes('/generate') && !url.includes('/accept')) {
                    return {
                        data: { data: { id: 999, status: 'processing' } },
                    };
                }
                if (url.includes('/accept')) {
                    // assert import flag present
                    expect(payload).toEqual({ import: true });
                    return { data: { data: { id: 777 } } };
                }
                return { data: {} };
            },
        );

        // stub status GET: first processing, then complete
        let callCount = 0;
        (axios.get as any).mockImplementation(async (url: string) => {
            if (url.includes('/generate/999')) {
                callCount++;
                if (callCount < 2) {
                    return {
                        data: {
                            data: { status: 'processing', llm_response: null },
                        },
                    };
                }
                return {
                    data: {
                        data: {
                            status: 'complete',
                            llm_response: { scenario_id: 777 },
                        },
                    },
                };
            }
            return { data: { data: [] } };
        });

        // perform generate
        const genRes = await store.generate();
        expect(store.generationId).toBe(999);

        // set importAfterAccept and simulate polling/complete
        store.importAfterAccept = true;
        await store.fetchStatus(); // first call -> processing
        await store.fetchStatus(); // second call -> complete and auto-accept should trigger

        // after auto-accept run, importAutoAccepted should be set
        expect(store.importAutoAccepted).toBe(true);
    });
});
