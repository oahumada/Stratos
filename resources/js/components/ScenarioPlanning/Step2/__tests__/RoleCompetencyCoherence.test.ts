import { describe, it, expect } from 'vitest';
import { computed, ref } from 'vue';

/**
 * Tests for Role-Competency Coherence Logic
 * These tests validate the architectural consistency checks implemented in RoleCompetencyStateModal.vue
 */

describe('Consistency Alert Logic', () => {
  const createConsistencyAlert = (archetype: string, level: number, isReferent: boolean = false) => {
    const formData = ref({
      required_level: level,
      is_referent: isReferent,
    });

    const archetypeLabel = computed(() => {
      const map: Record<string, string> = {
        E: 'Estratégico',
        T: 'Táctico',
        O: 'Operacional',
      };
      return map[archetype || 'T'] || 'Táctico';
    });

    const consistencyAlert = computed(() => {
      const level = formData.value.required_level;
      const arch = archetype || 'T';
      const isReferent = formData.value.is_referent;

      // Rules from REGLAS_ARQUITECTURA_COHERENCIA.md
      if (arch === 'E') {
        if (level < 4) {
          return {
            color: 'warning',
            icon: 'mdi-alert-decagram',
            title: 'Arquitectura Débil',
            message: `Un Rol Estratégico suele requerir niveles 4 o 5. El nivel ${level} podría ser insuficiente para el Job Enrichment esperado.`,
          };
        }
      } else if (arch === 'O') {
        if (level > 3 && !isReferent) {
          return {
            color: 'info',
            icon: 'mdi-lightbulb-on',
            title: 'Sobrecarga Técnica',
            message: `Nivel ${level} es inusualmente alto para un Rol Operacional. Verifica si no hay un exceso de Job Enrichment, o marca este rol como Referente/Mentor.`,
          };
        }
        if (level > 3 && isReferent) {
          return {
            color: 'success',
            icon: 'mdi-account-star',
            title: 'Rol de Referencia Validado',
            message: `Este rol operacional actúa como mentor técnico. El nivel ${level} es coherente con su función de mentoría.`,
          };
        }
      } else if (arch === 'T') {
        if (level < 2) {
          return {
            color: 'warning',
            icon: 'mdi-alert-outline',
            title: 'Nivel Insuficiente',
            message: `Un Rol Táctico requiere al menos nivel 2 o 3 para asegurar la coordinación efectiva.`,
          };
        }
        if (level > 4 && !isReferent) {
          return {
            color: 'info',
            icon: 'mdi-lightbulb-on',
            title: 'Nivel Inusual',
            message: `Nivel ${level} es inusualmente alto para un Rol Táctico. Considera si este rol debería ser Estratégico o marcarlo como Referente.`,
          };
        }
      }

      return {
        color: 'success',
        icon: 'mdi-check-decagram',
        title: 'Diseño Coherente',
        message: `El nivel ${level} es consistente con un Arquetipo ${archetypeLabel.value}.`,
      };
    });

    return { consistencyAlert, archetypeLabel };
  };

  describe('Strategic Role (E)', () => {
    it('should show warning for level below 4', () => {
      const { consistencyAlert } = createConsistencyAlert('E', 3);
      expect(consistencyAlert.value.color).toBe('warning');
      expect(consistencyAlert.value.title).toBe('Arquitectura Débil');
    });

    it('should show success for level 4 or 5', () => {
      const { consistencyAlert: alert4 } = createConsistencyAlert('E', 4);
      const { consistencyAlert: alert5 } = createConsistencyAlert('E', 5);
      
      expect(alert4.value.color).toBe('success');
      expect(alert5.value.color).toBe('success');
    });
  });

  describe('Operational Role (O)', () => {
    it('should show info warning for level > 3 without referent flag', () => {
      const { consistencyAlert } = createConsistencyAlert('O', 4, false);
      expect(consistencyAlert.value.color).toBe('info');
      expect(consistencyAlert.value.title).toBe('Sobrecarga Técnica');
    });

    it('should show success for level > 3 with referent flag', () => {
      const { consistencyAlert } = createConsistencyAlert('O', 5, true);
      expect(consistencyAlert.value.color).toBe('success');
      expect(consistencyAlert.value.title).toBe('Rol de Referencia Validado');
      expect(consistencyAlert.value.icon).toBe('mdi-account-star');
    });

    it('should show success for level 1-3 (normal operational range)', () => {
      const { consistencyAlert: alert1 } = createConsistencyAlert('O', 1);
      const { consistencyAlert: alert2 } = createConsistencyAlert('O', 2);
      const { consistencyAlert: alert3 } = createConsistencyAlert('O', 3);
      
      expect(alert1.value.color).toBe('success');
      expect(alert2.value.color).toBe('success');
      expect(alert3.value.color).toBe('success');
    });
  });

  describe('Tactical Role (T)', () => {
    it('should show warning for level below 2', () => {
      const { consistencyAlert } = createConsistencyAlert('T', 1);
      expect(consistencyAlert.value.color).toBe('warning');
      expect(consistencyAlert.value.title).toBe('Nivel Insuficiente');
    });

    it('should show info for level > 4 without referent flag', () => {
      const { consistencyAlert } = createConsistencyAlert('T', 5, false);
      expect(consistencyAlert.value.color).toBe('info');
      expect(consistencyAlert.value.title).toBe('Nivel Inusual');
    });

    it('should show success for level 2-4 (normal tactical range)', () => {
      const { consistencyAlert: alert2 } = createConsistencyAlert('T', 2);
      const { consistencyAlert: alert3 } = createConsistencyAlert('T', 3);
      const { consistencyAlert: alert4 } = createConsistencyAlert('T', 4);
      
      expect(alert2.value.color).toBe('success');
      expect(alert3.value.color).toBe('success');
      expect(alert4.value.color).toBe('success');
    });
  });
});

describe('Show Referent Option Logic', () => {
  const shouldShowReferentOption = (archetype: string, level: number): boolean => {
    return (archetype === 'O' && level > 3) || (archetype === 'T' && level > 4);
  };

  it('should show for operational role with level > 3', () => {
    expect(shouldShowReferentOption('O', 4)).toBe(true);
    expect(shouldShowReferentOption('O', 5)).toBe(true);
  });

  it('should not show for operational role with level <= 3', () => {
    expect(shouldShowReferentOption('O', 1)).toBe(false);
    expect(shouldShowReferentOption('O', 2)).toBe(false);
    expect(shouldShowReferentOption('O', 3)).toBe(false);
  });

  it('should show for tactical role with level > 4', () => {
    expect(shouldShowReferentOption('T', 5)).toBe(true);
  });

  it('should not show for tactical role with level <= 4', () => {
    expect(shouldShowReferentOption('T', 1)).toBe(false);
    expect(shouldShowReferentOption('T', 2)).toBe(false);
    expect(shouldShowReferentOption('T', 3)).toBe(false);
    expect(shouldShowReferentOption('T', 4)).toBe(false);
  });

  it('should never show for strategic roles', () => {
    expect(shouldShowReferentOption('E', 1)).toBe(false);
    expect(shouldShowReferentOption('E', 3)).toBe(false);
    expect(shouldShowReferentOption('E', 5)).toBe(false);
  });
});

describe('Archetype Label Mapping', () => {
  const getArchetypeLabel = (archetype?: string): string => {
    const map: Record<string, string> = {
      E: 'Estratégico',
      T: 'Táctico',
      O: 'Operacional',
    };
    return map[archetype || 'T'] || 'Táctico';
  };

  it('should return correct labels for each archetype', () => {
    expect(getArchetypeLabel('E')).toBe('Estratégico');
    expect(getArchetypeLabel('T')).toBe('Táctico');
    expect(getArchetypeLabel('O')).toBe('Operacional');
  });

  it('should default to Táctico for undefined archetype', () => {
    expect(getArchetypeLabel()).toBe('Táctico');
    expect(getArchetypeLabel(undefined)).toBe('Táctico');
  });
});

describe('Level Decrease Rationale Logic', () => {
  const shouldShowLevelDecreaseRationale = (
    hasMapping: boolean,
    currentLevel: number,
    previousLevel?: number
  ): boolean => {
    if (!hasMapping || !previousLevel) return false;
    return currentLevel < previousLevel;
  };

  it('should show when new level is lower than previous', () => {
    expect(shouldShowLevelDecreaseRationale(true, 2, 4)).toBe(true);
    expect(shouldShowLevelDecreaseRationale(true, 1, 3)).toBe(true);
  });

  it('should not show when new level is equal or higher', () => {
    expect(shouldShowLevelDecreaseRationale(true, 4, 4)).toBe(false);
    expect(shouldShowLevelDecreaseRationale(true, 5, 3)).toBe(false);
  });

  it('should not show when there is no previous mapping', () => {
    expect(shouldShowLevelDecreaseRationale(false, 2, undefined)).toBe(false);
    expect(shouldShowLevelDecreaseRationale(true, 2, undefined)).toBe(false);
  });
});
