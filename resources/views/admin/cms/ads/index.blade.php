@extends('layouts.admin')

@section('title', 'Advertisement Management System - Admin Panel')
@section('header_title', 'Advertisement Management System')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Left Column: Active & Inactive Advertisements List -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-slate-50/50 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-800 text-lg">Active & Inactive Campaigns</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Manage advertisements displayed on Left Sidebar, Right Sidebar, and Bottom Section.</p>
                </div>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse min-w-[650px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-100 text-gray-400 text-xs uppercase font-bold tracking-wider">
                            <th class="py-3.5 px-4">Media Preview</th>
                            <th class="py-3.5 px-4">Title / Link</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Position</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Loop Duration</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap">Order</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap">Status</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        @forelse($ads as $ad)
                        <tr class="hover:bg-slate-50/80 transition duration-150">
                            <td class="py-3.5 px-4 w-32">
                                @if($ad->image)
                                    <div class="w-28 h-16 bg-slate-900 rounded-lg border border-gray-200 overflow-hidden relative shadow-xs cursor-pointer group" onclick='previewMedia("{{ asset($ad->image) }}", "{{ $ad->media_type ?? "image" }}", "{{ e($ad->title) }}")'>
                                        @if(isset($ad->media_type) && $ad->media_type === 'video')
                                            <video src="{{ asset($ad->image) }}" muted class="w-full h-full object-cover"></video>
                                            <span class="absolute bottom-1 right-1 bg-indigo-600 text-white text-[9px] font-extrabold px-1.5 py-0.5 rounded flex items-center gap-1 shadow">
                                                <i class="fa-solid fa-video"></i> Video
                                            </span>
                                        @else
                                            <img src="{{ asset($ad->image) }}" alt="Ad Banner" class="w-full h-full object-cover">
                                            <span class="absolute bottom-1 right-1 bg-slate-900/80 text-white text-[9px] font-extrabold px-1.5 py-0.5 rounded flex items-center gap-1">
                                                <i class="fa-solid fa-image"></i> Image
                                            </span>
                                        @endif
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                                            <i class="fa-solid fa-eye text-white text-sm"></i>
                                        </div>
                                    </div>
                                @else
                                    <div class="w-28 h-16 bg-slate-100 rounded-lg border flex items-center justify-center text-slate-400 text-xs font-bold">No Media</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 max-w-[180px]">
                                <div class="font-bold text-slate-800 leading-snug truncate" title="{{ $ad->title }}">{{ $ad->title }}</div>
                                @if($ad->link)
                                    <a href="{{ $ad->link }}" target="_blank" class="text-xs text-indigo-600 hover:underline block mt-0.5 truncate font-medium" title="{{ $ad->link }}">
                                        <i class="fa-solid fa-link text-[10px]"></i> {{ $ad->link }}
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 block mt-0.5">No click link</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @php
                                    $posLabel = match($ad->position) {
                                        'left_sidebar', 'left' => 'Left Sidebar',
                                        'right_sidebar', 'right' => 'Right Sidebar',
                                        'bottom_banner', 'bottom', 'home_bottom' => 'Bottom Section',
                                        default => ucfirst(str_replace('_', ' ', $ad->position))
                                    };
                                    $posBadge = match($ad->position) {
                                        'left_sidebar', 'left' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'right_sidebar', 'right' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'bottom_banner', 'bottom', 'home_bottom' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        default => 'bg-slate-50 text-slate-700 border-slate-200'
                                    };
                                @endphp
                                <span class="font-bold text-[11px] uppercase border px-2.5 py-1 rounded-full whitespace-nowrap inline-block {{ $posBadge }}">
                                    {{ $posLabel }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-gray-600 font-semibold text-xs whitespace-nowrap">
                                <span class="bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200 text-slate-700 whitespace-nowrap inline-flex items-center gap-1.5">
                                    <i class="fa-regular fa-clock text-slate-400"></i> {{ $ad->duration_seconds ?? 3 }} sec
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center text-gray-600 font-mono text-xs whitespace-nowrap">
                                {{ $ad->sort_order ?? 0 }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <form action="{{ route('admin.cms.ads.toggle', $ad->id) }}" method="POST" class="m-0 p-0 inline-block">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 rounded-full text-xs font-bold transition inline-flex items-center gap-1.5 whitespace-nowrap cursor-pointer
                                        @if($ad->status) bg-emerald-100 text-emerald-800 hover:bg-emerald-200
                                        @else bg-slate-100 text-slate-600 hover:bg-slate-200 @endif">
                                        <span class="w-2 h-2 rounded-full {{ $ad->status ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        {{ $ad->status ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick='openEditAdModal({!! json_encode($ad) !!})' class="p-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg transition cursor-pointer" title="Edit Campaign">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </button>
                                    <form action="{{ route('admin.cms.ads.destroy', $ad->id) }}" method="POST" class="m-0 p-0 inline-block" onsubmit="return confirm('Are you sure you want to delete this advertisement campaign?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition cursor-pointer" title="Delete Campaign">
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-12 px-6 text-center text-gray-400 font-medium">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fa-solid fa-rectangle-ad text-4xl text-slate-300"></i>
                                    <span class="text-gray-600 text-base">No advertisement campaigns found!</span>
                                    <span class="text-xs text-gray-400">Add an image or video ad banner using the form on the right.</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Create Ad Form -->
    <div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-6">
            <h3 class="font-bold text-gray-800 text-lg border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-indigo-600"></i> Add New Advertisement
            </h3>
            
            <form action="{{ route('admin.cms.ads.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-sm">
                @csrf
                <div>
                    <label for="title" class="block font-semibold text-gray-700 mb-1">Campaign Title *</label>
                    <input type="text" name="title" id="title" required placeholder="e.g. Sammelan Banner 2026"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="position" class="block font-semibold text-gray-700 mb-1">Placement Section *</label>
                    <select name="position" id="position" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                        <option value="left_sidebar">Left Sidebar</option>
                        <option value="right_sidebar">Right Sidebar</option>
                        <option value="bottom_banner">Bottom Section</option>
                    </select>
                </div>

                <div>
                    <label for="link" class="block font-semibold text-gray-700 mb-1">Click Link URL (Optional)</label>
                    <input type="url" name="link" id="link" placeholder="https://..."
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="duration_seconds" class="block font-semibold text-gray-700 mb-1">Loop Duration *</label>
                        <select name="duration_seconds" id="duration_seconds" required class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                            <option value="2">2 Seconds</option>
                            <option value="3" selected>3 Seconds (Default)</option>
                            <option value="4">4 Seconds</option>
                            <option value="5">5 Seconds</option>
                            <option value="10">10 Seconds</option>
                        </select>
                    </div>

                    <div>
                        <label for="sort_order" class="block font-semibold text-gray-700 mb-1">Sort Priority</label>
                        <input type="number" name="sort_order" id="sort_order" value="0" min="0" placeholder="0"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label for="image" class="block font-semibold text-gray-700 mb-1">Media File (Image or Video) *</label>
                    <input type="file" name="image" id="image" required accept="image/*,video/*"
                           class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-[10px] text-gray-400 mt-1">Supports JPG, PNG, WEBP, GIF, MP4, WebM up to 20MB.</p>
                </div>

                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition duration-150 shadow flex items-center justify-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Publish Advertisement
                </button>
            </form>
        </div>
    </div>

</div>

<!-- Media Preview Modal -->
<div id="mediaPreviewModal" class="fixed inset-0 bg-black/75 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col">
        <div class="flex justify-between items-center p-4 border-b border-gray-100 bg-slate-50">
            <h3 id="previewTitle" class="text-base font-bold text-gray-800">Media Preview</h3>
            <button class="text-gray-400 hover:text-gray-600 transition p-1" onclick="closePreviewModal()">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <div class="p-4 flex items-center justify-center bg-slate-900 min-h-[300px]" id="previewContainer">
            <!-- Dynamic Image or Video inserted by JS -->
        </div>
    </div>
</div>

<!-- Edit Ad Modal -->
<div id="editAdModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col">
        <div class="flex justify-between items-center p-6 border-b border-gray-100 bg-slate-50/50">
            <h3 class="text-lg font-bold text-gray-800">Edit Advertisement Campaign</h3>
            <button class="text-gray-400 hover:text-gray-600 transition" onclick="closeEditAdModal()">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        
        <div class="p-6">
            <form id="editAdForm" method="POST" action="" enctype="multipart/form-data" class="space-y-4 text-sm">
                @csrf
                @method('PUT')
                <div>
                    <label for="edit_title" class="block font-semibold text-gray-700 mb-1">Campaign Title *</label>
                    <input type="text" name="title" id="edit_title" required class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="edit_position" class="block font-semibold text-gray-700 mb-1">Placement Section *</label>
                    <select name="position" id="edit_position" required class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                        <option value="left_sidebar">Left Sidebar</option>
                        <option value="right_sidebar">Right Sidebar</option>
                        <option value="bottom_banner">Bottom Section</option>
                    </select>
                </div>

                <div>
                    <label for="edit_link" class="block font-semibold text-gray-700 mb-1">Click Link URL (Optional)</label>
                    <input type="url" name="link" id="edit_link" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="edit_duration_seconds" class="block font-semibold text-gray-700 mb-1">Loop Duration *</label>
                        <select name="duration_seconds" id="edit_duration_seconds" required class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                            <option value="2">2 Seconds</option>
                            <option value="3">3 Seconds (Default)</option>
                            <option value="4">4 Seconds</option>
                            <option value="5">5 Seconds</option>
                            <option value="10">10 Seconds</option>
                        </select>
                    </div>

                    <div>
                        <label for="edit_sort_order" class="block font-semibold text-gray-700 mb-1">Sort Priority</label>
                        <input type="number" name="sort_order" id="edit_sort_order" min="0" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label for="edit_image" class="block font-semibold text-gray-700 mb-1">Replace Media File (Optional)</label>
                    <input type="file" name="image" id="edit_image" accept="image/*,video/*"
                           class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>

                <div class="flex items-center pt-2">
                    <input type="checkbox" name="status" id="edit_status" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4 mr-2">
                    <label for="edit_status" class="font-semibold text-gray-700 select-none cursor-pointer">Advertisement is Active</label>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
                    <button type="button" class="px-5 py-2 text-sm font-bold text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-slate-50 transition" onclick="closeEditAdModal()">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-sm font-bold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const adModal = document.getElementById('editAdModal');
const adForm = document.getElementById('editAdForm');
const previewModal = document.getElementById('mediaPreviewModal');
const previewContainer = document.getElementById('previewContainer');
const previewTitle = document.getElementById('previewTitle');

function openEditAdModal(ad) {
    adForm.action = `/admin/cms/ads/${ad.id}`;
    
    document.getElementById('edit_title').value = ad.title;
    document.getElementById('edit_link').value = ad.link || '';
    document.getElementById('edit_position').value = ad.position;
    document.getElementById('edit_duration_seconds').value = ad.duration_seconds || 3;
    document.getElementById('edit_sort_order').value = ad.sort_order || 0;
    document.getElementById('edit_status').checked = !!ad.status;
    
    adModal.classList.remove('hidden');
}

function closeEditAdModal() {
    adModal.classList.add('hidden');
}

function previewMedia(src, type, title) {
    previewTitle.innerText = title;
    if (type === 'video') {
        previewContainer.innerHTML = `<video src="${src}" controls autoplay class="max-h-[450px] w-auto rounded shadow-lg"></video>`;
    } else {
        previewContainer.innerHTML = `<img src="${src}" alt="Preview" class="max-h-[450px] w-auto rounded shadow-lg object-contain">`;
    }
    previewModal.classList.remove('hidden');
}

function closePreviewModal() {
    previewModal.classList.add('hidden');
    previewContainer.innerHTML = '';
}
</script>
@endsection
