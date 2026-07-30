@extends('layouts.admin')

@section('title', 'Bulk Email - Admin Panel')
@section('header_title', 'Bulk Email Module')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
    <div class="p-6 md:p-8">
        <form action="{{ route('admin.bulk-email.send') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Subject -->
            <div>
                <label for="subject" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Subject <span class="text-red-500">*</span></label>
                <input type="text" id="subject" name="subject" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm transition" placeholder="Enter email subject">
            </div>
            
            <!-- Message -->
            <div>
                <label for="message" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Body (HTML supported) <span class="text-red-500">*</span></label>
                <textarea id="message" name="message" rows="8" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm transition" placeholder="Write your message here... You can use HTML tags."></textarea>
            </div>
            
            <!-- User Selection -->
            <div>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-3 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Select Recipients <span class="text-red-500">*</span></label>
                        <button type="button" id="selectAllBtn" class="text-xs text-indigo-600 font-bold hover:underline mt-1 block">Select All Visible</button>
                    </div>
                    <div class="relative w-full sm:w-80 flex">
                        <input type="text" id="searchInput" placeholder="Search name or email..." class="w-full pl-3 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                    </div>
                </div>
                
                <div class="border border-gray-200 rounded-xl max-h-72 overflow-y-auto p-4 bg-slate-50/50">
                    @if ($users->isEmpty())
                        <p class="text-gray-500 text-sm">No users found with valid email addresses.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach ($users as $user)
                                <label class="user-label flex items-center space-x-3 bg-white p-2.5 border border-gray-100 rounded-xl shadow-sm hover:bg-slate-50 cursor-pointer transition select-none">
                                    <input type="checkbox" name="users[]" value="{{ $user->email }}" class="user-checkbox form-checkbox h-4 w-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                                    <div class="flex flex-col truncate">
                                        <span class="text-sm font-semibold text-gray-800 truncate">{{ $user->full_name }}</span>
                                        <span class="text-xs text-gray-400 truncate" title="{{ $user->email }}">{{ $user->email }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>
                <p class="text-xs text-gray-500 mt-2 font-semibold">Selected: <span id="selectedCount" class="text-indigo-600 font-bold">0</span> users</p>
            </div>
            
            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="submit" id="submitBtn" class="bg-indigo-600 text-white px-6 py-3 rounded-xl text-sm font-extrabold hover:bg-indigo-700 transition shadow-md flex items-center">
                    <i class="fa-solid fa-paper-plane mr-2"></i> Send Bulk Email
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const userLabels = document.querySelectorAll('.user-label');
    
    function performSearch() {
        if(!searchInput) return;
        const term = searchInput.value.toLowerCase();
        userLabels.forEach(label => {
            const text = label.textContent.toLowerCase();
            if(text.includes(term)) {
                label.style.display = 'flex';
            } else {
                label.style.display = 'none';
            }
        });
    }

    if(searchInput) {
        searchInput.addEventListener('keyup', performSearch);
    }

    const selectAllBtn = document.getElementById('selectAllBtn');
    const checkboxes = document.querySelectorAll('.user-checkbox');
    const selectedCountSpan = document.getElementById('selectedCount');
    let allSelected = false;
    
    function updateCount() {
        const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
        selectedCountSpan.textContent = checkedCount;
    }
    
    selectAllBtn.addEventListener('click', function() {
        allSelected = !allSelected;
        
        userLabels.forEach(label => {
            if (label.style.display !== 'none') {
                const cb = label.querySelector('.user-checkbox');
                if (cb) cb.checked = allSelected;
            }
        });
        
        selectAllBtn.textContent = allSelected ? 'Deselect All Visible' : 'Select All Visible';
        updateCount();
    });
    
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateCount();
            if (!this.checked) {
                allSelected = false;
                selectAllBtn.textContent = 'Select All Visible';
            }
        });
    });
    
    // Add loading state to button on submit
    document.querySelector('form').addEventListener('submit', function(e) {
        const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
        if(checkedCount === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'No Recipients Selected',
                text: 'Please select at least one user to send the email.'
            });
            return;
        }

        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Sending...';
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        btn.style.pointerEvents = 'none';
    });
});
</script>
@endsection
