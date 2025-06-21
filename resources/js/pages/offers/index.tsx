// resources/js/Pages/Offers/List.tsx
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { LazyOfferCard } from '@/components/individual/LazyOfferCard';
import { useState, useEffect, useRef, useCallback } from "react";
import { ChevronDown, ChevronUp, Filter, X } from "lucide-react";
import { OfferFilterBar } from '@/components/individual/OfferFilterBar';
import { useOffers } from '@/hooks/use-offers';

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
  offerer_type: string;
  offer_user: string;
  offer_company: string;
  logo_path: string;
  reward_total_cents: number;
  reward_offerer_percent: number;
  created_at: string;
  average_rating: number;
  status: "draft" | "live" | "hidden" | "matched" | "deleted";
  has_application?: boolean;
  application_status?: string;
  application_id?: number;
  is_owner?: boolean;
  in_match?: boolean;
};

export default function Index({ offers: initialOffers, pagination: initialPagination }) {
  const [showFilters, setShowFilters] = useState(false);
  const [search, setSearch] = useState({ title: "", offer_company: "" });
  const [filters, setFilters] = useState({
    offerer_type: "",
    status: "",
    average_rating_min: 0,
    created_at_from: "",
  });
  const [sort, setSort] = useState({ field: "created_at", direction: "desc" });
  
  // Offers Hook für Infinite Scrolling und serverseitige Filterung
  const { 
    offers, 
    loading, 
    error, 
    loadMore, 
    updateDelayedFilters,
    updateImmediateFilters,
    applyFilters,
    hasMore 
  } = useOffers();

  // Observer für Infinite Scrolling
  const observer = useRef<IntersectionObserver | null>(null);
  const lastOfferElementRef = useCallback((node: HTMLDivElement | null) => {
    if (loading) return;
    if (observer.current) observer.current.disconnect();
    
    observer.current = new IntersectionObserver(entries => {
      if (entries && entries.length > 0 && entries[0].isIntersecting && hasMore) {
        loadMore();
      }
    }, { rootMargin: '200px' }); // Frühzeitig laden, wenn 200px vor dem Ende
    
    if (node) observer.current.observe(node);
  }, [loading, hasMore, loadMore]);

  // Verzögerte Filter-Änderungen speichern
  useEffect(() => {
    updateDelayedFilters({
      title: search.title,
      offer_company: search.offer_company,
      created_at_from: filters.created_at_from
    });
  }, [search, filters.created_at_from, updateDelayedFilters]);
  
  // Sofortige Filter-Änderungen speichern und anwenden
  useEffect(() => {
    updateImmediateFilters({
      offerer_type: filters.offerer_type,
      status: filters.status,
      average_rating_min: filters.average_rating_min,
      sort_field: sort.field,
      sort_direction: sort.direction
    });
  }, [filters.offerer_type, filters.status, filters.average_rating_min, sort, updateImmediateFilters]);

  // Funktion zum Anwenden der Filter
  const handleApplyFilters = () => {
    applyFilters();
    if (isMobile) {
      setShowFilters(false);
    }
  };
  
  // Diese Funktion wird nicht mehr benötigt, da die sofortigen Filter direkt im Hook angewendet werden

  // Mobile-Detection (einfach, für Demo-Zwecke)
  const [isMobile, setIsMobile] = useState(false);
  useEffect(() => {
    const checkMobile = () => setIsMobile(window.innerWidth < 768);
    checkMobile();
    window.addEventListener('resize', checkMobile);
    return () => window.removeEventListener('resize', checkMobile);
  }, []);

  // Filter Button für die Header-Navigation
  const FilterButton = () => (
    <button
      className="flex items-center gap-2 px-4 py-2 bg-zinc-100 dark:bg-zinc-800 rounded hover:bg-zinc-200 dark:hover:bg-zinc-700 transition"
      onClick={() => setShowFilters(!showFilters)}
      aria-label="Filter & Suche öffnen"
      type="button"
    >
      {showFilters ? <Filter className="w-6 h-6 " /> : <Filter className="w-6 h-6 "  />}  { /* Filter & Suche */ }
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
                onApplyFilters={handleApplyFilters}
                onImmediateFilterChange={() => {}}
                onClose={() => setShowFilters(false)}
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
            onApplyFilters={handleApplyFilters}
            onImmediateFilterChange={() => {}}
          />
        </div>
      )}
      <div className="flex flex-col gap-4 rounded-xl p-4">
        {/* Gefilterte & sortierte Liste */}
        <div className="container mx-auto p-4">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 2xl:grid-cols-3 gap-8">
            {offers && offers.length > 0 ? offers.map((offer, index) => {
              // Letztes Element mit Ref für Infinite Scrolling
              if (offers.length === index + 1) {
                return (
                  <div ref={lastOfferElementRef} key={`offer-${offer.id}`}>
                    <LazyOfferCard offer={offer} />
                  </div>
                );
              } else {
                return <LazyOfferCard key={`offer-${offer.id}`} offer={offer} />;
              }
            }) : null}
          </div>
          
          {/* Lade-Indikator */}
          {loading && (
            <div className="flex justify-center my-8">
              <div className="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
            </div>
          )}
          
          {/* Fehleranzeige */}
          {error && (
            <div className="text-center my-8 text-red-500">
              {error}. <button onClick={() => loadMore()} className="text-blue-500 underline">Erneut versuchen</button>
            </div>
          )}
          
          {/* Ende der Liste */}
          {!hasMore && offers.length > 0 && (
            <div className="text-center my-8 text-gray-500">
              Keine weiteren Angebote verfügbar
            </div>
          )}
          
          {/* Keine Ergebnisse */}
          {!loading && offers.length === 0 && (
            <div className="text-center my-8 text-gray-500">
              Keine Angebote gefunden
            </div>
          )}
        </div>
      </div>
    </AppLayout>
  );
}