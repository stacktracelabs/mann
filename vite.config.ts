import {defineConfig} from 'vite'
import vue from '@vitejs/plugin-vue'
import * as path from "path";

export default defineConfig({
  plugins: [vue()],
  build: {
    manifest: true,
    lib: {
      entry: path.resolve(__dirname, 'resources/js/main.ts'),
      name: 'mann',
      fileName: format => `mann.${format}.js`,
    },
    rollupOptions: {
      external: [
        'vue',
        '@inertiajs/vue3',
      ],
      output: {
        globals: {
          vue: 'Vue',
          '@inertiajs/vue3': 'Inertia',
        }
      }
    }
  }
})
