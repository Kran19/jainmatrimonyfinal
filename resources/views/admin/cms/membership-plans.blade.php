@extends('layouts.admin')

@section('title', 'Membership Plans - Admin Panel')
@section('header_title', 'Membership Plans')

@section('content')
<!-- Page Header -->
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <p class="text-gray-500 text-sm">Manage subscription packages and pricing available for user signups.</p>
    </div>
    <button class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg text-sm font-bold hover:bg-indigo-700 transition shadow-sm flex items-center" onclick="openAddModal()">
        <i class="fa-solid fa-plus mr-2"></i> Add New Plan
    </button>
</div>

<!-- Plans Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    @forelse($plans as $plan)
    <div class="bg-white rounded-2xl shadow-sm border {{ $plan->status ? 'border-gray-100' : 'border-red-100 bg-red-50/10' }} overflow-hidden flex flex-col transition hover:shadow-md duration-150">
        <div class="bg-slate-50/50 p-6 text-center border-b border-gray-100 relative">
            @if(!$plan->status)
                <span class="absolute top-3 left-3 bg-red-100 text-red-800 text-[10px] font-extrabold px-2 py-0.5 rounded-full">Inactive</span>
            @endif
            <h4 class="text-xl font-bold text-gray-800">{{ $plan->plan_name }}</h4>
            <div class="mt-4 flex justify-center items-baseline">
                <span class="text-2xl font-semibold text-gray-400 mr-1">₹</span>
                <span class="text-4xl font-extrabold text-gray-800">{{ number_format($plan->price) }}</span>
            </div>
            <p class="text-xs text-gray-400 font-semibold mt-1">Duration: {{ $plan->duration_days }} Days</p>
        </div>
        <div class="p-6 flex-1">
            <ul class="space-y-3 text-sm text-gray-600">
                <li class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i> 
                    <span>Contact views: {{ $plan->contact_limit }} profiles</span>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fa-solid fa-{{ $plan->featured_profile ? 'circle-check text-emerald-500' : 'circle-xmark text-slate-300' }} text-xs"></i> 
                    <span class="{{ $plan->featured_profile ? '' : 'text-gray-400 line-through' }}">Featured Profile Placement</span>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fa-solid fa-{{ $plan->priority_support ? 'circle-check text-emerald-500' : 'circle-xmark text-slate-300' }} text-xs"></i> 
                    <span class="{{ $plan->priority_support ? '' : 'text-gray-400 line-through' }}">Priority Support</span>
                </li>
            </ul>
        </div>
        <div class="p-5 pt-0 border-t border-slate-50 mt-auto bg-white flex justify-between items-center">
            <button onclick='openEditModal({!! json_encode($plan) !!})' class="text-indigo-600 font-bold text-xs hover:text-indigo-700 transition flex items-center gap-1">
                <i class="fa-solid fa-pen-to-square"></i> Edit
            </button>
            
            <form action="{{ route('admin.membership-plans.destroy', $plan->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this plan? This can cause registration issues if users are currently using it.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-500 hover:text-red-600 transition text-xs font-bold flex items-center gap-1">
                    <i class="fa-solid fa-trash-can"></i> Delete
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-3 bg-white p-12 text-center text-gray-500 rounded-2xl border">
        <p class="font-semibold text-lg text-gray-700">No membership plans created yet.</p>
        <p class="text-xs text-gray-400 mt-1">Please add a plan so that users can complete step 6 of the wizard.</p>
    </div>
    @endforelse
</div>

<!-- Plan Modal -->
<div id="planModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl overflow-hidden flex flex-col">
        <div class="flex justify-between items-center p-6 border-b border-gray-100">
            <h3 id="modalTitle" class="text-lg font-bold text-gray-800">Create New Membership Plan</h3>
            <button class="text-gray-400 hover:text-gray-600 transition" onclick="closeModal()">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        
        <div class="p-6">
            <form id="planForm" method="POST" action="{{ route('admin.membership-plans.store') }}" class="space-y-5">
                @csrf
                <div id="methodField"></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Plan Name</label>
                        <input type="text" name="plan_name" id="plan_name" required placeholder="e.g. Premium Yearly" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Price (₹)</label>
                        <input type="number" name="price" id="price" required min="0" placeholder="e.g. 5000" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Duration (Days)</label>
                        <input type="number" name="duration_days" id="duration_days" required min="1" placeholder="e.g. 365" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Contact View Limit</label>
                        <input type="number" name="contact_limit" id="contact_limit" required min="0" placeholder="e.g. 150" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="space-y-3 bg-slate-50 p-4 rounded-xl border border-gray-100">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="featured_profile" id="featured_profile" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                        <span class="text-sm font-semibold text-gray-700">Featured Profile Placement</span>
                    </label>
                    <br>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="priority_support" id="priority_support" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                        <span class="text-sm font-semibold text-gray-700">Priority Support</span>
                    </label>
                    <br>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="status" id="status" value="1" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                        <span class="text-sm font-semibold text-gray-700">Make this plan Active immediately</span>
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" class="px-5 py-2 text-sm font-bold text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-slate-50 transition" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-sm font-bold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow-sm">Save Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const modal = document.getElementById('planModal');
const form = document.getElementById('planForm');
const methodField = document.getElementById('methodField');

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Create New Membership Plan';
    form.action = "{{ route('admin.membership-plans.store') }}";
    methodField.innerHTML = '';
    
    document.getElementById('plan_name').value = '';
    document.getElementById('price').value = '';
    document.getElementById('duration_days').value = '';
    document.getElementById('contact_limit').value = '';
    document.getElementById('featured_profile').checked = false;
    document.getElementById('priority_support').checked = false;
    document.getElementById('status').checked = true;
    
    modal.classList.remove('hidden');
}

function openEditModal(plan) {
    document.getElementById('modalTitle').textContent = 'Edit Membership Plan';
    form.action = `/admin/membership-plans/${plan.id}`;
    methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
    
    document.getElementById('plan_name').value = plan.plan_name;
    document.getElementById('price').value = Math.round(plan.price);
    document.getElementById('duration_days').value = plan.duration_days;
    document.getElementById('contact_limit').value = plan.contact_limit;
    document.getElementById('featured_profile').checked = !!plan.featured_profile;
    document.getElementById('priority_support').checked = !!plan.priority_support;
    document.getElementById('status').checked = !!plan.status;
    
    modal.classList.remove('hidden');
}

function closeModal() {
    modal.classList.add('hidden');
}
</script>
@endsection
