@extends('layouts.admin')

@section('title', 'Contact Messages - Admin Panel')
@section('header_title', 'Contact Messages')

@section('content')
<!-- Data Table -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-gray-100 text-gray-400 text-xs uppercase font-bold">
                    <th class="py-3 px-6">Sender Details</th>
                    <th class="py-3 px-6">Subject</th>
                    <th class="py-3 px-6">Message</th>
                    <th class="py-3 px-6">Received At</th>
                    <th class="py-3 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                @forelse($messages as $msg)
                <tr class="hover:bg-slate-50/50 transition duration-150">
                    <td class="py-4 px-6">
                        <div class="font-bold text-gray-900 leading-tight">{{ $msg->name }}</div>
                        <div class="text-xs text-slate-400 mt-1 flex flex-col gap-0.5">
                            <span class="flex items-center gap-1"><i class="fa-solid fa-envelope text-[10px]"></i> {{ $msg->email }}</span>
                            @if($msg->mobile)
                                <span class="flex items-center gap-1 font-mono"><i class="fa-solid fa-phone text-[10px]"></i> {{ $msg->mobile }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="py-4 px-6 text-gray-800 font-semibold">
                        {{ $msg->subject }}
                    </td>
                    <td class="py-4 px-6 text-gray-600 max-w-xs break-words font-medium">
                        "{{ $msg->message }}"
                    </td>
                    <td class="py-4 px-6 text-gray-500 font-medium">
                        {{ \Carbon\Carbon::parse($msg->created_at)->format('M d, Y H:i') }}
                    </td>
                    <td class="py-4 px-6 text-center">
                        <form action="{{ route('admin.contacts.destroy', $msg->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this message?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 bg-red-50 hover:bg-red-600 text-red-500 hover:text-white rounded-lg transition duration-150">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 px-6 text-center text-gray-400 font-medium">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <i class="fa-solid fa-circle-check text-4xl text-emerald-500"></i>
                            <span class="text-gray-600 text-base">No contact messages received yet!</span>
                            <span class="text-xs text-gray-400">All messages submitted via Contact Us page will show up here.</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
