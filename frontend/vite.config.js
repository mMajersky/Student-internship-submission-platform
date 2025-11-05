import { fileURLToPath, URL } from 'node:url'

import { defineConfig, loadEnv } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vite.dev/config/
export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '');

  // Use VITE_PROXY_TARGET (for Docker) or VITE_API_URL (for local dev) as proxy target
  const apiTarget = env.VITE_PROXY_TARGET || env.VITE_API_URL || 'http://localhost:8080';

  return {
    plugins: [
      vue(),
    ],
    resolve: {
      alias: {
        '@': fileURLToPath(new URL('./src', import.meta.url))
      },
    },
    server: {
      proxy: {
        '/api': {
          target: apiTarget,
          changeOrigin: true,
          secure: false,
        }
      }
    }
  }
})
