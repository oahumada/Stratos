import { defineConfig } from 'cypress'

export default defineConfig({
  e2e: {
    baseUrl: process.env.CYPRESS_BASE_URL || 'http://localhost:8000',
    specPattern: 'cypress/e2e/**/*.cy.{js,ts}'
  },
})
