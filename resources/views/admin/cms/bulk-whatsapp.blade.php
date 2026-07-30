@extends('layouts.admin')

@section('title', 'Bulk WhatsApp Messages - Admin Panel')
@section('header_title', 'Bulk WhatsApp Module')

@section('content')
<div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded-r-xl" role="alert">
    <p class="font-bold text-sm">Note on Bulk Sending:</p>
    <p class="text-xs mt-1">This tool drafts your message and creates official <a href="https://wa.me" target="_blank" class="underline font-bold">wa.me</a> links. Click the <strong>Send Message</strong> button next to each user to open a new tab with the pre-filled message, allowing you to send it directly via WhatsApp Web or your WhatsApp Desktop app without needing an API subscription.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    
    <!-- Message Drafting Section -->
    <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50/50">
            <h4 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-brands fa-whatsapp text-emerald-500 text-lg"></i> Draft Message
            </h4>
        </div>
        <div class="p-5">
            <div class="space-y-2">
                <label for="wa_message" class="block text-sm font-semibold text-gray-700">WhatsApp Message Body</label>
                <textarea id="wa_message" rows="12" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none text-sm transition resize-none" placeholder="Type your WhatsApp message here..."></textarea>
                <p class="text-[11px] text-gray-400 font-medium">Use *bold* for bold, _italic_ for italic.</p>
            </div>
        </div>
    </div>
    
    <!-- User List Section -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-slate-50/50 gap-4">
            <div class="flex items-center">
                <h4 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-users text-indigo-500"></i> Select Recipients
                </h4>
                <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full font-bold ml-2">
                    {{ count($users) }} users
                </span>
            </div>
            <div class="relative w-full sm:w-80">
                <input type="text" id="searchInput" placeholder="Search name or mobile..." class="w-full pl-3 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
            </div>
        </div>
        
        <div>
            @if ($users->isEmpty())
                <div class="p-6 text-center text-gray-500">No users found with valid mobile numbers.</div>
            @else
                <div class="overflow-y-auto max-h-[500px]">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-slate-50/50 sticky top-0 z-10">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Phone</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($users as $user)
                                @php
                                    // Clean phone number (remove +, spaces, dashes, etc.)
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $user->mobile);
                                    // Make sure it has a country code, assume 91 if it's 10 digits
                                    if(strlen($cleanPhone) == 10) {
                                        $cleanPhone = '91' . $cleanPhone;
                                    }
                                @endphp
                                <tr class="hover:bg-slate-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">{{ $user->full_name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500 font-mono">{{ $user->mobile }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button onclick="sendWhatsApp('{{ $cleanPhone }}', this)" class="bg-emerald-500 hover:bg-emerald-600 text-white px-3.5 py-1.5 rounded-lg inline-flex items-center text-xs font-bold transition shadow-sm">
                                            <i class="fa-brands fa-whatsapp mr-1.5 text-sm"></i> Send Message
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const rows = document.querySelectorAll('tbody tr');
    
    function performSearch() {
        if(!searchInput) return;
        const term = searchInput.value.toLowerCase();
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if(text.includes(term)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if(searchInput) {
        searchInput.addEventListener('keyup', performSearch);
    }
});

function sendWhatsApp(phone, btnElement) {
    if (!phone) {
        alert("Invalid phone number.");
        return;
    }
    
    let message = document.getElementById('wa_message').value.trim();
    if (message === "") {
        if(!confirm("Your message body is empty. Do you still want to continue?")) {
            return;
        }
    }
    
    let encodedMessage = encodeURIComponent(message);
    let waUrl = `https://wa.me/${phone}?text=${encodedMessage}`;
    
    // Change button appearance to indicate it was clicked
    btnElement.classList.remove('bg-emerald-500', 'hover:bg-emerald-600');
    btnElement.classList.add('bg-slate-400', 'hover:bg-slate-500');
    btnElement.innerHTML = '<i class="fa-solid fa-check mr-1.5"></i> Sent';
    
    // Open WhatsApp Web/App in a new tab
    window.open(waUrl, '_blank');
}
</script>
@endsection
