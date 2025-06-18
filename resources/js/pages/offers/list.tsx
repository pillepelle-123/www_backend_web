// import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
// import { useOffers } from '../hooks/useOffers.';
import { Offer } from '@/types/offer';
import { useState } from 'react';
import { useOffers } from '@/hooks/use-offers';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Show Offers',
        href: '/offers',
    },
];

export default function Index() {

  const [currentPage, setCurrentPage] = useState(1);
  const { offers, pagination } = useOffers(currentPage);

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
        <Head title="Dashboard" />
        <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 ">
        {/* <div className="grid auto-rows-min gap-4 md:grid-cols-3"> */}

        <div className="container mx-auto p-4">
      <h1 className="text-2xl font-bold mb-4">Offers</h1>
{/* border-sidebar-border/70 dark:border-sidebar-border relative aspect-video overflow-hidden rounded-xl border */}
      <div className="overflow-x-auto">
        <table className="min-w-full bg-white">
          <thead>
            <tr>
              <th className="py-2 px-4 border-b-4">ID</th>
              <th className="py-2 px-4 border-b-4">Title</th>
              <th className="py-2 px-4 border-b-4">Reward</th>
              <th className="py-2 px-4 border-b-4">Offerer Percent</th>
              <th className="py-2 px-4 border-b-4">Status</th>
              <th className="py-2 px-4 border-b-4">User</th>

            </tr>
          </thead>
          <tbody>
            {offers.map((offer: Offer) => (
              <tr key={offer.id}>
                <td className="py-2 px-4 ">{offer.id}</td>
                <td className="py-2 px-4 border-l">{offer.title}</td>
                <td className="py-2 px-4 border-l">{offer.reward_total_cents}</td>
                <td className="py-2 px-4 border-l">{offer.reward_offerer_percent}</td>
                <td className="py-2 px-4 border-l">{offer.status}</td>
                <td className="py-2 px-4 border-l">{offer.user.name}</td>
                {/*<td className="py-2 px-4 border">{offer.company.name}</td>
                <td className="py-2 px-4 border">
                  <span className={`px-2 py-1 rounded-full text-xs ${
                    offer.status === 'active'
                      ? 'bg-green-100 text-green-800'
                      : 'bg-red-100 text-red-800'
                  }`}>
                    {offer.status}
                  </span>
                </td>

                  reward_total_cents: number;
                    reward_offerer_percent: number;
                    status: string;


                */}
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Pagination Controls */}
      <div className="flex justify-between mt-4">
        <button
          onClick={() => setCurrentPage(p => Math.max(p - 1, 1))}
          disabled={currentPage === 1}
          className="px-4 py-2 bg-gray-200 rounded disabled:opacity-50"
        >
          Previous
        </button>

        <span>Page {pagination.current_page} of {pagination.last_page}</span>

        <button
          onClick={() => setCurrentPage(p => Math.min(p + 1, pagination.last_page))}
          disabled={currentPage === pagination.last_page}
          className="px-4 py-2 bg-gray-200 rounded disabled:opacity-50"
        >
          Next
        </button>
      </div>

    </div>
    {/* </div> */}
    </div>
    </AppLayout>
  );
}
