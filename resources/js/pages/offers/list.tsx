// resources/js/Pages/Offers/List.tsx
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { columns } from "./../../components/individual/dataTable/columns"
import { DataTable } from "./../../components/individual/dataTable/data-table";
// import { ColumnDef } from "@tanstack/react-table";

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Show Offers',
        href: '/offers',
    },
];

export type Offer = {
  id: number;
  title: string;
  offered_by_type: string;
  offer_user: string;
  reward_total_cents: number;
  status: "active" | "inactive" | "closed" | 'matched';
};

// const columns: ColumnDef<Offer>[] = [
//   {
//     accessorKey: "title",
//     header: "Title",
//   },
//   {
//     accessorKey: "offered_by_type",
//     header: "Offered by...",
//   },
//   {
//     accessorKey: "offer_user",
//     header: "User",
//   },
//   {
//     accessorKey: "reward_total_cents",
//     header: "Reward",
//     cell: ({ row }) => {
//       const reward = parseFloat(row.getValue("reward_total_cents"));
//       const formatted = new Intl.NumberFormat("de-DE", {
//         style: "currency",
//         currency: "EUR",

//       }).format(reward);

//       return <div className="font-medium">{formatted}</div>;
//     },
//   },
//   {
//     accessorKey: "status",
//     header: "Status",
//   },
// ];

export default function List({ offers }: { offers: Offer[] }) {
  return (
    <AppLayout breadcrumbs={breadcrumbs}>
        <Head title="Dashboard" />
        <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 ">
            <div className="container mx-auto p-4">
                <h1 className="text-2xl font-bold mb-4">Offers</h1>
                <div className="overflow-x-auto">
                    <DataTable columns={columns} data={offers} />
                </div>
            </div>
        {/* </div> */}
        </div>
        </AppLayout>





//     <AppLayout breadcrumbs={breadcrumbs}>
//       <Head title="Dashboard" />
//       <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 ">
//         {/* <div className="grid auto-rows-min gap-4 md:grid-cols-3"> */}
//         <div className="container mx-auto p-4">
//           <h1 className="text-2xl font-bold mb-4">Offers</h1>
//           {/* border-sidebar-border/70 dark:border-sidebar-border relative aspect-video overflow-hidden rounded-xl border */}
//           <div className="overflow-x-auto">
//             <div className="container mx-auto p-4">
//               <h1 className="text-2xl font-bold mb-4">Offers</h1>
//               <div className="container mx-auto py-10">
//                 <DataTable columns={columns} data={offers} />
//               </div>
//             </div>
//           </div>
//         </div>
//       </div>
//     </AppLayout>
   );
}
