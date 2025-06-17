<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Application;
use App\Models\Offer;
use App\Models\UserMatch;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the applications.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get applications where the user is either the applicant or the offer owner
        $applications = Application::where('applicant_id', $user->id)
            ->orWhere('offer_owner_id', $user->id)
            ->with(['offer', 'applicant', 'offerOwner'])
            ->latest()
            ->get()
            ->map(function ($application) use ($user) {
                $isApplicant = $application->applicant_id === $user->id;
                
                return [
                    'id' => $application->id,
                    'offer_id' => $application->offer_id,
                    'offer_title' => $application->offer->offer_title,
                    'company_name' => $application->offer->company->name,
                    'message' => $application->message,
                    'status' => $application->status,
                    'created_at' => $application->created_at->format('Y-m-d H:i:s'),
                    'responded_at' => $application->responded_at ? $application->responded_at->format('Y-m-d H:i:s') : null,
                    'is_unread' => $isApplicant ? !$application->is_read_by_applicant : !$application->is_read_by_owner,
                    'is_applicant' => $isApplicant,
                    'other_user' => $isApplicant ? $application->offerOwner->name : $application->applicant->name,
                ];
            });
        
        return Inertia::render('applications/index', [
            'applications' => $applications,
            'unreadCount' => $applications->where('is_unread', true)->count(),
        ]);
    }

    /**
     * Show the form for creating a new application.
     */
    public function create($offerId)
    {
        $offer = Offer::with(['user', 'company'])->findOrFail($offerId);
        
        // Check if the user already has an application for this offer
        $existingApplication = Application::where('offer_id', $offerId)
            ->where('applicant_id', Auth::id())
            ->first();
            
        if ($existingApplication) {
            return redirect()->route('web.applications.show', $existingApplication->id)
                ->with('info', 'Sie haben bereits eine Anfrage für dieses Angebot gestellt.');
        }
        
        // Check if the user is the owner of the offer
        if ($offer->user_id === Auth::id()) {
            return redirect()->route('web.offers.show', $offerId)
                ->with('error', 'Sie können keine Anfrage für Ihr eigenes Angebot stellen.');
        }
        
        $offerData = [
            'id' => $offer->id,
            'title' => $offer->offer_title,
            'description' => $offer->offer_description,
            'offered_by_type' => $offer->offered_by_type == 'referrer' ? 'Werbender' : 'Beworbener',
            'offer_user' => $offer->user->name,
            'offer_company' => $offer->company->name,
            'logo_url' => $offer->company->logo_url,
            'reward_total_cents' => $offer->reward_total_cents,
            'reward_offerer_percent' => $offer->reward_offerer_percent,
            'status' => $offer->status,
        ];
        
        return Inertia::render('offers/apply', [
            'offer' => $offerData,
        ]);
    }

    /**
     * Store a newly created application in storage.
     */
    public function store(Request $request, $offerId)
    {
        $validated = $request->validate([
            'message' => 'nullable|string|max:1000',
        ]);
        
        $offer = Offer::findOrFail($offerId);
        
        // Check if the user already has an application for this offer
        $existingApplication = Application::where('offer_id', $offerId)
            ->where('applicant_id', Auth::id())
            ->first();
            
        if ($existingApplication) {
            return redirect()->route('web.applications.show', $existingApplication->id)
                ->with('info', 'Sie haben bereits eine Anfrage für dieses Angebot gestellt.');
        }
        
        // Create the application
        $application = Application::create([
            'offer_id' => $offerId,
            'applicant_id' => Auth::id(),
            'offer_owner_id' => $offer->user_id,
            'message' => $validated['message'],
            'status' => 'pending',
            'is_read_by_applicant' => true,
            'is_read_by_owner' => false,
        ]);
        
        return redirect()->route('web.applications.show', $application->id)
            ->with('success', 'Ihre Anfrage wurde erfolgreich gesendet.');
    }

    /**
     * Display the specified application.
     */
    public function show($id)
    {
        $user = Auth::user();
        $application = Application::with(['offer', 'applicant', 'offerOwner'])->findOrFail($id);
        
        // Check if the user is either the applicant or the offer owner
        if ($application->applicant_id !== $user->id && $application->offer_owner_id !== $user->id) {
            abort(403, 'Sie haben keine Berechtigung, diese Anfrage anzusehen.');
        }
        
        // Mark as read
        if ($application->applicant_id === $user->id && !$application->is_read_by_applicant) {
            $application->update(['is_read_by_applicant' => true]);
        } elseif ($application->offer_owner_id === $user->id && !$application->is_read_by_owner) {
            $application->update(['is_read_by_owner' => true]);
        }
        
        $isApplicant = $application->applicant_id === $user->id;
        
        $applicationData = [
            'id' => $application->id,
            'offer_id' => $application->offer_id,
            'offer_title' => $application->offer->offer_title,
            'offer_description' => $application->offer->offer_description,
            'company_name' => $application->offer->company->name,
            'message' => $application->message,
            'status' => $application->status,
            'created_at' => $application->created_at->format('Y-m-d H:i:s'),
            'responded_at' => $application->responded_at ? $application->responded_at->format('Y-m-d H:i:s') : null,
            'is_applicant' => $isApplicant,
            'applicant' => [
                'id' => $application->applicant->id,
                'name' => $application->applicant->name,
            ],
            'offer_owner' => [
                'id' => $application->offerOwner->id,
                'name' => $application->offerOwner->name,
            ],
        ];
        
        return Inertia::render('applications/show', [
            'application' => $applicationData,
        ]);
    }

    /**
     * Approve the specified application.
     */
    public function approve($id)
    {
        $user = Auth::user();
        $application = Application::findOrFail($id);
        
        // Check if the user is the offer owner
        if ($application->offer_owner_id !== $user->id) {
            abort(403, 'Sie haben keine Berechtigung, diese Anfrage zu genehmigen.');
        }
        
        // Check if the application is already approved or rejected
        if ($application->status !== 'pending') {
            return redirect()->route('web.applications.show', $id)
                ->with('info', 'Diese Anfrage wurde bereits bearbeitet.');
        }
        
        // Update the application status
        $application->update([
            'status' => 'approved',
            'responded_at' => now(),
            'is_read_by_applicant' => false,
        ]);
        
        // Create a user match
        UserMatch::create([
            'offer_id' => $application->offer_id,
            'user_referrer_id' => $application->offer->offered_by_type === 'referrer' ? $application->offer_owner_id : $application->applicant_id,
            'user_referred_id' => $application->offer->offered_by_type === 'referrer' ? $application->applicant_id : $application->offer_owner_id,
            'link_clicked' => false,
        ]);
        
        return redirect()->route('web.applications.show', $id)
            ->with('success', 'Die Anfrage wurde erfolgreich genehmigt.');
    }

    /**
     * Reject the specified application.
     */
    public function reject($id)
    {
        $user = Auth::user();
        $application = Application::findOrFail($id);
        
        // Check if the user is the offer owner
        if ($application->offer_owner_id !== $user->id) {
            abort(403, 'Sie haben keine Berechtigung, diese Anfrage abzulehnen.');
        }
        
        // Check if the application is already approved or rejected
        if ($application->status !== 'pending') {
            return redirect()->route('web.applications.show', $id)
                ->with('info', 'Diese Anfrage wurde bereits bearbeitet.');
        }
        
        // Update the application status
        $application->update([
            'status' => 'rejected',
            'responded_at' => now(),
            'is_read_by_applicant' => false,
        ]);
        
        return redirect()->route('web.applications.show', $id)
            ->with('success', 'Die Anfrage wurde abgelehnt.');
    }
}