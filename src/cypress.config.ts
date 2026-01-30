// declare process to satisfy TypeScript in environments without Node types
declare const process: { env: Record<string, string | undefined> }

import { defineConfig } from 'cypress'

export default defineConfig({
  e2e: {
    setupNodeEvents(on, config) {
      on('before:browser:launch', (browser, launchOptions) => {
        if (browser.family === 'chromium' || browser.name === 'chrome') {
          launchOptions.args.push('--ozone-platform=wayland')
          launchOptions.args.push('--enable-features=UseOzonePlatform')
          launchOptions.args.push('--disable-dev-shm-usage')
        }
        return launchOptions
      })
      return config
    },
    baseUrl: process.env.CYPRESS_BASE_URL || 'http://localhost:8000',
    specPattern: 'cypress/e2e/**/*.cy.{js,ts}'
  },
})
