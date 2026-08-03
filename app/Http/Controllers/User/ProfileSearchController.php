<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProfileSearchController extends Controller
{
    /**
     * Search matrimonial profiles with filters.
     */
    public function index(Request $request)
    {
        $me = Auth::user();
        
        // Default target gender is opposite of logged-in candidate
        $defaultGender = $me->gender === 'Male' ? 'Female' : 'Male';
        $gender = $request->input('gender');

        // Normalize gender input from Girl/Boy to Female/Male
        $genderVal = null;
        if ($gender === 'Girl') {
            $genderVal = 'Female';
        } elseif ($gender === 'Boy') {
            $genderVal = 'Male';
        } elseif (in_array($gender, ['Female', 'Male'])) {
            $genderVal = $gender;
        }

        // Build list of liked IDs for this user
        $likedIds = UserLike::where('user_id', $me->id)->pluck('liked_user_id')->toArray();
        $likedIdsStr = implode(',', array_merge([0], $likedIds));

        // Base query
        $query = User::approved()->public()
            ->where('users.id', '!=', $me->id)
            ->select('users.*')
            ->selectRaw("IF(users.id IN ($likedIdsStr), 1, 0) AS is_liked");

        if ($genderVal) {
            $query->where('gender', $genderVal);
        }

        // 1. Filter by Match ID
        if ($request->filled('match_id')) {
            $query->where('profile_id', trim($request->match_id));
        }

        // 2. Filter by City / Native Place
        if ($request->filled('city')) {
            $query->where('native_place', 'like', "%{$request->city}%");
        }

        // 3. Filter by State
        if ($request->filled('state')) {
            $stateVal = trim($request->state);
            $query->where(function ($q) use ($stateVal) {
                $q->where('current_address', 'like', "%{$stateVal}%")
                  ->orWhere('permanent_address', 'like', "%{$stateVal}%")
                  ->orWhere('native_place', 'like', "%{$stateVal}%");
            });
        }

        // 4. Filter by Education
        if ($request->filled('education') && $request->education !== 'Education All') {
            $edu = trim($request->education);
            if (in_array($edu, ['Doctors', 'Doctor', 'Doctorate'])) {
                $query->where(function ($q) {
                    $q->where('higher_education', 'like', '%Doctor%')
                      ->orWhere('higher_education', 'like', '%MBBS%')
                      ->orWhere('higher_education', 'like', '%BDS%')
                      ->orWhere('higher_education', 'like', '%MD %')
                      ->orWhere('higher_education', 'like', '%M.D%')
                      ->orWhere('higher_education', 'like', '%MD(%')
                      ->orWhere('higher_education', '=', 'MD')
                      ->orWhere('higher_education', 'like', '%Surgery%')
                      ->orWhere('higher_education', 'like', '%Surgeon%')
                      ->orWhere('higher_education', 'like', '%BAMS%')
                      ->orWhere('higher_education', 'like', '%BHMS%')
                      ->orWhere('higher_education', 'like', '%MDS%')
                      ->orWhere('higher_education', 'like', '%Dentist%')
                      ->orWhere('higher_education', 'like', '%Medical%');
                });
            } elseif ($edu === 'Engineer') {
                $query->where(function ($q) {
                    $q->where('higher_education', 'like', '%Engineer%')
                      ->orWhere('higher_education', 'like', '%B.E%')
                      ->orWhere('higher_education', 'like', '%B.Tech%')
                      ->orWhere('higher_education', 'like', '%M.Tech%')
                      ->orWhere('higher_education', 'like', '%B Tech%')
                      ->orWhere('higher_education', 'like', '%M Tech%')
                      ->orWhere('higher_education', 'like', '%B. E%');
                });
            } elseif ($edu === 'MBA') {
                $query->where(function ($q) {
                    $q->where('higher_education', 'like', '%MBA%')
                      ->orWhere('higher_education', 'like', '%PGBDM%')
                      ->orWhere('higher_education', 'like', '%PGDM%');
                });
            } elseif ($edu === 'MCA') {
                $query->where('higher_education', 'like', '%MCA%');
            } elseif ($edu === 'MBA/MCA') {
                $query->where(function ($q) {
                    $q->where('higher_education', 'like', '%MBA%')
                      ->orWhere('higher_education', 'like', '%MCA%')
                      ->orWhere('higher_education', 'like', '%PGBDM%')
                      ->orWhere('higher_education', 'like', '%PGDM%');
                });
            } elseif ($edu === 'CA') {
                $query->where(function ($q) {
                    $q->where('higher_education', 'like', '% CA %')
                      ->orWhere('higher_education', 'like', 'CA %')
                      ->orWhere('higher_education', 'like', '% CA')
                      ->orWhere('higher_education', '=', 'CA')
                      ->orWhere('higher_education', 'like', '%Chartered Accountant%');
                });
            } elseif ($edu === 'CS') {
                $query->where(function ($q) {
                    $q->where('higher_education', 'like', '% CS %')
                      ->orWhere('higher_education', 'like', 'CS %')
                      ->orWhere('higher_education', 'like', '% CS')
                      ->orWhere('higher_education', '=', 'CS')
                      ->orWhere('higher_education', 'like', '%Company Secretary%');
                });
            } elseif ($edu === 'CA/CS') {
                $query->where(function ($q) {
                    $q->where('higher_education', 'like', '% CA %')
                      ->orWhere('higher_education', 'like', 'CA %')
                      ->orWhere('higher_education', 'like', '% CA')
                      ->orWhere('higher_education', '=', 'CA')
                      ->orWhere('higher_education', 'like', '%Chartered Accountant%')
                      ->orWhere('higher_education', 'like', '% CS %')
                      ->orWhere('higher_education', 'like', 'CS %')
                      ->orWhere('higher_education', 'like', '% CS')
                      ->orWhere('higher_education', '=', 'CS')
                      ->orWhere('higher_education', 'like', '%Company Secretary%');
                });
            } else {
                $query->where('higher_education', 'like', "%{$edu}%");
            }
        }

        // 5. Filter by Manglik Status
        if ($request->filled('manglik')) {
            $manglikVal = strtolower($request->manglik);
            if ($manglikVal === 'yes') {
                $query->where('manglik', 'Yes');
            } elseif ($manglikVal === 'no') {
                $query->where('manglik', 'No');
            }
        }

        // 6. Filter by Marital Status
        if ($request->filled('marital') && $request->marital !== 'All') {
            $query->where('marital_status', $request->marital);
        }

        // 7. Filter by Occupation
        if ($request->filled('occupation') && $request->occupation !== 'Occupation All') {
            $query->where('occupation', 'like', "%{$request->occupation}%");
        }

        // 8. Filter by Age range
        if ($request->filled('age_from') && is_numeric($request->age_from)) {
            $maxBirthDate = Carbon::now()->subYears((int)$request->age_from)->endOfDay()->toDateString();
            $query->where('birth_date', '<=', $maxBirthDate);
        }
        if ($request->filled('age_to') && is_numeric($request->age_to)) {
            $minBirthDate = Carbon::now()->subYears((int)$request->age_to + 1)->addDay()->toDateString();
            $query->where('birth_date', '>=', $minBirthDate);
        }

        // 9. Filter by NRI Status
        if ($request->filled('nri') && strtolower($request->nri) === 'yes') {
            $nriCountries = ['USA', 'United States', 'America', 'Canada', 'Dubai', 'UAE', 'Australia', 'United Kingdom', 'London', 'Singapore', 'New Zealand', 'Germany', 'France', 'Kuwait', 'Oman', 'Qatar', 'California', 'Texas', 'New York', 'Toronto', 'Melbourne', 'Sydney'];
            $query->where(function ($q) use ($nriCountries) {
                $q->whereNotIn('country_code', ['91', '+91', ' 91', ' +91', '091', '+091'])
                  ->whereNotNull('country_code')
                  ->where('country_code', '!=', '')
                  ->orWhere(function ($sub) use ($nriCountries) {
                      foreach ($nriCountries as $country) {
                          $sub->orWhere('current_address', 'like', "%{$country}%");
                      }
                  });
            });
        }

        // Apply Sorting
        $sortBy = $request->input('sort_by', 'name_asc');
        $query->orderByRaw("is_liked DESC");

        if ($sortBy === 'name_desc') {
            $query->orderByRaw("TRIM(full_name) DESC, birth_date DESC");
        } elseif ($sortBy === 'age_asc') {
            $query->orderByRaw("birth_date DESC, TRIM(full_name) ASC");
        } elseif ($sortBy === 'age_desc') {
            $query->orderByRaw("birth_date ASC, TRIM(full_name) ASC");
        } elseif ($sortBy === 'latest') {
            $query->orderBy('id', 'desc');
        } else {
            $query->orderByRaw("TRIM(full_name) ASC, birth_date DESC");
        }

        // Pagination
        $profiles = $query->paginate(10)->withQueryString();

        // Storing the current search URL in session so detail page can back out to it
        session(['last_search_url' => $request->fullUrl()]);

        return view('user.search', compact('profiles', 'likedIds', 'gender'));
    }

    public function showDetail(Request $request, User $profile)
    {
        // Admins can view any profile; regular users only see admin-approved profiles.
        // is_approved may be null if the DB migration hasn't run yet — treat null same as 1
        // when status is already 'approved' (backward-compatible fallback).
        $isAdmin = Auth::guard('admin')->check();
        $isApproved = ($profile->is_approved === null) ? ($profile->status === 'approved') : (bool)$profile->is_approved;
        if (!$isAdmin && ($profile->status !== 'approved' || !$isApproved)) {
            return abort(403, 'Profile is not active.');
        }

        // Load custom fields EAV
        $customData = $profile->customData()->with('field')->get();

        if ($request->ajax()) {
            $html = view('user.partials.profile-modal-content', compact('profile', 'customData'))->render();
            return response()->json(['html' => $html]);
        }

        return view('user.detail', compact('profile', 'customData'));
    }

    public function downloadPdf(Request $request, User $profile)
    {
        $isAdmin = Auth::guard('admin')->check();
        $isApproved = ($profile->is_approved === null) ? ($profile->status === 'approved') : (bool)$profile->is_approved;
        if (!$isAdmin && ($profile->status !== 'approved' || !$isApproved)) {
            return abort(403, 'Profile is not active.');
        }

        $customData = $profile->customData()->with('field')->get();
        return view('user.pdf-view', compact('profile', 'customData'));
    }

    /**
     * Toggle shortlisting status (Like / Unlike).
     */
    public function toggleLike(User $profile)
    {
        $me = Auth::user();

        if ($profile->id === $me->id) {
            return response()->json(['error' => 'You cannot shortlist your own profile.'], 400);
        }

        $like = UserLike::where('user_id', $me->id)->where('liked_user_id', $profile->id)->first();

        if ($like) {
            $like->delete();
            $liked = false;
            $message = 'Profile removed from shortlists.';
        } else {
            UserLike::create([
                'user_id' => $me->id,
                'liked_user_id' => $profile->id,
            ]);
            $liked = true;
            $message = 'Profile added to shortlists.';
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'message' => $message,
        ]);
    }
}
