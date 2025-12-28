module.exports = {
  extends: ['@commitlint/config-conventional'],
  rules: {
    'type-enum': [
      2,
      'always',
      [
        'feat',      // Nueva funcionalidad
        'fix',       // Corrección de bugs
        'docs',      // Cambios en documentación
        'style',     // Cambios de estilo (sin afectar lógica)
        'refactor',  // Refactoring de código
        'perf',      // Mejora de rendimiento
        'test',      // Agregar o actualizar tests
        'chore',     // Cambios en build, dependencias, etc
        'ci',        // Cambios en CI/CD
        'revert',    // Revertir un commit anterior
      ],
    ],
    'type-case': [2, 'always', 'lower-case'],
    'type-empty': [2, 'never'],
    'subject-empty': [2, 'never'],
    'subject-full-stop': [2, 'never', '.'],
    'subject-case': [2, 'always', 'lower-case'],
    'header-max-length': [2, 'always', 100],
  },
};
