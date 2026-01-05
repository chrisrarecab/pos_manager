import { createInertiaApp, router } from '@inertiajs/react'
import { createRoot } from 'react-dom/client'
import axios, { initCsrf } from './Config/axios'   
import '../css/index.css'
import type { ComponentType } from 'react';
import type { PageProps } from './types/PageProps';
import type { Page } from '@inertiajs/core';

const checkAuth = () => {
  	return window.localStorage.getItem('isAuthenticated') === 'true';
};


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
  });
  
})

/**
 * Nav listener
 */
router.on('success', (event) => {
  	const page = (event as CustomEvent<{ page: Page<PageProps> }>).detail.page;

	const isAuthenticated = page.props.auth.user !== null;
	window.localStorage.setItem('isAuthenticated', String(isAuthenticated));

	if (isAuthenticated && page.url === '/login') {
		window.location.href = '/dashboard'; 
	}

	if (!isAuthenticated && page.url !== '/login') {
		window.location.href = '/login';
	}
});

/**
 * Prevent browser back from showing dashboard after logout
 */
window.addEventListener('popstate', () => {
	const isAuthenticated = checkAuth();
	const path = window.location.pathname;

	if (isAuthenticated && path === '/login') {
		window.location.href = '/dashboard'; 
	}

	if (!isAuthenticated && path !== '/login') {
		window.location.href = '/login';
	}
}); 