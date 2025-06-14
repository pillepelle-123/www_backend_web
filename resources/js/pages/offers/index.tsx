// resources/js/Pages/Offers/List.tsx
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { LazyOfferCard } from '@/components/individual/LazyOfferCard';
import { useState, useEffect } from "react";
import { ChevronDown, ChevronUp, Filter, X } from "lucide-react";
import { OfferFilterBar } from '@/components/individual/OfferFilterBar';

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
  logo_path: string;
  reward_total_cents: number;
  reward_offerer_percent: number;
  created_at: string;
  average_rating: number;

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
  const [showFilters, setShowFilters] = useState(false);
  const [search, setSearch] = useState({ title: "", offer_company: "" });
  const [filters, setFilters] = useState({
    offered_by_type: "",
    status: "",
    average_rating_min: 0,
    created_at_from: "",
  });
  const [sort, setSort] = useState({ field: "created_at", direction: "desc" });

  // Mobile-Detection (einfach, für Demo-Zwecke)
  const [isMobile, setIsMobile] = useState(false);
  useEffect(() => {
    const checkMobile = () => setIsMobile(window.innerWidth < 768);
    checkMobile();
    window.addEventListener('resize', checkMobile);
    return () => window.removeEventListener('resize', checkMobile);
  }, []);

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="List of Offers" />
      {/* Mobile: Button unten rechts, Sheet von rechts */}
      {isMobile && !showFilters && (
        <button
          className="fixed bottom-4 right-4 z-50 rounded-full bg-zinc-100 dark:bg-zinc-800 p-3 shadow-lg border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition"
          onClick={() => setShowFilters(true)}
          aria-label="Filter & Sortierung öffnen"
          type="button"
        >
          <Filter className="w-6 h-6 text-gray-700 dark:text-gray-200" />
        </button>
      )}
      {isMobile && showFilters && (
        <>
          <div
            className="fixed inset-0 z-40 bg-black/30"
            onClick={() => setShowFilters(false)}
          />
          <div className="fixed bottom-0 right-0 z-50 w-full max-w-sm h-[80vh] bg-white dark:bg-zinc-900 shadow-lg rounded-tl-2xl flex flex-col animate-slide-in-right">
            <div className="flex justify-between items-center p-4 border-b border-zinc-200 dark:border-zinc-700">
              <span className="font-semibold">Filter & Sortierung</span>
              <button
                onClick={() => setShowFilters(false)}
                className="rounded-full p-2 hover:bg-zinc-200 dark:hover:bg-zinc-700"
              >
                <X className="w-5 h-5" />
              </button>
            </div>
            <div className="flex-1 overflow-y-auto p-4">
              <OfferFilterBar
                search={search}
                setSearch={setSearch}
                filters={filters}
                setFilters={setFilters}
                sort={sort}
                setSort={setSort}
                show={true}
                setShow={setShowFilters}
                isMobile={true}
              />
            </div>
          </div>
        </>
      )}
      {/* Desktop: Filterbar oben */}
      {!isMobile && (
        <OfferFilterBar
          search={search}
          setSearch={setSearch}
          filters={filters}
          setFilters={setFilters}
          sort={sort}
          setSort={setSort}
          show={showFilters}
          setShow={setShowFilters}
          isMobile={false}
        />
      )}
      <div className="flex flex-col gap-4 rounded-xl p-4">
        {/* Gefilterte & sortierte Liste */}
        <div className="container mx-auto p-4">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 2xl:grid-cols-3 gap-8">
            {offers
              .filter(offer =>
                (!search.title || offer.title.toLowerCase().includes(search.title.toLowerCase())) &&
                (!search.offer_company || offer.offer_company.toLowerCase().includes(search.offer_company.toLowerCase())) &&
                (!filters.offered_by_type || offer.offered_by_type === filters.offered_by_type) &&
                (!filters.status || offer.status === filters.status) &&
                (!filters.average_rating_min || offer.average_rating >= filters.average_rating_min) &&
                (!filters.created_at_from || new Date(offer.created_at) >= new Date(filters.created_at_from))
              )
              .sort((a, b) => {
                const { field, direction } = sort;
                let av = a[field];
                let bv = b[field];
                if (field === 'reward_total_cents') {
                  av = a.reward_total_cents;
                  bv = b.reward_total_cents;
                }
                if (field === 'reward_offerer_percent') {
                  av = a.reward_offerer_percent;
                  bv = b.reward_offerer_percent;
                }
                if (field === 'created_at') {
                  av = new Date(a.created_at).getTime();
                  bv = new Date(b.created_at).getTime();
                }
                if (field === 'average_rating') {
                  av = a.average_rating;
                  bv = b.average_rating;
                }
                if (av < bv) return direction === 'asc' ? -1 : 1;
                if (av > bv) return direction === 'asc' ? 1 : -1;
                return 0;
              })
              .map((offer) => (
                <LazyOfferCard key={offer.id} offer={offer} />
              ))}
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
