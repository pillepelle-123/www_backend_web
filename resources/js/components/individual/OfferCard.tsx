import { Link } from '@inertiajs/react';
import { Offer } from '@/pages/offers';

interface OfferCardProps {
  offer: Offer;
}

export function OfferCard({ offer }: OfferCardProps) {

  const formatCurrency = (cents: number) => {
    return new Intl.NumberFormat("de-DE", {
      style: "currency",
      currency: "EUR",
    }).format(cents);
  };

//   const formatPercent = (number: number) => {
//     return new Intl.NumberFormat("de-DE", {
//         style: 'percent',
//     }).format(1 - number);
//   };

  const truncateDescription = (text: string) => {
    if (!text) return '';
    if (text.length <= 200) return text;
    return text.substring(0, 197) + '...';
  };

  return (
    <div className="bg-white dark:bg-white/10 rounded-xl shadow-lg overflow-hidden hover:shadow-lg transition-shadow duration-300 relative">
      {offer.logo_url && (
        <div className="absolute right-0 top-0 w-[50%] pointer-events-none">
          <div className="relative w-full h-full">
            <img
              src={offer.logo_url}
              alt="Firmenlogo"
              className="w-full h-full object-contain"
            />
            <div className="absolute inset-0 bg-radial-[at_90%_10%] from-transparent dark:from-transparent via-white dark:via-[#474649] via-69% to-white dark:to-[#474649]" />
          </div>
        </div>
      )}
      <div className="p-6 relative">
        <div className="flex justify-between w-[75%] items-start mb-4 h-34">
          <h3 className="text-xl font-semibold text-gray-900 dark:text-white">
            {offer.title}
          </h3>
          {/* <span className="px-3 py-1 text-sm font-medium rounded-sm bg-zinc-800 dark:bg-zinc-800">
            {offer.offer_company}
          </span> */}
        </div>
        <div className="space-y-3">
          <div className="flex items-center text-gray-600 dark:text-gray-300">
            {offer.offer_company}
          </div>

          {offer.description && (
            <div className="text-gray-600 dark:text-gray-300">
              {/* <p className="font-medium mb-1">Beschreibung:</p> */}
              <p className="text-sm">{truncateDescription(offer.description)}</p>
            </div>
          )}

          <div className="flex justify-between items-center pt-4">
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
        </div>
      </div>
    </div>
  );
}
