@extends('layouts.admin')

@section('title', 'Contact Queries & Support Messages - Admin Panel')
@section('header_title', 'Contact & Support Queries')

@section('content')
<div class="space-y-6">

    <!-- 1. Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Total Queries -->
        <a href="{{ route('admin.contacts.index') }}" 
           class="bg-white p-5 rounded-2xl shadow-sm border {{ empty($statusFilter) ? 'border-indigo-500 ring-2 ring-indigo-500/20' : 'border-gray-100' }} hover:shadow-md transition flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Queries</p>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-800 mt-1">{{ $totalCount }}</h3>
                <p class="text-xs text-slate-500 mt-1">All contact messages</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-inbox"></i>
            </div>
        </a>

        <!-- Pending / Action Required -->
        <a href="{{ route('admin.contacts.index', ['status' => 'pending']) }}" 
           class="bg-white p-5 rounded-2xl shadow-sm border {{ $statusFilter === 'pending' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-gray-100' }} hover:shadow-md transition flex items-center justify-between group">
            <div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <p class="text-xs font-bold text-amber-700 uppercase tracking-wider">Pending Response (उत्तर बाकी)</p>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black text-amber-700 mt-1">{{ $pendingCount }}</h3>
                <p class="text-xs text-amber-600 font-semibold mt-1">Queries left to reply</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </a>

        <!-- Responded / Solved -->
        <a href="{{ route('admin.contacts.index', ['status' => 'responded']) }}" 
           class="bg-white p-5 rounded-2xl shadow-sm border {{ $statusFilter === 'responded' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-gray-100' }} hover:shadow-md transition flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Responded (उत्तर दिया गया)</p>
                <h3 class="text-2xl sm:text-3xl font-black text-emerald-700 mt-1">{{ $respondedCount }}</h3>
                <p class="text-xs text-emerald-600 font-semibold mt-1">Queries replied & resolved</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </a>
    </div>

    <!-- 2. Filter & Search Panel -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
        
        <!-- Status Tabs -->
        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl">
            <a href="{{ route('admin.contacts.index', request()->except('status', 'page')) }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ empty($statusFilter) ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                All ({{ $totalCount }})
            </a>
            <a href="{{ route('admin.contacts.index', array_merge(request()->except('page'), ['status' => 'pending'])) }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ $statusFilter === 'pending' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 hover:text-amber-700' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $statusFilter === 'pending' ? 'bg-white' : 'bg-amber-500' }}"></span>
                Pending ({{ $pendingCount }})
            </a>
            <a href="{{ route('admin.contacts.index', array_merge(request()->except('page'), ['status' => 'responded'])) }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ $statusFilter === 'responded' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-emerald-700' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $statusFilter === 'responded' ? 'bg-white' : 'bg-emerald-500' }}"></span>
                Responded ({{ $respondedCount }})
            </a>
        </div>

        <!-- Search Box -->
        <form action="{{ route('admin.contacts.index') }}" method="GET" class="flex items-center gap-2 flex-grow max-w-md">
            @if(!empty($statusFilter))
                <input type="hidden" name="status" value="{{ $statusFilter }}">
            @endif
            <div class="relative flex-grow">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, mobile, subject..." 
                       class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2 rounded-xl text-xs sm:text-sm transition">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('admin.contacts.index', request()->except('search', 'page')) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-2 rounded-xl text-xs sm:text-sm transition" title="Clear Search">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- 3. Contact Messages Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-100 text-gray-400 text-xs uppercase font-bold">
                        <th class="py-3.5 px-6">Status / Highlight</th>
                        <th class="py-3.5 px-6">Sender Details</th>
                        <th class="py-3.5 px-6">Subject & Message</th>
                        <th class="py-3.5 px-6">Received At</th>
                        <th class="py-3.5 px-6 text-center">Actions & Direct Reply</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    @forelse($messages as $msg)
                    @php
                        $isPending = empty($msg->status) || $msg->status === 'pending';
                    @endphp
                    <tr class="transition duration-150 {{ $isPending ? 'bg-amber-50/25 border-l-4 border-l-amber-500 hover:bg-amber-50/40' : 'border-l-4 border-l-emerald-500 hover:bg-slate-50/50' }}">
                        
                        <!-- Status Badge -->
                        <td class="py-4 px-6 align-top">
                            @if($isPending)
                                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-100 text-amber-900 border border-amber-300 font-black text-xs shadow-2xs">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span>Pending Response</span>
                                </div>
                                <span class="block text-[11px] text-amber-700 font-bold mt-1 pl-1">उत्तर देना बाकी</span>
                            @else
                                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-300 font-bold text-xs shadow-2xs">
                                    <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i>
                                    <span>Responded</span>
                                </div>
                                <span class="block text-[11px] text-emerald-700 font-medium mt-1 pl-1">उत्तर दिया गया</span>
                            @endif
                        </td>

                        <!-- Sender Details -->
                        <td class="py-4 px-6 align-top">
                            <div class="font-extrabold text-gray-900 text-sm leading-tight flex items-center gap-1.5">
                                <i class="fa-solid fa-user-circle text-slate-400 text-base"></i>
                                <span>{{ $msg->name }}</span>
                            </div>
                            <div class="text-xs text-slate-500 mt-1.5 space-y-1">
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-envelope text-indigo-500 text-[11px]"></i> 
                                    <a href="mailto:{{ $msg->email }}?subject=Re:%20{{ rawurlencode($msg->subject) }}" class="font-semibold text-indigo-600 hover:underline">
                                        {{ $msg->email }}
                                    </a>
                                </div>
                                @if($msg->mobile)
                                    <div class="flex items-center gap-1.5 font-mono text-slate-700 font-semibold">
                                        <i class="fa-solid fa-phone text-emerald-600 text-[11px]"></i> 
                                        <a href="tel:{{ $msg->mobile }}" class="hover:underline">{{ $msg->mobile }}</a>
                                    </div>
                                @endif
                            </div>
                        </td>

                        <!-- Subject & Message -->
                        <td class="py-4 px-6 align-top max-w-md">
                            <div class="font-bold text-gray-900 text-sm mb-1 flex items-center gap-1.5">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-xs font-semibold border border-slate-200">
                                    {{ $msg->subject }}
                                </span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-gray-200 text-slate-700 text-xs sm:text-sm leading-relaxed whitespace-pre-line shadow-2xs">
                                {{ $msg->message }}
                            </div>
                        </td>

                        <!-- Received At -->
                        <td class="py-4 px-6 align-top whitespace-nowrap">
                            <div class="font-semibold text-slate-800 text-xs sm:text-sm">
                                {{ \Carbon\Carbon::parse($msg->created_at)->format('d M Y') }}
                            </div>
                            <div class="text-xs text-slate-400 font-mono mt-0.5">
                                {{ \Carbon\Carbon::parse($msg->created_at)->format('h:i A') }}
                            </div>
                            <div class="text-[11px] text-slate-400 mt-1">
                                ({{ \Carbon\Carbon::parse($msg->created_at)->diffForHumans() }})
                            </div>
                        </td>

                        <!-- Actions & Direct Email Reply -->
                        <td class="py-4 px-6 align-top text-center">
                            <div class="flex flex-col items-center gap-2">
                                
                                <!-- 1. Direct Email Reply Button -->
                                <a href="mailto:{{ $msg->email }}?subject=Re:%20{{ rawurlencode($msg->subject) }}" 
                                   class="w-full inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs hover:shadow-sm transition"
                                   title="Open email to reply directly to {{ $msg->email }}">
                                    <i class="fa-solid fa-reply text-xs"></i>
                                    <span>Reply Email</span>
                                </a>

                                <!-- 2. Toggle Status (Pending / Responded) -->
                                <form action="{{ route('admin.contacts.toggle-status', $msg->id) }}" method="POST" class="w-full">
                                    @csrf
                                    @if($isPending)
                                        <button type="submit" class="w-full inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 font-bold text-[11px] transition duration-150" title="Mark as Responded">
                                            <i class="fa-solid fa-check text-xs"></i> Mark Responded
                                        </button>
                                    @else
                                        <button type="submit" class="w-full inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-amber-500 text-slate-600 hover:text-white border border-slate-200 font-bold text-[11px] transition duration-150" title="Mark as Pending Response">
                                            <i class="fa-solid fa-rotate-left text-xs"></i> Mark Pending
                                        </button>
                                    @endif
                                </form>

                                <!-- 3. Delete Message -->
                                <form action="{{ route('admin.contacts.destroy', $msg->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this contact inquiry?');" class="w-full">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full inline-flex items-center justify-center gap-1 px-2.5 py-1 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg text-xs font-semibold transition duration-150">
                                        <i class="fa-solid fa-trash-can text-[11px]"></i> Delete
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-16 px-6 text-center text-gray-400 font-medium">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-2xl">
                                    <i class="fa-solid fa-inbox"></i>
                                </div>
                                <h4 class="text-gray-700 font-bold text-base">No contact messages found</h4>
                                <p class="text-xs text-gray-400 max-w-sm">
                                    @if(request('search') || request('status'))
                                        No messages match your active filter criteria. Try resetting your search filters.
                                    @else
                                        All user inquiries submitted via the Contact Us form will appear here.
                                    @endif
                                </p>
                                @if(request('search') || request('status'))
                                    <a href="{{ route('admin.contacts.index') }}" class="mt-2 text-xs font-bold text-indigo-600 hover:underline">
                                        View All Contact Queries
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($messages->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $messages->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
