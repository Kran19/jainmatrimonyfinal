<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Membership;

class MembershipController extends Controller
{
    /**
     * Display a listing of the membership plans.
     */
    public function index()
    {
        $plans = Membership::all();
        return view('admin.cms.membership-plans', compact('plans'));
    }

    /**
     * Store a newly created membership plan in database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'plan_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'contact_limit' => 'required|integer|min:0',
            'featured_profile' => 'nullable|boolean',
            'priority_support' => 'nullable|boolean',
            'status' => 'nullable|boolean',
        ]);

        Membership::create([
            'plan_name' => $request->plan_name,
            'price' => $request->price,
            'duration_days' => $request->duration_days,
            'contact_limit' => $request->contact_limit,
            'featured_profile' => $request->has('featured_profile'),
            'priority_support' => $request->has('priority_support'),
            'status' => $request->has('status'),
        ]);

        return redirect()->route('admin.membership-plans.index')->with('success', 'Membership plan created successfully.');
    }

    /**
     * Update the specified membership plan.
     */
    public function update(Request $request, Membership $plan)
    {
        $request->validate([
            'plan_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'contact_limit' => 'required|integer|min:0',
            'featured_profile' => 'nullable|boolean',
            'priority_support' => 'nullable|boolean',
            'status' => 'nullable|boolean',
        ]);

        $plan->update([
            'plan_name' => $request->plan_name,
            'price' => $request->price,
            'duration_days' => $request->duration_days,
            'contact_limit' => $request->contact_limit,
            'featured_profile' => $request->has('featured_profile'),
            'priority_support' => $request->has('priority_support'),
            'status' => $request->has('status'),
        ]);

        return redirect()->route('admin.membership-plans.index')->with('success', 'Membership plan updated successfully.');
    }

    /**
     * Remove the specified membership plan.
     */
    public function destroy(Membership $plan)
    {
        $plan->delete();
        return redirect()->route('admin.membership-plans.index')->with('success', 'Membership plan deleted successfully.');
    }
}
