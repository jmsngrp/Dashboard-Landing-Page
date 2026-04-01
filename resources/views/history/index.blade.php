@extends('layouts.erp', ['currentModule' => 'fundraising'])

@section('content')
<div x-data="historyPanel()" x-init="init()">

    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">Deposit History</h1>
        <p class="mt-1 text-sm text-gray-500">View past deposit submissions and their processing status.</p>
    </div>

    {{-- Filter bar --}}
    <div class="card mb-5 flex flex-wrap items-center gap-3 px-4 py-3">
        {{-- Status dropdown --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" type="button"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:border-gray-400 focus:border-accent focus:ring-2 focus:ring-accent/20 focus:outline-none">
                <span x-text="statusFilter === 'pending' ? 'Pending' : statusFilter === 'processed' ? 'Processed' : 'All Statuses'"></span>
                <svg class="h-4 w-4 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
            </button>
            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-1"
                 class="absolute left-0 top-full z-20 mt-1 w-44 rounded-lg border border-gray-200 bg-white py-1 shadow-lg">
                <button @click="statusFilter = ''; open = false; onFilterChange()" type="button"
                        class="flex w-full items-center gap-2 px-3 py-2 text-sm transition hover:bg-gray-50"
                        :class="statusFilter === '' ? 'text-accent font-medium' : 'text-gray-700'">
                    <svg class="h-3.5 w-3.5" :class="statusFilter === '' ? 'text-accent' : 'text-transparent'" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                    All Statuses
                </button>
                <button @click="statusFilter = 'pending'; open = false; onFilterChange()" type="button"
                        class="flex w-full items-center gap-2 px-3 py-2 text-sm transition hover:bg-gray-50"
                        :class="statusFilter === 'pending' ? 'text-accent font-medium' : 'text-gray-700'">
                    <svg class="h-3.5 w-3.5" :class="statusFilter === 'pending' ? 'text-accent' : 'text-transparent'" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                    Pending
                </button>
                <button @click="statusFilter = 'processed'; open = false; onFilterChange()" type="button"
                        class="flex w-full items-center gap-2 px-3 py-2 text-sm transition hover:bg-gray-50"
                        :class="statusFilter === 'processed' ? 'text-accent font-medium' : 'text-gray-700'">
                    <svg class="h-3.5 w-3.5" :class="statusFilter === 'processed' ? 'text-accent' : 'text-transparent'" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                    Processed
                </button>
            </div>
        </div>

        {{-- Organization dropdown --}}
        <div x-show="isSuperAdmin" class="relative" x-data="{ open: false }">
            <button @click="open = !open" type="button"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:border-gray-400 focus:border-accent focus:ring-2 focus:ring-accent/20 focus:outline-none">
                <span x-text="orgFilter ? organizations.find(o => o.id == orgFilter)?.name || 'Organization' : 'All Organizations'"></span>
                <svg class="h-4 w-4 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
            </button>
            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-1"
                 class="absolute left-0 top-full z-20 mt-1 w-56 max-h-60 overflow-y-auto rounded-lg border border-gray-200 bg-white py-1 shadow-lg">
                <button @click="orgFilter = ''; open = false; onFilterChange()" type="button"
                        class="flex w-full items-center gap-2 px-3 py-2 text-sm transition hover:bg-gray-50"
                        :class="orgFilter === '' ? 'text-accent font-medium' : 'text-gray-700'">
                    <svg class="h-3.5 w-3.5" :class="orgFilter === '' ? 'text-accent' : 'text-transparent'" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                    All Organizations
                </button>
                <template x-for="org in organizations" :key="org.id">
                    <button @click="orgFilter = String(org.id); open = false; onFilterChange()" type="button"
                            class="flex w-full items-center gap-2 px-3 py-2 text-sm transition hover:bg-gray-50"
                            :class="orgFilter == org.id ? 'text-accent font-medium' : 'text-gray-700'">
                        <svg class="h-3.5 w-3.5" :class="orgFilter == org.id ? 'text-accent' : 'text-transparent'" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        <span x-text="org.name"></span>
                    </button>
                </template>
            </div>
        </div>

        <div class="flex-1">
            <input type="text" x-model="searchQuery" @input="onSearchInput()" placeholder="Search batch ID or campaign..."
                   class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-accent focus:ring-2 focus:ring-accent/20 focus:outline-none">
        </div>
    </div>

    {{-- Alert --}}
    <div x-show="alert" x-text="alert" class="mb-4 rounded-lg bg-green-50 px-4 py-2.5 text-sm text-green-700" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-init="$watch('alert', v => { if(v) setTimeout(() => alert = '', 3000) })"></div>

    {{-- Loading --}}
    <div x-show="loading" class="py-12 text-center text-sm text-gray-500">Loading submissions...</div>

    {{-- Table --}}
    <div x-show="!loading" class="card overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-xs font-medium uppercase tracking-wider text-gray-500">
                    <th class="px-3 py-3 w-8"></th>
                    <th class="px-3 py-3">Status</th>
                    <th class="px-3 py-3">Batch ID</th>
                    <th class="px-3 py-3">Date</th>
                    <th class="px-3 py-3">Checks</th>
                    <th class="px-3 py-3">Total</th>
                    <th class="px-3 py-3" x-show="isAdmin">Submitted By</th>
                    <th class="px-3 py-3" x-show="isSuperAdmin">Org</th>
                    <th class="px-3 py-3">Submitted</th>
                    <th class="px-3 py-3" x-show="isAdmin">QBO</th>
                    <th class="px-3 py-3 w-24" x-show="isAdmin"></th>
                </tr>
            </thead>
            <template x-for="sub in submissions" :key="sub.id">
                <tbody class="divide-y divide-gray-50 border-b border-gray-100">
                    {{-- Main row --}}
                    <tr class="hover:bg-gray-50/50 cursor-pointer" @click="toggleExpand(sub.id)">
                            <td class="px-3 py-2.5">
                                <svg class="h-4 w-4 text-gray-500 transition-transform" :class="expanded === sub.id ? 'rotate-90' : ''"
                                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                                </svg>
                            </td>
                            <td class="px-3 py-2.5">
                                <span class="badge"
                                      :class="sub.status === 'processed' ? 'badge-success' : 'badge-warning'">
                                    <span class="badge-dot"></span>
                                    <span x-text="sub.status === 'processed' ? 'Processed' : 'Pending'"></span>
                                </span>
                            </td>
                            <td class="px-3 py-2.5 font-mono text-xs text-gray-600" x-text="sub.batch_id"></td>
                            <td class="px-3 py-2.5 text-gray-600" x-text="sub.deposit_date ? new Date(sub.deposit_date + 'T12:00:00').toLocaleDateString() : '—'"></td>
                            <td class="px-3 py-2.5 text-gray-700" x-text="sub.check_count"></td>
                            <td class="px-3 py-2.5 font-mono text-gray-700" x-text="'$' + parseFloat(sub.total_amount).toFixed(2)"></td>
                            <td class="px-3 py-2.5 text-gray-600" x-show="isAdmin" x-text="sub.user ? sub.user.name : '—'"></td>
                            <td class="px-3 py-2.5 text-gray-600" x-show="isSuperAdmin" x-text="sub.user && sub.user.organization ? sub.user.organization.name : '—'"></td>
                            <td class="px-3 py-2.5 text-xs text-gray-500" x-text="sub.created_at ? new Date(sub.created_at).toLocaleDateString() : ''"></td>
                            <td class="px-3 py-2.5" x-show="isAdmin" @click.stop>
                                <template x-if="!sub.qbo_push_status">
                                    <button @click="qboPush(sub)" :disabled="sub._qboPushing"
                                            class="rounded-lg bg-olive px-2.5 py-1 text-xs font-medium text-white transition hover:opacity-90 disabled:opacity-50">
                                        <span x-text="sub._qboPushing ? 'Pushing...' : 'Push to QBO'"></span>
                                    </button>
                                </template>
                                <template x-if="sub.qbo_push_status === 'completed'">
                                    <span class="badge badge-success">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                        Pushed
                                    </span>
                                </template>
                                <template x-if="sub.qbo_push_status === 'processing'">
                                    <span class="badge badge-info">
                                        <svg class="h-3 w-3 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                        Processing
                                    </span>
                                </template>
                                <template x-if="sub.qbo_push_status === 'failed'">
                                    <div class="flex items-center gap-1.5">
                                        <span class="badge badge-danger">Failed</span>
                                        <button @click="qboPush(sub)" :disabled="sub._qboPushing"
                                                class="rounded px-1.5 py-0.5 text-xs text-red-600 hover:bg-red-50 font-medium">
                                            Retry
                                        </button>
                                    </div>
                                </template>
                            </td>
                            <td class="px-3 py-2.5" x-show="isAdmin" @click.stop>
                                <button x-show="sub.status === 'pending'" @click="markStatus(sub.id, 'processed')"
                                        class="rounded-lg bg-olive px-2.5 py-1 text-xs font-medium text-white transition hover:opacity-90">
                                    Mark Processed
                                </button>
                                <button x-show="sub.status === 'processed'" @click="markStatus(sub.id, 'pending')"
                                        class="rounded-lg border border-gray-300 px-2.5 py-1 text-xs font-medium text-gray-600 transition hover:bg-gray-50">
                                    Revert
                                </button>
                            </td>
                        </tr>
                        {{-- Expanded detail row --}}
                        <tr x-show="expanded === sub.id" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                            <td :colspan="isAdmin ? (isSuperAdmin ? 11 : 10) : 7" class="bg-gray-50 px-6 py-4">
                                <div class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-500">Check Details</div>
                                <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                                    <table class="w-full text-left text-xs">
                                        <thead>
                                            <tr class="border-b border-gray-100 text-[10px] font-medium uppercase tracking-wider text-gray-500">
                                                <th class="px-3 py-2">#</th>
                                                <th class="px-3 py-2">Payee</th>
                                                <th class="px-3 py-2">Amount</th>
                                                <th class="px-3 py-2">Check #</th>
                                                <th class="px-3 py-2">Campaign</th>
                                                <th class="px-3 py-2">Type</th>
                                                <th class="px-3 py-2">Account</th>
                                                <th class="px-3 py-2">Class</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50">
                                            <template x-for="(check, ci) in (sub.payload && sub.payload.checks ? sub.payload.checks : [])" :key="ci">
                                                <tr class="hover:bg-gray-50/50">
                                                    <td class="px-3 py-1.5 text-gray-500" x-text="ci + 1"></td>
                                                    <td class="px-3 py-1.5 font-medium text-gray-800" x-text="check.payee || '—'"></td>
                                                    <td class="px-3 py-1.5 font-mono text-gray-700" x-text="check.amount || '—'"></td>
                                                    <td class="px-3 py-1.5 font-mono text-gray-500" x-text="check.check_number || '—'"></td>
                                                    <td class="px-3 py-1.5 text-gray-600" x-text="check.campaign || '—'"></td>
                                                    <td class="px-3 py-1.5 text-gray-600" x-text="check.donation_type || '—'"></td>
                                                    <td class="px-3 py-1.5 text-gray-600" x-text="check.account || '—'"></td>
                                                    <td class="px-3 py-1.5 text-gray-600" x-text="check.class || check.class_name || '—'"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-2 flex flex-wrap gap-4 text-xs text-gray-500">
                                    <span x-show="sub.payload && sub.payload.notes" x-text="'Notes: ' + (sub.payload ? sub.payload.notes : '')"></span>
                                    <span x-show="sub.updated_at" x-text="'Last updated: ' + (sub.updated_at ? new Date(sub.updated_at).toLocaleString() : '')"></span>
                                    <span x-show="sub.qbo_pushed_at" class="text-green-600" x-text="'Pushed to QBO: ' + (sub.qbo_pushed_at ? new Date(sub.qbo_pushed_at).toLocaleString() : '')"></span>
                                </div>
                                <div x-show="sub.qbo_push_status === 'failed' && sub.qbo_push_error" class="mt-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2">
                                    <p class="text-xs font-medium text-red-700">QBO Push Error</p>
                                    <p class="mt-0.5 text-xs text-red-600" x-text="sub.qbo_push_error"></p>
                                </div>
                            </td>
                        </tr>
                </tbody>
            </template>
        </table>
        <div x-show="submissions.length === 0 && !loading" class="py-12 text-center text-sm text-gray-500">No deposits found.</div>
    </div>

    {{-- Pagination --}}
    <div x-show="!loading && (prevPageUrl || nextPageUrl)" class="mt-4 flex items-center justify-between text-sm">
        <button @click="prevPage()" :disabled="!prevPageUrl"
                class="rounded-lg border border-gray-300 px-4 py-2 text-gray-600 transition hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed">
            &larr; Previous
        </button>
        <span class="text-gray-500" x-text="'Page ' + currentPage"></span>
        <button @click="nextPage()" :disabled="!nextPageUrl"
                class="rounded-lg border border-gray-300 px-4 py-2 text-gray-600 transition hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed">
            Next &rarr;
        </button>
    </div>
</div>

@push('scripts')
<script>
function historyPanel() {
    return {
        submissions: [],
        loading: true,
        alert: '',
        isAdmin: @json((bool) auth()->user()->is_admin),
        isSuperAdmin: @json(auth()->user()->organization->is_super_admin ?? false),
        organizations: [],

        // Filters
        statusFilter: '',
        orgFilter: '',
        searchQuery: '',

        // Pagination
        currentPage: 1,
        nextPageUrl: null,
        prevPageUrl: null,

        // Expansion
        expanded: null,

        get csrfToken() {
            return document.querySelector('meta[name="csrf-token"]').content;
        },

        async init() {
            await this.loadSubmissions();
        },

        async loadSubmissions() {
            this.loading = true;
            const params = new URLSearchParams();
            if (this.statusFilter) params.set('status', this.statusFilter);
            if (this.orgFilter) params.set('organization_id', this.orgFilter);
            if (this.searchQuery) params.set('search', this.searchQuery);
            if (this.currentPage > 1) params.set('page', this.currentPage);

            try {
                const resp = await fetch('/api/submissions?' + params.toString(), {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await resp.json();

                // simplePaginate response
                const paginated = data.submissions;
                this.submissions = paginated.data || [];
                this.nextPageUrl = paginated.next_page_url || null;
                this.prevPageUrl = paginated.prev_page_url || null;
                this.currentPage = paginated.current_page || 1;

                if (data.organizations) this.organizations = data.organizations;
            } catch (e) {
                console.error('Failed to load submissions:', e);
                this.submissions = [];
            } finally {
                this.loading = false;
            }
        },

        async qboPush(sub) {
            if (!confirm('Push this deposit to QuickBooks Online? This will create Sales Receipts and a Deposit in QBO.')) return;
            sub._qboPushing = true;
            try {
                const resp = await fetch('/admin/api/submissions/' + sub.id + '/qbo-push', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                });
                const data = await resp.json();
                if (resp.ok && data.success) {
                    sub.qbo_push_status = data.qbo_push_status;
                    sub.qbo_push_result = data.qbo_push_result;
                    sub.qbo_pushed_at = new Date().toISOString();
                    this.alert = 'Successfully pushed to QuickBooks';
                } else {
                    sub.qbo_push_status = data.qbo_push_status || 'failed';
                    sub.qbo_push_error = data.error || 'Unknown error';
                }
            } catch (e) {
                sub.qbo_push_status = 'failed';
                sub.qbo_push_error = e.message;
            } finally {
                sub._qboPushing = false;
            }
        },

        async markStatus(submissionId, newStatus) {
            try {
                const resp = await fetch('/admin/api/submissions/' + submissionId + '/status', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ status: newStatus }),
                });
                if (resp.ok) {
                    const sub = this.submissions.find(s => s.id === submissionId);
                    if (sub) sub.status = newStatus;
                    this.alert = 'Status updated to ' + newStatus;
                } else {
                    const err = await resp.json();
                    alert(err.error || err.message || 'Failed to update status');
                }
            } catch (e) {
                alert('Failed to update status: ' + e.message);
            }
        },

        toggleExpand(id) {
            this.expanded = this.expanded === id ? null : id;
        },

        onSearchInput() {
            clearTimeout(this._searchTimer);
            this._searchTimer = setTimeout(() => {
                this.currentPage = 1;
                this.loadSubmissions();
            }, 300);
        },

        onFilterChange() {
            this.currentPage = 1;
            this.loadSubmissions();
        },

        nextPage() {
            if (this.nextPageUrl) {
                this.currentPage++;
                this.loadSubmissions();
            }
        },

        prevPage() {
            if (this.prevPageUrl && this.currentPage > 1) {
                this.currentPage--;
                this.loadSubmissions();
            }
        },
    };
}
</script>
@endpush
@endsection
