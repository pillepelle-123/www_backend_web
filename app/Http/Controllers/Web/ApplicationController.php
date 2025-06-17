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
            
            return [
                'id' => $application->id,
                'offer_id' => $application->offer_id,
                'offer_title' => $application->offer->offer_title,
                'company_name' => $application->offer->company->name,
                'message' => $application->message,
                'status' => $application->status,
                'created_at' => $application->created_at->format('Y-m-d H:i:s'),
                'responded_at' => $application->responded_at ? $application->responded_at->format('Y-m-d H:i:s') : null,
                'is_unread' => $isUnread,
                'is_applicant' => $isApplicant,
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
    public function create(Offer $offer)
    {
        return Inertia::render('applications/create', [
            'offer' => [
                'id' => $offer->id,
                'title' => $offer->offer_title,
                'description' => $offer->offer_description,
                'company' => $offer->company->name,
                'user' => $offer->user->name,
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'offer_id' => 'required|exists:offers,id',
            'message' => 'nullable|string|max:1000',
        ]);
        
        $offer = Offer::findOrFail($validated['offer_id']);
        
        // Prüfe, ob der Benutzer bereits eine Bewerbung für dieses Angebot hat
        $existingApplication = Application::where('offer_id', $offer->id)
            ->where('applicant_id', Auth::id())
            ->first();
            
        if ($existingApplication) {
            return redirect()->back()->with('error', 'Sie haben sich bereits auf dieses Angebot beworben.');
        }
        
        $application = Application::create([
            'offer_id' => $offer->id,
            'applicant_id' => Auth::id(),
            'offer_owner_id' => $offer->user_id,
            'message' => $validated['message'],
            'status' => 'pending',
            'is_read_by_applicant' => true,
            'is_read_by_owner' => false,
        ]);
        
        return redirect()->route('web.applications.show', $application->id)
            ->with('success', 'Bewerbung erfolgreich gesendet.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application)
    {
        $user = Auth::user();
        
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
                'offer_title' => $application->offer->offer_title,
                'offer_description' => $application->offer->offer_description,
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
    public function approve(Application $application)
    {
        $user = Auth::user();
        
        // Prüfe, ob der Benutzer der Angebotseigentümer ist
        if ($user->id !== $application->offer_owner_id) {
            abort(403, 'Unbefugter Zugriff.');
        }
        
        // Prüfe, ob die Bewerbung noch ausstehend ist
        if ($application->status !== 'pending') {
            return redirect()->back()->with('error', 'Diese Bewerbung wurde bereits bearbeitet.');
        }
        
        // Aktualisiere den Status
        $application->update([
            'status' => 'approved',
            'responded_at' => now(),
        ]);
        
        // Erstelle ein UserMatch
        $offer = $application->offer;
        
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
        
        return redirect()->route('web.applications.show', $application->id)
            ->with('success', 'Bewerbung erfolgreich genehmigt.');
    }

    /**
     * Reject the application.
     */
    public function reject(Application $application)
    {
        $user = Auth::user();
        
        // Prüfe, ob der Benutzer der Angebotseigentümer ist
        if ($user->id !== $application->offer_owner_id) {
            abort(403, 'Unbefugter Zugriff.');
        }
        
        // Prüfe, ob die Bewerbung noch ausstehend ist
        if ($application->status !== 'pending') {
            return redirect()->back()->with('error', 'Diese Bewerbung wurde bereits bearbeitet.');
        }
        
        // Aktualisiere den Status
        $application->update([
            'status' => 'rejected',
            'responded_at' => now(),
        ]);
        
        return redirect()->route('web.applications.show', $application->id)
            ->with('success', 'Bewerbung abgelehnt.');
    }
}