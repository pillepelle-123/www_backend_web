import React, { useState } from "react";
import { Star, StarOff } from "lucide-react";

interface OfferFilterBarFilters {
  offered_by_type: string;
  status: string;
  average_rating_min: number;
  created_at_from: string;
}

interface OfferFilterBarProps {
  search: { title: string; offer_company: string };
  setSearch: (s: { title: string; offer_company: string }) => void;
  filters: OfferFilterBarFilters;
  setFilters: (f: OfferFilterBarFilters) => void;
  sort: { field: string; direction: string };
  setSort: (s: { field: string; direction: string }) => void;
  show: boolean;
  isMobile: boolean;
}

export function OfferFilterBar({
  search,
  setSearch,
  filters,
  setFilters,
  sort,
  setSort,
  show,
  isMobile,
}: OfferFilterBarProps) {
  // Für Sterne-Filter
  const [hoveredStar, setHoveredStar] = useState<number | null>(null);

  // Wrapper-Klassen je nach Modus
  const wrapperClass = isMobile
    ? `fixed top-0 left-0 w-full z-50 bg-white dark:bg-zinc-900 shadow-lg p-4` // floating
    : `w-full bg-white dark:bg-zinc-900 shadow p-2`;

  return (
    <div className={wrapperClass} style={isMobile ? { maxWidth: '100vw' } : {}}>
      {show && (
        <div className="flex flex-wrap gap-4 items-end">
          {/* Suche */}
          <div>
            <label className="block text-xs font-medium">Titel</label>
            <input
              type="text"
              value={search.title}
              onChange={e => setSearch({ ...search, title: e.target.value })}
              className="rounded border px-2 py-1 text-sm w-40"
              placeholder="Titel suchen"
            />
          </div>
          <div>
            <label className="block text-xs font-medium">Firma</label>
            <input
              type="text"
              value={search.offer_company}
              onChange={e => setSearch({ ...search, offer_company: e.target.value })}
              className="rounded border px-2 py-1 text-sm w-40"
              placeholder="Firma suchen"
            />
          </div>
          {/* Filter */}
          <div>
            <label className="block text-xs font-medium">Typ</label>
            <select
              value={filters.offered_by_type}
              onChange={e => setFilters({ ...filters, offered_by_type: e.target.value })}
              className="rounded border px-2 py-1 text-sm w-32"
            >
              <option value="">Alle</option>
              <option value="Werbender">Werbender</option>
              <option value="Beworbener">Beworbener</option>
            </select>
          </div>
          <div>
            <label className="block text-xs font-medium">Status</label>
            <select
              value={filters.status}
              onChange={e => setFilters({ ...filters, status: e.target.value })}
              className="rounded border px-2 py-1 text-sm w-32"
            >
              <option value="">Alle</option>
              <option value="active">Aktiv</option>
              <option value="inactive">Inaktiv</option>
              <option value="closed">Geschlossen</option>
              <option value="matched">Zugewiesen</option>
            </select>
          </div>
          <div>
            <label className="block text-xs font-medium">Ø Bewertung (min)</label>
            <div className="flex gap-1 items-center">
              {[1, 2, 3, 4, 5].map((star) => (
                <button
                  key={star}
                  type="button"
                  className="group"
                  onClick={() => setFilters({ ...filters, average_rating_min: star })}
                  onMouseEnter={() => setHoveredStar(star)}
                  onMouseLeave={() => setHoveredStar(null)}
                >
                  <Star
                    className={`w-5 h-5 ${
                      (hoveredStar ?? filters.average_rating_min) >= star
                        ? 'fill-yellow-500 stroke-yellow-500'
                        : 'fill-none stroke-yellow-500'
                    }`}
                  />
                </button>
              ))}
                <button
                  type="button"
                  className="ml-2 p-1 rounded hover:bg-zinc-200 dark:hover:bg-zinc-700"
                  onClick={() => setFilters({ ...filters, average_rating_min: 0 })}
                  title="Sternefilter zurücksetzen"
                >
                  <StarOff className="w-5 h-5 text-gray-400" />
                </button>
            </div>
          </div>
          <div>
            <label className="block text-xs font-medium">Erstellt (ab)</label>
            <input
              type="date"
              value={filters.created_at_from}
              onChange={e => setFilters({ ...filters, created_at_from: e.target.value })}
              className="rounded border px-2 py-1 text-sm w-32"
            />
          </div>
          {/* Sortierung */}
          <div>
            <label className="block text-xs font-medium">Sortierung</label>
            <select
              value={sort.field + ':' + sort.direction}
              onChange={e => {
                const [field, direction] = e.target.value.split(':');
                setSort({ field, direction });
              }}
              className="rounded border px-2 py-1 text-sm w-40"
            >
              <option value="created_at:desc">Neueste zuerst</option>
              <option value="created_at:asc">Älteste zuerst</option>
              <option value="reward_total_cents:desc">Prämie absteigend</option>
              <option value="reward_total_cents:asc">Prämie aufsteigend</option>
              <option value="reward_offerer_percent:desc">Anteil absteigend</option>
              <option value="reward_offerer_percent:asc">Anteil aufsteigend</option>
              <option value="average_rating:desc">Ø Bewertung absteigend</option>
              <option value="average_rating:asc">Ø Bewertung aufsteigend</option>
            </select>
          </div>
          <button
            type="button"
            className="ml-2 px-3 py-1 rounded bg-zinc-200 dark:bg-zinc-700 text-xs"
            onClick={() => {
              setSearch({ title: "", offer_company: "" });
              setFilters({
                offered_by_type: "",
                status: "",
                average_rating_min: 0,
                created_at_from: "",
              });
              setSort({ field: "created_at", direction: "desc" });
            }}
          >
            Zurücksetzen
          </button>
        </div>
      )}
    </div>
  );
}
