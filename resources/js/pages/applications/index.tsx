import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import { CheckCircle, Clock, XCircle, Mail, MailOpen, Ban } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Nachrichten',
        href: '/applications',
    },
];

type Application = {
  id: number;
  offer_id: number;
  title: string;
  company_name: string;
  message: string | null;
  status: 'pending' | 'approved' | 'rejected' | 'retracted';
  created_at: string;
  responded_at: string | null;
  is_unread: boolean;
  is_applicant: boolean;
  other_user: string;
};

export default function Index({ applications, unreadCount }: { applications: Application[], unreadCount: number }) {
  const [filter, setFilter] = useState<'all' | 'sent' | 'received'>('all');
  const [statusFilter, setStatusFilter] = useState<'all' | 'pending' | 'approved' | 'rejected' | 'retracted'>('all');

  const filteredApplications = applications.filter(app => {
    // Filter nach Typ (gesendet/empfangen)
    if (filter !== 'all') {
      if (filter === 'sent' && !app.is_applicant) return false;
      if (filter === 'received' && app.is_applicant) return false;
    }
    
    // Filter nach Status
    if (statusFilter !== 'all' && app.status !== statusFilter) return false;
    
    return true;
  });

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('de-DE', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
    });
  };

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'pending':
        return (
          <span className="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
            <Clock className="w-3 h-3" /> Ausstehend
          </span>
        );
      case 'approved':
        return (
          <span className="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
            <CheckCircle className="w-3 h-3" /> Genehmigt
          </span>
        );
      case 'rejected':
        return (
          <span className="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
            <XCircle className="w-3 h-3" /> Abgelehnt
          </span>
        );
      case 'retracted':
        return (
          <span className="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
            <XCircle className="w-3 h-3" /> Zurückgezogen
          </span>
        );
      default:
        return null;
    }
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Nachrichten" />
      <div className="container mx-auto p-4">
        <div className="bg-white dark:bg-white/10 rounded-xl shadow-lg overflow-hidden">
          <div className="p-6">
            <div className="flex justify-between items-center mb-6">
              <h1 className="text-2xl font-semibold text-gray-900 dark:text-white">
                Nachrichten {unreadCount > 0 && <span className="ml-2 text-sm bg-red-500 text-white px-2 py-0.5 rounded-full">{unreadCount} neu</span>}
              </h1>
              <div className="flex flex-col gap-2">
                <div className="flex gap-2">
                  <button
                    onClick={() => setFilter('all')}
                    className={`px-3 py-1 rounded-md text-sm ${filter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'}`}
                  >
                    Alle
                  </button>
                  <button
                    onClick={() => setFilter('sent')}
                    className={`px-3 py-1 rounded-md text-sm ${filter === 'sent' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'}`}
                  >
                    Gesendet
                  </button>
                  <button
                    onClick={() => setFilter('received')}
                    className={`px-3 py-1 rounded-md text-sm ${filter === 'received' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'}`}
                  >
                    Empfangen
                  </button>
                </div>
                <div className="flex gap-2">
                  <button
                    onClick={() => setStatusFilter('all')}
                    className={`px-3 py-1 rounded-md text-sm ${statusFilter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'}`}
                  >
                    Alle Status
                  </button>
                  <button
                    onClick={() => setStatusFilter('pending')}
                    className={`px-3 py-1 rounded-md text-sm ${statusFilter === 'pending' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'}`}
                  >
                    Ausstehend
                  </button>
                  <button
                    onClick={() => setStatusFilter('approved')}
                    className={`px-3 py-1 rounded-md text-sm ${statusFilter === 'approved' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'}`}
                  >
                    Genehmigt
                  </button>
                  <button
                    onClick={() => setStatusFilter('rejected')}
                    className={`px-3 py-1 rounded-md text-sm ${statusFilter === 'rejected' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'}`}
                  >
                    Abgelehnt
                  </button>
                  <button
                    onClick={() => setStatusFilter('retracted')}
                    className={`px-3 py-1 rounded-md text-sm ${statusFilter === 'retracted' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'}`}
                  >
                    Zurückgezogen
                  </button>
                </div>
              </div>
            </div>

            {filteredApplications.length === 0 ? (
              <div className="text-center py-8 text-gray-500">
                Keine Nachrichten gefunden
              </div>
            ) : (
              <div className="divide-y divide-gray-200 dark:divide-gray-700">
                {filteredApplications.map((application) => (
                  <Link
                    key={application.id}
                    href={`/applications/${application.id}`}
                    className={`block py-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors ${application.is_unread ? 'bg-blue-50 dark:bg-blue-900/20' : ''}`}
                    preserveState={false}
                  >
                    <div className="flex items-center gap-3">
                      <div className="flex-shrink-0">
                        {application.is_unread ? (
                          <Mail className="w-5 h-5 text-blue-600" />
                        ) : (
                          <MailOpen className="w-5 h-5 text-gray-400" />
                        )}
                      </div>
                      <div className="min-w-0 flex-1">
                        <div className="flex justify-between text-sm">
                          <p className="font-medium text-gray-900 dark:text-white truncate">
                            {application.title}
                          </p>
                          <p className="text-gray-500 dark:text-gray-400">
                            {formatDate(application.created_at)}
                          </p>
                        </div>
                        <div className="flex justify-between mt-1">
                          <p className="text-sm text-gray-500 dark:text-gray-400 truncate">
                            {application.is_applicant ? 'An: ' : 'Von: '}{application.other_user} • {application.company_name}
                          </p>
                          <div className="flex items-center gap-2">
                            {getStatusBadge(application.status)}
                            
                            {/* Aktions-Buttons für Empfänger */}
                            {!application.is_applicant && application.status === 'pending' && (
                              <div className="flex gap-1">
                                <form action={route('web.applications.approve', { id: application.id })} method="POST" className="inline">
                                  <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''} />
                                  <button
                                    type="submit"
                                    className="p-1 rounded-full bg-green-100 text-green-800 hover:bg-green-200"
                                    title="Annehmen"
                                  >
                                    <CheckCircle className="w-4 h-4" />
                                  </button>
                                </form>
                                <form action={route('web.applications.reject', { id: application.id })} method="POST" className="inline">
                                  <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''} />
                                  <button
                                    type="submit"
                                    className="p-1 rounded-full bg-red-100 text-red-800 hover:bg-red-200"
                                    title="Ablehnen"
                                  >
                                    <Ban className="w-4 h-4" />
                                  </button>
                                </form>
                              </div>
                            )}
                            
                            {/* Zurückziehen-Button für Absender */}
                            {application.is_applicant && application.status === 'pending' && (
                              <form action={route('web.applications.retract', { id: application.id })} method="POST" className="inline">
                                <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''} />
                                <button
                                  type="submit"
                                  className="p-1 rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200"
                                  title="Zurückziehen"
                                >
                                  <XCircle className="w-4 h-4" />
                                </button>
                              </form>
                            )}
                          </div>
                        </div>
                      </div>
                    </div>
                  </Link>
                ))}
              </div>
            )}
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
