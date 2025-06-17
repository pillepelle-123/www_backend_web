<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AffiliateLink;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AffiliateLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $affiliateLinks = AffiliateLink::with('company')
            ->where('admin_status', 'active')
            ->paginate(10);
            
        return Inertia::render('affiliate-links/index', [
            'affiliateLinks' => $affiliateLinks,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::where('admin_status', 'active')->get();
        
        return Inertia::render('affiliate-links/create', [
            'companies' => $companies,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'url' => 'nullable|url',
            'parameters' => 'nullable|string',
        ]);
        
        $affiliateLink = AffiliateLink::create([
            'company_id' => $validated['company_id'],
            'url' => $validated['url'],
            'parameters' => $validated['parameters'],
            'admin_status' => 'pending',
        ]);
        
        return redirect()->route('web.affiliate-links.show', $affiliateLink->id)
            ->with('success', 'Affiliate Link created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AffiliateLink $affiliateLink)
    {
        $affiliateLink->load('company');
        
        return Inertia::render('affiliate-links/show', [
            'affiliateLink' => $affiliateLink,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AffiliateLink $affiliateLink)
    {
        $affiliateLink->load('company');
        $companies = Company::where('admin_status', 'active')->get();
        
        return Inertia::render('affiliate-links/edit', [
            'affiliateLink' => $affiliateLink,
            'companies' => $companies,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AffiliateLink $affiliateLink)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'url' => 'nullable|url',
            'parameters' => 'nullable|string',
        ]);
        
        $affiliateLink->update([
            'company_id' => $validated['company_id'],
            'url' => $validated['url'],
            'parameters' => $validated['parameters'],
        ]);
        
        return redirect()->route('web.affiliate-links.show', $affiliateLink->id)
            ->with('success', 'Affiliate Link updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AffiliateLink $affiliateLink)
    {
        $affiliateLink->delete();
        
        return redirect()->route('web.affiliate-links.index')
            ->with('success', 'Affiliate Link deleted successfully.');
    }
    
    /**
     * Track a click on an affiliate link.
     */
    public function trackClick(AffiliateLink $affiliateLink)
    {
        $affiliateLink->incrementClickCount();
        
        // Redirect to the affiliate link URL
        return redirect($affiliateLink->url);
    }
}