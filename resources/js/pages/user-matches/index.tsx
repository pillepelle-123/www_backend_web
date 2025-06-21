import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import { CheckCircle, CircleCheck, CircleX, CircleAlert, Handshake } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Matches',
        href: '/user-matches',
    },
];

type UserMatch = {
  id: number;
  title: string;
  company_name: string;
  status: 'opened' | 'closed';
  success_status: 'pending' | 'successful' | 'unsuccessful';
  created_at: string;
  is_applicant: boolean;
  user_role: 'referrer' | 'referred';
  partner_name: string;
  partner_role: 'referrer' | 'referred';
  user_reward: number;
  is_archived: boolean;
};

export default function Index({ userMatches }: { userMatches: UserMatch[] }) {
  const [activeTab, setActiveTab] = useState<'matches' | 'archive'>('matches');

  const filteredMatches = userMatches.filter(match => {
    if (activeTab === 'matches' && match.is_archived) return false;
    if (activeTab === 'archive' && !match.is_archived) return false;
    return true;
  });

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('de-DE', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
    });
  };

  const getStatusBadge = (status: string, successStatus: string) => {
    if (status === 'opened') {
      return (
        <span className="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
          <CheckCircle className="w-3 h-3" /> Aktiv
        </span>
      );
    }

    if (successStatus === 'successful') {
      return (
        <span className="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
          <CircleCheck className="w-3 h-3" /> Erfolgreich
        </span>
      );
    }

    return (
      <span className="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
        <CircleX className="w-3 h-3" /> Erfolglos
      </span>
    );
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Matches" />

      <div className="flex border-b border-gray-200 dark:border-gray-700">
        <button
          onClick={() => setActiveTab('matches')}
          className={`px-4 py-2 text-sm font-medium ${
            activeTab === 'matches'
              ? 'border-b-2 border-blue-500 text-blue-600'
              : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
          }`}
        >
          Matches
        </button>
        <button
          onClick={() => setActiveTab('archive')}
          className={`px-4 py-2 text-sm font-medium ${
            activeTab === 'archive'
              ? 'border-b-2 border-blue-500 text-blue-600'
              : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
          }`}
        >
          Archiv
        </button>
      </div>

      <div className="p-6">
        <div className="flex justify-between items-center mb-3">
          <h1 className="text-2xl font-semibold text-gray-900 dark:text-white">
            {activeTab === 'matches' ? 'Matches' : 'Archiv'}
          </h1>
        </div>

        {filteredMatches.length === 0 ? (
          <div className="text-center py-8 text-gray-500">
            {activeTab === 'matches' ? 'Keine aktiven Matches gefunden' : 'Keine archivierten Matches gefunden'}
          </div>
        ) : (
          <div className="divide-y divide-gray-200 dark:divide-gray-700">
            {filteredMatches.map((match) => (
              <div
                key={match.id}
                className="block py-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors"
              >
                <div className="flex items-center gap-3">
                  <div className="flex-shrink-0">
                    <Handshake className="w-5 h-5 text-blue-600" />
                  </div>

                  <div className="min-w-0 flex-1">
                    <div className="flex justify-between text-sm">
                      <Link
                        href={`/user-matches/${match.id}`}
                        className="font-medium text-gray-900 dark:text-white truncate hover:underline"
                        preserveState={false}
                      >
                        {match.title}
                      </Link>
                      <p className="text-gray-500 dark:text-gray-400">
                        {formatDate(match.created_at)}
                      </p>
                    </div>
                    <div className="flex justify-between mt-1">
                      <p className="text-sm text-gray-500 dark:text-gray-400 truncate">
                        {match.company_name} • Partner: {match.partner_name} • Ihr Anteil: {match.user_reward.toFixed(2)}€
                      </p>
                      <div className="flex items-center gap-2">
                        {getStatusBadge(match.status, match.success_status)}

                        {match.status === 'opened' && activeTab === 'matches' && (
                          <div className="flex gap-1 z-10 relative">
                            <Link
                              href={route('web.user-matches.mark-successful', { id: match.id })}
                              method="post"
                              as="button"
                              className="p-1 rounded-full bg-green-100 text-green-800 hover:bg-green-200 cursor-pointer"
                              title="Match als erfolgreich abschließen"
                              preserveState={false}
                            >
                              <CircleCheck className="w-4 h-4" />
                            </Link>
                            <Link
                              href={route('web.user-matches.dissolve', { id: match.id })}
                              method="post"
                              as="button"
                              className="p-1 rounded-full bg-red-100 text-red-800 hover:bg-red-200 cursor-pointer"
                              title="Match auflösen"
                              preserveState={false}
                            >
                              <CircleX className="w-4 h-4" />
                            </Link>
                            <button
                              onClick={() => console.log(`Match ${match.id} gemeldet`)}
                              className="p-1 rounded-full bg-yellow-100 text-yellow-800 hover:bg-yellow-200 cursor-pointer"
                              title="Match Partner melden"
                            >
                              <CircleAlert className="w-4 h-4" />
                            </button>
                          </div>
                        )}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </AppLayout>
  );
}
