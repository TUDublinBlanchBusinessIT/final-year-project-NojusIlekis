<div x-data="chatbot()" x-cloak>
    <!-- Chat bubble button -->
    <button @click="toggle()"
            style="touch-action: manipulation;"
            class="fixed bottom-6 right-6 w-14 h-14 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full shadow-lg flex items-center justify-center text-white text-2xl hover:scale-110 transition-transform z-[50]">
        <span x-show="!isOpen">💬</span>
        <span x-show="isOpen">✕</span>
    </button>

    <!-- Chat panel -->
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="chatbot-panel fixed bottom-24 right-6 w-[380px] h-[500px] bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden z-[50] border border-slate-200">

        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-3 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-2">
                <span class="text-xl">🤖</span>
                <div>
                    <h3 class="font-semibold text-sm">SnugBot</h3>
                    <p class="text-xs text-blue-100">SnugBug Help Assistant</p>
                </div>
            </div>
            <button @click="toggle()"
                    style="touch-action: manipulation;"
                    class="w-10 h-10 flex items-center justify-center text-white/80 hover:text-white rounded-full transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Messages area -->
        <div x-ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-3" style="scroll-behavior: smooth;">
            <!-- Welcome message -->
            <template x-if="messages.length === 0">
                <div>
                    <div class="bg-slate-100 rounded-2xl rounded-tl-sm px-4 py-3 text-sm text-gray-700 max-w-[85%]">
                        <p class="font-medium">Hi! I'm SnugBot 👋</p>
                        <p class="mt-1">How can I help you today? Pick a topic below or type your question.</p>
                    </div>
                    <!-- Category buttons -->
                    <div class="mt-3 flex flex-wrap gap-2">
                        <template x-for="cat in categories" :key="cat">
                            <button @click="showCategory(cat)"
                                    class="text-xs bg-blue-50 text-blue-700 px-3 py-1.5 rounded-full hover:bg-blue-100 transition">
                                <span x-text="cat"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Message bubbles -->
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.type === 'user' ? 'flex justify-end' : 'flex flex-col items-start gap-1'">
                    <div :class="msg.type === 'user'
                            ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl rounded-br-sm'
                            : 'bg-slate-100 text-gray-700 rounded-2xl rounded-tl-sm'"
                         class="px-4 py-3 text-sm max-w-[85%]">
                        <p x-html="msg.text" style="white-space: pre-line;"></p>

                        <!-- Category topic buttons (for topics menu message) -->
                        <template x-if="msg.isTopicsMenu">
                            <div class="mt-2 pt-2 border-t border-slate-300 flex flex-wrap gap-1.5">
                                <template x-for="cat in categories" :key="cat">
                                    <button @click="showCategory(cat)"
                                            class="text-xs bg-blue-50 text-blue-700 border border-blue-200 px-3 py-1.5 rounded-full hover:bg-blue-100 transition"
                                            x-text="cat">
                                    </button>
                                </template>
                            </div>
                        </template>

                        <!-- Clickable category questions -->
                        <template x-if="msg.questions && msg.questions.length > 0">
                            <div class="mt-2 pt-2 border-t border-slate-300 flex flex-col gap-1">
                                <template x-for="q in msg.questions" :key="q">
                                    <button @click="sendMessage(q)"
                                            class="text-left text-xs bg-white text-blue-700 border border-blue-200 rounded-full px-3 py-1.5 hover:bg-blue-50 hover:border-blue-400 transition"
                                            x-text="q">
                                    </button>
                                </template>
                            </div>
                        </template>

                        <!-- Related questions -->
                        <template x-if="msg.related && msg.related.length > 0">
                            <div class="mt-2 pt-2 border-t border-slate-200">
                                <p class="text-xs opacity-70 mb-1">Related questions:</p>
                                <template x-for="rel in msg.related" :key="rel.question">
                                    <button @click="sendMessage(rel.question)"
                                            class="block text-xs text-blue-600 hover:underline mt-1 text-left"
                                            x-text="'→ ' + rel.question">
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- Back to topics link (bot messages only, not the last one if it's a topics menu) -->
                    <template x-if="msg.type === 'bot'">
                        <button @click="showTopicsMenu()"
                                class="text-xs text-slate-400 hover:text-blue-600 transition ml-1">
                            ← Back to topics
                        </button>
                    </template>
                </div>
            </template>

            <!-- Typing indicator -->
            <template x-if="isTyping">
                <div class="flex justify-start">
                    <div class="bg-slate-100 rounded-2xl rounded-tl-sm px-4 py-3">
                        <div class="flex space-x-1">
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Input area -->
        <div class="border-t border-slate-200 p-3 flex-shrink-0">
            <div class="flex gap-2">
                <button @click="showTopicsMenu()"
                        title="Browse topics"
                        class="w-10 h-10 bg-slate-100 hover:bg-slate-200 rounded-full flex items-center justify-center text-slate-600 transition flex-shrink-0 text-base">
                    📋
                </button>
                <input x-model="userInput"
                       @keydown.enter.prevent="sendMessage(userInput)"
                       type="text"
                       placeholder="Type your question..."
                       class="flex-1 text-sm rounded-full border-slate-200 px-4 py-2 focus:border-blue-500 focus:ring-blue-500">
                <button @click="sendMessage(userInput)"
                        :disabled="!userInput.trim()"
                        class="w-10 h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full flex items-center justify-center text-white hover:opacity-90 disabled:opacity-50 transition flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function chatbot() {
    return {
        isOpen: false,
        isTyping: false,
        userInput: '',
        messages: [],
        categories: ['General', 'Fees & Payments', 'Daily Routine', 'Safety & Policies', 'Using the App'],
        categoryQuestions: {},

        toggle() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        async showCategory(category) {
            if (Object.keys(this.categoryQuestions).length === 0) {
                try {
                    const res = await fetch('/chatbot/suggestions');
                    const data = await res.json();
                    this.categoryQuestions = data.categories;
                } catch(e) {
                    console.error('Failed to load suggestions:', e);
                    return;
                }
            }

            const questions = this.categoryQuestions[category] || [];
            this.messages.push({
                type: 'bot',
                text: `📂 <strong>${category}</strong>\n\nClick a question below or type your own:`,
                related: [],
                questions: questions,
            });
            this.$nextTick(() => this.scrollToBottom());
        },

        async showTopicsMenu() {
            // Preload questions if not cached
            if (Object.keys(this.categoryQuestions).length === 0) {
                try {
                    const res = await fetch('/chatbot/suggestions');
                    const data = await res.json();
                    this.categoryQuestions = data.categories;
                } catch(e) {
                    console.error('Failed to load suggestions:', e);
                }
            }

            this.messages.push({
                type: 'bot',
                text: '📋 <strong>Topics</strong>\n\nChoose a category to browse questions:',
                related: [],
                questions: [],
                isTopicsMenu: true,
            });
            this.$nextTick(() => this.scrollToBottom());
        },

        async sendMessage(text) {
            if (!text || !text.trim()) return;

            const message = text.trim();
            this.userInput = '';

            this.messages.push({ type: 'user', text: message, related: [] });
            this.$nextTick(() => this.scrollToBottom());

            this.isTyping = true;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch('/chatbot/ask', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ message: message }),
                });

                const data = await res.json();

                await new Promise(resolve => setTimeout(resolve, 500 + Math.random() * 500));

                this.isTyping = false;
                this.messages.push({
                    type: 'bot',
                    text: data.answer,
                    related: data.related || [],
                });
            } catch (error) {
                this.isTyping = false;
                this.messages.push({
                    type: 'bot',
                    text: 'Sorry, something went wrong. Please try again or message the centre manager directly.',
                    related: [],
                });
            }

            this.$nextTick(() => this.scrollToBottom());
        },

        scrollToBottom() {
            if (this.$refs.messagesContainer) {
                this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
            }
        },
    }
}
</script>
