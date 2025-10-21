import { createInertiaApp } from '@inertiajs/react'
import { createRoot } from 'react-dom/client'
import axios, { initCsrf } from './Config/axios'   
import '../css/index.css'
import type { ComponentType } from 'react';

initCsrf()
  .then(() => {
  createInertiaApp({
    resolve: (name) => {
      const pages = import.meta.glob('./Pages/**/*.tsx', { eager: true }) as Record<
        string,
        { default: ComponentType<any> }
      >;

      const page = pages[`./Pages/${name}.tsx`];
      if (!page) {
        throw new Error(`Page not found: ${name}`);
      }
      return page.default;

    },
    setup({ el, App, props }) {
      createRoot(el).render(<App {...props} />)
    },
  })
})