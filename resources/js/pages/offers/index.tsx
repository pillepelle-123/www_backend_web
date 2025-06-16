// resources/js/Pages/Offers/List.tsx
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { LazyOfferCard } from '@/components/individual/LazyOfferCard';
import { useState, useEffect, useMemo } from "react";
import { Filter, X, Loader2 } from "lucide-react";
import { OfferFilterBar } from '@/components/individual/OfferFilterBar';
import { useOffers, type OfferFilters, type OfferSort } from '@/hooks/use-offers';
import { useInfiniteScroll } from '@/hooks/use-infinite-scroll';
import { useDebounce } from '@/hooks/use-debounce';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Show Offers',
        href: '/offers',
    },
];

export default function Index({ initialOffers }: { initialOffers: any[] }) {
  const [showFilters, setShowFilters] = useState(false);
  const [search, setSearch] = useState({ title: "", offer_company: "" });
  const [filters, setFilters] = useState({
    offered_by_type: "",
    status: "",
    average_rating_min: 0,
    created_at_from: "",
  });
  const [sort, setSort] = useState({ field: "created_at", direction: "desc" });

  // Use the offers hook for data management
  const { offers, loading, hasMore, loadMore, applyFilters } = useOffers();

  // Set up infinite scrolling
  const sentinelRef = useInfiniteScroll({
    hasMore,
    loading,
    onLoadMore: loadMore,
    threshold: 300, // Load more when 300px from bottom
  });

  // Mobile-Detection (einfach, für Demo-Zwecke)
  const [isMobile, setIsMobile] = useState(false);
  useEffect(() => {
    const checkMobile = () => setIsMobile(window.innerWidth < 768);
    checkMobile();
    window.addEventListener('resize', checkMobile);
    return () => window.removeEventListener('resize', checkMobile);
  }, []);

  // Debounce search inputs to avoid too many API calls
  const debouncedSearch = useDebounce(search, 500);

  // Apply filters when they change
  useEffect(() => {
    const offerFilters: OfferFilters = {
      title: debouncedSearch.title || undefined,
      offer_company: debouncedSearch.offer_company || undefined,
      offered_by_type: filters.offered_by_type || undefined,
      status: filters.status || undefined,
      average_rating_min: filters.average_rating_min > 0 ? filters.average_rating_min : undefined,
      created_at_from: filters.created_at_from || undefined,
    };

    const offerSort: OfferSort = {
      field: sort.field,
      direction: sort.direction as 'asc' | 'desc',
    };

    applyFilters(offerFilters, offerSort);
  }, [debouncedSearch, filters, sort, applyFilters]);

  // Filter Button für die Header-Navigation
  const FilterButton = () => (
    <button
      className="flex items-center gap-2 px-4 py-2 bg-zinc-100 dark:bg-zinc-800 rounded hover:bg-zinc-200 dark:hover:bg-zinc-700 transition"
      onClick={() => setShowFilters(!showFilters)}
      aria-label="Filter & Suche öffnen"
      type="button"
    >
      <Filter className="w-6 h-6" />
    </button>
  );

  return (
    <AppLayout
      breadcrumbs={breadcrumbs}
      headerRightContent={!isMobile ? <FilterButton /> : undefined}
    >
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
                isMobile={true}
              />
            </div>
          </div>
        </>
      )}
      {/* Desktop: Filterbar oben */}
      {!isMobile && (
        <div className={`transition-all duration-300 overflow-hidden ${showFilters ? 'max-h-96' : 'max-h-0'}`}>
          <OfferFilterBar
            search={search}
            setSearch={setSearch}
            filters={filters}
            setFilters={setFilters}
            sort={sort}
            setSort={setSort}
            show={showFilters}
            isMobile={false}
          />
        </div>
      )}
      <div className="flex flex-col gap-4 rounded-xl p-4">
        {/* Offers Grid */}
        <div className="container mx-auto p-4">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 2xl:grid-cols-3 gap-8">
            {offers.map((offer) => (
              <LazyOfferCard key={offer.id} offer={offer} />
            ))}
          </div>
          
          {/* Loading indicator */}
          {loading && (
            <div className="flex justify-center items-center py-8">
              <Loader2 className="w-8 h-8 animate-spin text-gray-500" />
              <span className="ml-2 text-gray-500">Lade weitere Angebote...</span>
            </div>
          )}
          
          {/* Infinite scroll sentinel */}
          <div ref={sentinelRef} className="h-4" />
          
          {/* No more data indicator */}
          {!hasMore && offers.length > 0 && (
            <div className="text-center py-8 text-gray-500">
              Alle Angebote wurden geladen.
            </div>
          )}
          
          {/* No offers found */}
          {!loading && offers.length === 0 && (
            <div className="text-center py-12">
              <p className="text-gray-500 text-lg">Keine Angebote gefunden.</p>
              <p className="text-gray-400 text-sm mt-2">
                Versuchen Sie, Ihre Filter anzupassen.
              </p>
            </div>
          )}
        </div>
      </div>
    </AppLayout>
  );
}
