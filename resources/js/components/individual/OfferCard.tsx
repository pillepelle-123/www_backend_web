import { Link } from '@inertiajs/react';
import { Offer } from '@/pages/offers';
import { Info } from 'lucide-react';
import React, { useState } from 'react';
import { createPortal } from 'react-dom';

interface OfferCardProps {
  offer: Offer;
}

export function OfferCard({ offer }: OfferCardProps) {
  const [tooltipPos, setTooltipPos] = useState<{x: number, y: number} | null>(null);
  const [showTooltip, setShowTooltip] = useState(false);

  const formatCurrency = (cents: number) => {
    return new Intl.NumberFormat("de-DE", {
      style: "currency",
      currency: "EUR",
    }).format(cents / 100);
  };

  const formatDateTime = (date: string) => {
    return new Date(date).toLocaleDateString('de-DE', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      hour12: false,
    }).replace(',', ' | ');
  };

  const getTooltipText = (type: string) => {
    if (type === 'Werbender') {
      return 'Hat einen Account und möchte Sie werben.';
    } else if (type === 'Beworbener') {
      return 'Hat noch keinen Account und möchte von Ihnen beworben werden.';
    }
  };

//   const formatPercent = (number: number) => {
//     return new Intl.NumberFormat("de-DE", {
//         style: 'percent',
//     }).format(1 - number);
//   };

  const truncateTitle = (text: string) => {
    if (!text) return '';
    if (text.length <= 47) return text;
    return text.substring(0, 44) + '...';
  };

  const truncateCompany = (text: string) => {
    if (!text) return '';
    if (text.length <= 40) return text;
    return text.substring(0, 37) + '...';
  };

  const truncateDescription = (text: string) => {
    if (!text) return '';
    if (text.length <= 200) return text;
    return text.substring(0, 197) + '...';
  };

  return (
    <div className="bg-white dark:bg-white/10 rounded-xl shadow-lg hover:shadow-lg transition-shadow duration-300 relative overflow-visible">
      {offer.logo_path && (
        <div className="absolute right-0 top-0 w-[50%] pointer-events-none">
          <div className="relative w-full h-full">
            {/* <img src="storage/company_logos/apple.svg" alt="Logo" /> */}
            <img
              src={`/storage/${offer.logo_path}`}
              alt="Firmenlogo"
              className="w-full h-full object-contain"
            />

            <div className="absolute inset-0 bg-radial-[at_90%_10%] from-transparent dark:from-transparent via-white dark:via-[#535258] via-69% to-white dark:to-[#535258]" />
          </div>
        </div>
      )}
      <div className="p-6 relative">
      <div className="w-[75%] items-start text-xs mb-4">{formatDateTime(offer.created_at) }
        </div>
        <div className="flex justify-between w-[75%] items-start  min-h-26">
          <h3 className="text-xl font-semibold text-gray-900 dark:text-white group w-full">
            <span className="relative block w-full">
              <span className="relative z-10 block w-full">{truncateTitle(offer.title)}</span>
              {offer.title.length > 47 && (
                <span className="absolute -left-2 -top-2 w-full z-20 pointer-events-none transition-opacity duration-300 opacity-0 group-hover:opacity-100">
                  <span className="block w-full bg-neutral-800 bg-opacity-95 text-white text-xl  rounded-lg text-left whitespace-pre-line p-2">
                    {offer.title}
                  </span>
                </span>
              )}
            </span>
          </h3>
          {/* <span className="px-3 py-1 text-sm font-medium rounded-sm bg-zinc-800 dark:bg-zinc-800">
            {offer.offer_company}
          </span> */}
        </div>
        <div className="space-y-3">
          <div className="flex items-center text-gray-600 dark:text-gray-300 group w-full">
            <span className="relative block w-full">
              <span className="relative z-10 block w-full">{truncateCompany(offer.offer_company)}</span>
              {offer.offer_company.length > 40 && (
                <span className="absolute -left-2 -top-2 w-full z-20 pointer-events-none transition-opacity duration-300 opacity-0 group-hover:opacity-100">
                  <span className="block w-full bg-neutral-800 bg-opacity-95 text-white text-base rounded-lg text-left whitespace-pre-line p-2">
                    {offer.offer_company}
                  </span>
                </span>
              )}
            </span>
          </div>
          {offer.description && (
            <div className="text-gray-600 dark:text-gray-300 min-h-22">
              {/* <p className="font-medium mb-1">Beschreibung:</p> */}
              <p className="text-sm">{truncateDescription(offer.description)}</p>
            </div>
          )}

          <div className="flex justify-between items-center pt-4 min-h-14">
              <div className="text-gray-600 dark:text-gray-300">
              <span className="font-medium">Gesamte Prämie:<br/></span>
              <span className="ml-2">{formatCurrency(offer.reward_total_cents)}</span>
              </div>
              <div className="text-gray-600 dark:text-gray-300">
                <span className="font-medium">Anteil für Sie:<br/></span>
                <span className="ml-2">{
                formatCurrency((1-offer.reward_offerer_percent) * offer.reward_total_cents)
                //formatPercent(offer.reward_offerer_percent)
                }</span>
              </div>


          </div>
          <div className="flex justify-between items-center pt-4">
            <div className="space-y-1">
              <div className="text-gray-600 dark:text-gray-300 flex flex-row gap-2">
                <Link
                href={`/offers/${offer.id}`}
                className="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
                >
                Details anzeigen
                </Link>
              </div>
            </div>
          </div>
          <div className="absolute right-0 bottom-0 w-[30%]">
            <div
              className="flex flex-row w-full h-full bg-neutral-800 bg-opacity-95 text-white text-sm text-right rounded-tl-lg text-left whitespace-pre-line p-1 pr-2 relative group"
              onMouseEnter={e => {
                const rect = (e.currentTarget as HTMLElement).getBoundingClientRect();
                setTooltipPos({ x: rect.left, y: rect.top });
                setShowTooltip(true);
              }}
              onMouseLeave={() => setShowTooltip(false)}
            >
              <Info className="text-white w-4 h-4 mr-2" />
              <span className="block w-full">{offer.offered_by_type}</span>
            </div>
          </div>
        </div>
      </div>
      {showTooltip && tooltipPos && createPortal(
        <span
          className="fixed z-[9999] pointer-events-none transition-opacity duration-300 opacity-100 w-60"
          style={{ left: tooltipPos.x - 110, top: tooltipPos.y }}
        >
          <span className="block w-full bg-neutral-800 bg-opacity-95 text-white text-sm rounded-lg text-left whitespace-pre-line p-2">
          <b>{offer.offered_by_type}:</b> {getTooltipText(offer.offered_by_type)}
          </span>
        </span>,
        document.body
      )}
    </div>
  );
}
