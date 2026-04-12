import { defineConfig, loadEnv } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

const adminJsEntries = [
  "resources/js/admin/add-product.js",
  "resources/js/admin/analytics.js",
  "resources/js/admin/complete-orders.js",
  "resources/js/admin/completed-orders-chart.js",
  "resources/js/admin/completed-orders-invoice.js",
  "resources/js/admin/pending-orders.js",
  "resources/js/admin/process-refunds.js",
  "resources/js/admin/profile.js",
  "resources/js/admin/view-orders.js",
];

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), "");
  const devServerHost = env.VITE_DEV_SERVER_HOST;
  const devServerPort = Number(env.VITE_PORT || 5173);

  return {
    plugins: [
      laravel({
        input: [
          "resources/css/app.css",
          "resources/js/app.js",
          ...adminJsEntries,
        ],
        refresh: true,
      }),
      tailwindcss(),
    ],
    server: devServerHost
      ? {
          host: "0.0.0.0",
          port: devServerPort,
          strictPort: true,
          origin: `http://${devServerHost}:${devServerPort}`,
          cors: true,
          hmr: {
            host: devServerHost,
            clientPort: devServerPort,
          },
        }
      : undefined,
  };
});
