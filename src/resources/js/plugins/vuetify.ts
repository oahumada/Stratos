import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import { aliases, mdi } from 'vuetify/iconsets/mdi'
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'

export default createVuetify({
    components,
    directives,
    icons: {
        defaultSet: 'mdi',
        aliases,
        sets: {
            mdi,
        },
    },
    theme: {
        defaultTheme: localStorage.getItem('app-theme') || 'purple',
        themes: {
            purple: {
                dark: false,
                colors: {
                    primary: '#667eea',
                    secondary: '#764ba2',
                    accent: '#f093fb',
                    error: '#f5576c',
                    info: '#4fc3f7',
                    success: '#66bb6a',
                    warning: '#ffa726',
                    background: '#f5f7fa',
                    surface: '#ffffff',
                },
            },
            pastel: {
                dark: false,
                colors: {
                    primary: '#ffb6c1',
                    secondary: '#dda0dd',
                    accent: '#ffd1dc',
                    error: '#ff6b9d',
                    info: '#87ceeb',
                    success: '#98d8c8',
                    warning: '#f7b731',
                    background: '#fef6f6',
                    surface: '#ffffff',
                },
            },
            green: {
                dark: false,
                colors: {
                    primary: '#4caf50',
                    secondary: '#66bb6a',
                    accent: '#81c784',
                    error: '#ef5350',
                    info: '#26c6da',
                    success: '#66bb6a',
                    warning: '#ffa726',
                    background: '#f1f8f4',
                    surface: '#ffffff',
                },
            },
            blue: {
                dark: false,
                colors: {
                    primary: '#2196f3',
                    secondary: '#42a5f5',
                    accent: '#64b5f6',
                    error: '#f44336',
                    info: '#03a9f4',
                    success: '#4caf50',
                    warning: '#ff9800',
                    background: '#e3f2fd',
                    surface: '#ffffff',
                },
            },
        },
    },
})
