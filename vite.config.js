import { defineConfig } from 'vite';
import path from 'path';

export default defineConfig({
  root: '.',
  base: '/',
  build: {
    outDir: 'build',
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: {
        'assets/src/js/main.js': path.resolve(__dirname, 'assets/src/js/main.js'),
        'assets/src/scss/main.scss': path.resolve(__dirname, 'assets/src/scss/main.scss'),
      },
    },
  },
  css: { devSourcemap: true }
});