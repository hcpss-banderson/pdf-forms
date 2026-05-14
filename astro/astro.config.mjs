// @ts-check
import { defineConfig } from 'astro/config';
import mdx from '@astrojs/mdx';

// https://astro.build/config
export default defineConfig({
  i18n: {
    defaultLocale: 'en',
    locales: ['en', 'es', 'ko', 'zh'],
    routing: "manual",
  },
  integrations: [mdx()],
  server: {
    allowedHosts: true
  },
  devToolbar: { enabled: true }
});