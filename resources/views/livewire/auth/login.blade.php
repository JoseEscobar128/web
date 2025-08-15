<div>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit.prevent="login">

        <div>
            <x-text-input wire:model.defer="email" id="email" class="block mt-1 w-full text-sm  !bg-white" style="color: black;" type="email" name="email" placeholder="Usuario" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-text-input wire:model.defer="password" id="password" class="block mt-1 w-full !bg-white text-sm" style="color: black;"  type="password" name="password" placeholder="Contraseña" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-center mt-4">
            <div class="g-recaptcha"
                data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"
                data-callback="setRecaptchaToken">
            </div>
            @error('recaptchaToken') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center justify-center mt-6">
            <button type="submit" class="w-full justify-center inline-flex items-center px-4 py-3 bg-mostaza border border-transparent rounded-md font-semibold text-white tracking-widest hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-mostaza text-sm">
                Iniciar Sesión
            </button>
        </div>

        <div class="flex justify-end mt-8">
            <a  class="text-xs font-medium" style="color: #0F38A1;">
                ¿Olvidaste tu contraseña?
            </a>
        </div>

    </form>
</div>

@push('scripts')
<script>
    function setRecaptchaToken(token) {
        // Cuando reCAPTCHA es resuelto, esta función se llama.
        // Le pasa el token a una propiedad 'recaptchaToken' en tu componente Livewire.
        @this.set('recaptchaToken', token);
    }
</script>
@endpush