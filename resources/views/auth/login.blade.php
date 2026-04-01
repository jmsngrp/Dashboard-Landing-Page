@extends('layouts.guest')

@section('content')
<div class="flex min-h-screen items-center justify-center px-4" x-data="loginForm()">
    <div class="w-full max-w-sm">
        {{-- Logo --}}
        <div class="mb-8 flex items-center justify-center gap-3">
            <svg width="28" height="28" viewBox="0 0 28 28" xmlns="http://www.w3.org/2000/svg">
                <rect x="0" y="4" width="28" height="5" rx="2.5" fill="#1C1B30"/>
                <rect x="0" y="12" width="22" height="5" rx="2.5" fill="#9E7870" opacity="0.7"/>
                <rect x="0" y="20" width="14" height="5" rx="2.5" fill="#9E7870" opacity="0.4"/>
            </svg>
            <span class="text-2xl tracking-tight brand-gradient-text" style="font-family:'Lato',sans-serif; letter-spacing:0.5px;"><span style="font-weight:400;">Foundry</span><span style="font-weight:400; margin-left:6px; display:inline-block;">OS</span></span>
        </div>

        <div class="relative">
            {{-- Decorative shadow layer behind card --}}
            <div class="absolute top-2 left-3 h-full w-full rounded-2xl bg-gray-300/[0.18]"></div>
        <div class="relative rounded-2xl border border-gray-200/60 bg-white p-8 shadow-xl shadow-black/[0.03]">
            {{-- Step 1: Email --}}
            <div x-show="!codeSent" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <h1 class="text-center text-xl text-gray-900">Welcome back</h1>
                <p class="mb-6 mt-1 text-center text-base font-medium text-gray-800">Enter your email to receive a login code</p>

                <div x-show="error" x-text="error" class="mb-4 rounded-xl bg-red-50 px-4 py-2.5 text-base text-red-600" x-cloak></div>

                <label class="mb-1.5 block text-base font-bold text-gray-900">Email address</label>
                <input type="email" x-model="email" @keydown.enter="sendCode()"
                       class="mb-5 block w-full rounded-xl border border-gray-200 px-4 py-3 text-base shadow-sm transition focus:border-[#AEACBC] focus:ring-4 focus:ring-[#AEACBC]/10 focus:outline-none"
                       placeholder="you@company.com" autofocus>

                <button @click="sendCode()" :disabled="loading"
                        class="btn-primary flex w-full items-center justify-center px-4 py-3 text-base">
                    <span x-show="!loading">Send login code</span>
                    <span x-show="loading" class="flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="3" stroke-linecap="round" class="opacity-75"/></svg>
                        Sending...
                    </span>
                </button>
            </div>

            {{-- Step 2: Code --}}
            <div x-show="codeSent" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak>
                <h1 class="text-center text-xl text-gray-900">Check your email</h1>
                <p class="mb-6 mt-1 text-center text-base font-medium text-gray-800">
                    We sent a code to <span class="font-medium text-gray-900" x-text="email"></span>
                </p>

                <div x-show="error" x-text="error" class="mb-4 rounded-xl bg-red-50 px-4 py-2.5 text-base text-red-600" x-cloak></div>

                {{-- 6 digit inputs --}}
                <div class="mb-6 flex justify-center gap-2">
                    <template x-for="(_, i) in 6" :key="i">
                        <input type="text" maxlength="1" inputmode="numeric"
                               :id="'code-' + i"
                               x-model="digits[i]"
                               @input="onDigitInput(i, $event)"
                               @keydown.backspace="onBackspace(i, $event)"
                               @paste.prevent="onPaste($event)"
                               class="h-13 w-11 rounded-xl border border-gray-200 text-center font-mono text-lg font-bold shadow-sm transition focus:border-[#AEACBC] focus:ring-4 focus:ring-[#AEACBC]/10 focus:outline-none">
                    </template>
                </div>

                <button @click="verifyCode()" :disabled="loading || digits.join('').length < 6"
                        class="btn-primary flex w-full items-center justify-center px-4 py-3 text-base disabled:opacity-50">
                    <span x-show="!loading">Verify code</span>
                    <span x-show="loading" class="flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="3" stroke-linecap="round" class="opacity-75"/></svg>
                        Verifying...
                    </span>
                </button>

                <button @click="codeSent = false; error = ''; digits = Array(6).fill('')"
                        class="mt-3 block w-full text-center text-base font-medium text-gray-800 transition hover:text-[#AEACBC]">
                    Use a different email
                </button>
            </div>
        </div>
        </div>

        <p class="mt-6 text-center text-xs font-medium text-gray-700">Powered by <span style="font-family:'Lato',sans-serif; letter-spacing:0.5px;"><span style="font-weight:400;">Foundry</span><span style="font-weight:400;">OS</span></span></p>
    </div>
</div>

@push('scripts')
<script>
function loginForm() {
    return {
        email: '',
        codeSent: false,
        digits: Array(6).fill(''),
        loading: false,
        error: '',

        get csrfToken() {
            return document.querySelector('meta[name="csrf-token"]').content;
        },

        async sendCode() {
            if (!this.email) return;
            this.loading = true;
            this.error = '';
            try {
                const resp = await fetch('/auth/send-code', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ email: this.email }),
                });
                const data = await resp.json();
                if (!resp.ok) { this.error = data.error || 'Failed to send code'; return; }
                this.codeSent = true;
                if (data.debug_code) this.error = data.message + ' | Code: ' + data.debug_code;
                this.$nextTick(() => document.getElementById('code-0')?.focus());
            } catch (e) { this.error = 'Network error'; }
            finally { this.loading = false; }
        },

        async verifyCode() {
            const code = this.digits.join('');
            if (code.length < 6) return;
            this.loading = true;
            this.error = '';
            try {
                const resp = await fetch('/auth/verify-code', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ email: this.email, code }),
                });
                const data = await resp.json();
                if (!resp.ok) { this.error = data.error || 'Invalid code'; return; }
                window.location = '/';
            } catch (e) { this.error = 'Network error'; }
            finally { this.loading = false; }
        },

        onDigitInput(i, e) {
            const val = e.target.value.replace(/\D/g, '');
            this.digits[i] = val.slice(0, 1);
            e.target.value = this.digits[i];
            if (val && i < 5) document.getElementById('code-' + (i + 1))?.focus();
            if (this.digits.join('').length === 6) this.verifyCode();
        },

        onBackspace(i, e) {
            if (!this.digits[i] && i > 0) {
                this.digits[i - 1] = '';
                document.getElementById('code-' + (i - 1))?.focus();
            }
        },

        onPaste(e) {
            const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
            for (let i = 0; i < 6 && i < text.length; i++) this.digits[i] = text[i];
            const next = Math.min(text.length, 5);
            document.getElementById('code-' + next)?.focus();
            if (text.length >= 6) this.verifyCode();
        },
    };
}
</script>
@endpush
@endsection
