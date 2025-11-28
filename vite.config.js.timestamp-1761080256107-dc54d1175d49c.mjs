// vite.config.js
import vue from "file:///home/eduarduar/dev/projectWeb/node_modules/.pnpm/@vitejs+plugin-vue@5.2.4_vite@5.4.19_@types+node@24.0.10_sass@1.76.0__vue@3.5.17_typescript@5.8.3_/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import vueJsx from "file:///home/eduarduar/dev/projectWeb/node_modules/.pnpm/@vitejs+plugin-vue-jsx@4.2.0_vite@5.4.19_@types+node@24.0.10_sass@1.76.0__vue@3.5.17_typescript@5.8.3_/node_modules/@vitejs/plugin-vue-jsx/dist/index.mjs";
import laravel from "file:///home/eduarduar/dev/projectWeb/node_modules/.pnpm/laravel-vite-plugin@1.3.0_vite@5.4.19_@types+node@24.0.10_sass@1.76.0_/node_modules/laravel-vite-plugin/dist/index.js";
import { fileURLToPath } from "node:url";
import AutoImport from "file:///home/eduarduar/dev/projectWeb/node_modules/.pnpm/unplugin-auto-import@0.18.6_@vueuse+core@10.11.1_vue@3.5.17_typescript@5.8.3___rollup@4.44.2/node_modules/unplugin-auto-import/dist/vite.js";
import Components from "file:///home/eduarduar/dev/projectWeb/node_modules/.pnpm/unplugin-vue-components@0.27.5_@babel+parser@7.28.0_rollup@4.44.2_vue@3.5.17_typescript@5.8.3_/node_modules/unplugin-vue-components/dist/vite.js";
import { defineConfig } from "file:///home/eduarduar/dev/projectWeb/node_modules/.pnpm/vite@5.4.19_@types+node@24.0.10_sass@1.76.0/node_modules/vite/dist/node/index.js";
import requireTransform from "file:///home/eduarduar/dev/projectWeb/node_modules/.pnpm/vite-plugin-require-transform@1.0.21/node_modules/vite-plugin-require-transform/dist/index.mjs";
import vueDevTools from "file:///home/eduarduar/dev/projectWeb/node_modules/.pnpm/vite-plugin-vue-devtools@7.7.7_rollup@4.44.2_vite@5.4.19_@types+node@24.0.10_sass@1.76.0__vue@3.5.17_typescript@5.8.3_/node_modules/vite-plugin-vue-devtools/dist/vite.mjs";
import vuetify from "file:///home/eduarduar/dev/projectWeb/node_modules/.pnpm/vite-plugin-vuetify@2.0.3_vite@5.4.19_@types+node@24.0.10_sass@1.76.0__vue@3.5.17_typescript@5.8.3__vuetify@3.7.5/node_modules/vite-plugin-vuetify/dist/index.mjs";
import svgLoader from "file:///home/eduarduar/dev/projectWeb/node_modules/.pnpm/vite-svg-loader@5.1.0_vue@3.5.17_typescript@5.8.3_/node_modules/vite-svg-loader/index.js";
var __vite_injected_original_import_meta_url = "file:///home/eduarduar/dev/projectWeb/vite.config.js";
var vite_config_default = defineConfig({
  plugins: [
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false
        }
      }
    }),
    vueDevTools(),
    vueJsx(),
    laravel({
      input: ["resources/js/main.js"],
      refresh: true
    }),
    // Docs: https://github.com/vuetifyjs/vuetify-loader/tree/master/packages/vite-plugin
    vuetify({
      styles: {
        configFile: "resources/styles/variables/_vuetify.scss"
      }
    }),
    Components({
      dirs: ["resources/js/@core/components", "resources/js/components"],
      dts: true,
      resolvers: [
        (componentName) => {
          if (componentName === "VueApexCharts")
            return { name: "default", from: "vue3-apexcharts", as: "VueApexCharts" };
        }
      ]
    }),
    // Docs: https://github.com/antfu/unplugin-auto-import#unplugin-auto-import
    AutoImport({
      imports: ["vue", "vue-router", "@vueuse/core", "@vueuse/math", "pinia"],
      vueTemplate: true,
      // ℹ️ Disabled to avoid confusion & accidental usage
      ignore: ["useCookies", "useStorage"],
      eslintrc: {
        enabled: true,
        filepath: "./.eslintrc-auto-import.json"
      }
    }),
    svgLoader(),
    requireTransform()
  ],
  define: { "process.env": {} },
  resolve: {
    alias: {
      "@core-scss": fileURLToPath(new URL("./resources/styles/@core", __vite_injected_original_import_meta_url)),
      "@": fileURLToPath(new URL("./resources/js", __vite_injected_original_import_meta_url)),
      "@core": fileURLToPath(new URL("./resources/js/@core", __vite_injected_original_import_meta_url)),
      "@layouts": fileURLToPath(new URL("./resources/js/@layouts", __vite_injected_original_import_meta_url)),
      "@images": fileURLToPath(new URL("./resources/images/", __vite_injected_original_import_meta_url)),
      "@styles": fileURLToPath(new URL("./resources/styles/", __vite_injected_original_import_meta_url)),
      "@configured-variables": fileURLToPath(new URL("./resources/styles/variables/_template.scss", __vite_injected_original_import_meta_url)),
      "tailwind-config": fileURLToPath(
        new URL("./tailwind.config.js", __vite_injected_original_import_meta_url)
      )
    }
  },
  build: {
    chunkSizeWarningLimit: 5e3
  },
  optimizeDeps: {
    exclude: ["vuetify"],
    entries: [
      "./resources/js/**/*.vue"
    ]
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCIvaG9tZS9lZHVhcmR1YXIvZGV2L3Byb2plY3RXZWJcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfZmlsZW5hbWUgPSBcIi9ob21lL2VkdWFyZHVhci9kZXYvcHJvamVjdFdlYi92aXRlLmNvbmZpZy5qc1wiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9pbXBvcnRfbWV0YV91cmwgPSBcImZpbGU6Ly8vaG9tZS9lZHVhcmR1YXIvZGV2L3Byb2plY3RXZWIvdml0ZS5jb25maWcuanNcIjtpbXBvcnQgdnVlIGZyb20gJ0B2aXRlanMvcGx1Z2luLXZ1ZSdcbmltcG9ydCB2dWVKc3ggZnJvbSAnQHZpdGVqcy9wbHVnaW4tdnVlLWpzeCdcbmltcG9ydCBsYXJhdmVsIGZyb20gJ2xhcmF2ZWwtdml0ZS1wbHVnaW4nXG5pbXBvcnQgeyBmaWxlVVJMVG9QYXRoIH0gZnJvbSAnbm9kZTp1cmwnXG5pbXBvcnQgQXV0b0ltcG9ydCBmcm9tICd1bnBsdWdpbi1hdXRvLWltcG9ydC92aXRlJ1xuaW1wb3J0IENvbXBvbmVudHMgZnJvbSAndW5wbHVnaW4tdnVlLWNvbXBvbmVudHMvdml0ZSdcbmltcG9ydCB7IGRlZmluZUNvbmZpZyB9IGZyb20gJ3ZpdGUnXG5pbXBvcnQgcmVxdWlyZVRyYW5zZm9ybSBmcm9tICd2aXRlLXBsdWdpbi1yZXF1aXJlLXRyYW5zZm9ybSdcbmltcG9ydCB2dWVEZXZUb29scyBmcm9tIFwidml0ZS1wbHVnaW4tdnVlLWRldnRvb2xzXCJcbmltcG9ydCB2dWV0aWZ5IGZyb20gJ3ZpdGUtcGx1Z2luLXZ1ZXRpZnknXG5pbXBvcnQgc3ZnTG9hZGVyIGZyb20gJ3ZpdGUtc3ZnLWxvYWRlcidcblxuLy8gaHR0cHM6Ly92aXRlanMuZGV2L2NvbmZpZy9cbmV4cG9ydCBkZWZhdWx0IGRlZmluZUNvbmZpZyh7XG4gIHBsdWdpbnM6IFt2dWUoe1xuICAgIHRlbXBsYXRlOiB7XG4gICAgICB0cmFuc2Zvcm1Bc3NldFVybHM6IHtcbiAgICAgICAgYmFzZTogbnVsbCxcbiAgICAgICAgaW5jbHVkZUFic29sdXRlOiBmYWxzZSxcbiAgICAgIH0sXG4gICAgfSxcbiAgfSksXG4gIHZ1ZURldlRvb2xzKCksXG4gIHZ1ZUpzeCgpLFxuICBsYXJhdmVsKHtcbiAgICBpbnB1dDogWydyZXNvdXJjZXMvanMvbWFpbi5qcyddLFxuICAgIHJlZnJlc2g6IHRydWUsXG4gIH0pLCAvLyBEb2NzOiBodHRwczovL2dpdGh1Yi5jb20vdnVldGlmeWpzL3Z1ZXRpZnktbG9hZGVyL3RyZWUvbWFzdGVyL3BhY2thZ2VzL3ZpdGUtcGx1Z2luXG4gIHZ1ZXRpZnkoe1xuICAgIHN0eWxlczoge1xuICAgICAgY29uZmlnRmlsZTogJ3Jlc291cmNlcy9zdHlsZXMvdmFyaWFibGVzL192dWV0aWZ5LnNjc3MnLFxuICAgIH0sXG4gIH0pLFxuICBDb21wb25lbnRzKHtcbiAgICBkaXJzOiBbJ3Jlc291cmNlcy9qcy9AY29yZS9jb21wb25lbnRzJywgJ3Jlc291cmNlcy9qcy9jb21wb25lbnRzJ10sXG4gICAgZHRzOiB0cnVlLFxuICAgIHJlc29sdmVyczogW1xuICAgICAgY29tcG9uZW50TmFtZSA9PiB7XG4gICAgICAgIC8vIEF1dG8gaW1wb3J0IGBWdWVBcGV4Q2hhcnRzYFxuICAgICAgICBpZiAoY29tcG9uZW50TmFtZSA9PT0gJ1Z1ZUFwZXhDaGFydHMnKVxuICAgICAgICAgIHJldHVybiB7IG5hbWU6ICdkZWZhdWx0JywgZnJvbTogJ3Z1ZTMtYXBleGNoYXJ0cycsIGFzOiAnVnVlQXBleENoYXJ0cycgfVxuICAgICAgfSxcbiAgICBdLFxuICB9KSwgLy8gRG9jczogaHR0cHM6Ly9naXRodWIuY29tL2FudGZ1L3VucGx1Z2luLWF1dG8taW1wb3J0I3VucGx1Z2luLWF1dG8taW1wb3J0XG4gIEF1dG9JbXBvcnQoe1xuICAgIGltcG9ydHM6IFsndnVlJywgJ3Z1ZS1yb3V0ZXInLCAnQHZ1ZXVzZS9jb3JlJywgJ0B2dWV1c2UvbWF0aCcsICdwaW5pYSddLFxuICAgIHZ1ZVRlbXBsYXRlOiB0cnVlLFxuXG4gICAgLy8gXHUyMTM5XHVGRTBGIERpc2FibGVkIHRvIGF2b2lkIGNvbmZ1c2lvbiAmIGFjY2lkZW50YWwgdXNhZ2VcbiAgICBpZ25vcmU6IFsndXNlQ29va2llcycsICd1c2VTdG9yYWdlJ10sXG4gICAgZXNsaW50cmM6IHtcbiAgICAgIGVuYWJsZWQ6IHRydWUsXG4gICAgICBmaWxlcGF0aDogJy4vLmVzbGludHJjLWF1dG8taW1wb3J0Lmpzb24nLFxuICAgIH0sXG4gIH0pLFxuICBzdmdMb2FkZXIoKSxcbiAgcmVxdWlyZVRyYW5zZm9ybSgpXSxcbiAgZGVmaW5lOiB7ICdwcm9jZXNzLmVudic6IHt9IH0sXG4gIHJlc29sdmU6IHtcbiAgICBhbGlhczoge1xuICAgICAgJ0Bjb3JlLXNjc3MnOiBmaWxlVVJMVG9QYXRoKG5ldyBVUkwoJy4vcmVzb3VyY2VzL3N0eWxlcy9AY29yZScsIGltcG9ydC5tZXRhLnVybCkpLFxuICAgICAgJ0AnOiBmaWxlVVJMVG9QYXRoKG5ldyBVUkwoJy4vcmVzb3VyY2VzL2pzJywgaW1wb3J0Lm1ldGEudXJsKSksXG4gICAgICAnQGNvcmUnOiBmaWxlVVJMVG9QYXRoKG5ldyBVUkwoJy4vcmVzb3VyY2VzL2pzL0Bjb3JlJywgaW1wb3J0Lm1ldGEudXJsKSksXG4gICAgICAnQGxheW91dHMnOiBmaWxlVVJMVG9QYXRoKG5ldyBVUkwoJy4vcmVzb3VyY2VzL2pzL0BsYXlvdXRzJywgaW1wb3J0Lm1ldGEudXJsKSksXG4gICAgICAnQGltYWdlcyc6IGZpbGVVUkxUb1BhdGgobmV3IFVSTCgnLi9yZXNvdXJjZXMvaW1hZ2VzLycsIGltcG9ydC5tZXRhLnVybCkpLFxuICAgICAgJ0BzdHlsZXMnOiBmaWxlVVJMVG9QYXRoKG5ldyBVUkwoJy4vcmVzb3VyY2VzL3N0eWxlcy8nLCBpbXBvcnQubWV0YS51cmwpKSxcbiAgICAgICdAY29uZmlndXJlZC12YXJpYWJsZXMnOiBmaWxlVVJMVG9QYXRoKG5ldyBVUkwoJy4vcmVzb3VyY2VzL3N0eWxlcy92YXJpYWJsZXMvX3RlbXBsYXRlLnNjc3MnLCBpbXBvcnQubWV0YS51cmwpKSxcbiAgICAgIFwidGFpbHdpbmQtY29uZmlnXCI6IGZpbGVVUkxUb1BhdGgoXG4gICAgICAgIG5ldyBVUkwoXCIuL3RhaWx3aW5kLmNvbmZpZy5qc1wiLCBpbXBvcnQubWV0YS51cmwpLFxuICAgICAgKSxcbiAgICB9LFxuICB9LFxuICBidWlsZDoge1xuICAgIGNodW5rU2l6ZVdhcm5pbmdMaW1pdDogNTAwMCxcbiAgfSxcbiAgb3B0aW1pemVEZXBzOiB7XG4gICAgZXhjbHVkZTogWyd2dWV0aWZ5J10sXG4gICAgZW50cmllczogW1xuICAgICAgJy4vcmVzb3VyY2VzL2pzLyoqLyoudnVlJyxcbiAgICBdLFxuICB9LFxufSlcbiJdLAogICJtYXBwaW5ncyI6ICI7QUFBNFEsT0FBTyxTQUFTO0FBQzVSLE9BQU8sWUFBWTtBQUNuQixPQUFPLGFBQWE7QUFDcEIsU0FBUyxxQkFBcUI7QUFDOUIsT0FBTyxnQkFBZ0I7QUFDdkIsT0FBTyxnQkFBZ0I7QUFDdkIsU0FBUyxvQkFBb0I7QUFDN0IsT0FBTyxzQkFBc0I7QUFDN0IsT0FBTyxpQkFBaUI7QUFDeEIsT0FBTyxhQUFhO0FBQ3BCLE9BQU8sZUFBZTtBQVY4SSxJQUFNLDJDQUEyQztBQWFyTixJQUFPLHNCQUFRLGFBQWE7QUFBQSxFQUMxQixTQUFTO0FBQUEsSUFBQyxJQUFJO0FBQUEsTUFDWixVQUFVO0FBQUEsUUFDUixvQkFBb0I7QUFBQSxVQUNsQixNQUFNO0FBQUEsVUFDTixpQkFBaUI7QUFBQSxRQUNuQjtBQUFBLE1BQ0Y7QUFBQSxJQUNGLENBQUM7QUFBQSxJQUNELFlBQVk7QUFBQSxJQUNaLE9BQU87QUFBQSxJQUNQLFFBQVE7QUFBQSxNQUNOLE9BQU8sQ0FBQyxzQkFBc0I7QUFBQSxNQUM5QixTQUFTO0FBQUEsSUFDWCxDQUFDO0FBQUE7QUFBQSxJQUNELFFBQVE7QUFBQSxNQUNOLFFBQVE7QUFBQSxRQUNOLFlBQVk7QUFBQSxNQUNkO0FBQUEsSUFDRixDQUFDO0FBQUEsSUFDRCxXQUFXO0FBQUEsTUFDVCxNQUFNLENBQUMsaUNBQWlDLHlCQUF5QjtBQUFBLE1BQ2pFLEtBQUs7QUFBQSxNQUNMLFdBQVc7QUFBQSxRQUNULG1CQUFpQjtBQUVmLGNBQUksa0JBQWtCO0FBQ3BCLG1CQUFPLEVBQUUsTUFBTSxXQUFXLE1BQU0sbUJBQW1CLElBQUksZ0JBQWdCO0FBQUEsUUFDM0U7QUFBQSxNQUNGO0FBQUEsSUFDRixDQUFDO0FBQUE7QUFBQSxJQUNELFdBQVc7QUFBQSxNQUNULFNBQVMsQ0FBQyxPQUFPLGNBQWMsZ0JBQWdCLGdCQUFnQixPQUFPO0FBQUEsTUFDdEUsYUFBYTtBQUFBO0FBQUEsTUFHYixRQUFRLENBQUMsY0FBYyxZQUFZO0FBQUEsTUFDbkMsVUFBVTtBQUFBLFFBQ1IsU0FBUztBQUFBLFFBQ1QsVUFBVTtBQUFBLE1BQ1o7QUFBQSxJQUNGLENBQUM7QUFBQSxJQUNELFVBQVU7QUFBQSxJQUNWLGlCQUFpQjtBQUFBLEVBQUM7QUFBQSxFQUNsQixRQUFRLEVBQUUsZUFBZSxDQUFDLEVBQUU7QUFBQSxFQUM1QixTQUFTO0FBQUEsSUFDUCxPQUFPO0FBQUEsTUFDTCxjQUFjLGNBQWMsSUFBSSxJQUFJLDRCQUE0Qix3Q0FBZSxDQUFDO0FBQUEsTUFDaEYsS0FBSyxjQUFjLElBQUksSUFBSSxrQkFBa0Isd0NBQWUsQ0FBQztBQUFBLE1BQzdELFNBQVMsY0FBYyxJQUFJLElBQUksd0JBQXdCLHdDQUFlLENBQUM7QUFBQSxNQUN2RSxZQUFZLGNBQWMsSUFBSSxJQUFJLDJCQUEyQix3Q0FBZSxDQUFDO0FBQUEsTUFDN0UsV0FBVyxjQUFjLElBQUksSUFBSSx1QkFBdUIsd0NBQWUsQ0FBQztBQUFBLE1BQ3hFLFdBQVcsY0FBYyxJQUFJLElBQUksdUJBQXVCLHdDQUFlLENBQUM7QUFBQSxNQUN4RSx5QkFBeUIsY0FBYyxJQUFJLElBQUksK0NBQStDLHdDQUFlLENBQUM7QUFBQSxNQUM5RyxtQkFBbUI7QUFBQSxRQUNqQixJQUFJLElBQUksd0JBQXdCLHdDQUFlO0FBQUEsTUFDakQ7QUFBQSxJQUNGO0FBQUEsRUFDRjtBQUFBLEVBQ0EsT0FBTztBQUFBLElBQ0wsdUJBQXVCO0FBQUEsRUFDekI7QUFBQSxFQUNBLGNBQWM7QUFBQSxJQUNaLFNBQVMsQ0FBQyxTQUFTO0FBQUEsSUFDbkIsU0FBUztBQUFBLE1BQ1A7QUFBQSxJQUNGO0FBQUEsRUFDRjtBQUNGLENBQUM7IiwKICAibmFtZXMiOiBbXQp9Cg==
