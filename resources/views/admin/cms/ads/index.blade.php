@extends('layouts.admin')

@section('title', 'Manage Advertisements - Admin Panel')
@section('header_title', 'Hero Advertisements')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Left Column: Ads List -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-slate-50/50">
                <h3 class="font-bold text-gray-800 text-lg">Active & Inactive Campaigns</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-100 text-gray-400 text-xs uppercase font-bold">
                            <th class="py-3 px-6">Banner/Video</th>
                            <th class="py-3 px-6">Title / Link</th>
                            <th class="py-3 px-6">Position</th>
                            <th class="py-3 px-6">Status</th>
                            <th class="py-3 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        @forelse($ads as $ad)
                        <tr class="hover:bg-slate-50 transition duration-150">
                            <td class="py-4 px-6 w-36">
                                @if($ad->image)
                                    <div class="w-28 h-16 bg-slate-100 rounded border overflow-hidden relative shadow-sm">
                                        @if(isset($ad->media_type) && $ad->media_type === 'video')
                                            <video src="/{{ $ad->image }}" muted class="w-full h-full object-cover"></video>
                                            <span class="absolute bottom-1 right-1 bg-slate-900/80 text-white text-[9px] font-extrabold px-1 rounded flex items-center gap-0.5">
                                                <i class="fa-solid fa-video"></i> Video
                                            </span>
                                        @else
                                            <img src="/{{ $ad->image }}" alt="Ad Banner" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                @else
                                    <div class="w-28 h-16 bg-slate-100 rounded border flex items-center justify-center text-slate-400 text-xs font-bold">No Media</div>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-gray-900 leading-tight">{{ $ad->title }}</div>
                                @if($ad->link)
                                    <a href="{{ $ad->link }}" target="_blank" class="text-xs text-indigo-600 hover:underline block mt-1 truncate max-w-xs font-medium">
                                        <i class="fa-solid fa-link text-[10px]"></i> {{ $ad->link }}
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 block mt-1">No click link</span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-bold text-xs uppercase text-indigo-700 bg-indigo-50 border border-indigo-100 px-2.5 py-1 rounded-full">
                                    {{ str_replace('_', ' ', $ad->position) }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                    @if($ad->status) bg-green-100 text-green-800
                                    @else bg-slate-100 text-slate-800 @endif">
                                    {{ $ad->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick='openEditAdModal({!! json_encode($ad) !!})' class="p-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg transition" title="Edit Campaign">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <form action="{{ route('admin.cms.ads.toggle', $ad->id) }}" method="POST" class="m-0 p-0">
                                        @csrf
                                        <button type="submit" class="p-2 bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-lg transition" title="Toggle Status">
                                            <i class="fa-solid fa-arrows-rotate"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.cms.ads.destroy', $ad->id) }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Delete this advertisement campaign?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition" title="Delete Campaign">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 px-6 text-center text-gray-400 font-medium">
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
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 text-lg border-b border-gray-100 pb-3 mb-4">
                Register Advertisement
            </h3>
            
            <form action="{{ route('admin.cms.ads.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-sm">
                @csrf
                <div>
                    <label for="title" class="block font-semibold text-gray-700 mb-1">Campaign Title *</label>
                    <input type="text" name="title" id="title" required placeholder="e.g. Sammelan Banner 2026"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="link" class="block font-semibold text-gray-700 mb-1">Click Link URL (Optional)</label>
                    <input type="url" name="link" id="link" placeholder="https://..."
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="position" class="block font-semibold text-gray-700 mb-1">Placement Position *</label>
                    <select name="position" id="position" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                        <option value="home_top">Home Top Banner</option>
                        <option value="home_bottom">Home Bottom Banner</option>
                        <option value="sidebar">Sidebar Banner</option>
                        <option value="left_sidebar">Left Sidebar</option>
                        <option value="right_sidebar">Right Sidebar</option>
                        <option value="bottom_banner">Bottom Footer Banner</option>
                    </select>
                </div>

                <div>
                    <label for="image" class="block font-semibold text-gray-700 mb-1">Ad Graphic / Video *</label>
                    <input type="file" name="image" id="image" required accept="image/*,video/*"
                           class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-[10px] text-gray-400 mt-1">Supports JPG, PNG, WEBP, GIF, MP4, and WebM up to 20MB.</p>
                </div>

                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition duration-150 shadow">
                    Publish Ad Campaign
                </button>
            </form>
        </div>
    </div>

</div>

<!-- Edit Ad Modal -->
<div id="editAdModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col">
        <div class="flex justify-between items-center p-6 border-b border-gray-100">
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
                    <label for="edit_link" class="block font-semibold text-gray-700 mb-1">Click Link URL (Optional)</label>
                    <input type="url" name="link" id="edit_link" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="edit_position" class="block font-semibold text-gray-700 mb-1">Placement Position *</label>
                    <select name="position" id="edit_position" required class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                        <option value="home_top">Home Top Banner</option>
                        <option value="home_bottom">Home Bottom Banner</option>
                        <option value="sidebar">Sidebar Banner</option>
                        <option value="left_sidebar">Left Sidebar</option>
                        <option value="right_sidebar">Right Sidebar</option>
                        <option value="bottom_banner">Bottom Footer Banner</option>
                    </select>
                </div>

                <div>
                    <label for="edit_image" class="block font-semibold text-gray-700 mb-1">Replace Graphic/Video (Optional)</label>
                    <input type="file" name="image" id="edit_image" accept="image/*,video/*"
                           class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="status" id="edit_status" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4 mr-2">
                    <label for="edit_status" class="font-semibold text-gray-700 select-none cursor-pointer">Ad is Active</label>
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

function openEditAdModal(ad) {
    adForm.action = `/admin/cms/ads/${ad.id}`;
    
    document.getElementById('edit_title').value = ad.title;
    document.getElementById('edit_link').value = ad.link || '';
    document.getElementById('edit_position').value = ad.position;
    document.getElementById('edit_status').checked = !!ad.status;
    
    adModal.classList.remove('hidden');
}

function closeEditAdModal() {
    adModal.classList.add('hidden');
}
</script>
@endsection
