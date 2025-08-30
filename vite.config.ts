import { svelte } from "@sveltejs/vite-plugin-svelte";
import tailwindcss from "@tailwindcss/vite";
import laravel from "laravel-vite-plugin";
import path from "path";
import { defineConfig } from "vite";
import { wayfinder } from '@laravel/vite-plugin-wayfinder'

export default defineConfig({
  build: {
    rollupOptions: {
      output: {
        manualChunks: (id) => {
          if (id.includes("node_modules")) {
            return "vendor";
          }
        }
      }
    }
  },
  plugins: [
    laravel({
      input: ["resources/css/app.css", "resources/js/app.ts"],
      ssr: "resources/js/ssr.ts",
      refresh: true
    }),
    svelte(),
    tailwindcss(),
    wayfinder()
  ],
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./resources/js")
    }
  }
});
