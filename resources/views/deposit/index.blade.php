@extends('layouts.erp', ['currentModule' => 'fundraising'])

@section('content')
<div x-data="depositFlow()" x-cloak>

    {{-- Page header --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">New Deposit</h1>
        <p class="mt-1 text-sm text-gray-500">Upload check images and review extracted data before submitting.</p>
    </div>

    {{-- Persistent org banner for super-admins (visible on all steps once org is selected) --}}
    <div x-show="isSuperAdmin && selectedClientOrgId && step <= 4" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="mb-4 flex items-center gap-3 rounded-lg border-2 border-secondary bg-secondary-soft px-5 py-3">
        <svg class="h-5 w-5 shrink-0 text-accent" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
        <span class="text-lg font-bold text-accent" x-text="clientOrgs.find(o => o.id == selectedClientOrgId)?.name"></span>
        <button x-show="step === 1" @click="selectedClientOrgId = ''" class="ml-auto text-xs text-secondary hover:text-accent transition">Change</button>
    </div>

    {{-- Stepper (clickable navigation) --}}
    <div class="mb-8 tab-group" x-show="step <= 4">
        <template x-for="(label, i) in ['Deposit Info', 'Check Photos', 'Verify Details', 'Categorize']" :key="i">
            <button @click="goToStep(i + 1)" :disabled="i + 1 > maxStep"
                 class="tab-item flex items-center gap-2"
                 :class="{
                     'tab-item-active': step === i + 1,
                     'cursor-pointer': i + 1 <= maxStep,
                     '!text-gray-500 !cursor-not-allowed': i + 1 > maxStep
                 }">
                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-[10px] font-bold"
                      :class="{
                          'bg-amber text-white': step === i + 1,
                          'bg-olive text-white': step !== i + 1 && i + 1 < step,
                          'bg-gray-200 text-gray-500': i + 1 > maxStep && step !== i + 1
                      }" x-text="i + 1 < step ? '✓' : i + 1"></span>
                <span x-text="label"></span>
            </button>
        </template>
    </div>

    {{-- Step 1: Deposit Info --}}
    <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="card p-6">
        <h2 class="mb-4 text-base font-bold text-gray-900">Deposit Information</h2>

        {{-- Super-admin org selector --}}
        <div x-show="isSuperAdmin" class="mb-5 rounded-lg border border-secondary/30 bg-secondary-soft p-4">
            <label class="mb-1.5 block text-sm font-semibold text-accent">Client Organization <span class="text-terra">*</span></label>
            <select x-model="selectedClientOrgId"
                    class="block w-full rounded-lg border border-secondary/40 bg-white px-3 py-2 text-sm font-medium focus:border-accent focus:ring-2 focus:ring-accent/20 focus:outline-none">
                <option value="">— Select organization —</option>
                <template x-for="org in clientOrgs" :key="org.id">
                    <option :value="org.id" x-text="org.name"></option>
                </template>
            </select>
            <p class="mt-1 text-xs text-accent/70">Deposit will be filed under the selected client organization.</p>
        </div>

        {{-- Same campaign checkbox --}}
        <div class="mb-5 rounded-lg border border-gray-200 bg-gray-50 p-4">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" x-model="sameCampaign" @change="if (sameCampaign) loadCampaignsEarly()"
                       class="h-4 w-4 rounded border-gray-300 text-accent focus:ring-accent/30">
                <span class="text-sm font-medium text-gray-700">All checks are from the same campaign</span>
            </label>
            <div x-show="sameCampaign" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="mt-3">
                <label class="mb-1 block text-sm font-medium text-gray-700">Campaign</label>
                <select x-model="selectedCampaignId" @change="onBatchCampaignChange($event)"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-accent focus:ring-2 focus:ring-accent/20 focus:outline-none">
                    <option value="">— Select campaign —</option>
                    <template x-for="c in campaigns" :key="c.id">
                        <option :value="c.id" x-text="c.name"></option>
                    </template>
                    <option value="__new__">+ Add new campaign</option>
                </select>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Deposit Date <span class="text-terra">*</span></label>
                <input type="date" x-model="depositDate" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-accent focus:ring-2 focus:ring-accent/20 focus:outline-none">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Number of Checks</label>
                <input type="number" x-model="expectedCount" min="0" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-accent focus:ring-2 focus:ring-accent/20 focus:outline-none" placeholder="Optional">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Expected Total</label>
                <input type="number" x-model="expectedTotal" step="0.01" min="0" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-accent focus:ring-2 focus:ring-accent/20 focus:outline-none" placeholder="Optional">
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1 block text-sm font-medium text-gray-700">Notes</label>
                <textarea x-model="notes" rows="2" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-accent focus:ring-2 focus:ring-accent/20 focus:outline-none" placeholder="Optional notes for this deposit"></textarea>
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <button @click="advanceStep(2)" :disabled="!depositDate || (isSuperAdmin && !selectedClientOrgId)"
                    class="rounded-lg bg-accent px-5 py-2.5 text-sm font-medium text-white transition hover:bg-accent-hover disabled:opacity-50">
                Continue
            </button>
        </div>
    </div>

    {{-- Step 2: Upload --}}
    <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        {{-- Upload instructions --}}
        <div class="card mb-6 px-6 py-6">
            <div class="mx-auto max-w-2xl space-y-6">
                {{-- Tip 1: Individual uploads — two overlapping pages with down arrows --}}
                <div class="flex items-start gap-5">
                    <div class="flex-shrink-0 pt-0.5">
                        <svg class="h-10 w-10 text-olive" viewBox="0 0 48 48" fill="none">
                            {{-- Back page --}}
                            <rect x="14" y="4" width="22" height="28" rx="3" stroke="currentColor" stroke-width="2" fill="white"/>
                            <line x1="19" y1="12" x2="31" y2="12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" opacity="0.4"/>
                            <line x1="19" y1="17" x2="31" y2="17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" opacity="0.4"/>
                            <line x1="19" y1="22" x2="27" y2="22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" opacity="0.4"/>
                            {{-- Front page --}}
                            <rect x="10" y="8" width="22" height="28" rx="3" stroke="currentColor" stroke-width="2" fill="white"/>
                            <line x1="15" y1="16" x2="27" y2="16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <line x1="15" y1="21" x2="27" y2="21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <line x1="15" y1="26" x2="23" y2="26" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            {{-- Down arrows --}}
                            <path d="M17 40 l0 -5 m0 5 l-2.5 -3 m2.5 3 l2.5 -3" stroke="#7A9178" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M27 40 l0 -5 m0 5 l-2.5 -3 m2.5 3 l2.5 -3" stroke="#7A9178" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">You can upload individual photos of up to 10 checks.</h3>
                        <p class="mt-1 text-sm text-gray-500">Take or upload a separate photo of each check. Do not upload multiple checks in a single image. Each check must be submitted as its own file.</p>
                    </div>
                </div>

                {{-- Tip 2: Cover sensitive info — check with redaction bar --}}
                <div class="flex items-start gap-5">
                    <div class="flex-shrink-0 pt-0.5">
                        <svg class="h-10 w-10 text-terra" viewBox="0 0 48 48" fill="none">
                            {{-- Check body --}}
                            <rect x="4" y="8" width="40" height="26" rx="3" stroke="currentColor" stroke-width="2" fill="white"/>
                            {{-- Check lines --}}
                            <line x1="9" y1="15" x2="25" y2="15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <line x1="9" y1="20" x2="30" y2="20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            {{-- Amount box --}}
                            <rect x="33" y="13" width="7" height="5" rx="1" stroke="currentColor" stroke-width="1.2"/>
                            {{-- Redaction bar over MICR line --}}
                            <rect x="8" y="27" width="32" height="4" rx="1" fill="#9E7870" opacity="0.2"/>
                            <rect x="8" y="27" width="32" height="4" rx="1" stroke="#9E7870" stroke-width="1.5" fill="none"/>
                            {{-- X marks over the blocked area --}}
                            <line x1="12" y1="27.5" x2="16" y2="30.5" stroke="#9E7870" stroke-width="1.2" stroke-linecap="round"/>
                            <line x1="16" y1="27.5" x2="12" y2="30.5" stroke="#9E7870" stroke-width="1.2" stroke-linecap="round"/>
                            <line x1="22" y1="27.5" x2="26" y2="30.5" stroke="#9E7870" stroke-width="1.2" stroke-linecap="round"/>
                            <line x1="26" y1="27.5" x2="22" y2="30.5" stroke="#9E7870" stroke-width="1.2" stroke-linecap="round"/>
                            <line x1="32" y1="27.5" x2="36" y2="30.5" stroke="#9E7870" stroke-width="1.2" stroke-linecap="round"/>
                            <line x1="36" y1="27.5" x2="32" y2="30.5" stroke="#9E7870" stroke-width="1.2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Cover Account and Routing Numbers</h3>
                        <p class="mt-1 text-sm text-gray-500">Before taking photos, cover the account number and routing number at the bottom of each check. This protects your sensitive banking information.</p>
                    </div>
                </div>

                {{-- Tip 3: Photo quality — landscape image with checkmark --}}
                <div class="flex items-start gap-5">
                    <div class="flex-shrink-0 pt-0.5">
                        <svg class="h-10 w-10 text-amber" viewBox="0 0 48 48" fill="none">
                            {{-- Image frame --}}
                            <rect x="4" y="8" width="34" height="28" rx="3" stroke="currentColor" stroke-width="2" fill="white"/>
                            {{-- Mountain / landscape --}}
                            <path d="M4 30 l10 -10 l6 6 l4 -4 l14 12" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" fill="none"/>
                            {{-- Sun --}}
                            <circle cx="30" cy="16" r="3" stroke="currentColor" stroke-width="1.5"/>
                            {{-- Checkmark badge --}}
                            <circle cx="38" cy="36" r="7" fill="#B8906A" stroke="white" stroke-width="2"/>
                            <path d="M34.5 36 l2 2 l4.5 -4.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Ensure Clear, Readable Photos</h3>
                        <p class="mt-1 text-sm text-gray-500">Make sure your photos are well-lit and in focus. All visible information on the check should be clearly readable, except for the covered account and routing numbers.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <h2 class="mb-4 text-base font-bold text-gray-900">Upload Check Photos</h2>

            {{-- Drop zone --}}
            <div class="relative rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-8 text-center transition hover:border-accent hover:bg-accent-soft"
                 @dragover.prevent="$el.classList.add('border-accent', 'bg-accent-soft')"
                 @dragleave.prevent="$el.classList.remove('border-accent', 'bg-accent-soft')"
                 @drop.prevent="$el.classList.remove('border-accent', 'bg-accent-soft'); handleDrop($event)">
                <svg class="mx-auto h-10 w-10 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                <p class="mt-2 text-sm text-gray-600">Drag & drop check images here, or</p>
                <label class="mt-2 inline-block cursor-pointer rounded-lg border border-gray-300 bg-white px-4 py-1.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50">
                    Browse files
                    <input type="file" class="hidden" accept="image/*" multiple @change="handleFiles($event.target.files)">
                </label>
                <p class="mt-2 text-xs text-gray-500">JPG, PNG, or HEIC</p>
            </div>

            {{-- File list --}}
            <div class="mt-4 space-y-2" x-show="uploadedFiles.length > 0">
                <template x-for="(f, i) in uploadedFiles" :key="i">
                    <div class="flex items-center gap-3 rounded-lg border border-gray-100 bg-gray-50 px-3 py-2">
                        <img :src="f.preview" class="h-10 w-10 rounded object-cover">
                        <span class="flex-1 truncate text-sm text-gray-700" x-text="f.file.name"></span>
                        <button @click="uploadedFiles.splice(i, 1)" class="text-gray-500 transition hover:text-terra">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
            </div>

            {{-- Progress --}}
            <div x-show="scanning" class="mt-4" x-cloak>
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <span>Scanning checks...</span>
                    <span x-text="Math.round(scanProgress) + '%'"></span>
                </div>
                <div class="mt-1 h-2 overflow-hidden rounded-full bg-gray-200">
                    <div class="h-full rounded-full bg-accent transition-all" :style="'width:' + scanProgress + '%'"></div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between">
                <button @click="step = 1" class="text-sm text-gray-500 transition hover:text-gray-700">&larr; Back</button>
                <button @click="runScan()" :disabled="uploadedFiles.length === 0 || scanning"
                        class="rounded-lg bg-accent px-5 py-2.5 text-sm font-medium text-white transition hover:bg-accent-hover disabled:opacity-50">
                    <span x-text="scanning ? 'Scanning...' : 'Scan All Checks'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- Step 3: Verify Check Details --}}
    <div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="card">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-xs font-medium uppercase tracking-wider text-gray-500">
                            <th class="px-3 py-3 w-8">#</th>
                            <th class="px-3 py-3">Image</th>
                            <th class="px-3 py-3">Payee / Donor</th>
                            <th class="px-3 py-3">Amount</th>
                            <th class="px-3 py-3">Check #</th>
                            <th class="px-3 py-3">Date</th>
                            <th class="px-3 py-3">Memo</th>
                            <th class="px-3 py-3 w-8"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="(check, idx) in extractedChecks" :key="idx">
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-3 py-2 text-gray-500" x-text="idx + 1"></td>
                                <td class="px-3 py-2">
                                    <img :src="check._thumbUrl" @click="lightboxUrl = check._thumbUrl" class="h-8 w-12 cursor-pointer rounded object-cover transition hover:opacity-80">
                                </td>
                                <td class="px-3 py-2"><input x-model="check.payee" class="w-full min-w-[140px] rounded border border-transparent bg-transparent px-1.5 py-1 text-sm transition focus:border-accent focus:bg-white focus:ring-1 focus:ring-accent/20"></td>
                                <td class="px-3 py-2"><input x-model="check.amount" class="w-24 rounded border border-transparent bg-transparent px-1.5 py-1 font-mono text-sm transition focus:border-accent focus:bg-white focus:ring-1 focus:ring-accent/20"></td>
                                <td class="px-3 py-2"><input x-model="check.check_number" class="w-20 rounded border border-transparent bg-transparent px-1.5 py-1 font-mono text-sm transition focus:border-accent focus:bg-white focus:ring-1 focus:ring-accent/20"></td>
                                <td class="px-3 py-2"><input x-model="check.date" class="w-28 rounded border border-transparent bg-transparent px-1.5 py-1 text-sm transition focus:border-accent focus:bg-white focus:ring-1 focus:ring-accent/20"></td>
                                <td class="px-3 py-2"><input x-model="check.memo" class="w-full min-w-[100px] rounded border border-transparent bg-transparent px-1.5 py-1 text-sm transition focus:border-accent focus:bg-white focus:ring-1 focus:ring-accent/20"></td>
                                <td class="px-3 py-2">
                                    <button @click="extractedChecks.splice(idx, 1)" class="text-gray-500 transition hover:text-terra">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Verification bar --}}
        <div class="mt-4 rounded-lg border px-5 py-3"
             :class="verifyMatch ? 'border-olive/30 bg-olive-soft' : (!expectedCount && !expectedTotal ? 'border-gray-200 bg-gray-50' : 'border-terra/30 bg-terra-soft')">
            <div class="flex flex-wrap items-center gap-x-8 gap-y-2 text-sm">
                {{-- Check count --}}
                <div class="flex items-center gap-2">
                    <template x-if="countMatch && expectedCount">
                        <svg class="h-4.5 w-4.5 shrink-0 text-olive" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                    </template>
                    <template x-if="!countMatch && expectedCount">
                        <svg class="h-4.5 w-4.5 shrink-0 text-terra" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </template>
                    <template x-if="!expectedCount">
                        <svg class="h-4.5 w-4.5 shrink-0 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/></svg>
                    </template>
                    <span class="font-semibold" :class="countMatch ? 'text-[#4d6a4b]' : (expectedCount ? 'text-[#7a5248]' : 'text-gray-700')"
                          x-text="extractedChecks.length + ' check(s)'"></span>
                    <span class="text-gray-500">·</span>
                    <span :class="expectedCount ? (countMatch ? 'text-olive' : 'text-terra') : 'text-gray-500'">Expected</span>
                    <input type="number" x-model="expectedCount" min="0" placeholder="—"
                           class="w-14 rounded border px-1.5 py-0.5 text-sm font-medium text-center transition focus:outline-none focus:ring-1"
                           :class="!expectedCount ? 'border-gray-300 bg-white text-gray-500 focus:border-accent focus:ring-accent/20' : (countMatch ? 'border-olive/40 bg-olive-soft text-[#4d6a4b] focus:border-olive focus:ring-olive/20' : 'border-terra/40 bg-white text-[#7a5248] focus:border-terra focus:ring-terra/20')">
                </div>

                <div class="text-gray-500">|</div>

                {{-- Dollar total --}}
                <div class="flex items-center gap-2">
                    <template x-if="totalMatch && expectedTotal">
                        <svg class="h-4.5 w-4.5 shrink-0 text-olive" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                    </template>
                    <template x-if="!totalMatch && expectedTotal">
                        <svg class="h-4.5 w-4.5 shrink-0 text-terra" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </template>
                    <template x-if="!expectedTotal">
                        <svg class="h-4.5 w-4.5 shrink-0 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/></svg>
                    </template>
                    <span class="font-semibold font-mono" :class="totalMatch ? 'text-[#4d6a4b]' : (expectedTotal ? 'text-[#7a5248]' : 'text-gray-700')"
                          x-text="'$' + totalAmount.toFixed(2)"></span>
                    <span class="text-gray-500">·</span>
                    <span :class="expectedTotal ? (totalMatch ? 'text-olive' : 'text-terra') : 'text-gray-500'">Expected $</span>
                    <input type="number" x-model="expectedTotal" step="0.01" min="0" placeholder="—"
                           class="w-24 rounded border px-1.5 py-0.5 text-sm font-medium font-mono text-center transition focus:outline-none focus:ring-1"
                           :class="!expectedTotal ? 'border-gray-300 bg-white text-gray-500 focus:border-accent focus:ring-accent/20' : (totalMatch ? 'border-olive/40 bg-olive-soft text-[#4d6a4b] focus:border-olive focus:ring-olive/20' : 'border-terra/40 bg-white text-[#7a5248] focus:border-terra focus:ring-terra/20')">
                </div>

                {{-- Mismatch label --}}
                <div x-show="!verifyMatch" class="ml-auto flex items-center gap-1.5 text-terra font-medium">
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126Z"/></svg>
                    Mismatch
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between">
            <button @click="step = 2" class="text-sm text-gray-500 transition hover:text-gray-700">&larr; Back</button>
            <button @click="runCategorization()" :disabled="categorizing || extractedChecks.length === 0 || !verifyMatch"
                    class="rounded-lg bg-accent px-6 py-2.5 text-sm font-medium text-white transition hover:bg-accent-hover disabled:opacity-50">
                <span x-show="!categorizing">Continue to Categorize</span>
                <span x-show="categorizing" class="flex items-center gap-2">
                    <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="3" stroke-linecap="round" class="opacity-75"/></svg>
                    Analyzing...
                </span>
            </button>
        </div>
    </div>

    {{-- Step 4: Categorize --}}
    <div x-show="step === 4" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        {{-- Batch info + suggestions banner --}}
        <div x-show="!categorizationError" class="mb-4 rounded-lg border border-secondary/30 bg-secondary-soft px-4 py-3 text-sm text-accent/80">
            <div class="flex flex-wrap items-center gap-x-6 gap-y-1">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456Z"/></svg>
                    <span><strong>Suggestions Applied, Need Review</strong></span>
                </div>
                <div class="text-secondary">|</div>
                <div><strong>Uploaded By:</strong> {{ auth()->user()->name }}</div>
            </div>
        </div>
        {{-- Error state: categorization unavailable --}}
        <div x-show="categorizationError" class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
            <div class="flex flex-wrap items-center gap-x-6 gap-y-1">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                    <span><strong>Suggestions unavailable</strong> — please categorize checks manually.</span>
                </div>
                <div class="text-amber-300">|</div>
                <div><strong>Uploaded By:</strong> {{ auth()->user()->name }}</div>
            </div>
        </div>

        {{-- Count / total summary --}}
        <div class="mb-4 flex items-center gap-4 text-sm text-gray-500">
            <span x-text="extractedChecks.length + ' check(s)'"></span>
            <span class="font-mono font-medium text-gray-700" x-text="'$' + totalAmount.toFixed(2)"></span>
        </div>

        <div class="card">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-xs font-medium uppercase tracking-wider text-gray-500">
                            <th class="px-3 py-3 w-8">#</th>
                            <th class="px-3 py-3">Payee / Donor</th>
                            <th class="px-3 py-3">Amount</th>
                            <th class="px-3 py-3">
                                <div class="flex items-center gap-1.5">
                                    Campaign
                                    <button x-show="sameCampaign && !editCampaignsIndividually" @click="editCampaignsIndividually = true" class="text-secondary hover:text-accent" title="Edit individually">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                    </button>
                                </div>
                            </th>
                            <th class="px-3 py-3">Type</th>
                            <th class="px-3 py-3">
                                <div class="flex items-center gap-1.5">
                                    Fund
                                    <button x-show="defaultClass && !editFundsIndividually" @click="editFundsIndividually = true" class="text-secondary hover:text-accent" title="Edit individually">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                    </button>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="(check, idx) in extractedChecks" :key="idx">
                            <tr class="hover:bg-gray-50/50" :class="check._aiConfidence >= 0.7 ? '' : 'bg-amber-50/30'">
                                <td class="px-3 py-2 text-gray-500" x-text="idx + 1"></td>
                                <td class="px-3 py-2">
                                    <div class="font-medium text-gray-900" x-text="check.payee || '—'"></div>
                                    <div class="text-xs text-gray-500" x-text="check.memo ? 'Memo: ' + check.memo : ''"></div>
                                </td>
                                <td class="px-3 py-2 font-mono text-gray-700" x-text="check.amount || '—'"></td>
                                <td class="px-3 py-2">
                                    {{-- Locked display when same-campaign applied --}}
                                    <div x-show="sameCampaign && !editCampaignsIndividually" class="flex items-center gap-1.5 text-sm text-gray-600">
                                        <svg class="h-3.5 w-3.5 shrink-0 text-secondary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                                        <span x-text="selectedCampaignName" class="truncate"></span>
                                    </div>
                                    {{-- Editable dropdown --}}
                                    <select x-show="!sameCampaign || editCampaignsIndividually" x-model="check.campaign_id" @change="onCampaignChange(idx, $event)" class="w-full min-w-[130px] rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 pr-8 text-sm transition focus:border-accent focus:ring-2 focus:ring-accent/20 focus:outline-none">
                                        <option value="">— Select —</option>
                                        <template x-for="c in campaigns" :key="c.id">
                                            <option :value="c.id" x-text="c.name"></option>
                                        </template>
                                        <option value="__new__">+ Add new</option>
                                    </select>
                                </td>
                                <td class="px-3 py-2">
                                    <select x-model="check.account" @change="onAccountChange(idx, $event)" class="w-full min-w-[130px] rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 pr-8 text-sm transition focus:border-accent focus:ring-2 focus:ring-accent/20 focus:outline-none">
                                        <option value="">— Select —</option>
                                        <template x-for="a in accounts" :key="a.id">
                                            <option :value="a.name" x-text="a.name"></option>
                                        </template>
                                        <option value="__new__">+ Add new</option>
                                    </select>
                                </td>
                                <td class="px-3 py-2">
                                    {{-- Locked display when default fund applied --}}
                                    <div x-show="defaultClass && !editFundsIndividually" class="flex items-center gap-1.5 text-sm text-gray-600">
                                        <svg class="h-3.5 w-3.5 shrink-0 text-secondary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                                        <span x-text="check.class_name" class="truncate"></span>
                                    </div>
                                    {{-- Editable dropdown --}}
                                    <select x-show="!defaultClass || editFundsIndividually" x-model="check.class_name" @change="onClassChange(idx, $event)" class="w-full min-w-[130px] rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 pr-8 text-sm transition focus:border-accent focus:ring-2 focus:ring-accent/20 focus:outline-none">
                                        <option value="">— Select —</option>
                                        <template x-for="cl in classes" :key="cl.id">
                                            <option :value="cl.name" x-text="cl.name"></option>
                                        </template>
                                        <option value="__new__">+ Add new</option>
                                    </select>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between">
            <button @click="step = 3" class="text-sm text-gray-500 transition hover:text-gray-700">&larr; Back to Details</button>
            <button @click="submitBatch()" :disabled="submitting || extractedChecks.length === 0"
                    class="rounded-lg bg-accent px-6 py-2.5 text-sm font-medium text-white transition hover:bg-accent-hover disabled:opacity-50">
                <span x-text="submitting ? 'Submitting...' : 'Submit Deposit'"></span>
            </button>
        </div>
    </div>

    {{-- Step 5: Success --}}
    <div x-show="step === 5" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="py-12 text-center">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-olive-soft">
            <svg class="h-8 w-8 text-olive" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900">Deposit Submitted!</h2>
        <p class="mt-1 text-sm text-gray-500">Your check batch has been processed successfully.</p>
        <div class="mt-6 inline-flex gap-8 rounded-lg border border-gray-200 bg-white px-6 py-4 text-sm">
            <div><span class="text-gray-500">Checks:</span> <strong x-text="resultData.check_count"></strong></div>
            <div><span class="text-gray-500">Total:</span> <strong x-text="'$' + resultData.total_amount"></strong></div>
            <div><span class="text-gray-500">Batch:</span> <strong class="font-mono text-xs" x-text="resultData.batch_id"></strong></div>
        </div>
        <div class="mt-8">
            <button @click="resetForm()" class="rounded-lg bg-accent px-6 py-2.5 text-sm font-medium text-white transition hover:bg-accent-hover">
                Start New Deposit
            </button>
        </div>
    </div>

    {{-- Lightbox --}}
    <div x-show="lightboxUrl" @click="lightboxUrl = ''" x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-8" x-cloak>
        <img :src="lightboxUrl" class="max-h-full max-w-full rounded-lg shadow-2xl">
    </div>
</div>

@push('scripts')
<script>
function depositFlow() {
    return {
        step: 1,
        maxStep: 1,
        depositDate: new Date().toISOString().split('T')[0],
        expectedCount: '',
        expectedTotal: '',
        notes: '',
        uploadedFiles: [],
        extractedChecks: [],
        campaigns: [],
        classes: [],
        accounts: [],
        defaultClass: @json(auth()->user()->default_class ?? ''),
        defaultAccount: @json(auth()->user()->default_account ?? ''),
        scanning: false,
        scanProgress: 0,
        categorizing: false,
        categorizationError: false,
        submitting: false,
        lightboxUrl: '',
        resultData: {},

        // Same-campaign batch option
        sameCampaign: false,
        selectedCampaignId: '',
        selectedCampaignName: '',
        editCampaignsIndividually: false,
        editFundsIndividually: false,

        // Super-admin org switching
        isSuperAdmin: @json(auth()->user()->organization->is_super_admin ?? false),
        clientOrgs: [],
        selectedClientOrgId: '',

        async init() {
            if (this.isSuperAdmin) {
                try {
                    const resp = await fetch('/api/organizations');
                    const data = await resp.json();
                    this.clientOrgs = data.organizations || [];
                } catch (e) {
                    this.clientOrgs = [];
                }
            }
        },

        get effectiveOrgParam() {
            return this.isSuperAdmin && this.selectedClientOrgId
                ? '?organization_id=' + this.selectedClientOrgId
                : '';
        },

        goToStep(n) {
            if (n <= this.maxStep) this.step = n;
        },

        advanceStep(n) {
            this.step = n;
            if (n > this.maxStep) this.maxStep = n;
        },

        get csrfToken() {
            return document.querySelector('meta[name="csrf-token"]').content;
        },

        get totalAmount() {
            return this.extractedChecks.reduce((sum, c) => {
                const n = parseFloat((c.amount || '').replace(/[^0-9.\-]/g, ''));
                return sum + (isNaN(n) ? 0 : n);
            }, 0);
        },

        get countMatch() {
            return !this.expectedCount || parseInt(this.expectedCount) === this.extractedChecks.length;
        },

        get totalMatch() {
            return !this.expectedTotal || Math.abs(parseFloat(this.expectedTotal) - this.totalAmount) < 0.01;
        },

        get verifyMatch() {
            return this.countMatch && this.totalMatch;
        },

        async apiPost(url, body) {
            const resp = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify(body),
            });
            const data = await resp.json();
            if (!resp.ok) throw new Error(data.error || data.message || `HTTP ${resp.status}`);
            return data;
        },

        handleDrop(e) {
            this.handleFiles(e.dataTransfer.files);
        },

        handleFiles(fileList) {
            for (const file of fileList) {
                if (!file.type.startsWith('image/')) continue;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.uploadedFiles.push({
                        file,
                        base64: e.target.result.split(',')[1],
                        preview: e.target.result,
                        mediaType: file.type || 'image/jpeg',
                    });
                };
                reader.readAsDataURL(file);
            }
        },

        async runScan() {
            this.scanning = true;
            this.extractedChecks = [];
            const userName = @json(auth()->user()->name);

            for (let i = 0; i < this.uploadedFiles.length; i++) {
                this.scanProgress = ((i + 1) / this.uploadedFiles.length) * 100;
                try {
                    const ocrBody = {
                        image_base64: this.uploadedFiles[i].base64,
                        media_type: this.uploadedFiles[i].mediaType,
                    };
                    if (this.isSuperAdmin && this.selectedClientOrgId) ocrBody.organization_id = this.selectedClientOrgId;
                    const result = await this.apiPost('/api/ocr', ocrBody);
                    result._thumbUrl = this.uploadedFiles[i].preview;
                    result.uploaded_by = userName;
                    result.class_name = this.defaultClass;
                    result.account = this.defaultAccount;
                    result.campaign_id = this.sameCampaign ? this.selectedCampaignId : '';
                    result.campaign = this.sameCampaign ? this.selectedCampaignName : '';
                    result._aiConfidence = 0;
                    this.extractedChecks.push(result);
                } catch (err) {
                    this.extractedChecks.push({
                        payee: 'Could not read', amount: '', check_number: '', date: '', memo: err.message,
                        _thumbUrl: this.uploadedFiles[i].preview, uploaded_by: userName,
                        class_name: this.defaultClass, account: this.defaultAccount,
                        campaign_id: this.sameCampaign ? this.selectedCampaignId : '',
                        campaign: this.sameCampaign ? this.selectedCampaignName : '',
                        _aiConfidence: 0,
                    });
                }
            }
            this.scanning = false;
            await this.loadLookups();
            this.applyDefaults();
            this.advanceStep(3);
        },

        async runCategorization() {
            this.categorizing = true;
            this.categorizationError = false;

            try {
                const checksPayload = this.extractedChecks.map(c => ({
                    payee: c.payee || '',
                    amount: c.amount || '',
                    memo: c.memo || '',
                    check_number: c.check_number || '',
                }));

                const catBody = { checks: checksPayload };
                if (this.isSuperAdmin && this.selectedClientOrgId) catBody.organization_id = this.selectedClientOrgId;
                const data = await this.apiPost('/api/categorize', catBody);

                if (data.success && Array.isArray(data.suggestions) && data.suggestions.length > 0) {
                    // Apply AI suggestions — only fill empty fields
                    data.suggestions.forEach((suggestion, i) => {
                        if (i >= this.extractedChecks.length) return;
                        const check = this.extractedChecks[i];

                        if (!check.campaign_id && suggestion.campaign_id) {
                            check.campaign_id = String(suggestion.campaign_id);
                            const camp = this.campaigns.find(c => String(c.id) === String(suggestion.campaign_id));
                            check.campaign = camp ? camp.name : '';
                        }
                        if (!check.account && suggestion.account) {
                            check.account = suggestion.account;
                        }
                        if (!check.class_name && suggestion.class_name) {
                            check.class_name = suggestion.class_name;
                        }
                        check._aiConfidence = suggestion.confidence || 0;
                    });
                } else {
                    this.categorizationError = true;
                }
            } catch (err) {
                console.error('Categorization failed:', err);
                this.categorizationError = true;
            } finally {
                this.categorizing = false;
                this.advanceStep(4);
            }
        },

        async loadLookups() {
            try {
                const qs = this.effectiveOrgParam;
                const [c, cl, a] = await Promise.all([
                    fetch('/api/campaigns' + qs).then(r => r.json()),
                    fetch('/api/classes' + qs).then(r => r.json()),
                    fetch('/api/accounts' + qs).then(r => r.json()),
                ]);
                this.campaigns = c.campaigns;
                this.classes = cl.classes;
                this.accounts = a.accounts;
            } catch (e) {
                this.campaigns = []; this.classes = []; this.accounts = [];
            }
        },

        applyDefaults() {
            // Re-apply user defaults after lookups are loaded so select options match
            this.extractedChecks.forEach(check => {
                if (!check.class_name && this.defaultClass) {
                    check.class_name = this.defaultClass;
                }
                if (!check.account && this.defaultAccount) {
                    check.account = this.defaultAccount;
                }
            });
        },

        async loadCampaignsEarly() {
            if (this.campaigns.length > 0) return;
            try {
                const qs = this.effectiveOrgParam;
                const data = await fetch('/api/campaigns' + qs).then(r => r.json());
                this.campaigns = data.campaigns || [];
            } catch (e) {
                this.campaigns = [];
            }
        },

        async onBatchCampaignChange(e) {
            if (e.target.value === '__new__') {
                const name = prompt('New campaign name:');
                if (!name) { this.selectedCampaignId = ''; return; }
                try {
                    const data = await this.apiPost('/api/campaigns', this.quickAddBody(name));
                    if (!this.campaigns.find(c => c.id === data.campaign.id)) this.campaigns.push(data.campaign);
                    this.selectedCampaignId = String(data.campaign.id);
                    this.selectedCampaignName = data.campaign.name;
                } catch (err) { alert(err.message); this.selectedCampaignId = ''; }
            } else {
                const camp = this.campaigns.find(c => String(c.id) === String(e.target.value));
                this.selectedCampaignName = camp ? camp.name : '';
            }
        },

        quickAddBody(name) {
            const body = { name };
            if (this.isSuperAdmin && this.selectedClientOrgId) body.organization_id = this.selectedClientOrgId;
            return body;
        },

        async onCampaignChange(idx, e) {
            if (e.target.value === '__new__') {
                const name = prompt('New campaign name:');
                if (!name) { this.extractedChecks[idx].campaign_id = ''; return; }
                try {
                    const data = await this.apiPost('/api/campaigns', this.quickAddBody(name));
                    if (!this.campaigns.find(c => c.id === data.campaign.id)) this.campaigns.push(data.campaign);
                    this.extractedChecks[idx].campaign_id = data.campaign.id;
                    this.extractedChecks[idx].campaign = data.campaign.name;
                } catch (err) { alert(err.message); this.extractedChecks[idx].campaign_id = ''; }
            } else {
                const camp = this.campaigns.find(c => String(c.id) === String(e.target.value));
                this.extractedChecks[idx].campaign = camp ? camp.name : '';
            }
        },

        async onClassChange(idx, e) {
            if (e.target.value === '__new__') {
                const name = prompt('New class name:');
                if (!name) { this.extractedChecks[idx].class_name = ''; return; }
                try {
                    const data = await this.apiPost('/api/classes', this.quickAddBody(name));
                    if (!this.classes.find(c => c.id === data.class.id)) this.classes.push(data.class);
                    this.extractedChecks[idx].class_name = data.class.name;
                } catch (err) { alert(err.message); this.extractedChecks[idx].class_name = ''; }
            }
        },

        async onAccountChange(idx, e) {
            if (e.target.value === '__new__') {
                const name = prompt('New account name:');
                if (!name) { this.extractedChecks[idx].account = ''; return; }
                try {
                    const data = await this.apiPost('/api/accounts', this.quickAddBody(name));
                    if (!this.accounts.find(a => a.id === data.account.id)) this.accounts.push(data.account);
                    this.extractedChecks[idx].account = data.account.name;
                } catch (err) { alert(err.message); this.extractedChecks[idx].account = ''; }
            }
        },

        async submitBatch() {
            this.submitting = true;
            const batchId = 'CB-' + Date.now().toString(36).toUpperCase() + '-' + Math.random().toString(36).substring(2, 6).toUpperCase();
            const checks = this.extractedChecks.map(c => ({
                payee: c.payee || '', amount: c.amount || '', check_number: c.check_number || '',
                date: c.date || '', memo: c.memo || '',
                campaign: c.campaign || '', campaign_id: c.campaign_id || null,
                uploaded_by: c.uploaded_by || '',
                account: c.account || '', class: c.class_name || '',
            }));

            try {
                const submitBody = {
                    batch_id: batchId, deposit_date: this.depositDate,
                    campaign_name: null, expected_count: this.expectedCount || null,
                    expected_total: this.expectedTotal || null, notes: this.notes || null,
                    checks,
                };
                if (this.isSuperAdmin && this.selectedClientOrgId) submitBody.organization_id = this.selectedClientOrgId;
                const data = await this.apiPost('/api/submit', submitBody);
                this.resultData = data;
                this.advanceStep(5);
            } catch (err) {
                alert('Submit failed: ' + err.message);
            } finally {
                this.submitting = false;
            }
        },

        resetForm() {
            this.step = 1;
            this.maxStep = 1;
            this.depositDate = new Date().toISOString().split('T')[0];
            this.expectedCount = '';
            this.expectedTotal = '';
            this.notes = '';
            this.uploadedFiles = [];
            this.extractedChecks = [];
            this.campaigns = [];
            this.classes = [];
            this.accounts = [];
            this.scanProgress = 0;
            this.categorizing = false;
            this.categorizationError = false;
            this.resultData = {};
            this.sameCampaign = false;
            this.selectedCampaignId = '';
            this.selectedCampaignName = '';
            this.editCampaignsIndividually = false;
            this.editFundsIndividually = false;
            this.selectedClientOrgId = '';
        },
    };
}
</script>
@endpush
@endsection
