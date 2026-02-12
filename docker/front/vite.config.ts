import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    vueDevTools(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
  },
  server: {
    host: '0.0.0.0', // Écoute sur toutes les interfaces
    port: 5173,
    strictPort: true, // Échoue si le port est déjà pris
    watch: {
      usePolling: true, // Nécessaire pour WSL2 + Docker
    },
  },
})