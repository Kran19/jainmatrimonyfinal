@extends('layouts.app')

@section('title', 'Share Your Success Story - Jain Digambar Matrimony')

@section('content')
<section class="py-16 bg-light min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-4 max-w-2xl">
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100" data-aos="fade-up">
            <h2 class="text-3xl font-black text-center text-dark mb-2">Share Your Success Story</h2>
            <p class="text-center text-gray-500 mb-8 text-sm">We would love to hear how you found your life partner through our platform.</p>
            
            <form action="{{ route('success-stories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Couple Name (e.g., Rahul & Priya) <span class="text-red-500">*</span></label>
                    <input type="text" name="couple_name" required value="{{ old('couple_name') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-primary focus:border-primary focus:outline-none bg-gray-50 text-sm"
                           placeholder="Rahul & Priya">
                    @error('couple_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">City <span class="text-red-500">*</span></label>
                    <input type="text" name="city" required value="{{ old('city') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-primary focus:border-primary focus:outline-none bg-gray-50 text-sm"
                           placeholder="e.g. Indore, Madhya Pradesh">
                    @error('city') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Your Story <span class="text-red-500">*</span></label>
                    <textarea name="story" required rows="6"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-primary focus:border-primary focus:outline-none bg-gray-50 text-sm"
                              placeholder="Share your journey with us...">{{ old('story') }}</textarea>
                    @error('story') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Couple Photo <span class="text-red-500">*</span></label>
                    <input type="file" name="photo" accept="image/*" required
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-primary hover:file:bg-blue-100">
                    <p class="text-xs text-gray-400 mt-1">Maximum file size: 10MB. Allowed formats: JPG, JPEG, PNG, GIF</p>
                    @error('photo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-between border-t pt-6">
                    <a href="{{ route('stories') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 flex items-center gap-1">
                        <i class="fas fa-arrow-left text-xs"></i> Cancel
                    </a>
                    <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-lg hover:bg-opacity-90 transition font-bold text-sm shadow-md">
                        Submit Story
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
