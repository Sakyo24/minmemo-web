import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  server: {
    host: true,
    hmr: {
      host: 'localhost',
    },
  },
  plugins: [
    laravel({
      input: ['resources/sass/app.scss', 'resources/js/app.js'],
      refresh: true,
    }),
    vue(),
  ],
});
