@extends('layouts.app')

@section('title', 'News & Updates - Jain Digambar Matrimony')

@section('content')
<div class="bg-gray-50 py-12 min-h-[60vh]">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="text-center mb-12" data-aos="fade-up">
            <i class="fas fa-newspaper text-5xl text-primary mb-4"></i>
            <h1 class="text-4xl md:text-5xl font-black text-dark mb-4">News & Updates</h1>
            <p class="text-gray-500 text-sm max-w-lg mx-auto">Stay informed with the latest news, announcements, and programs from the community.</p>
        </div>

        @if ($news_items->isEmpty())
            <div class="text-center py-12 bg-white rounded-2xl shadow-sm border border-gray-100 max-w-lg mx-auto">
                <p class="text-gray-500 text-sm mb-6">No news articles found at the moment. Please check back later.</p>
                <a href="{{ route('home') }}" class="bg-primary text-white px-6 py-2.5 rounded-lg font-bold hover:bg-opacity-90 transition text-sm">Return to Home</a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($news_items as $news)
                    @php
                        $imgSrc = '';
                        if (!empty($news->image)) {
                            if (str_starts_with($news->image, 'data:image/') || preg_match('/^https?:\/\//i', $news->image)) {
                                $imgSrc = $news->image;
                            } else {
                                $imgSrc = route('image.serve', ['file' => $news->image]);
                            }
                        }
                    @endphp
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition flex flex-col justify-between" data-aos="fade-up">
                        <div>
                            @if (!empty($imgSrc))
                                <div class="h-48 overflow-hidden bg-gray-50">
                                    <img src="{{ $imgSrc }}" alt="{{ $news->title }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="w-full h-48 bg-slate-100 flex items-center justify-center text-slate-300">
                                    <i class="fas fa-image text-4xl"></i>
                                </div>
                            @endif
                            
                            <div class="p-6">
                                <div class="text-[10px] font-bold text-primary mb-2 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="far fa-calendar-alt"></i> {{ $news->created_at ? $news->created_at->format('M d, Y') : 'N/A' }}
                                </div>
                                <h3 class="text-lg font-extrabold text-dark mb-3 leading-snug line-clamp-2">{{ $news->title }}</h3>
                                <div class="text-gray-600 text-xs mb-4 leading-relaxed line-clamp-3">
                                    {!! nl2br(e($news->content)) !!}
                                </div>
                            </div>
                        </div>
                        
                        <div class="px-6 pb-6 pt-2">
                            <button onclick="openNewsModal({{ $news->id }})" class="text-primary font-bold hover:underline text-xs inline-flex items-center gap-1">
                                View full news <i class="fas fa-arrow-right text-[10px]"></i>
                            </button>
                        </div>

                        <!-- Hidden data for modal injection -->
                        <div id="news-title-{{ $news->id }}" class="hidden">{{ $news->title }}</div>
                        <div id="news-date-{{ $news->id }}" class="hidden"><i class="far fa-calendar-alt mr-2"></i> {{ $news->created_at ? $news->created_at->format('M d, Y') : 'N/A' }}</div>
                        <div id="news-content-{{ $news->id }}" class="hidden">{!! nl2br(e($news->content)) !!}</div>
                        <div id="news-image-{{ $news->id }}" class="hidden">
                            @if (!empty($imgSrc))
                                <img src="{{ $imgSrc }}" alt="{{ $news->title }}" class="w-full h-64 md:h-80 object-cover rounded-lg mb-6 shadow-sm">
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- News Detail Modal -->
            <div id="newsModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col transform transition-all">
                    <div class="flex justify-between items-start p-6 border-b">
                        <div>
                            <h2 id="modal-title" class="text-xl font-black text-dark mb-1 leading-snug"></h2>
                            <div id="modal-date" class="text-xs text-primary font-bold uppercase tracking-wider"></div>
                        </div>
                        <button onclick="closeNewsModal()" class="w-8 h-8 rounded-full hover:bg-slate-100 text-slate-500 flex items-center justify-center transition">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    <div class="p-6 overflow-y-auto custom-scrollbar">
                        <div id="modal-image"></div>
                        <div id="modal-body" class="text-gray-700 text-sm leading-relaxed whitespace-pre-line"></div>
                    </div>
                    <div class="p-4 border-t text-right bg-slate-50 rounded-b-2xl">
                        <button onclick="closeNewsModal()" class="px-5 py-2 bg-slate-600 text-white font-bold rounded-lg hover:bg-slate-700 transition text-xs shadow-sm">Close</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function openNewsModal(id) {
    document.getElementById('modal-title').innerText = document.getElementById('news-title-' + id).innerText;
    document.getElementById('modal-date').innerHTML = document.getElementById('news-date-' + id).innerHTML;
    document.getElementById('modal-image').innerHTML = document.getElementById('news-image-' + id).innerHTML;
    document.getElementById('modal-body').innerHTML = document.getElementById('news-content-' + id).innerHTML;
    
    const modal = document.getElementById('newsModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeNewsModal() {
    const modal = document.getElementById('newsModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
}

// Close on background click
document.getElementById('newsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeNewsModal();
    }
});
</script>
@endsection
