<div>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-4 text-sm text-login-azul" style="text-align: center;">
        Antes de continuar es necesario verificar tu identidad, ingresa el codigo que recibiste en tu correo.
    </div>

    <form wire:submit.prevent="verify">
        <input wire:model="email" type="hidden" />

        <div class="bg-input-rect p-6 rounded-lg">
            <div>
                <x-text-input wire:model.defer="code" id="code" class="block mt-1 w-1/2 mx-auto !bg-white text-sm text-center" placeholder="Codigo" style="color: black;" type="text" name="code" required autofocus />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
        </div>

        <div class="mt-4 flex justify-center">
            <div>
                <div class="g-recaptcha"
                    data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"
                    data-callback="setRecaptchaToken">
                </div>
                @error('recaptchaToken') <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-center">
            <button type="submit" class="w-full justify-center inline-flex items-center px-4 py-3 bg-mostaza border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-opacity-90 focus:outline-none">
                Verificar
            </button>
        </div>

        <div class="mt-4 flex justify-end">
            <button type="button" wire:click="resendCode" class="text-sm text-login-azul hover:opacity-80 rounded-md focus:outline-none">
                No recibí el código
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
    function setRecaptchaToken(token) {
        @this.set('recaptchaToken', token);
    }
</script>
@endpush