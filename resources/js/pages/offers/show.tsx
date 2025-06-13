import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { Offer } from './index';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Offers',
        href: '/offers',
    },
    {
        title: 'Details',
        href: '#',
    },
];

export default function Show({ offer }: { offer: Offer }) {
  const formatCurrency = (cents: number) => {
    return new Intl.NumberFormat("de-DE", {
      style: "currency",
      currency: "EUR",
    }).format(cents);
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title={`Offer: ${offer.title}`} />
      <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <div className="container mx-auto p-4">
          <div className="max-w-4xl mx-auto">
            <div className="bg-white dark:bg-white/10 rounded-xl shadow-md overflow-hidden relative">
              {offer.logo_path && (
                <div className="absolute inset-0 opacity-5 pointer-events-none">
                  <img
                    src={offer.logo_path}
                    alt="Firmenlogo"
                    className="w-full h-full object-contain"
                  />
                </div>
              )}
              <div className="p-8 relative">
                {/* Header mit Titel */}
                <div className="mb-8">
                  <div className="space-y-2">
                    <h1 className="text-3xl font-bold text-gray-900 dark:text-white">
                      {offer.title}
                    </h1>
                    <span className="px-4 py-2 text-sm font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-200 dark:text-blue-200">
                      {offer.status}
                    </span>
                  </div>
                </div>

                {/* Hauptinhalt */}
                <div className="space-y-6">
                  {/* Beschreibung */}
                  <div className="prose dark:prose-invert max-w-none">
                    <h2 className="text-xl font-semibold mb-4">Beschreibung</h2>
                    <p className="text-gray-600 dark:text-gray-300">
                      {offer.description}
                    </p>
                  </div>

                  {/* Finanzielle Details */}
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                    <div>
                      <h3 className="text-lg font-semibold mb-2">Gesamte Prämie</h3>
                      <p className="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        {formatCurrency(offer.reward_total_cents)}
                      </p>
                    </div>
                    <div>
                      <h3 className="text-lg font-semibold mb-2">Ihr Anteil</h3>
                      <p className="text-2xl font-bold text-green-600 dark:text-green-400">
                        {formatCurrency((1-offer.reward_offerer_percent) * offer.reward_total_cents)}
                      </p>
                    </div>
                  </div>

                  {/* Anbieter Informationen */}
                  <div className="p-6 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                    <h3 className="text-lg font-semibold mb-4">Anbieter Informationen</h3>
                    <div className="space-y-2">
                      <p className="text-gray-600 dark:text-gray-300">
                        <span className="font-medium">Angeboten von:</span> {offer.offer_company}
                      </p>
                      <p className="text-gray-600 dark:text-gray-300">
                        <span className="font-medium">Vermittler:</span> {offer.offer_user}
                      </p>
                    </div>
                  </div>
                </div>

                {/* Aktions-Buttons */}
                <div className="mt-8 flex justify-between items-center">
                  <Link
                    href="/offers"
                    className="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200"
                  >
                    Zurück zur Übersicht
                  </Link>
                  <button
                    className="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
                  >
                    Angebot annehmen
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
