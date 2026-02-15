import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import path from 'path'

export default defineConfig({
  plugins: [react()],
  server: {
    // Watch is needed for hot reload in Docker usually
    watch: {
      usePolling: true,
    },
    host: true, // Needed for Docker port mapping
    strictPort: true,
    port: 5173,
    proxy: {
      '/api': {
        target: 'http://backend:80', // Points to the Docker Container Name
        changeOrigin: true,
        secure: false,
      },
    },
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
})
