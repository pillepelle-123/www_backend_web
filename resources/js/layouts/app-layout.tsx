import AppLayoutTemplate from '@/layouts/app/app-sidebar-layout';
import { type BreadcrumbItem } from '@/types';
import { type ReactNode } from 'react';

interface AppLayoutProps {
    children: ReactNode;
    breadcrumbs?: BreadcrumbItem[];
    headerRightContent?: ReactNode;
}

export default ({ children, breadcrumbs, headerRightContent, ...props }: AppLayoutProps) => (
    <AppLayoutTemplate
        breadcrumbs={breadcrumbs}
        headerRightContent={headerRightContent}
        {...props}
    >
        {children}
    </AppLayoutTemplate>
);
