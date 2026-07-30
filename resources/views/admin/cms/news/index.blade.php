@extends('layouts.admin')

@section('title', 'Manage Announcements - Admin Panel')
@section('header_title', 'News & Announcements')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Left Column: Announcements & Marquees lists -->
    <div class="lg:col-span-2 space-y-8">
        
        <!-- News & Announcements -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-slate-50/50">
                <h3 class="font-bold text-gray-800 text-lg">General Announcements</h3>
            </div>
            
            <div class="divide-y divide-gray-100">
                @forelse($newsItems as $news)
                <div class="p-6 flex items-start gap-4 hover:bg-slate-50 transition duration-150">
                    @if($news->image)
                        <img src="{{ $news->image }}" alt="Cover" class="w-16 h-16 object-cover rounded-lg border flex-shrink-0">
                    @else
                        <div class="w-16 h-16 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400 flex-shrink-0">
                            <i class="fa-solid fa-bullhorn text-xl"></i>
                        </div>
                    @endif
                    <div class="flex-grow">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-gray-900">{{ $news->title }}</h4>
                            <span class="text-xs text-gray-400">{{ $news->created_at->format('M d, Y') }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1 whitespace-pre-wrap">{{ Str::limit($news->content, 200) }}</p>
                        
                        <div class="mt-3 flex items-center gap-3">
                            <button onclick='openEditNewsModal({!! json_encode($news) !!})' class="text-xs font-bold text-indigo-600 hover:text-indigo-700 transition">
                                Edit Details
                            </button>
                            <span class="text-gray-300">|</span>
                            <form action="{{ route('admin.cms.news.toggle', $news->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-2.5 py-1 rounded text-xs font-bold transition
                                    {{ $news->status ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                    {{ $news->status ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                            <span class="text-gray-300">|</span>
                            <form action="{{ route('admin.cms.news.destroy', $news->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this announcement?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-red-600 hover:text-red-500">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-gray-500 text-sm">No announcements posted.</div>
                @endforelse
            </div>
            
            @if($newsItems->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-slate-50">
                {{ $newsItems->links() }}
            </div>
            @endif
        </div>

        <!-- Marquee Tickers -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-slate-50/50">
                <h3 class="font-bold text-gray-800 text-lg">Marquee Notice Tickers</h3>
            </div>
            
            <div class="divide-y divide-gray-100">
                @forelse($marqueeItems as $marquee)
                <div class="p-6 flex items-center justify-between hover:bg-slate-50 transition duration-150">
                    <div class="flex-grow pr-4">
                        <p class="text-sm font-semibold text-gray-800">"{{ $marquee->notice_text }}"</p>
                        <span class="text-xs text-gray-400 block mt-1">Added: {{ $marquee->created_at->format('M d, H:i') }}</span>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        <button onclick='openEditMarqueeModal({!! json_encode($marquee) !!})' class="text-slate-400 hover:text-indigo-600 transition p-1" title="Edit Ticker">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <form action="{{ route('admin.cms.marquee.toggle', $marquee->id) }}" method="POST" class="m-0 p-0">
                            @csrf
                            <button type="submit" class="px-2.5 py-1 rounded text-xs font-bold transition
                                {{ $marquee->status ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                {{ $marquee->status ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                        <form action="{{ route('admin.cms.marquee.destroy', $marquee->id) }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Delete this marquee ticker?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-red-600 transition p-1">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-gray-500 text-sm">No marquee tickers added.</div>
                @endforelse
            </div>

            @if($marqueeItems->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-slate-50">
                {{ $marqueeItems->links() }}
            </div>
            @endif
        </div>

    </div>

    <!-- Right Column: Create forms -->
    <div class="space-y-6">
        <!-- Post Announcement Form -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 text-lg border-b border-gray-100 pb-3 mb-4">
                Post Announcement
            </h3>
            <form action="{{ route('admin.cms.news.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-sm">
                @csrf
                <div>
                    <label for="title" class="block font-semibold text-gray-700 mb-1">Title *</label>
                    <input type="text" name="title" id="title" required placeholder="Announcement Title"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="content" class="block font-semibold text-gray-700 mb-1">Content *</label>
                    <textarea name="content" id="content" rows="4" required placeholder="Type announcement details here..."
                              class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <div>
                    <label for="image" class="block font-semibold text-gray-700 mb-1">Cover Graphic (Optional)</label>
                    <input type="file" name="image" id="image" accept="image/*"
                           class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition shadow">
                    Publish Announcement
                </button>
            </form>
        </div>

        <!-- Add Marquee Ticker Form -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 text-lg border-b border-gray-100 pb-3 mb-4">
                Add Marquee Notice
            </h3>
            <form action="{{ route('admin.cms.marquee.store') }}" method="POST" class="space-y-4 text-sm">
                @csrf
                <div>
                    <label for="notice_text" class="block font-semibold text-gray-700 mb-1">Notice Ticker Text *</label>
                    <textarea name="notice_text" id="notice_text" rows="3" required placeholder="e.g. Registrations for Digambar Samaj Sammelan are now open!"
                              class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition shadow">
                    Save Marquee Ticker
                </button>
            </form>
        </div>
    </div>

</div>

<!-- Edit News Modal -->
<div id="editNewsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col">
        <div class="flex justify-between items-center p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Edit Announcement Details</h3>
            <button class="text-gray-400 hover:text-gray-600 transition" onclick="closeEditNewsModal()">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        
        <div class="p-6">
            <form id="editNewsForm" method="POST" action="" enctype="multipart/form-data" class="space-y-4 text-sm">
                @csrf
                @method('PUT')
                <div>
                    <label for="edit_title" class="block font-semibold text-gray-700 mb-1">Title *</label>
                    <input type="text" name="title" id="edit_title" required class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="edit_content" class="block font-semibold text-gray-700 mb-1">Content *</label>
                    <textarea name="content" id="edit_content" rows="6" required class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <div>
                    <label for="edit_image" class="block font-semibold text-gray-700 mb-1">Replace Cover Graphic (Optional)</label>
                    <input type="file" name="image" id="edit_image" accept="image/*"
                           class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="status" id="edit_news_status" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4 mr-2">
                    <label for="edit_news_status" class="font-semibold text-gray-700 select-none cursor-pointer">Announcement is Active</label>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
                    <button type="button" class="px-5 py-2 text-sm font-bold text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-slate-50 transition" onclick="closeEditNewsModal()">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-sm font-bold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Marquee Modal -->
<div id="editMarqueeModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col">
        <div class="flex justify-between items-center p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Edit Marquee Notice</h3>
            <button class="text-gray-400 hover:text-gray-600 transition" onclick="closeEditMarqueeModal()">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        
        <div class="p-6">
            <form id="editMarqueeForm" method="POST" action="" class="space-y-4 text-sm">
                @csrf
                @method('PUT')
                <div>
                    <label for="edit_notice_text" class="block font-semibold text-gray-700 mb-1">Notice Ticker Text *</label>
                    <textarea name="notice_text" id="edit_notice_text" rows="3" required class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="status" id="edit_marquee_status" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4 mr-2">
                    <label for="edit_marquee_status" class="font-semibold text-gray-700 select-none cursor-pointer">Ticker is Active</label>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
                    <button type="button" class="px-5 py-2 text-sm font-bold text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-slate-50 transition" onclick="closeEditMarqueeModal()">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-sm font-bold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// News Modals
const newsModal = document.getElementById('editNewsModal');
const newsForm = document.getElementById('editNewsForm');

function openEditNewsModal(news) {
    newsForm.action = `/admin/cms/news/${news.id}`;
    
    document.getElementById('edit_title').value = news.title;
    document.getElementById('edit_content').value = news.content;
    document.getElementById('edit_news_status').checked = !!news.status;
    
    newsModal.classList.remove('hidden');
}

function closeEditNewsModal() {
    newsModal.classList.add('hidden');
}

// Marquee Modals
const marqueeModal = document.getElementById('editMarqueeModal');
const marqueeForm = document.getElementById('editMarqueeForm');

function openEditMarqueeModal(marquee) {
    marqueeForm.action = `/admin/cms/marquee/${marquee.id}`;
    
    document.getElementById('edit_notice_text').value = marquee.notice_text;
    document.getElementById('edit_marquee_status').checked = !!marquee.status;
    
    marqueeModal.classList.remove('hidden');
}

function closeEditMarqueeModal() {
    marqueeModal.classList.add('hidden');
}
</script>
@endsection
