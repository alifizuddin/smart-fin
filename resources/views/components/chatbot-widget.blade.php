<div x-data="{ open: false, messages: [], newMessage: '', isLoading: false }" 
     class="fixed bottom-6 right-6 z-50 flex flex-col items-end">
    
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
         class="mb-4 w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col h-[600px]"
         style="display: none;">
        
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 p-5 text-white flex justify-between items-center shadow-md">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path></svg>
                </div>
                <h3 class="font-bold text-lg">SmartFin Assistant</h3>
            </div>
            <button @click="open = false" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-1 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gradient-to-b from-gray-50 to-white" id="chat-messages">
            <div class="flex justify-start">
                <div class="bg-gray-100 text-gray-700 rounded-2xl rounded-tl-none py-3 px-4 max-w-[80%] text-sm shadow-sm">
                    <p class="font-medium text-gray-900">Hello! 👋</p>
                    <p class="text-gray-600 mt-1">I can help you check budgets or analyze expenses. What do you need?</p>
                </div>
            </div>

            <template x-for="msg in messages" :key="msg.id">
                <div :class="msg.isUser ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="msg.isUser ? 'bg-indigo-600 text-white rounded-2xl rounded-tr-none shadow-md' : 'bg-gray-100 text-gray-800 rounded-2xl rounded-tl-none shadow-sm'"
                         class="py-3 px-4 max-w-[80%] text-sm leading-relaxed"
                         x-text="msg.text"></div>
                </div>
            </template>
            
            <div x-show="isLoading" class="flex justify-start">
                <div class="bg-gray-100 text-gray-600 rounded-2xl rounded-tl-none py-3 px-4 text-sm shadow-sm flex items-center gap-2">
                    <span class="inline-flex gap-1">
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms;"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms;"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms;"></span>
                    </span>
                    <span>Thinking...</span>
                </div>
            </div>
        </div>

        <div class="p-4 border-t border-gray-100 bg-white">
            <form @submit.prevent="sendMessage" class="flex gap-2">
                <input type="text" x-model="newMessage" 
                       class="flex-1 rounded-full border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder-gray-400"
                       placeholder="Ask me anything..." :disabled="isLoading">
                <button type="submit" 
                        class="bg-indigo-600 text-white px-4 py-2.5 rounded-full hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all hover:shadow-lg active:scale-95"
                        :disabled="isLoading || !newMessage.trim()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>
        </div>
    </div>

    <button @click="open = !open" 
            class="bg-gradient-to-br from-indigo-600 to-indigo-700 text-white p-4 rounded-full shadow-xl hover:shadow-2xl transition-all hover:scale-110 active:scale-95 relative group">
        <div class="absolute inset-0 bg-indigo-500 rounded-full opacity-0 group-hover:opacity-20 transition-opacity"></div>
        <svg x-show="!open" class="w-6 h-6 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
        <svg x-show="open" class="w-6 h-6 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
</div>

<script>
    function sendMessage() {
        const text = this.newMessage.trim();
        if (!text) return;

        // Add user message
        this.messages.push({ id: Date.now(), text: text, isUser: true });
        this.newMessage = '';
        this.isLoading = true;
        
        // Auto-scroll
        this.$nextTick(() => {
            const container = document.getElementById('chat-messages');
            container.scrollTop = container.scrollHeight;
        });

        // Send to Backend
        axios.post('/chat/send', { message: text })
            .then(response => {
                this.messages.push({ 
                    id: Date.now() + 1, 
                    text: response.data.reply, 
                    isUser: false 
                });
            })
            .catch(error => {
                this.messages.push({ 
                    id: Date.now() + 1, 
                    text: "Sorry, I couldn't reach the server.", 
                    isUser: false 
                });
            })
            .finally(() => {
                this.isLoading = false;
                this.$nextTick(() => {
                    const container = document.getElementById('chat-messages');
                    container.scrollTop = container.scrollHeight;
                });
            });
    }
</script>