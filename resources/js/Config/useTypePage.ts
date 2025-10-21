import { usePage } from '@inertiajs/react';
import type { PageProps } from '@/types/PageProps';

export const useTypedPage = () => usePage<PageProps>();
