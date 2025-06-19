import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import { CheckCircle, Clock, XCircle, Mail, MailOpen, Ban, Archive, RotateCcw, RefreshCw, Eye, EyeOff, Square, CheckSquare } from 'lucide-react';

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
  is_archived: boolean;
  other_user: string;
};

export default function Index({ applications, unreadCount }: { applications: Application[], unreadCount: number }) {
  const [activeTab, setActiveTab] = useState<'applications' | 'archive'>('applications');
  const [filter, setFilter] = useState<'all' | 'sent' | 'received'>('all');
  const [statusFilter, setStatusFilter] = useState<'all' | 'pending' | 'approved' | 'rejected' | 'retracted'>('all');
  const [selectedApplications, setSelectedApplications] = useState<number[]>([]);
  const [selectAll, setSelectAll] = useState(false);

  const filteredApplications = applications.filter(app => {
    // Filter nach Tab (Anträge/Archiv)
    if (activeTab === 'applications' && app.is_archived) return false;
    if (activeTab === 'archive' && !app.is_archived) return false;

    // Filter nach Typ (gesendet/empfangen)
    if (filter !== 'all') {
      if (filter === 'sent' && !app.is_applicant) return false;
      if (filter === 'received' && app.is_applicant) return false;
    }

    // Filter nach Status (nur im Anträge-Tab)
    if (activeTab === 'applications' && statusFilter !== 'all' && app.status !== statusFilter) return false;

    return true;
  });

  // Hilfsfunktionen für Checkboxen
  const toggleSelectApplication = (id: number) => {
    if (selectedApplications.includes(id)) {
      setSelectedApplications(selectedApplications.filter(appId => appId !== id));
    } else {
      setSelectedApplications([...selectedApplications, id]);
    }
  };

  const toggleSelectAll = () => {
    if (selectAll) {
      setSelectedApplications([]);
    } else {
      setSelectedApplications(filteredApplications.map(app => app.id));
    }
    setSelectAll(!selectAll);
  };

  // Hilfsfunktion für Bulk-Aktionen
  const handleBulkAction = (action: string) => {
    if (selectedApplications.length === 0) return;

    if(action == 'approve' || action == 'archive' || action == 'unarchive') {
        // Zeige Bestätigungsdialog
        if (!confirm(`Möchten Sie die Aktion "${action}" für ${selectedApplications.length} ausgewählte Nachrichten durchführen?`)) {
        return;
        }
    }

    // Führe die entsprechende Aktion für jede ausgewählte Anwendung aus
    const promises = selectedApplications.map(id => {
      switch (action) {
        case 'markAsRead':
          return fetch(route('web.applications.mark-read', { id }), {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
              'Content-Type': 'application/json',
            }
          });

        case 'markAsUnread':
          // Wir verwenden toggle-read mit is_unread=false, um als ungelesen zu markieren
          return fetch(route('web.applications.toggle-read', { id }), {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({ is_unread: false })
          });

        case 'approve':
          return fetch(route('web.applications.approve', { id }), {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
              'Content-Type': 'application/json',
            }
          });

        case 'archive':
          return fetch(route('web.applications.archive', { id }), {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
              'Content-Type': 'application/json',
            }
          });

        case 'unarchive':
          return fetch(route('web.applications.unarchive', { id }), {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
              'Content-Type': 'application/json',
            }
          });

        default:
          return Promise.resolve();
      }
    });

    // Warte auf alle Anfragen und aktualisiere dann die Seite
    Promise.all(promises)
      .then(() => {
        window.location.reload();
      })
      .catch(error => {
        console.error('Fehler bei der Bulk-Aktion:', error);
        alert('Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.');
      });
  };

  // Hilfsfunktion zum Umschalten des Lesestatus einer Nachricht
  const toggleReadStatus = (applicationId: number, currentIsUnread: boolean) => {
    // API-Anfrage zum Umschalten des Lesestatus
    fetch(route('web.applications.toggle-read', { id: applicationId }), {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ is_unread: currentIsUnread }),
    }).then(response => {
      if (response.ok) {
        // Aktualisiere die Anzeige
        window.location.reload();
      }
    });
  };

  // Hilfsfunktion zum Markieren einer Nachricht als gelesen (für andere Buttons)
  const markAsRead = (applicationId: number) => {
    toggleReadStatus(applicationId, true);
  };

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
      {/* <div className="container mx-auto p-4"> */}
        {/* <div className="bg-white dark:bg-white/10 rounded-xl shadow-lg overflow-hidden"> */}
          {/* Tabs */}
          <div className="flex border-b border-gray-200 dark:border-gray-700">
            <button
              onClick={() => setActiveTab('applications')}
              className={`px-4 py-2 text-sm font-medium ${
                activeTab === 'applications'
                  ? 'border-b-2 border-blue-500 text-blue-600'
                  : 'text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              Anträge
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
            <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
              <h1 className="text-2xl font-semibold text-gray-900 dark:text-white">
                {activeTab === 'applications' ? 'Nachrichten' : 'Archiv'}
                {unreadCount > 0 && activeTab === 'applications' &&
                  <span className="ml-2 text-sm bg-red-500 text-white px-2 py-0.5 rounded-full">{unreadCount} neu</span>
                }
              </h1>


              {/* Filters - nur im Anträge-Tab */}
              <div className="flex flex-col gap-2 w-full md:w-auto">
                <div className="flex gap-2 flex-wrap p-2 border rounded-lg">

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

                {/* Status-Filter nur im Anträge-Tab anzeigen */}
                {activeTab === 'applications' && (
                  <div className="flex gap-2 flex-wrap p-2 border rounded-lg">
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
                )}

              {/* Bulk Actions */}
              <div className="flex gap-2 flex-wrap p-2 border rounded-lg">
                {activeTab === 'applications' && (
                  <>
                    <button
                      onClick={() => handleBulkAction('markAsRead')}
                      disabled={selectedApplications.length === 0}
                      className={`px-3 py-1 rounded-md text-sm flex items-center gap-1 ${
                        selectedApplications.length === 0
                          ? 'bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500 cursor-not-allowed'
                          : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
                      }`}
                    >
                      <EyeOff className="w-4 h-4" /> Als gelesen
                    </button>
                    <button
                      onClick={() => handleBulkAction('markAsUnread')}
                      disabled={selectedApplications.length === 0}
                      className={`px-3 py-1 rounded-md text-sm flex items-center gap-1 ${
                        selectedApplications.length === 0
                          ? 'bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500 cursor-not-allowed'
                          : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
                      }`}
                    >
                      <Eye className="w-4 h-4" /> Als ungelesen
                    </button>
                    <button
                      onClick={() => handleBulkAction('approve')}
                      disabled={selectedApplications.length === 0}
                      className={`px-3 py-1 rounded-md text-sm flex items-center gap-1 ${
                        selectedApplications.length === 0
                          ? 'bg-green-100/50 text-green-700/50 dark:bg-green-800/20 dark:text-green-300/50 cursor-not-allowed'
                          : 'bg-green-100 text-green-700 dark:bg-green-800/30 dark:text-green-300'
                      }`}
                    >
                      <CheckCircle className="w-4 h-4" /> Genehmigen
                    </button>
                    <button
                      onClick={() => handleBulkAction('archive')}
                      disabled={selectedApplications.length === 0}
                      className={`px-3 py-1 rounded-md text-sm flex items-center gap-1 ${
                        selectedApplications.length === 0
                          ? 'bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500 cursor-not-allowed'
                          : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
                      }`}
                    >
                      <Archive className="w-4 h-4" /> Archivieren
                    </button>
                  </>
                )}
                {activeTab === 'archive' && (
                  <button
                    onClick={() => handleBulkAction('unarchive')}
                    disabled={selectedApplications.length === 0}
                    className={`px-3 py-1 rounded-md text-sm flex items-center gap-1 ${
                      selectedApplications.length === 0
                        ? 'bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500 cursor-not-allowed'
                        : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
                    }`}
                  >
                    <RotateCcw className="w-4 h-4" /> Wiederherstellen
                  </button>
                )}
              </div>
              </div>
            </div>

            {filteredApplications.length === 0 ? (
              <div className="text-center py-8 text-gray-500">
                {activeTab === 'applications' ? 'Keine Nachrichten gefunden' : 'Keine archivierten Nachrichten gefunden'}
              </div>
            ) : (
              <div className="divide-y divide-gray-200 dark:divide-gray-700">
                {/* Select All Checkbox */}
                <div className="py-2 flex items-center">
                  <button
                    onClick={toggleSelectAll}
                    className="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300"
                  >
                    {selectAll ? (
                      <CheckSquare className="w-5 h-5 text-blue-600" />
                    ) : (
                      <Square className="w-5 h-5" />
                    )}
                    Alle auswählen
                  </button>
                </div>

                {filteredApplications.map((application) => (
                  <div
                    key={application.id}
                    className={`block py-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors ${application.is_unread ? 'bg-blue-50 dark:bg-blue-900/20' : ''}`}
                  >
                    <div className="flex items-center gap-3">
                      {/* Checkbox */}
                      <div className="flex-shrink-0">
                        <button
                          onClick={() => toggleSelectApplication(application.id)}
                          className="flex items-center"
                        >
                          {selectedApplications.includes(application.id) ? (
                            <CheckSquare className="w-5 h-5 text-blue-600" />
                          ) : (
                            <Square className="w-5 h-5 text-gray-400" />
                          )}
                        </button>
                      </div>

                      {/* Read/Unread Icon */}
                      <div className="flex-shrink-0">
                        {activeTab === 'applications' && (
                        <button
                          onClick={() => toggleReadStatus(application.id, application.is_unread)}
                          className="focus:outline-none cursor-pointer"
                          title={application.is_unread ? "Als gelesen markieren" : "Als ungelesen markieren"}
                        >
                          {application.is_unread ? (
                            <Mail className="w-5 h-5 text-blue-600" />
                          ) : (
                            <MailOpen className="w-5 h-5 text-gray-400" />
                          )}
                        </button>
                        )}
                      </div>

                      <div className="min-w-0 flex-1">
                        <div className="flex justify-between text-sm">
                          <Link
                            href={`/applications/${application.id}`}
                            className="font-medium text-gray-900 dark:text-white truncate hover:underline"
                            preserveState={false}
                          >
                            {application.title}
                          </Link>
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
                            {!application.is_applicant && application.status === 'pending' && activeTab === 'applications' && (
                              <div className="flex gap-1 z-10 relative">
                                <Link
                                  href={route('web.applications.approve', { id: application.id })}
                                  method="post"
                                  as="button"
                                  className="p-1 rounded-full bg-green-100 text-green-800 hover:bg-green-200"
                                  title="Annehmen"
                                  preserveState={false}
                                  onClick={() => application.is_unread && markAsRead(application.id)}
                                >
                                  <CheckCircle className="w-4 h-4" />
                                </Link>
                                <Link
                                  href={route('web.applications.reject', { id: application.id })}
                                  method="post"
                                  as="button"
                                  className="p-1 rounded-full bg-red-100 text-red-800 hover:bg-red-200"
                                  title="Ablehnen"
                                  preserveState={false}
                                  onClick={() => application.is_unread && markAsRead(application.id)}
                                >
                                  <Ban className="w-4 h-4" />
                                </Link>
                              </div>
                            )}

                            {/* Ablehnen-Button für Empfänger bei genehmigten Anträgen */}
                            {!application.is_applicant && application.status === 'approved' && activeTab === 'applications' && (
                              <div className="z-10 relative">
                                <Link
                                  href={route('web.applications.reject', { id: application.id })}
                                  method="post"
                                  as="button"
                                  className="p-1 rounded-full bg-red-100 text-red-800 hover:bg-red-200"
                                  title="Ablehnen"
                                  preserveState={false}
                                  onClick={() => application.is_unread && markAsRead(application.id)}
                                >
                                  <Ban className="w-4 h-4" />
                                </Link>
                              </div>
                            )}

                            {/* Annehmen-Button für Empfänger bei abgelehnten Anträgen */}
                            {!application.is_applicant && application.status === 'rejected' && activeTab === 'applications' && (
                              <div className="z-10 relative">
                                <Link
                                  href={route('web.applications.approve', { id: application.id })}
                                  method="post"
                                  as="button"
                                  className="p-1 rounded-full bg-green-100 text-green-800 hover:bg-green-200"
                                  title="Annehmen"
                                  preserveState={false}
                                  onClick={() => application.is_unread && markAsRead(application.id)}
                                >
                                  <CheckCircle className="w-4 h-4" />
                                </Link>
                              </div>
                            )}

                            {/* Zurückziehen-Button für Absender bei ausstehenden oder genehmigten Anträgen */}
                            {application.is_applicant && (application.status === 'pending' || application.status === 'approved') && activeTab === 'applications' && (
                              <div className="z-10 relative">
                                <Link
                                  href={route('web.applications.retract', { id: application.id })}
                                  method="post"
                                  as="button"
                                  className="p-1 rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200"
                                  title="Zurückziehen"
                                  preserveState={false}
                                  onClick={() => application.is_unread && markAsRead(application.id)}
                                >
                                  <XCircle className="w-4 h-4" />
                                </Link>
                              </div>
                            )}

                            {/* Erneut stellen-Button für Absender bei zurückgezogenen Anträgen */}
                            {application.is_applicant && application.status === 'retracted' && activeTab === 'applications' && (
                              <div className="z-10 relative">
                                <Link
                                  href={route('web.applications.reapply', { id: application.id })}
                                  method="post"
                                  as="button"
                                  className="p-1 rounded-full bg-blue-100 text-blue-800 hover:bg-blue-200"
                                  title="Antrag erneut stellen"
                                  preserveState={false}
                                  onClick={() => application.is_unread && markAsRead(application.id)}
                                >
                                  <RefreshCw className="w-4 h-4" />
                                </Link>
                              </div>
                            )}

                            {/* Archivieren/Wiederherstellen-Button */}
                            {/* Archivieren-Button nur anzeigen, wenn Status pending, rejected oder retracted ist */}
                            {activeTab === 'applications' &&
                             (application.status === 'pending' || application.status === 'rejected' || application.status === 'retracted') && (
                              <div className="z-10 relative">
                                <Link
                                  href={route('web.applications.archive', { id: application.id })}
                                  method="post"
                                  as="button"
                                  className="p-1 rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200"
                                  title="Archivieren"
                                  preserveState={false}
                                  onClick={() => application.is_unread && markAsRead(application.id)}
                                >
                                  <Archive className="w-4 h-4" />
                                </Link>
                              </div>
                            )}
                            {activeTab === 'archive' && (
                              <div className="z-10 relative">
                                <Link
                                  href={route('web.applications.unarchive', { id: application.id })}
                                  method="post"
                                  as="button"
                                  className="p-1 rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200"
                                  title="Wiederherstellen"
                                  preserveState={false}
                                  onClick={() => application.is_unread && markAsRead(application.id)}
                                >
                                  <RotateCcw className="w-4 h-4" />
                                </Link>
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
        {/* </div> */}
      {/* </div> */}
    </AppLayout>
  );
}
