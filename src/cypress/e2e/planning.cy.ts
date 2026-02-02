describe('Planning acceptance (Cypress)', () => {
  const email = Cypress.env('E2E_ADMIN_EMAIL') || 'admin@example.com';
  const password = Cypress.env('E2E_ADMIN_PASSWORD') || 'password';

  it('authenticates and shows planning map', () => {
    // Acquire CSRF cookie (Laravel Sanctum)
    cy.request('GET', '/sanctum/csrf-cookie');

    // read XSRF-TOKEN cookie and login via POST so session cookie is set
    cy.getCookie('XSRF-TOKEN').then((c) => {
      const xsrf = c ? decodeURIComponent(c.value) : null;
      cy.request({
        method: 'POST',
        url: '/login',
        headers: xsrf ? { 'X-XSRF-TOKEN': xsrf } : {},
        body: { email, password },
      });
    });

    // visit planning page and assert nodes exist
    cy.visit('/strategic-planning');
    cy.get('svg .nodes g.node-group', { timeout: 30000 }).should('exist');
  });
});
