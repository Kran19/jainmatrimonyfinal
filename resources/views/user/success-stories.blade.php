@extends('layouts.app')

@section('title', 'Success Stories - Jain Digambar Matrimony')

@section('content')
<section class="py-16 bg-white min-h-screen">
    <div class="container mx-auto px-4">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-12 max-w-5xl mx-auto" data-aos="fade-up">
            <div class="text-center md:text-left mb-6 md:mb-0">
                <h1 class="text-4xl md:text-5xl font-black text-dark">Success Stories</h1>
                <p class="text-gray-500 mt-2 text-sm">Real stories of couples who found their soulmates on our platform.</p>
            </div>
            
            @auth('web')
                @if(Auth::user()->status === 'approved')
                    <a href="{{ route('success-stories.add') }}" class="bg-primary text-white px-6 py-3 rounded-lg font-bold shadow-md hover:bg-opacity-90 transition flex items-center gap-2">
                        <i class="fas fa-plus"></i> Share Your Success Story
                    </a>
                @endif
            @endauth
        </div>

        @if(session('success'))
            <div class="max-w-5xl mx-auto bg-green-50 border-l-4 border-green-500 p-4 mb-8">
                <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        @endif
        
        <!-- Stories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            @forelse($stories as $story)
                @php
                    $photo = '';
                    if (!empty($story->photo)) {
                        if (str_starts_with($story->photo, 'data:image/') || preg_match('/^https?:\/\//i', $story->photo)) {
                            $photo = $story->photo;
                        } else {
                            $photo = route('image.serve', ['file' => $story->photo]);
                        }
                    } else {
                        $photo = asset('assets/images/placeholder-couple.png');
                    }
                @endphp
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 group border border-gray-100 flex flex-col" data-aos="fade-up">
                    <div class="relative overflow-hidden aspect-[3/4] h-72 bg-gray-50 flex-shrink-0">
                        <img src="{{ $photo }}" alt="Couple" class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-500">
                        <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black via-black/60 to-transparent p-4 z-20">
                            <h3 class="text-white font-bold text-lg leading-tight">{{ $story->couple_name }}</h3>
                            <p class="text-gray-200 text-xs font-semibold mt-0.5"><i class="fas fa-map-marker-alt mr-1"></i> {{ $story->city }}</p>
                        </div>
                        @if ($story->created_at && $story->created_at->gt(now()->subDays(7)))
                            <div class="absolute top-3 right-3 bg-green-500 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-sm z-20 uppercase tracking-wider">New</div>
                        @endif
                    </div>
                    <div class="p-5 flex-grow">
                        <p class="text-gray-600 text-xs leading-relaxed italic">"{{ $story->story }}"</p>
                    </div>
                </div>
            @empty
                <div class="col-span-1 md:col-span-3 text-center text-gray-500 py-12 bg-gray-50 rounded-xl border">
                    <i class="fa-solid fa-heart-crack text-4xl text-slate-300 mb-2"></i>
                    <p class="font-bold text-sm">No success stories available yet.</p>
                    @auth('web')
                        @if(Auth::user()->status === 'approved')
                            <p class="text-xs text-slate-400 mt-1">Be the first to <a href="{{ route('success-stories.add') }}" class="text-primary font-bold underline">share your story</a>!</p>
                        @endif
                    @endauth
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
