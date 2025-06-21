import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { /*BookOpen, Folder,*/ LayoutGrid, ListTodo, CirclePlus, Mail, Handshake } from 'lucide-react';

import AppLogo from './app-logo';
// import { cn } from '@/lib/utils';
// import { route } from '@/lib/ziggy';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Show Offers',
        href: '/offers',
        icon: ListTodo,
    },
    {
        title: 'Set Offer',
        href: '/offers/create',
        icon: CirclePlus,
    },
    {
        title: 'Nachrichten',
        href: '/applications',
        icon: Mail,
    },
    {
        title: 'Matches',
        href: '/user-matches',
        icon: Handshake,
    },
];

const footerNavItems: NavItem[] = [
    // {
    //     title: 'Repository',
    //     href: 'https://github.com/laravel/react-starter-kit',
    //     icon: Folder,
    // },
    // {
    //     title: 'Documentation',
    //     href: 'https://laravel.com/docs/starter-kits#react',
    //     icon: BookOpen,
    // },
];

export function AppSidebar() {
    const { props } = usePage();
    const unreadCount = props.unreadApplicationsCount || 0;

    // Füge Badge zum Nachrichten-Menüpunkt hinzu
    const navItemsWithBadges = mainNavItems.map(item => {
        if (item.title === 'Nachrichten') {
            return {
                ...item,
                badge: unreadCount
            };
        }
        return item;
    });

    return (
        <Sidebar collapsible="icon" variant="sidebar">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/dashboard" prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={navItemsWithBadges} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
