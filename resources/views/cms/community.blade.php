@extends('layouts.app')

@section('title', 'Community Initiatives')

@section('content')
<div class="bg-gray-50 py-12">
    <div class="container mx-auto px-4 max-w-4xl" data-aos="fade-up">
        <div>{!! $community_content !!}</div>
        <div class="mt-12 text-center">
            <a href="{{ route('home') }}" class="bg-primary text-white px-6 py-3 rounded-md font-semibold hover:bg-opacity-90 transition">Return to Home</a>
        </div>
    </div>
</div>
@endsection
