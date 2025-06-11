// resources/js/Pages/Offers/List.tsx
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { LazyOfferCard } from '@/components/individual/LazyOfferCard';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Show Offers',
        href: '/offers',
    },
];

export type Offer = {
  id: number;
  title: string;
  description: string;
  offered_by_type: string;
  offer_user: string;
  offer_company: string;
  logo_url: string;
  reward_total_cents: number;
  reward_offerer_percent: number;
//   user: {
//     name: string;
//   };
//   company: {
//     name: string;
//     logo_url: string;
//   };

  status: "active" | "inactive" | "closed" | 'matched';
};

export default function Index({ offers }: { offers: Offer[] }) {
  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Dashboard" />
      <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <div className="container mx-auto p-4">
          <h1 className="text-2xl font-bold mb-6">Offers</h1>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-8">
            {offers.map((offer) => (
              <LazyOfferCard key={offer.id} offer={offer} />
            ))}
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
