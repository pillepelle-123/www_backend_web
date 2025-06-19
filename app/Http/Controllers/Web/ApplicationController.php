<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Offer;
use App\Models\UserMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $applications = Application::where(function ($query) use ($user) {
            $query->where('applicant_id', $user->id)
                  ->orWhere('offer_owner_id', $user->id);
        })
        ->with(['offer', 'applicant', 'offerOwner'])
        ->latest()
        ->get()
        ->map(function ($application) use ($user) {
            $isApplicant = $application->applicant_id === $user->id;
            $otherUser = $isApplicant ? $application->offerOwner->name : $application->applicant->name;
            $isUnread = $isApplicant ? !$application->is_read_by_applicant : !$application->is_read_by_owner;

            // Bestimme, ob die Bewerbung archiviert ist
            $isArchived = $isApplicant ? $application->is_archived_by_applicant : $application->is_archived_by_owner;

            return [
                'id' => $application->id,
                'offer_id' => $application->offer_id,
                'title' => $application->offer->title,
                'company_name' => $application->offer->company->name,
                'message' => $application->message,
                'status' => $application->status,
                'created_at' => $application->created_at->format('Y-m-d H:i:s'),
                'responded_at' => $application->responded_at ? $application->responded_at->format('Y-m-d H:i:s') : null,
                'is_unread' => $isUnread,
                'is_applicant' => $isApplicant,
                'is_archived' => $isArchived,
                'other_user' => $otherUser,
            ];
        });

        // Zähle ungelesene Nachrichten
        $unreadCount = $applications->where('is_unread', true)->count();

        return Inertia::render('applications/index', [
            'applications' => $applications,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($offer_id)
    {
        // Finde das Angebot
        $offer = Offer::with(['company', 'user'])->findOrFail($offer_id);

        return Inertia::render('offers/applications/create', [
            'offer' => [
                'id' => $offer->id,
                'title' => $offer->title,
                'description' => $offer->description,
                'offer_company' => $offer->company ? $offer->company->name : null,
                'offer_user' => $offer->user ? $offer->user->name : null,
                'offered_by_type' => $offer->offered_by_type == 'referrer' ? 'Werbender' : 'Beworbener',
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $offer_id)
    {
        $validated = $request->validate([
            'message' => 'nullable|string|max:1000',
        ]);

        $offer = Offer::findOrFail($offer_id);

        // Prüfe, ob der Benutzer bereits eine aktive Bewerbung für dieses Angebot hat
        $existingApplication = Application::where('offer_id', $offer->id)
            ->where('applicant_id', Auth::id())
            ->whereNotIn('status', ['retracted'])
            ->first();

        if ($existingApplication) {
            return redirect()->back()->with('error', 'Sie haben sich bereits auf dieses Angebot beworben.');
        }

        $application = Application::create([
            'offer_id' => $offer->id,
            'applicant_id' => Auth::id(),
            'offer_owner_id' => $offer->user_id,
            'message' => $validated['message'] ?? null,
            'status' => 'pending',
            'is_read_by_applicant' => true,
            'is_read_by_owner' => false,
        ]);

        return redirect()->route('web.offers.show', $offer->id)
            ->with('success', 'Bewerbung erfolgreich gesendet.');
    }

    /**
     * Retract an application
     */
    public function retract($id)
    {
        $user = Auth::user();
        $application = Application::findOrFail($id);

        // Prüfe, ob der Benutzer der Bewerber ist
        if ($user->id !== $application->applicant_id) {
            abort(403, 'Unbefugter Zugriff.');
        }

        // Aktualisiere den Status
        $application->update([
            'status' => 'retracted',
            'responded_at' => now(),
        ]);

        return redirect()->route('web.applications.index')
            ->with('success', 'Bewerbung zurückgezogen.');
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Auth::user();
        $application = Application::findOrFail($id);

        // Prüfe, ob der Benutzer berechtigt ist, diese Bewerbung zu sehen
        if ($user->id !== $application->applicant_id && $user->id !== $application->offer_owner_id) {
            abort(403, 'Unbefugter Zugriff.');
        }

        // Markiere als gelesen
        if ($user->id === $application->applicant_id && !$application->is_read_by_applicant) {
            $application->update(['is_read_by_applicant' => true]);
        } elseif ($user->id === $application->offer_owner_id && !$application->is_read_by_owner) {
            $application->update(['is_read_by_owner' => true]);
        }

        $application->load(['offer.company', 'applicant', 'offerOwner']);

        return Inertia::render('applications/show', [
            'application' => [
                'id' => $application->id,
                'offer_id' => $application->offer_id,
                'title' => $application->offer->title,
                'description' => $application->offer->description,
                'company_name' => $application->offer->company->name,
                'message' => $application->message,
                'status' => $application->status,
                'created_at' => $application->created_at->format('Y-m-d H:i:s'),
                'responded_at' => $application->responded_at ? $application->responded_at->format('Y-m-d H:i:s') : null,
                'is_applicant' => $user->id === $application->applicant_id,
                'applicant' => [
                    'id' => $application->applicant->id,
                    'name' => $application->applicant->name,
                ],
                'offer_owner' => [
                    'id' => $application->offerOwner->id,
                    'name' => $application->offerOwner->name,
                ],
            ],
        ]);
    }

    /**
     * Approve the application.
     */
    public function approve($id)
    {
        $user = Auth::user();
        $application = Application::findOrFail($id);

        // Prüfe, ob der Benutzer der Angebotseigentümer ist
        if ($user->id !== $application->offer_owner_id) {
            abort(403, 'Unbefugter Zugriff.');
        }

        // Prüfe, ob die Bewerbung ausstehend oder abgelehnt ist
        if ($application->status !== 'pending' && $application->status !== 'rejected') {
            return redirect()->back()->with('error', 'Diese Bewerbung kann nicht genehmigt werden.');
        }

        // Aktualisiere den Status
        $application->update([
            'status' => 'approved',
            'responded_at' => now(),
        ]);

        // Erstelle ein UserMatch
        $offer = $application->offer;

        // Setze den Offer-Status auf 'matched'
        $offer->update(['status' => 'matched']);

        // Finde oder erstelle einen AffiliateLink
        $affiliateLink = \App\Models\AffiliateLink::firstOrCreate(
            ['company_id' => $offer->company_id],
            [
                'url' => $offer->company->referral_program_url ?? null,
                'admin_status' => 'active'
            ]
        );

        UserMatch::create([
            'offer_id' => $offer->id,
            'user_referrer_id' => $application->offer_owner_id,
            'user_referred_id' => $application->applicant_id,
            'affiliate_link_id' => $affiliateLink->id,
            'status' => 'opened',
            'success_status' => 'pending',
        ]);

        return redirect()->route('web.applications.index')
            ->with('success', 'Bewerbung erfolgreich genehmigt.');
    }

    /**
     * Reject the application.
     */
    public function reject($id)
    {
        $user = Auth::user();
        $application = Application::findOrFail($id);

        // Prüfe, ob der Benutzer der Angebotseigentümer ist
        if ($user->id !== $application->offer_owner_id) {
            abort(403, 'Unbefugter Zugriff.');
        }

        // Prüfe, ob die Bewerbung noch ausstehend ist
        if ($application->status !== 'pending' && $application->status !== 'approved') {
            return redirect()->back()->with('error', 'Diese Bewerbung wurde bereits bearbeitet.');
        }

        // Aktualisiere den Status
        $application->update([
            'status' => 'rejected',
            'responded_at' => now(),
        ]);

        return redirect()->route('web.applications.index')
            ->with('success', 'Bewerbung abgelehnt.');
    }

    /**
     * Mark the application as read.
     */
    public function markRead($id)
    {
        $user = Auth::user();
        $application = Application::findOrFail($id);

        // Prüfe, ob der Benutzer berechtigt ist
        if ($user->id !== $application->applicant_id && $user->id !== $application->offer_owner_id) {
            abort(403, 'Unbefugter Zugriff.');
        }

        // Markiere als gelesen basierend auf der Benutzerrolle
        if ($user->id === $application->applicant_id) {
            $application->update(['is_read_by_applicant' => true]);
        } else {
            $application->update(['is_read_by_owner' => true]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Toggle the read status of the application.
     */
    public function toggleRead($id)
    {
        $user = Auth::user();
        $application = Application::findOrFail($id);

        // Prüfe, ob der Benutzer berechtigt ist
        if ($user->id !== $application->applicant_id && $user->id !== $application->offer_owner_id) {
            abort(403, 'Unbefugter Zugriff.');
        }

        // Toggle den Lesestatus basierend auf der Benutzerrolle
        if ($user->id === $application->applicant_id) {
            $application->update(['is_read_by_applicant' => !$application->is_read_by_applicant]);
        } else {
            $application->update(['is_read_by_owner' => !$application->is_read_by_owner]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Reapply the application.
     */
    public function reapply($id)
    {
        $user = Auth::user();
        $application = Application::findOrFail($id);

        // Prüfe, ob der Benutzer der Bewerber ist
        if ($user->id !== $application->applicant_id) {
            abort(403, 'Unbefugter Zugriff.');
        }

        // Prüfe, ob die Bewerbung zurückgezogen wurde
        if ($application->status !== 'retracted') {
            return redirect()->back()->with('error', 'Diese Bewerbung kann nicht erneut gestellt werden.');
        }

        // Aktualisiere den Status auf "pending"
        $application->update([
            'status' => 'pending',
            'responded_at' => null,
            'is_read_by_owner' => false,
        ]);

        return redirect()->route('web.applications.index')
            ->with('success', 'Bewerbung erneut gestellt.');
    }

    /**
     * Archive the application.
     */
    public function archive($id)
    {
        $user = Auth::user();
        $application = Application::findOrFail($id);

        // Prüfe, ob der Benutzer berechtigt ist
        if ($user->id !== $application->applicant_id && $user->id !== $application->offer_owner_id) {
            abort(403, 'Unbefugter Zugriff.');
        }

        // Archiviere die Bewerbung basierend auf der Benutzerrolle
        if ($user->id === $application->applicant_id) {
            // Wenn der Status "pending" ist, setze ihn auf "retracted"
            if ($application->status === 'pending') {
                $application->update([
                    'status' => 'retracted',
                    'responded_at' => now(),
                    'is_archived_by_applicant' => true,
                ]);
            } else {
                $application->update(['is_archived_by_applicant' => true]);
            }
        } else {
            // Wenn der Status "pending" ist, setze ihn auf "rejected"
            if ($application->status === 'pending') {
                $application->update([
                    'status' => 'rejected',
                    'responded_at' => now(),
                    'is_archived_by_owner' => true,
                ]);
            } else {
                $application->update(['is_archived_by_owner' => true]);
            }
        }

        return redirect()->route('web.applications.index')
            ->with('success', 'Bewerbung archiviert.');
    }

    /**
     * Unarchive the application.
     */
    public function unarchive($id)
    {
        $user = Auth::user();
        $application = Application::findOrFail($id);

        // Prüfe, ob der Benutzer berechtigt ist
        if ($user->id !== $application->applicant_id && $user->id !== $application->offer_owner_id) {
            abort(403, 'Unbefugter Zugriff.');
        }

        // Dearchiviere die Bewerbung basierend auf der Benutzerrolle
        if ($user->id === $application->applicant_id) {
            $application->update(['is_archived_by_applicant' => false]);
        } else {
            $application->update(['is_archived_by_owner' => false]);
        }

        return redirect()->route('web.applications.index')
            ->with('success', 'Bewerbung wiederhergestellt.');
    }
}
