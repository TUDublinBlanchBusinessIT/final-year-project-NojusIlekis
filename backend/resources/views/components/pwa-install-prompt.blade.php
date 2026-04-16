{{-- PWA Install Prompt — shows on mobile when app is not installed --}}
<div x-data="pwaInstall()" x-show="showPrompt" x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 -translate-y-full"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 -translate-y-full"
     class="fixed top-0 left-0 right-0 z-[100] bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg">

    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <span class="text-xl">📱</span>
            </div>
            <div>
                <p class="font-semibold text-sm">Install SnugBug</p>
                <p class="text-xs text-blue-100">Add to your home screen for quick access</p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <button @click="installApp()"
                    style="touch-action: manipulation;"
                    class="px-4 py-2 min-h-[44px] bg-white text-indigo-600 text-sm font-semibold rounded-full hover:bg-blue-50 transition">
                Install
            </button>
            <button @click="dismissPrompt()"
                    style="touch-action: manipulation;"
                    class="w-11 h-11 flex items-center justify-center text-white/70 hover:text-white transition rounded-full">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
function pwaInstall() {
    return {
        showPrompt: false,
        deferredPrompt: null,

        init() {
            // Don't show if already installed (standalone mode)
            if (window.matchMedia('(display-mode: standalone)').matches) return;

            // Don't show if user dismissed it recently (check cookie)
            if (document.cookie.includes('pwa_dismissed=1')) return;

            // Listen for the browser's install prompt
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                this.deferredPrompt = e;
                // Show our custom prompt after a short delay
                setTimeout(() => {
                    this.showPrompt = true;
                }, 3000); // Wait 3 seconds so user sees the page first
            });

            // For iOS Safari (doesn't fire beforeinstallprompt)
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
            const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
            if (isIOS && isSafari && !window.navigator.standalone) {
                setTimeout(() => {
                    this.showPrompt = true;
                }, 5000);
            }
        },

        async installApp() {
            if (this.deferredPrompt) {
                // Chrome/Android — use the native prompt
                this.deferredPrompt.prompt();
                const { outcome } = await this.deferredPrompt.userChoice;
                console.log('[PWA] Install outcome:', outcome);
                this.deferredPrompt = null;
            } else {
                // iOS — show manual instructions
                alert('To install SnugBug:\n\n1. Tap the Share button (📤) at the bottom of Safari\n2. Scroll down and tap "Add to Home Screen"\n3. Tap "Add"\n\nSnugBug will appear on your home screen!');
            }
            this.showPrompt = false;
        },

        dismissPrompt() {
            this.showPrompt = false;
            // Set cookie to not show again for 7 days
            const expires = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toUTCString();
            document.cookie = `pwa_dismissed=1; expires=${expires}; path=/`;
        }
    }
}
</script>
