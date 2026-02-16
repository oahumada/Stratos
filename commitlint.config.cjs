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
    'subject-case': [0], // Relaxed: allow any case in subject
    'header-max-length': [2, 'always', 500],
    'body-max-line-length': [0], // No limit on body line length
    'footer-max-line-length': [0], // No limit on footer line length
  },
};
