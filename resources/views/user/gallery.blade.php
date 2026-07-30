@extends('layouts.app')

@section('title', 'Photo Gallery - Jain Digambar Matrimony')

@section('content')
<!-- Page Header -->
<section class="relative py-16 md:py-24 bg-primary text-white text-center">
    <div class="absolute inset-0 bg-black bg-opacity-40"></div>
    <div class="container mx-auto px-4 relative z-10">
        <h1 class="text-4xl md:text-5xl font-black drop-shadow-md" data-aos="fade-up">Photo Gallery</h1>
        <p class="text-gray-200 mt-2 text-sm md:text-base font-medium">Glimpses of our community events and gatherings.</p>
    </div>
</section>

<!-- Photos Section -->
<section class="py-16 bg-light">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-black text-dark mb-3">Event Photo Gallery</h2>
            <div class="w-16 h-1 bg-primary mx-auto"></div>
        </div>
        
        @if(empty($photos))
            <p class="text-center text-gray-500">No photos available at the moment.</p>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                @foreach($photos as $p) 
                    @php
                        if (str_starts_with($p->image_path, 'data:image/') || preg_match('/^https?:\/\//i', $p->image_path)) {
                            $imgSrc = $p->image_path;
                        } else {
                            $imgSrc = route('image.serve', ['file' => $p->image_path]);
                        }
                    @endphp
                    <a href="{{ $imgSrc }}" data-fancybox="gallery" data-caption="{{ $p->title }}" class="group block overflow-hidden rounded-xl shadow-sm hover:shadow-md transition bg-white border">
                        <div class="aspect-w-4 aspect-h-3 h-48 bg-gray-50">
                            <img src="{{ $imgSrc }}" alt="{{ $p->title }}" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500">
                        </div>
                        @if(!empty($p->title))
                            <div class="p-3 bg-white border-t border-gray-50">
                                <h3 class="text-xs font-semibold text-gray-800 text-center truncate">{{ $p->title }}</h3>
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

<!-- Documents Section -->
@if(!empty($pdfs) && count($pdfs) > 0)
<section class="py-16 bg-white border-t border-gray-100">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-black text-dark mb-3">Documents & PDFs</h2>
            <div class="w-16 h-1 bg-primary mx-auto"></div>
            <p class="text-gray-600 mt-4">Downloadable resources and documents.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($pdfs as $pdf) 
                @php
                    if (str_starts_with($pdf->image_path, 'data:')) {
                        $pdfLink = route('image.serve', ['file' => $pdf->image_path]);
                    } else {
                        $pdfLink = route('image.serve', ['file' => $pdf->image_path]);
                    }
                @endphp
                <a href="{{ $pdfLink }}" target="_blank" class="bg-light p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition flex items-start gap-4 group">
                    <div class="bg-red-100 text-red-500 rounded-lg p-3 group-hover:bg-red-500 group-hover:text-white transition">
                        <i class="fas fa-file-pdf text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 mb-1 leading-tight">{{ $pdf->title ?: 'Document' }}</h3>
                        <p class="text-xs text-gray-500">{{ $pdf->category ?? '' }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Videos Section -->
<section class="py-16 bg-white border-t border-gray-100">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-black text-dark mb-3">Event Videos</h2>
            <div class="w-16 h-1 bg-primary mx-auto"></div>
            <p class="text-gray-600 mt-4">Watch highlights from our past events and programs.</p>
        </div>
        
        @if(empty($videos))
            <p class="text-center text-gray-500">No videos available at the moment.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @php $delay = 0; @endphp
                @foreach($videos as $vid)
                    <div class="bg-light rounded-xl overflow-hidden shadow-md border" data-aos="fade-up" data-aos-delay="{{ $delay }}">
                        <div class="aspect-w-16 aspect-h-9 h-64 bg-black">
                            @if ($vid['video_type'] === 'youtube' && !empty($vid['video_url'])) 
                                @php
                                    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $vid['video_url'], $match);
                                    $yt_id = $match[1] ?? '';
                                @endphp
                                <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $yt_id }}" title="{{ $vid['title'] }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            @elseif ($vid['video_type'] === 'mp4' && !empty($vid['video_file'])) 
                                @php
                                    if (str_starts_with($vid['video_file'], 'data:') && isset($vid['source']) && $vid['source'] === 'gallery') {
                                        $vidSrc = route('image.serve', ['file' => $vid['video_file']]);
                                    } else {
                                        $vidSrc = route('image.serve', ['file' => $vid['video_file']]);
                                    }
                                @endphp
                                <video class="w-full h-full object-cover" controls>
                                    <source src="{{ $vidSrc }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            @endif
                        </div>
                        <div class="p-4 bg-white">
                            <h3 class="font-bold text-lg text-dark leading-tight">{{ $vid['title'] }}</h3>
                            @if(!empty($vid['description']))
                                <p class="text-gray-600 text-xs mt-2 leading-relaxed">{{ $vid['description'] }}</p>
                            @endif
                        </div>
                    </div>
                    @php $delay += 100; @endphp
                @endforeach
            </div>
        @endif
    </div>
</section>

<!-- Fancybox Script and CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Fancybox.bind("[data-fancybox]", {
            // Options
        });
    });
</script>
@endsection
