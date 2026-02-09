/**
 * useHierarchicalUpdate
 *
 * Composable for updating hierarchical data structures in Vue reactive trees.
 * Ensures all data sources are updated from leaf to root to maintain consistency
 * when collapsing/expanding nodes.
 *
 * Pattern: When modifying a leaf node, update UPWARD to the root.
 *
 * Hierarchy: Capability → Competency → Skill
 *
 * Data sources (from leaf to root):
 * - grandChildNodes: rendered skill nodes
 * - childNodes: rendered competency nodes
 * - selectedChild: currently selected competency
 * - focusedNode: currently focused capability
 * - nodes: root capabilities array
 *
 * @example
 * ```typescript
 * import { useHierarchicalUpdate } from '@/composables/useHierarchicalUpdate';
 *
 * // Setup with component refs
 * const hierarchicalUpdate = useHierarchicalUpdate(
 *     { nodes, focusedNode, childNodes, selectedChild, grandChildNodes },
 *     { wrapLabel, debug: false }
 * );
 *
 * // Update a skill across all data sources
 * await hierarchicalUpdate.update('skill', freshSkillData, competencyId);
 *
 * // Update a competency
 * await hierarchicalUpdate.update('competency', freshCompData);
 *
 * // Remove a skill
 * await hierarchicalUpdate.remove('skill', skillId, competencyId);
 * ```
 *
 * @see openmemory.md for detailed documentation
 */

import { type Ref, nextTick } from 'vue';

export interface HierarchicalRefs {
    nodes: Ref<any[]>;
    focusedNode: Ref<any>;
    childNodes: Ref<any[]>;
    selectedChild: Ref<any>;
    grandChildNodes: Ref<any[]>;
}

export interface UpdateOptions {
    /** Function to wrap labels for display */
    wrapLabel?: (text: string, maxLength: number) => string;
    /** Enable debug logging */
    debug?: boolean;
}

type EntityType = 'skill' | 'competency' | 'capability';

/**
 * Creates a hierarchical update manager for reactive tree structures
 */
export function useHierarchicalUpdate(
    refs: HierarchicalRefs,
    options: UpdateOptions = {},
) {
    const { nodes, focusedNode, childNodes, selectedChild, grandChildNodes } =
        refs;
    const { wrapLabel = (t: string) => t, debug = false } = options;

    const log = (...args: any[]) => {
        if (debug) console.debug('[useHierarchicalUpdate]', ...args);
    };

    /**
     * Update a skill across all data sources
     */
    function updateSkill(freshSkill: any, competencyId: number) {
        if (!freshSkill || typeof freshSkill.id === 'undefined') {
            log('updateSkill: invalid freshSkill', freshSkill);
            return false;
        }

        const skillId = freshSkill.id;
        log('updateSkill', { skillId, competencyId, name: freshSkill.name });

        // Helper to update skill in an array
        const updateSkillInArray = (skills: any[]): any[] => {
            return skills.map((s: any) => {
                const sId = s.id ?? s.raw?.id;
                if (sId === skillId) {
                    return {
                        ...freshSkill,
                        pivot: s.pivot ?? s.raw?.pivot,
                        raw: { ...freshSkill, pivot: s.pivot ?? s.raw?.pivot },
                    };
                }
                return s;
            });
        };

        // 1. Update grandChildNodes (rendered skill nodes)
        grandChildNodes.value = grandChildNodes.value.map((gn: any) => {
            const gnRawId = gn.raw?.id ?? gn.skillId;
            if (gnRawId === skillId) {
                return {
                    ...gn,
                    name: freshSkill.name,
                    description: freshSkill.description,
                    displayName: wrapLabel(freshSkill.name ?? gn.name, 12),
                    raw: { ...freshSkill, pivot: gn.raw?.pivot },
                };
            }
            return gn;
        });
        log('Updated grandChildNodes');

        // 2. Update selectedChild.skills
        if (
            selectedChild.value &&
            Array.isArray((selectedChild.value as any).skills)
        ) {
            const updatedSkills = updateSkillInArray(
                (selectedChild.value as any).skills,
            );
            selectedChild.value = {
                ...selectedChild.value,
                skills: updatedSkills,
                raw: {
                    ...(selectedChild.value as any).raw,
                    skills: updatedSkills,
                },
            } as any;
            log('Updated selectedChild.skills');
        }

        // 3. Update childNodes[].skills
        childNodes.value = childNodes.value.map((cn: any) => {
            if (cn.compId === competencyId || cn.raw?.id === competencyId) {
                const updatedSkills = Array.isArray(cn.skills)
                    ? updateSkillInArray(cn.skills)
                    : cn.skills;
                return {
                    ...cn,
                    skills: updatedSkills,
                    raw: {
                        ...cn.raw,
                        skills: updatedSkills,
                    },
                };
            }
            return cn;
        });
        log('Updated childNodes[].skills');

        // 4. Update focusedNode.competencies[].skills (source for expandCompetencies)
        const focusedComps = (focusedNode.value as any)?.competencies;
        if (Array.isArray(focusedComps)) {
            const comp = focusedComps.find((c: any) => c.id === competencyId);
            if (comp && Array.isArray(comp.skills)) {
                comp.skills = updateSkillInArray(comp.skills);
                log('Updated focusedNode.competencies[].skills');
            }
        }

        // 5. Update nodes[].competencies[].skills (root source)
        nodes.value = nodes.value.map((n: any) => {
            if (Array.isArray(n.competencies)) {
                const comp = n.competencies.find(
                    (c: any) => c.id === competencyId,
                );
                if (comp && Array.isArray(comp.skills)) {
                    comp.skills = updateSkillInArray(comp.skills);
                }
            }
            return n;
        });
        log('Updated nodes[].competencies[].skills');

        return true;
    }

    /**
     * Update a competency across all data sources
     */
    function updateCompetency(freshComp: any, capabilityId?: number) {
        if (!freshComp || typeof freshComp.id === 'undefined') {
            log('updateCompetency: invalid freshComp', freshComp);
            return false;
        }

        const compId = freshComp.id;
        log('updateCompetency', { compId, capabilityId, name: freshComp.name });

        // Helper to merge competency data
        const mergeCompData = (existing: any) => ({
            name: freshComp.name ?? existing.name,
            description: freshComp.description ?? existing.description,
            readiness: freshComp.readiness ?? existing.readiness,
            skills: freshComp.skills ?? existing.skills,
        });

        // 1. Update childNodes (rendered competency nodes)
        childNodes.value = childNodes.value.map((cn: any) => {
            if (cn.compId === compId || cn.raw?.id === compId) {
                return {
                    ...cn,
                    ...mergeCompData(cn),
                    displayName: wrapLabel(freshComp.name ?? cn.name, 14),
                    raw: { ...(cn.raw ?? {}), ...freshComp },
                };
            }
            return cn;
        });
        log('Updated childNodes');

        // 2. Update selectedChild
        if (
            selectedChild.value &&
            ((selectedChild.value as any).compId === compId ||
                (selectedChild.value as any).raw?.id === compId)
        ) {
            selectedChild.value = {
                ...(selectedChild.value as any),
                ...mergeCompData(selectedChild.value),
                displayName: wrapLabel(
                    freshComp.name ?? (selectedChild.value as any).name,
                    14,
                ),
                raw: {
                    ...((selectedChild.value as any).raw ?? {}),
                    ...freshComp,
                },
            } as any;
            log('Updated selectedChild');
        }

        // 3. Update focusedNode.competencies[] (source for expandCompetencies)
        const focusedComps = (focusedNode.value as any)?.competencies;
        if (Array.isArray(focusedComps)) {
            const comp = focusedComps.find((c: any) => c.id === compId);
            if (comp) {
                Object.assign(comp, mergeCompData(comp));
                log('Updated focusedNode.competencies[]');
            }
        }

        // 4. Update nodes[].competencies[] (root source)
        nodes.value = nodes.value.map((n: any) => {
            if (Array.isArray(n.competencies)) {
                const comp = n.competencies.find((c: any) => c.id === compId);
                if (comp) {
                    Object.assign(comp, mergeCompData(comp));
                }
            }
            return n;
        });
        log('Updated nodes[].competencies[]');

        return true;
    }

    /**
     * Update a capability across all data sources
     */
    function updateCapability(freshCap: any) {
        if (!freshCap || typeof freshCap.id === 'undefined') {
            log('updateCapability: invalid freshCap', freshCap);
            return false;
        }

        const capId = freshCap.id;
        log('updateCapability', { capId, name: freshCap.name });

        // Helper to merge capability data
        const mergeCapData = (existing: any) => ({
            name: freshCap.name ?? existing.name,
            description: freshCap.description ?? existing.description,
            competencies: freshCap.competencies ?? existing.competencies,
        });

        // 1. Update nodes[] (rendered capability nodes / root source)
        nodes.value = nodes.value.map((n: any) => {
            if (n.id === capId) {
                return {
                    ...n,
                    ...mergeCapData(n),
                    displayName: wrapLabel(freshCap.name ?? n.name, 16),
                    raw: { ...(n.raw ?? {}), ...freshCap },
                };
            }
            return n;
        });
        log('Updated nodes[]');

        // 2. Update focusedNode if it's the same capability
        if (focusedNode.value && (focusedNode.value as any).id === capId) {
            Object.assign(focusedNode.value, mergeCapData(focusedNode.value));
            (focusedNode.value as any).displayName = wrapLabel(
                freshCap.name ?? (focusedNode.value as any).name,
                16,
            );
            if ((focusedNode.value as any).raw) {
                Object.assign((focusedNode.value as any).raw, freshCap);
            }
            log('Updated focusedNode');
        }

        return true;
    }

    /**
     * Remove a skill from all data sources
     */
    function removeSkill(skillId: number, competencyId: number) {
        log('removeSkill', { skillId, competencyId });

        const filterSkill = (skills: any[]) =>
            skills.filter((s: any) => (s.id ?? s.raw?.id) !== skillId);

        // 1. Remove from grandChildNodes
        grandChildNodes.value = grandChildNodes.value.filter((g: any) => {
            const nodeSkillId = g.skillId ?? g.raw?.id ?? g.raw?.raw?.id;
            return nodeSkillId !== skillId && g.id !== skillId;
        });

        // 2. Remove from selectedChild.skills
        if (
            selectedChild.value &&
            Array.isArray((selectedChild.value as any).skills)
        ) {
            (selectedChild.value as any).skills = filterSkill(
                (selectedChild.value as any).skills,
            );
        }
        if (
            selectedChild.value &&
            Array.isArray((selectedChild.value as any).raw?.skills)
        ) {
            (selectedChild.value as any).raw.skills = filterSkill(
                (selectedChild.value as any).raw.skills,
            );
        }

        // 3. Remove from focusedNode.competencies[].skills
        const focusedComps = (focusedNode.value as any)?.competencies;
        if (Array.isArray(focusedComps)) {
            const comp = focusedComps.find((c: any) => c.id === competencyId);
            if (comp && Array.isArray(comp.skills)) {
                comp.skills = filterSkill(comp.skills);
            }
        }

        // 4. Remove from childNodes[].skills
        const childNode = childNodes.value.find(
            (c: any) => c.compId === competencyId,
        );
        if (childNode) {
            if (Array.isArray(childNode.skills)) {
                childNode.skills = filterSkill(childNode.skills);
            }
            if (Array.isArray(childNode.raw?.skills)) {
                childNode.raw.skills = filterSkill(childNode.raw.skills);
            }
        }

        // 5. Remove from nodes[].competencies[].skills
        nodes.value.forEach((n: any) => {
            if (Array.isArray(n.competencies)) {
                const comp = n.competencies.find(
                    (c: any) => c.id === competencyId,
                );
                if (comp && Array.isArray(comp.skills)) {
                    comp.skills = filterSkill(comp.skills);
                }
            }
        });

        return true;
    }

    /**
     * Generic update function that routes to the appropriate handler
     */
    async function update(
        entityType: EntityType,
        freshData: any,
        parentId?: number,
    ): Promise<boolean> {
        let result = false;

        switch (entityType) {
            case 'skill':
                if (!parentId) {
                    log('updateSkill requires parentId (competencyId)');
                    return false;
                }
                result = updateSkill(freshData, parentId);
                break;
            case 'competency':
                result = updateCompetency(freshData, parentId);
                break;
            case 'capability':
                result = updateCapability(freshData);
                break;
        }

        // Force Vue to detect changes
        await nextTick();

        return result;
    }

    /**
     * Generic remove function
     */
    async function remove(
        entityType: EntityType,
        entityId: number,
        parentId?: number,
    ): Promise<boolean> {
        let result = false;

        switch (entityType) {
            case 'skill':
                if (!parentId) {
                    log('removeSkill requires parentId (competencyId)');
                    return false;
                }
                result = removeSkill(entityId, parentId);
                break;
            // TODO: Add removeCompetency and removeCapability if needed
        }

        await nextTick();
        return result;
    }

    return {
        // Specific methods
        updateSkill,
        updateCompetency,
        updateCapability,
        removeSkill,
        // Generic methods
        update,
        remove,
    };
}

export type HierarchicalUpdateManager = ReturnType<
    typeof useHierarchicalUpdate
>;
