@extends('layouts.admin')

@section('title', 'Media Gallery - Admin Panel')
@section('header_title', 'Content Management: Photos & Videos')

@section('content')

@if(session('success'))
    <div id="flash-message" class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center justify-between transition-opacity duration-500">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="document.getElementById('flash-message')?.remove()" class="text-emerald-600 hover:text-emerald-800 p-1">
            <i class="fa-solid fa-xmark text-base"></i>
        </button>
    </div>
@endif

@if(session('error'))
    <div id="flash-message" class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold flex items-center justify-between transition-opacity duration-500">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button onclick="document.getElementById('flash-message')?.remove()" class="text-rose-600 hover:text-rose-800 p-1">
            <i class="fa-solid fa-xmark text-base"></i>
        </button>
    </div>
@endif

@if($errors->any())
    <div id="flash-message" class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm space-y-1 transition-opacity duration-500 relative">
        <div class="font-bold flex items-center justify-between text-rose-900 mb-1">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                <span>Please fix the following issues:</span>
            </div>
            <button onclick="document.getElementById('flash-message')?.remove()" class="text-rose-600 hover:text-rose-800 p-1">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>
        <ul class="list-disc list-inside pl-2 text-rose-700 font-medium">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Left Column: Media listings -->
    <div class="lg:col-span-2 space-y-8">
        
        <!-- Photo Gallery -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="font-bold text-gray-800 text-lg">Photo Gallery</h3>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                @forelse($galleryItems as $photo)
                @php
                    $imgSrc = (str_starts_with($photo->image_path, 'data:image/') || preg_match('/^https?:\/\//i', $photo->image_path))
                        ? $photo->image_path
                        : route('image.serve', ['file' => $photo->image_path]);
                @endphp
                <div class="border rounded-xl overflow-hidden hover:shadow-md transition duration-150 relative bg-slate-50">
                    <img src="{{ $imgSrc }}" alt="Photo" class="w-full h-32 object-cover">
                    <div class="p-3 text-xs">
                        <div class="font-bold truncate text-gray-900">{{ $photo->title }}</div>
                        <div class="text-slate-400 mt-0.5">{{ $photo->category }}</div>
                        
                        <div class="mt-2 flex items-center justify-between">
                            <form action="{{ route('admin.cms.gallery.photo.toggle', $photo->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-2 py-0.5 rounded text-[10px] font-bold transition
                                    {{ $photo->status ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                    {{ $photo->status ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.cms.gallery.photo.destroy', $photo->id) }}" method="POST" onsubmit="return confirm('Delete this image?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 transition">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-3 text-center text-gray-500 text-sm py-4">No photos in gallery.</div>
                @endforelse
            </div>
            
            @if($galleryItems->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-slate-50">
                {{ $galleryItems->links() }}
            </div>
            @endif
        </div>

        <!-- Video Links -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="font-bold text-gray-800 text-lg">Video Gallery Links</h3>
            </div>
            
            <div class="divide-y divide-gray-100">
                @forelse($videoItems as $video)
                <div class="p-6 flex items-center justify-between hover:bg-slate-50 transition duration-150">
                    <div>
                        <h4 class="font-bold text-gray-900">{{ $video->title }}</h4>
                        <p class="text-xs text-indigo-600 mt-0.5 font-semibold">
                            Type: {{ strtoupper($video->video_type) }} | URL: 
                            <a href="{{ $video->video_type === 'youtube' ? $video->video_url : $video->video_file }}" target="_blank" class="hover:underline">
                                {{ Str::limit($video->video_type === 'youtube' ? $video->video_url : $video->video_file, 50) }}
                            </a>
                        </p>
                        @if($video->description)
                            <p class="text-xs text-gray-500 mt-1">{{ $video->description }}</p>
                        @endif
                    </div>
                    
                    <div class="flex items-center gap-3 flex-shrink-0">
                        <form action="{{ route('admin.cms.gallery.video.toggle', $video->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-2.5 py-1 rounded text-xs font-bold transition
                                {{ $video->status === 'active' ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                {{ $video->status === 'active' ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                        <form action="{{ route('admin.cms.gallery.video.destroy', $video->id) }}" method="POST" onsubmit="return confirm('Delete this video?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-red-600 transition p-1">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-gray-500 text-sm">No videos linked.</div>
                @endforelse
            </div>

            @if($videoItems->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-slate-50">
                {{ $videoItems->links() }}
            </div>
            @endif
        </div>

    </div>

    <!-- Right Column: Forms -->
    <div class="space-y-6">
        <!-- Gallery Header Banner Configuration -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 text-lg border-b border-gray-100 pb-3 mb-4 flex items-center justify-between">
                <span>Gallery Banner Image</span>
                <i class="fa-solid fa-image text-indigo-500"></i>
            </h3>
            
            @if(!empty($galleryBanner))
                <div class="mb-4">
                    <span class="block text-xs font-semibold text-gray-500 mb-1">Current Active Banner:</span>
                    @php
                        $bannerPreview = (str_starts_with($galleryBanner, 'data:image/') || preg_match('/^https?:\/\//i', $galleryBanner))
                            ? $galleryBanner
                            : route('image.serve', ['file' => $galleryBanner]);
                    @endphp
                    <div class="h-28 w-full rounded-lg overflow-hidden border bg-slate-100 relative">
                        <img src="{{ $bannerPreview }}" alt="Current Banner" class="w-full h-full object-cover">
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.cms.gallery.banner.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-sm">
                @csrf
                <div>
                    <label for="banner_image" class="block font-semibold text-gray-700 mb-1">Upload New Header Banner (Max 10MB)</label>
                    <input type="file" name="banner_image" id="banner_image" required accept="image/*"
                           class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition shadow-sm">
                    <i class="fa-solid fa-cloud-arrow-up mr-2"></i>Save Banner Image
                </button>
            </form>
        </div>

        <!-- Add Photo -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 text-lg border-b border-gray-100 pb-3 mb-4">
                Add Photo
            </h3>
            <form action="{{ route('admin.cms.gallery.photo.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-sm">
                @csrf
                <div>
                    <label for="photo_title" class="block font-semibold text-gray-700 mb-1">Photo Title</label>
                    <input type="text" name="title" id="photo_title" required placeholder="Image Caption"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="category" class="block font-semibold text-gray-700 mb-1">Category</label>
                    <select name="category" id="category" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="Sammelan">Sammelan</option>
                        <option value="Committee">Committee</option>
                        <option value="Social Events">Social Events</option>
                        <option value="All Photos">Other Photos</option>
                    </select>
                </div>
                <div>
                    <label for="image" class="block font-semibold text-gray-700 mb-1">Select Image File (Max 10MB)</label>
                    <input type="file" name="image" id="image" required accept="image/*"
                           class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition">
                    Upload to Gallery
                </button>
            </form>
        </div>

        <!-- Add Video Link -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 text-lg border-b border-gray-100 pb-3 mb-4">
                Add Video Link
            </h3>
            <form action="{{ route('admin.cms.gallery.video.store') }}" method="POST" class="space-y-4 text-sm">
                @csrf
                <div>
                    <label for="video_title" class="block font-semibold text-gray-700 mb-1">Video Title</label>
                    <input type="text" name="title" id="video_title" required placeholder="Event Name / Intro Title"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="video_type" class="block font-semibold text-gray-700 mb-1">Video Source</label>
                    <select name="video_type" id="video_type" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="youtube">YouTube Embed Link</option>
                        <option value="mp4">Direct MP4 URL</option>
                    </select>
                </div>
                <div>
                    <label for="video_url" class="block font-semibold text-gray-700 mb-1">YouTube URL</label>
                    <input type="text" name="video_url" id="video_url" placeholder="https://www.youtube.com/watch?v=..."
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="video_file" class="block font-semibold text-gray-700 mb-1">MP4 File Link (If MP4 Selected)</label>
                    <input type="text" name="video_file" id="video_file" placeholder="https://myhost.com/video.mp4"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="display_order" class="block font-semibold text-gray-700 mb-1">Display Order</label>
                    <input type="number" name="display_order" id="display_order" value="0"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition">
                    Save Video Link
                </button>
            </form>
        </div>
    </div>

</div>

{{-- Auto-remove flash message after 5 seconds (5000ms) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var flashMessages = document.querySelectorAll('#flash-message');
        flashMessages.forEach(function(flashMsg) {
            if (flashMsg) {
                setTimeout(function() {
                    flashMsg.style.opacity = '0';
                    setTimeout(function() {
                        flashMsg.remove();
                    }, 500);
                }, 5000); // 5 seconds timer
            }
        });
    });
</script>
@endsection
