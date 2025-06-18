import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { CheckCircle, Clock, XCircle } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Nachrichten',
        href: '/applications',
    },
    {
        title: 'Details',
        href: '#',
    },
];

type ApplicationDetails = {
  id: number;
  offer_id: number;
  title: string;
  description: string;
  company_name: string;
  message: string | null;
  status: 'pending' | 'approved' | 'rejected';
  created_at: string;
  responded_at: string | null;
  is_applicant: boolean;
  applicant: {
    id: number;
    name: string;
  };
  offer_owner: {
    id: number;
    name: string;
  };
};

export default function Show({ application }: { application: ApplicationDetails }) {
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

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'pending':
        return (
          <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
            <Clock className="w-4 h-4" /> Ausstehend
          </span>
        );
      case 'approved':
        return (
          <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
            <CheckCircle className="w-4 h-4" /> Genehmigt
          </span>
        );
      case 'rejected':
        return (
          <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
            <XCircle className="w-4 h-4" /> Abgelehnt
          </span>
        );
      default:
        return null;
    }
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title={`Anfrage: ${application.title}`} />
      <div className="container mx-auto p-4">
        <div className="max-w-3xl mx-auto">
          <div className="bg-white dark:bg-white/10 rounded-xl shadow-lg overflow-hidden">
            <div className="p-6">
              <div className="flex justify-between items-start mb-6">
                <h1 className="text-2xl font-semibold text-gray-900 dark:text-white">
                  {application.title}
                </h1>
                <div>
                  {getStatusBadge(application.status)}
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                  <p className="text-sm text-gray-500 dark:text-gray-400">Unternehmen</p>
                  <p className="font-medium">{application.company_name}</p>
                </div>
                <div>
                  <p className="text-sm text-gray-500 dark:text-gray-400">Erstellt am</p>
                  <p className="font-medium">{formatDate(application.created_at)}</p>
                </div>
                <div>
                  <p className="text-sm text-gray-500 dark:text-gray-400">Anfragender</p>
                  <p className="font-medium">{application.applicant.name}</p>
                </div>
                <div>
                  <p className="text-sm text-gray-500 dark:text-gray-400">Anbieter</p>
                  <p className="font-medium">{application.offer_owner.name}</p>
                </div>
                {application.responded_at && (
                  <div>
                    <p className="text-sm text-gray-500 dark:text-gray-400">Beantwortet am</p>
                    <p className="font-medium">{formatDate(application.responded_at)}</p>
                  </div>
                )}
              </div>

              <div className="mb-6">
                <h2 className="text-lg font-medium text-gray-900 dark:text-white mb-2">Angebotsbeschreibung</h2>
                <div className="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4">
                  <p className="text-gray-600 dark:text-gray-300">{application.description}</p>
                </div>
              </div>

              {application.message && (
                <div className="mb-6">
                  <h2 className="text-lg font-medium text-gray-900 dark:text-white mb-2">Nachricht</h2>
                  <div className="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4">
                    <p className="text-gray-600 dark:text-gray-300">{application.message}</p>
                  </div>
                </div>
              )}

              <div className="flex justify-between pt-4">
                <div className="flex gap-2">
                  <Link
                    href={route('web.applications.index')}
                    className="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200"
                    preserveState={false}
                    only={[]}
                  >
                    Zurück zur Übersicht
                  </Link>
                  <Link
                    href={route('web.offers.show', { id: application.offer_id })}
                    className="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200"
                  >
                    Zum Angebot
                  </Link>
                </div>

                {!application.is_applicant && application.status === 'pending' && (
                  <div className="flex gap-2">
                    <Link
                      href={route('web.applications.reject', application.id)}
                      method="post"
                      as="button"
                      className="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors duration-200"
                      preserveState={false}
                    >
                      Ablehnen
                    </Link>
                    <Link
                      href={route('web.applications.approve', application.id)}
                      method="post"
                      as="button"
                      className="inline-flex items-center px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200"
                      preserveState={false}
                    >
                      Genehmigen
                    </Link>
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
