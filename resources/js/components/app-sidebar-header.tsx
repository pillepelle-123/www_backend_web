import { Breadcrumbs } from '@/components/breadcrumbs';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { type BreadcrumbItem as BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/react';
import { type ReactNode } from 'react';

export function AppSidebarHeader({
    breadcrumbs = [],
    rightContent
}: {
    breadcrumbs?: BreadcrumbItemType[],
    rightContent?: ReactNode
}) {
    return (
        <header className="border-sidebar-border/50 flex h-16 shrink-0 items-center gap-2 border-b px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4">
            <div className="flex items-center gap-2 flex-1">
                <SidebarTrigger className="-ml-1" badge={usePage().props.unreadApplicationsCount || 0} />
                <Breadcrumbs breadcrumbs={breadcrumbs} />
            </div>
            {rightContent && (
                <div className="flex items-center">
                    {rightContent}
                </div>
            )}
        </header>
    );
}
