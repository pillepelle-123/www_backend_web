import { Link } from '@inertiajs/react';
import { Offer } from '@/pages/offers';
import { UserRound, UsersRound, Building2, Star, StarHalf, CheckCircle } from 'lucide-react';
import React, { useState } from 'react';
import { createPortal } from 'react-dom';

interface OfferCardProps {
  offer: Offer;
}

export function OfferCard({ offer }: OfferCardProps) {

  // ### Tooltips ###
  const [showRatingTooltip, setShowRatingTooltip] = useState(false);
  const [showTypeTooltip, setShowTypeTooltip] = useState(false);
  const [tooltipPos, setTooltipPos] = useState<{x: number, y: number} | null>(null);

  const getTooltipText = (type: string | undefined | null) => {
    if (type === 'Werbender') {
      return 'Hat einen Account und möchte dich werben.';
    } else if (type === 'Beworbener') {
      return 'Hat noch keinen Account und möchte von dir beworben werden.';
    }
    return '';
  };

  // ### Formatting ###
  const formatCurrency = (cents: number | undefined | null) => {
    if (cents === undefined || cents === null) return '€0,00';
    return new Intl.NumberFormat("de-DE", {
      style: "currency",
      currency: "EUR",
    }).format(cents / 100);
  };

  const formatDateTime = (date: string | undefined | null) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('de-DE', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      hour12: false,
    }).replace(',', ' | ');
  };




//   const formatPercent = (number: number) => {
//     return new Intl.NumberFormat("de-DE", {
//         style: 'percent',
//     }).format(1 - number);
//   };

  // ### Truncation ###
  const truncateTitle = (text: string | undefined | null) => {
    if (!text) return '';
    if (text.length <= 47) return text;
    return text.substring(0, 44) + '...';
  };

  const truncateCompany = (text: string | undefined | null) => {
    if (!text) return '';
    if (text.length <= 40) return text;
    return text.substring(0, 37) + '...';
  };

  const truncateDescription = (text: string | undefined | null) => {
    if (!text) return '';
    if (text.length <= 200) return text;
    return text.substring(0, 197) + '...';
  };

  // ### Star Icons ###
  function getStarIcons(averageRating: number) {
    // Auf halbe Sterne runden
    const rounded = Math.round(averageRating * 2) / 2;
    const fullStars = Math.floor(rounded);
    const hasHalf = rounded % 1 !== 0;
    const emptyStars = 5 - fullStars - (hasHalf ? 1 : 0);
    const stars: React.ReactNode[] = [];
    for (let i = 0; i < fullStars; i++) {
      stars.push(<Star key={i} className="w-3 h-3 fill-yellow-500 stroke-yellow-500" />);
    }
    if (hasHalf) {
      stars.push(
        <span key="half" className="relative inline-block w-3 h-3">
          <Star className="w-3 h-3 fill-none stroke-yellow-500 absolute left-0 top-0" />
          <StarHalf className="w-3 h-3 fill-yellow-500 stroke-yellow-500 absolute left-0 top-0" />
        </span>
      );
    }
    for (let i = 0; i < emptyStars; i++) {
      stars.push(<Star key={fullStars + (hasHalf ? 1 : 0) + i} className="w-3 h-3 fill-none stroke-yellow-500" />);
    }
    return stars;
  }

  return (
    <div className={`bg-white dark:bg-white/10 rounded-xl shadow-lg hover:shadow-lg transition-shadow duration-300 relative overflow-visible`}>

      {offer.status === 'matched' && (
        <div className="absolute -top-2 -left-2 z-10">
          <span className="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
            <CheckCircle className="w-3 h-3" /> Matched
          </span>
        </div>
      )}
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
      <div className={`p-6 relative ${offer.status === 'matched' ? 'opacity-60' : ''}`}>




      <div className="w-[75%] items-start text-xs mb-4">{offer?.created_at ? formatDateTime(offer.created_at) : ''}
        </div>
        <div className="flex justify-between w-[75%] items-start  min-h-26">
          <h3 className="text-xl font-semibold text-gray-900 dark:text-white group w-full">
            <span className="relative block w-full">
              <span className="relative z-10 block w-full">{truncateTitle(offer?.title || '')}</span>
              {offer?.title && offer.title.length > 47 && (
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
        {/* Company, User, Description */}
        <div className="space-y-3">
          <div className="flex gap-2 items-start text-gray-600 dark:text-gray-300 group w-full text-sm">
            <div className="flex flex-none items-start basis-0 grow">

            <Building2 className="flex-none w-5 h-5 mr-1" />
            <div className="flex flex-col items-left grow">
            <span className="relative block w-full">
              <span className="relative z-10 block w-full">{truncateCompany(offer?.offer_company || '')}</span>
              {offer?.offer_company && offer.offer_company.length > 40 && (
                <span className="absolute -left-2 -top-2 w-full z-20 pointer-events-none transition-opacity duration-300 opacity-0 group-hover:opacity-100">
                  <span className="block w-full bg-neutral-800 bg-opacity-95 text-white text-base rounded-lg text-left whitespace-pre-line p-2">
                    {offer.offer_company}
                  </span>
                </span>
              )}
            </span>
            </div>
            </div>
            <div className="flex items-start basis-0 grow">
            <UserRound className=" flex-none w-5 h-5 mr-1 " />
            <div className="flex flex-col items-left grow">

            {/* // Username */}
            <span className="ml-2">{offer?.offer_user || ''}</span>

            {/* // Rating */}
            <span className="ml-2 text-xs text-gray-500 flex flex-row items-center gap-0.5 relative"
              onMouseEnter={e => {
                const rect = (e.currentTarget as HTMLElement).getBoundingClientRect();
                setTooltipPos({ x: rect.left + rect.width / 2, y: rect.top + rect.height * 4 });
                setShowRatingTooltip(true);
              }}
              onMouseLeave={() => setShowRatingTooltip(false)}
            >
              {/* // Star Icons */}
              {getStarIcons(offer.average_rating ?? 0)}
              {showRatingTooltip && tooltipPos && createPortal(
                <span
                  className="fixed z-[9999] pointer-events-none transition-opacity duration-300 opacity-100 w-44 text-center"
                  style={{ left: tooltipPos.x - 80, top: tooltipPos.y - 36 }}
                >
                  {/* // Tooltip */}
                  <span className="block w-fit bg-neutral-800/90 text-white text-xs rounded-lg p-2 shadow-lg">
                    ⌀ Bewertung: {offer.average_rating?.toFixed(2) ?? '0.00'}
                  </span>
                </span>,
                document.body
              )}
            </span>
            </div>
            </div>
          </div>
          {/* {offer.description && (
            <div className="text-gray-600 dark:text-gray-300 min-h-22">
              <p className="text-sm">{truncateDescription(offer.description)}</p>
            </div>
          )} */}
          {/* Reward */}
          <div className="flex gap-2 items-start justify-between pt-4 min-h-14 w-full" >
              <div className="flex flex-col grow text-gray-600 dark:text-gray-300">
              <span className="font-medium">Gesamte Prämie:<br/></span>
              <span className="ml-2 text-xl">{formatCurrency(offer?.reward_total_cents || 0)}</span>
              </div>
              <div className="flex flex-col grow text-gray-600 dark:text-gray-300">
                <span className="font-medium">Anteil für dich:<br/></span>
                <span className="ml-2 text-xl text-green-500 font-bold">{
                formatCurrency((1-(offer?.reward_offerer_percent || 0)) * (offer?.reward_total_cents || 0))
                //formatPercent(offer.reward_offerer_percent)
                }</span>
              </div>


          </div>
          <div className="flex justify-between items-center pt-4">
            <div className="space-y-1">
              <div className="text-gray-600 dark:text-gray-300 flex flex-row gap-2">
                <Link
                href={`/offers/${offer?.id || ''}`}
                className="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
                >
                Details anzeigen
                </Link>
              </div>
            </div>

            {/* Zeige einen Badge an, wenn der Benutzer eine aktive Anfrage gestellt hat */}
            {offer.has_application && offer.application_status !== 'retracted' && (
              <div className="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                {offer.application_status === 'pending' && 'Anfrage gesendet'}
                {offer.application_status === 'approved' && 'Anfrage angenommen'}
                {offer.application_status === 'rejected' && 'Anfrage abgelehnt'}
              </div>
            )}
          </div>
          <div className="absolute right-0 bottom-0 w-27">
            <div
              className="flex flex-row w-full h-full bg-neutral-800 bg-opacity-95 text-white text-sm text-right rounded-tl-lg text-left whitespace-pre-line p-1 pr-2 relative group"
              onMouseEnter={e => {
                const rect = (e.currentTarget as HTMLElement).getBoundingClientRect();
                setTooltipPos({ x: rect.left, y: rect.top });
                setShowTypeTooltip(true);
              }}
              onMouseLeave={() => setShowTypeTooltip(false)}
            >
              <UsersRound className="text-white w-5 h-5 mr-2" />
              <span className="block w-full">{offer?.offered_by_type || ''}</span>
            </div>
            {showTypeTooltip && tooltipPos && createPortal(
              <span
                className="fixed z-[9999] pointer-events-none transition-opacity duration-300 opacity-100 w-60"
                style={{ left: tooltipPos.x - 110, top: tooltipPos.y }}
              >
                <span className="block w-fit bg-neutral-800 bg-opacity-95 text-white text-sm rounded-lg text-left whitespace-pre-line p-1">
                  <b>{offer?.offered_by_type || ''}:</b> {getTooltipText(offer?.offered_by_type || '')}
                </span>
              </span>,
              document.body
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
