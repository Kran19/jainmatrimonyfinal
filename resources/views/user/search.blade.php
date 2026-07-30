@extends('layouts.app')

@section('title', 'Search Matrimonial Profiles - Jain Digambar Matrimony')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Left Filter Sidebar -->
        <div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-4">
                <h3 class="font-black text-gray-800 text-lg border-b pb-3 mb-4">
                    <i class="fa-solid fa-filter mr-2 text-primary"></i>Search Filters
                </h3>

                <form action="{{ route('profiles') }}" method="GET" class="space-y-5 text-sm">
                    <!-- 1. Match ID -->
                    <div>
                        <input type="text" name="match_id" value="{{ request('match_id') }}" placeholder="Enter Match ID (e.g. DJP12345)"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-gray-700 placeholder-gray-400">
                    </div>

                    <!-- 2. City / Native Place -->
                    <div>
                        <input type="text" name="city" value="{{ request('city') }}" placeholder="Enter City / Native Place"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-gray-700 placeholder-gray-400">
                    </div>

                    <!-- 3. State -->
                    <div>
                        <select name="state" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-gray-700">
                            <option value="">Any State</option>
                            <option value="Maharashtra" {{ request('state') == 'Maharashtra' ? 'selected' : '' }}>Maharashtra</option>
                            <option value="Madhya Pradesh" {{ request('state') == 'Madhya Pradesh' ? 'selected' : '' }}>Madhya Pradesh</option>
                            <option value="Gujarat" {{ request('state') == 'Gujarat' ? 'selected' : '' }}>Gujarat</option>
                            <option value="Rajasthan" {{ request('state') == 'Rajasthan' ? 'selected' : '' }}>Rajasthan</option>
                            <option value="Karnataka" {{ request('state') == 'Karnataka' ? 'selected' : '' }}>Karnataka</option>
                            <option value="Delhi" {{ request('state') == 'Delhi' ? 'selected' : '' }}>Delhi</option>
                            <option value="Uttar Pradesh" {{ request('state') == 'Uttar Pradesh' ? 'selected' : '' }}>Uttar Pradesh</option>
                            <option value="Haryana" {{ request('state') == 'Haryana' ? 'selected' : '' }}>Haryana</option>
                            <option value="Punjab" {{ request('state') == 'Punjab' ? 'selected' : '' }}>Punjab</option>
                            <option value="Tamil Nadu" {{ request('state') == 'Tamil Nadu' ? 'selected' : '' }}>Tamil Nadu</option>
                            <option value="Andhra Pradesh" {{ request('state') == 'Andhra Pradesh' ? 'selected' : '' }}>Andhra Pradesh</option>
                            <option value="Telangana" {{ request('state') == 'Telangana' ? 'selected' : '' }}>Telangana</option>
                        </select>
                    </div>

                    <!-- 4. Education -->
                    <div>
                        <select name="education" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-gray-700">
                            <option value="">Education All</option>
                            <option value="Doctor" {{ request('education') == 'Doctor' ? 'selected' : '' }}>Doctor (MBBS/MD/BDS...)</option>
                            <option value="Engineer" {{ request('education') == 'Engineer' ? 'selected' : '' }}>Engineer (BE/BTech...)</option>
                            <option value="MBA" {{ request('education') == 'MBA' ? 'selected' : '' }}>MBA / PGDM</option>
                            <option value="MCA" {{ request('education') == 'MCA' ? 'selected' : '' }}>MCA</option>
                            <option value="MBA/MCA" {{ request('education') == 'MBA/MCA' ? 'selected' : '' }}>MBA / MCA</option>
                            <option value="CA" {{ request('education') == 'CA' ? 'selected' : '' }}>CA</option>
                            <option value="CS" {{ request('education') == 'CS' ? 'selected' : '' }}>CS</option>
                            <option value="CA/CS" {{ request('education') == 'CA/CS' ? 'selected' : '' }}>CA / CS</option>
                            <option value="Graduate" {{ request('education') == 'Graduate' ? 'selected' : '' }}>Graduate</option>
                            <option value="Post Graduate" {{ request('education') == 'Post Graduate' ? 'selected' : '' }}>Post Graduate</option>
                            <option value="Ph.D" {{ request('education') == 'Ph.D' ? 'selected' : '' }}>Ph.D</option>
                            <option value="Diploma" {{ request('education') == 'Diploma' ? 'selected' : '' }}>Diploma</option>
                        </select>
                    </div>

                    <!-- 5. Marital Status -->
                    <div>
                        <select name="marital" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-gray-700">
                            <option value="">Marital Status All</option>
                            <option value="Unmarried" {{ request('marital') == 'Unmarried' ? 'selected' : '' }}>Unmarried</option>
                            <option value="Divorcee" {{ request('marital') == 'Divorcee' ? 'selected' : '' }}>Divorcee</option>
                            <option value="Widow" {{ request('marital') == 'Widow' ? 'selected' : '' }}>Widow</option>
                            <option value="Widower" {{ request('marital') == 'Widower' ? 'selected' : '' }}>Widower</option>
                        </select>
                    </div>

                    <!-- 6. Occupation -->
                    <div>
                        <select name="occupation" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-gray-700">
                            <option value="">Occupation All</option>
                            <option value="Business" {{ request('occupation') == 'Business' ? 'selected' : '' }}>Business</option>
                            <option value="Service" {{ request('occupation') == 'Service' ? 'selected' : '' }}>Service</option>
                            <option value="Professional" {{ request('occupation') == 'Professional' ? 'selected' : '' }}>Professional</option>
                            <option value="Government Job" {{ request('occupation') == 'Government Job' ? 'selected' : '' }}>Government Job</option>
                            <option value="Private Job" {{ request('occupation') == 'Private Job' ? 'selected' : '' }}>Private Job</option>
                            <option value="Not Working" {{ request('occupation') == 'Not Working' ? 'selected' : '' }}>Not Working</option>
                        </select>
                    </div>

                    <!-- 7. Gender -->
                    <div>
                        <select name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-gray-700">
                            <option value="">All</option>
                            <option value="Girl" {{ request('gender') === 'Girl' ? 'selected' : '' }}>Girl</option>
                            <option value="Boy" {{ request('gender') === 'Boy' ? 'selected' : '' }}>Boy</option>
                        </select>
                    </div>

                    <!-- 8. Age -->
                    <div class="flex items-center space-x-2">
                        <input type="number" name="age_from" value="{{ request('age_from') }}" placeholder="Age From"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-gray-700 placeholder-gray-400">
                        <span class="text-gray-500 font-medium">to</span>
                        <input type="number" name="age_to" value="{{ request('age_to') }}" placeholder="Age To"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-gray-700 placeholder-gray-400">
                    </div>

                    <!-- 9. Sort By -->
                    <div>
                        <select name="sort_by" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary bg-white text-gray-700">
                            <option value="name_asc" {{ request('sort_by', 'name_asc') == 'name_asc' ? 'selected' : '' }}>Sort By: Name (A to Z)</option>
                            <option value="name_desc" {{ request('sort_by') == 'name_desc' ? 'selected' : '' }}>Sort By: Name (Z to A)</option>
                            <option value="age_asc" {{ request('sort_by') == 'age_asc' ? 'selected' : '' }}>Sort By: Age (Young to Old)</option>
                            <option value="age_desc" {{ request('sort_by') == 'age_desc' ? 'selected' : '' }}>Sort By: Age (Old to Young)</option>
                            <option value="latest" {{ request('sort_by') == 'latest' ? 'selected' : '' }}>Sort By: Recently Added</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full py-2 bg-primary hover:bg-opacity-90 text-white font-bold rounded-lg transition duration-150">
                        Filter Profiles
                    </button>
                    <a href="{{ route('profiles') }}" class="block w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-center transition">
                        Clear Filters
                    </a>
                </form>
            </div>
        </div>

        <!-- Right Results Grid -->
        <div class="lg:col-span-3 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($profiles as $profile)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition duration-150 flex flex-col justify-between">
                    <!-- Photo Header -->
                    <div class="h-48 bg-slate-100 relative">
                        @php
                            $photoExists = !empty($profile->profile_photo) && (str_starts_with($profile->profile_photo, 'data:image/') || resolve_media_path($profile->profile_photo) !== null);
                        @endphp
                        @if($photoExists)
                            <img src="{{ route('image.serve', ['file' => $profile->profile_photo]) }}" alt="Photo" class="w-full h-full object-contain object-center bg-slate-200">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300 font-black text-6xl">
                                {{ substr($profile->full_name, 0, 1) }}
                            </div>
                        @endif

                        <!-- Shortlist Heart button -->
                        <button onclick="toggleShortlist({{ $profile->id }}, this)"
                                class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/95 hover:bg-white text-rose-500 shadow-sm flex items-center justify-center transition">
                            @if(in_array($profile->id, $likedIds))
                                <i class="fa-solid fa-heart text-lg"></i>
                            @else
                                <i class="fa-regular fa-heart text-lg"></i>
                            @endif
                        </button>
                    </div>

                    <!-- Profile Info -->
                    <div class="p-5 flex-grow">
                        <h4 class="font-extrabold text-gray-900 truncate leading-tight">{{ $profile->full_name }}</h4>
                        <p class="text-xs font-semibold text-primary mt-1 font-mono">{{ $profile->profile_id }}</p>
                        
                        <div class="grid grid-cols-2 gap-2 mt-4 text-xs font-medium text-gray-500 border-t pt-3">
                            <div>Age: <span class="font-bold text-gray-800">{{ $profile->birth_date ? $profile->birth_date->age : 'N/A' }} Yrs</span></div>
                            <div>Height: <span class="font-bold text-gray-800">{{ $profile->height ?? 'N/A' }}</span></div>
                            <div class="col-span-2">Gotra: <span class="font-bold text-gray-800">{{ $profile->gotra ?? 'N/A' }}</span></div>
                            <div class="col-span-2">Occupation: <span class="font-bold text-gray-800 truncate block">{{ $profile->occupation ?? 'N/A' }}</span></div>
                        </div>
                    </div>

                    <!-- Profile CTA -->
                    <div class="p-5 bg-slate-50 border-t flex-shrink-0">
                        <a href="{{ route('profiles.detail', $profile->id) }}" target="_blank"
                           class="block w-full py-2 bg-white border hover:bg-slate-50 text-primary font-bold rounded-lg text-xs transition border-gray-200 text-center">
                            View Full Profile
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-span-3 bg-white border p-12 text-center text-gray-500 rounded-2xl">
                    <i class="fa-solid fa-users-slash text-4xl text-slate-300 mb-2"></i>
                    <p class="font-bold text-sm">No match profiles found.</p>
                    <p class="text-xs text-slate-400 mt-1">Try relaxing your filter parameters.</p>
                </div>
                @endforelse
            </div>

            @if($profiles->hasPages())
            <div class="bg-white p-4 rounded-xl border">
                {{ $profiles->links() }}
            </div>
            @endif
        </div>

    </div>
</div>

<!-- Profile Detail Modal -->
<div id="profile-modal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white w-full max-w-2xl rounded-2xl overflow-hidden shadow-2xl flex flex-col max-h-[90vh]">
        <div class="p-4 border-b flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-gray-800">Candidate Details</h3>
            <div class="flex items-center gap-2">
                <button onclick="downloadModalPDF()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[11px] px-3 py-1.5 rounded-lg shadow-sm flex items-center gap-1.5 transition">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
                <button onclick="closeProfileModal()" class="w-8 h-8 rounded-full hover:bg-slate-200 text-slate-500 flex items-center justify-center transition focus:outline-none">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>
        <div id="profile-modal-body" class="p-6 overflow-y-auto">
            <!-- Dynamically injected via AJAX -->
            <div class="flex items-center justify-center py-12">
                <i class="fa-solid fa-circle-notch fa-spin text-3xl text-primary"></i>
            </div>
        </div>
    </div>
</div>

<!-- AJAX Scripts -->
<script>
    function toggleShortlist(profileId, btn) {
        const heartIcon = btn.querySelector('i');
        const isLiked = heartIcon.classList.contains('fa-solid');

        fetch(`/profiles/${profileId}/like`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.liked) {
                heartIcon.className = 'fa-solid fa-heart text-lg';
            } else {
                heartIcon.className = 'fa-regular fa-heart text-lg';
            }
        })
        .catch(err => console.error(err));
    }

    function openProfileModal(profileId) {
        const modal = document.getElementById('profile-modal');
        const modalBody = document.getElementById('profile-modal-body');
        
        modalBody.innerHTML = `
            <div class="flex items-center justify-center py-12">
                <i class="fa-solid fa-circle-notch fa-spin text-3xl text-primary"></i>
            </div>
        `;
        modal.classList.remove('hidden');

        fetch(`/profiles/${profileId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.html) {
                modalBody.innerHTML = data.html;
            } else {
                modalBody.innerHTML = `<p class="text-center text-red-500 font-bold p-6">Failed to retrieve profile.</p>`;
            }
        })
        .catch(err => {
            console.error(err);
            modalBody.innerHTML = `<p class="text-center text-red-500 font-bold p-6">Connection error.</p>`;
        });
    }

    function closeProfileModal() {
        document.getElementById('profile-modal').classList.add('hidden');
    }

    function downloadModalPDF() {
        const element = document.querySelector('#profile-modal-body #pdf-content');
        if (!element) {
            alert('Biodata is not loaded yet.');
            return;
        }
        element.style.display = 'block';
        const opt = {
          margin: 8,
          filename: 'Profile_Biodata.pdf',
          image: { type: 'jpeg', quality: 0.98 },
          html2canvas: { scale: 2, useCORS: true, logging: false, allowTaint: true },
          jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save().then(function() {
            element.style.display = 'none';
        });
    }

    // Escape key modal closer
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeProfileModal();
        }
    });
</script>
@endsection
