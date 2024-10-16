<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'GamerGo') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script type="importmap">
        {
            "imports": {
                "three": "https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.module.js",
                "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.160.0/examples/jsm/"
            }
        }
    </script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        @keyframes marquee {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        .animate-marquee {
            animation: marquee 20s linear infinite;
        }

        input:focus {
            @apply outline-none ring-0;
        }

        .chat-container {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div id="app" class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Banner -->
        <livewire:banner />

        <!-- Announcement -->
        <livewire:announcement />

        <!-- Navigation -->
        <livewire:navigation />

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>

        <!-- Footer -->
        <livewire:footer />
    </div>

    <!-- Flowbite -->
    <script src="{{ asset('js/flowbite.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('swal:success', (data) => {
                Swal.fire({
                    title: data[0].title,
                    text: data[0].text,
                    icon: data[0].icon,
                    timer: data[0].timer,
                    showConfirmButton: false
                });
            });
        });
    </script>

    <!-- Custom Chat Widget -->
    <div x-data="chatWidget()" x-cloak>
        <!-- Toggle Button -->
        <button @click="toggleChat()"
            class="fixed bottom-4 right-4 bg-blue-500 text-white p-3 rounded-full shadow-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <svg x-show="!isOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
            </svg>
            <svg x-show="isOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Chat Widget -->
        <div x-show="isOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="fixed bottom-20 right-4 w-80 bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="bg-blue-500 text-white p-4">
                <h3 class="font-bold">Virtual Assistant</h3>
            </div>
            <div class="chat-container p-4 h-80 overflow-y-auto">
                <div x-html="chatHistory"></div>
            </div>
            <div class="p-4 border-t">
                <div class="flex space-x-2">
                    <input x-model="userInput"
                        @keydown.enter="sendMessage()"
                        type="text"
                        placeholder="Type a message..."
                        class="flex-grow px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button @click="sendMessage()"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Send
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('chatWidget', () => ({
                isOpen: false,
                chatHistory: '',
                userInput: '',
                questions: [
                    'What services do you offer?',
                    'How can I contact support?',
                    'What are your business hours?',
                    'Do you offer custom solutions?',
                    'Chat with a live person'
                ],
                init() {
                    this.addMessage('bot', "Hello! How can I assist you today? Here are some options:");
                    this.showQuestions();
                },
                toggleChat() {
                    this.isOpen = !this.isOpen;
                },
                addMessage(sender, message) {
                    const messageClass = sender === 'bot' ? 'bg-gray-100' : 'bg-blue-100 text-right';
                    this.chatHistory += `<div class="mb-2 p-2 rounded ${messageClass}">${message}</div>`;
                    this.$nextTick(() => {
                        const chatContainer = this.$el.querySelector('.chat-container');
                        chatContainer.scrollTop = chatContainer.scrollHeight;
                    });
                },
                showQuestions() {
                    let questionButtons = this.questions.map(q =>
                        `<button @click="sendMessage('${q}')" class="block w-full text-left p-2 mb-2 bg-gray-200 hover:bg-gray-300 rounded">${q}</button>`
                    ).join('');
                    this.chatHistory += `<div class="mb-4">${questionButtons}</div>`;
                },
                sendMessage(message = null) {
                    const userMessage = message || this.userInput;
                    if (userMessage.trim() === '') return;

                    this.addMessage('user', userMessage);
                    this.userInput = '';

                    // Send message to BotMan
                    fetch('/botman', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                message: userMessage
                            }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            this.addMessage('bot', data.message);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            this.addMessage('bot', "I'm sorry, there was an error processing your request.");
                        });
                }
            }))
        })
    </script>

    @livewireScripts
</body>

</html>