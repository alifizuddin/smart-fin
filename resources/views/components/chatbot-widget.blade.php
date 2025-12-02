<div x-data="{ open: false, messages: [], newMessage: '', isLoading: false }" 
     class="fixed bottom-6 right-6 z-50 flex flex-col items-end">
    
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="mb-4 w-96 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden"
         style="display: none;">
        
        <div class="bg-indigo-600 p-4 text-white flex justify-between items-center">
            <h3 class="font-bold">SmartFin Assistant</h3>
            <button @click="open = false" class="text-white hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="h-80 overflow-y-auto p-4 space-y-3 bg-gray-50" id="chat-messages">
            <div class="flex justify-start">
                <div class="bg-gray-200 text-gray-800 rounded-lg py-2 px-3 max-w-[80%] text-sm">
                    Hello! I can help you check budgets or analyze expenses. What do you need?
                </div>
            </div>

            <template x-for="msg in messages" :key="msg.id">
                <div :class="msg.isUser ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="msg.isUser ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-800'"
                         class="rounded-lg py-2 px-3 max-w-[80%] text-sm"
                         x-text="msg.text"></div>
                </div>
            </template>
            
            <div x-show="isLoading" class="flex justify-start">
                <div class="bg-gray-200 text-gray-500 rounded-lg py-2 px-3 text-sm italic">
                    Thinking...
                </div>
            </div>
        </div>

        <div class="p-3 border-t bg-white">
            <form @submit.prevent="sendMessage" class="flex gap-2">
                <input type="text" x-model="newMessage" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                       placeholder="Type your question..." :disabled="isLoading">
                <button type="submit" 
                        class="bg-indigo-600 text-white px-3 py-2 rounded-md hover:bg-indigo-700 disabled:opacity-50"
                        :disabled="isLoading || !newMessage.trim()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>
        </div>
    </div>

    <button @click="open = !open" 
            class="bg-indigo-600 text-white p-4 rounded-full shadow-lg hover:bg-indigo-700 transition-transform hover:scale-105">
        <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
        <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
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