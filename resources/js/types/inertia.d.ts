// Ambient module augmentation for Inertia
import type { PageProps as AppPageProps } from '@/Types/PageProps';

declare module '@inertiajs/react' {
  interface PageProps extends AppPageProps {}
}
