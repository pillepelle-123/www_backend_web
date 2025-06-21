import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { Offer } from '@/pages/offers';

const getBreadcrumbs = (offerId: number): BreadcrumbItem[] => [
  {
    title: 'Offers',
    href: '/offers',
  },
  {
    title: 'Details',
    href: `/offers/${offerId}`,
  },
  {
    title: 'Anfragen',
    href: '#',
  },
];

export default function Create({ offer, /* auth */ }: { offer: Offer, auth?: { user: { id: number, name: string } } }) {
  const { data, setData, post, processing, errors } = useForm({
    message: '',
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post(route('web.applications.store', { offer_id: offer.id }), {
      preserveScroll: true,

    });
  };

  return (
    <AppLayout breadcrumbs={getBreadcrumbs(offer.id)}>
      <Head title={`Anfrage für: ${offer.title}`} />
      <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <div className="container mx-auto p-4">
          <div className="max-w-2xl mx-auto">
            <div className="bg-white dark:bg-white/10 rounded-xl shadow-lg overflow-hidden">
              <div className="p-6">
                <h1 className="text-2xl font-semibold text-gray-900 dark:text-white mb-6">
                  Anfrage für: {offer.title}
                </h1>

                <div className="mb-6">
                  <div className="flex items-center gap-2 text-gray-600 dark:text-gray-300 mb-2">
                    <span className="font-medium">Angebot von:</span>
                    <span>{offer.offer_user}</span>
                  </div>
                  <div className="flex items-center gap-2 text-gray-600 dark:text-gray-300 mb-2">
                    <span className="font-medium">Unternehmen:</span>
                    <span>{offer.offer_company}</span>
                  </div>
                  {offer.offerer_type && (
                    <div className="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                      <span className="font-medium">Typ:</span>
                      <span>{offer.offerer_type}</span>
                    </div>
                  )}
                </div>

                <div className="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4 mb-6">
                  <p className="text-gray-600 dark:text-gray-300 text-sm">
                    Sie sind dabei, eine Anfrage für dieses Angebot zu stellen. Der Anbieter wird benachrichtigt und kann Ihre Anfrage annehmen oder ablehnen.
                  </p>
                </div>

                <form onSubmit={handleSubmit} className="space-y-4">
                  <div>
                    <label htmlFor="message" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                      Nachricht an den Anbieter (optional)
                    </label>
                    <textarea
                      id="message"
                      name="message"
                      rows={4}
                      className="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700"
                      placeholder="Schreiben Sie hier Ihre Nachricht..."
                      value={data.message}
                      onChange={(e) => setData('message', e.target.value)}
                    />
                    {errors.message && (
                      <p className="mt-1 text-sm text-red-600">{errors.message}</p>
                    )}
                  </div>

                  <div className="flex justify-between pt-4">
                    <a
                      href={`/offers/${offer.id}`}
                      className="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200"
                    >
                      Zurück
                    </a>
                    <button
                      type="submit"
                      disabled={processing}
                      className="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 disabled:opacity-50"
                    >
                      {processing ? 'Wird gesendet...' : 'Anfrage senden'}
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
