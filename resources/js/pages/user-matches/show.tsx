import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { Handshake, ArrowLeftRight, DivideIcon } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Matches',
        href: '/user-matches',
    },
    {
        title: 'Details',
        href: '#',
    },
];

type UserMatchDetails = {
  id: number;
  title: string;
  description: string;
  company_name: string;
  affiliate_url: string | null;
  status: 'opened' | 'closed';
  success_status: 'pending' | 'successful' | 'unsuccessful';
  created_at: string;
  is_applicant: boolean;
  user_name: string;
  user_role: 'referrer' | 'referred';
  partner_name: string;
  partner_role: 'referrer' | 'referred';
  reward_total_euro: number;
  user_reward: number;
  application_message: string | null;
};

export default function Show({ userMatch }: { userMatch: UserMatchDetails }) {
  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('de-DE', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      hour12: false,
    }).replace(',', ' | ');
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title={`Match: ${userMatch.title}`} />
      <div className="container mx-auto p-4">
        <div className="max-w-3xl mx-auto">
          <div className="bg-white dark:bg-white/10 rounded-xl shadow-lg overflow-hidden">
            <div className="p-6">
              <h1 className="text-2xl font-semibold text-gray-900 dark:text-white mb-6">
                Match Details
              </h1>

              {/* Offer Section */}
              <div className="mb-6">
                <h2 className="text-lg font-medium text-gray-900 dark:text-white mb-4">Offer</h2>
                <div className="space-y-4">
                  <div>
                    <p className="text-sm text-gray-500 dark:text-gray-400">Titel</p>
                    <p className="font-medium">{userMatch.title}</p>
                  </div>
                  <div>
                    <p className="text-sm text-gray-500 dark:text-gray-400">Beschreibung</p>
                    <div className="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4">
                      <p className="text-gray-600 dark:text-gray-300">{userMatch.description}</p>
                    </div>
                  </div>
                  <div>
                    <p className="text-sm text-gray-500 dark:text-gray-400">Unternehmen</p>
                    <p className="font-medium">{userMatch.company_name}</p>
                  </div>
                  {userMatch.affiliate_url && (
                    <div>
                      <p className="text-sm text-gray-500 dark:text-gray-400">Affiliate Link</p>
                      <a
                        href={userMatch.affiliate_url}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="text-blue-600 hover:text-blue-800 underline break-all"
                      >
                        {userMatch.affiliate_url}
                      </a>
                    </div>
                  )}
                </div>
              </div>

              {/* Match Partner Section */}
              <div className="mb-6">
                <h2 className="text-lg font-medium text-gray-900 dark:text-white mb-4">Match Partner</h2>
                <div className="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4">
                <div className='flex flex-row flex-wrap items-center justify-between'>
                  <div className="flex-1 ">
                    <div className="text-center">
                        <p className="font-medium">{userMatch.user_name}</p>
                        <p className="text-sm text-gray-500">
                        {userMatch.user_role === 'referrer' ? 'Werbender' : 'Beworbener'} (Sie)
                        </p>
                    </div>
                  </div>
                  { /* Handshake */ }
                  <div className='flex-1'>
                    <div className="flex flex-col items-center gap-2">
                        <Handshake className="w-8 h-8 text-blue-600" />
                        <ArrowLeftRight className="w-6 h-6 text-gray-400" />
                    </div>
                  </div>
                   { /* Partner */ }
                  <div className="flex-1 text-center">
                    <p className="font-medium">{userMatch.partner_name}</p>
                    <p className="text-sm text-gray-500">
                      {userMatch.partner_role === 'referrer' ? 'Werbender' : 'Beworbener'}
                    </p>
                  </div>
                   { /* Line Break */ }
                  <div className="basis-full mt-6"></div>
                  <div className="flex-1 text-center">
                    <p className="font-bold text-2xl text-green-600">{userMatch.user_reward.toFixed(2)}€</p>
                    <p className="text-sm text-gray-500">
                    Anteil für dich ({userMatch.user_reward_percent} %)
                    </p>
                  </div>
                  <div className="flex-1 text-center">
                    <p className="text-2xl">{userMatch.reward_total_euro.toFixed(2)}€</p>
                    <p className="text-sm text-gray-500 dark:text-gray-400">Gesamtbelohnung</p>
                  </div>
                  <div className="flex-1 text-center">
                    <p className="text-xl">{userMatch.partner_reward.toFixed(2)}€</p>
                    <p className="text-sm text-gray-500">
                      Anteil für Partner ({userMatch.partner_reward_percent} %)
                    </p>
                  </div>
                  </div>
                </div>
              </div>

              {/* Application Section */}
              {userMatch.application_message && (
                <div className="mb-6">
                  <h2 className="text-lg font-medium text-gray-900 dark:text-white mb-2">Application</h2>
                  <div className="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4">
                    <p className="text-gray-600 dark:text-gray-300">{userMatch.application_message}</p>
                  </div>
                </div>
              )}

              {/* Match Info */}
              <div className="mb-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <p className="text-sm text-gray-500 dark:text-gray-400">Match erstellt</p>
                    <p className="font-medium">{formatDate(userMatch.created_at)}</p>
                  </div>
                  <div>
                    <p className="text-sm text-gray-500 dark:text-gray-400">Status</p>
                    <p className="font-medium">
                      {userMatch.status === 'opened' ? 'Aktiv' :
                       userMatch.success_status === 'successful' ? 'Erfolgreich abgeschlossen' : 'Erfolglos beendet'}
                    </p>
                  </div>
                </div>
              </div>

              <div className="flex justify-between pt-4">
                <Link
                  href={route('web.user-matches.index')}
                  className="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200"
                  preserveState={false}
                >
                  Zurück zur Übersicht
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
