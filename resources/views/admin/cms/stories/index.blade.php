@extends('layouts.admin')

@section('title', 'Manage Success Stories - Admin Panel')
@section('header_title', 'Content Management: Success Stories')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100">
        <h3 class="font-bold text-gray-800 text-lg">Registered Couple Stories</h3>
    </div>
    
    <div class="divide-y divide-gray-100">
        @forelse($stories as $story)
        <div class="p-6 flex flex-col md:flex-row gap-6 hover:bg-slate-50 transition duration-150">
            <!-- Story photo -->
            <div class="w-full md:w-48 h-48 rounded-xl overflow-hidden border flex-shrink-0 bg-slate-50">
                @if($story->photo)
                    <img src="{{ $story->photo }}" alt="Couple" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center text-slate-400">
                        <i class="fa-solid fa-heart text-4xl mb-2"></i>
                        <span class="text-xs">No image uploaded</span>
                    </div>
                @endif
            </div>

            <!-- Story content details -->
            <div class="flex-grow">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h4 class="text-lg font-bold text-gray-900">{{ $story->couple_name ?? 'Testimonial Couple' }}</h4>
                    <span class="text-xs text-gray-400">Submitted: {{ $story->created_at->format('M d, Y') }}</span>
                </div>
                
                <div class="flex gap-4 text-xs font-semibold text-indigo-600 mt-1">
                    @if($story->engagement_date)
                        <span><i class="fa-solid fa-ring mr-1"></i>Engagement: {{ $story->engagement_date->format('M d, Y') }}</span>
                    @endif
                    @if($story->marriage_date)
                        <span><i class="fa-solid fa-hand-holding-heart mr-1"></i>Marriage: {{ $story->marriage_date->format('M d, Y') }}</span>
                    @endif
                </div>

                <p class="text-sm text-gray-600 mt-3 whitespace-pre-wrap font-sans">
                    {{ $story->story }}
                </p>

                <div class="mt-4 flex items-center gap-3">
                    <span class="px-2 py-0.5 rounded text-xs font-bold uppercase
                        @if($story->status === 'approved') bg-green-100 text-green-800
                        @else bg-orange-100 text-orange-800 @endif">
                        {{ $story->status }}
                    </span>

                    <div class="flex items-center gap-2 ml-4">
                        @if($story->status !== 'approved')
                        <form action="{{ route('admin.cms.stories.status', $story->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-bold transition">
                                Approve Story
                            </button>
                        </form>
                        @endif

                        @if($story->status !== 'pending')
                        <form action="{{ route('admin.cms.stories.status', $story->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="pending">
                            <button type="submit" class="px-3 py-1 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded text-xs font-bold transition">
                                Reset Pending
                            </button>
                        </form>
                        @endif

                        <form action="{{ route('admin.cms.stories.destroy', $story->id) }}" method="POST" onsubmit="return confirm('Delete this success story?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1 bg-red-50 hover:bg-red-100 text-red-600 rounded text-xs font-bold transition">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-500 text-sm">
            No success stories have been submitted yet.
        </div>
        @endforelse
    </div>

    @if($stories->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-slate-50">
        {{ $stories->links() }}
    </div>
    @endif
</div>
@endsection
