<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Botman -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/assets/css/chat.min.css">

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

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Drift Code (without automatic widget) -->
    <script>
        "use strict";

        ! function() {
            var t = window.driftt = window.drift = window.driftt || [];
            if (!t.init) {
                if (t.invoked) return void(window.console && console.error && console.error("Drift snippet included twice."));
                t.invoked = !0, t.methods = ["identify", "config", "track", "reset", "debug", "show", "ping", "page", "hide", "off", "on"],
                    t.factory = function(e) {
                        return function() {
                            var n = Array.prototype.slice.call(arguments);
                            return n.unshift(e), t.push(n), t;
                        };
                    }, t.methods.forEach(function(e) {
                        t[e] = t.factory(e);
                    }), t.load = function(t) {
                        var e = 3e5,
                            n = Math.ceil(new Date() / e) * e,
                            o = document.createElement("script");
                        o.type = "text/javascript", o.async = !0, o.crossorigin = "anonymous", o.src = "https://js.driftt.com/include/" + n + "/" + t + ".js";
                        var i = document.getElementsByTagName("script")[0];
                        i.parentNode.insertBefore(o, i);
                    };
            }
        }();
        drift.SNIPPET_VERSION = '0.3.1';
        drift.load('va746htz54vz');

        // Disable automatic widget display
        drift.on('ready', function(api) {
            api.widget.hide();
        });
    </script>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <livewire:navigation />
        <div>
            {{ $slot }}
        </div>
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
    <div x-data="chatWidget()"
        class="fixed bottom-4 right-4 w-80 bg-white rounded-lg shadow-xl overflow-hidden">
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

    <script>
        function chatWidget() {
            return {
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

                    if (userMessage === 'Chat with a live person') {
                        this.activateLiveChat();
                        return;
                    }

                    // Simulate bot response (replace with actual API call to BotMan in production)
                    setTimeout(() => {
                        let botResponse = "I'm sorry, I don't have an answer for that question. Here are some options:";
                        if (this.questions.includes(userMessage)) {
                            switch (userMessage) {
                                case 'What services do you offer?':
                                    botResponse = "We offer a wide range of services including web development, mobile app development, and digital marketing.";
                                    break;
                                case 'How can I contact support?':
                                    botResponse = "You can contact our support team via email at support@example.com or call us at 1-800-123-4567.";
                                    break;
                                case 'What are your business hours?':
                                    botResponse = "Our business hours are Monday to Friday, 9 AM to 5 PM EST.";
                                    break;
                                case 'Do you offer custom solutions?':
                                    botResponse = "Yes, we provide custom solutions tailored to your specific needs. Please contact our sales team for more information.";
                                    break;
                            }
                        }
                        this.addMessage('bot', botResponse);
                        this.showQuestions();
                    }, 500);
                },
                activateLiveChat() {
                    this.addMessage('bot', "Certainly! I'm connecting you with a live person now. Please wait a moment.");
                    // Activate Drift chat
                    if (window.drift) {
                        window.drift.api.showWelcomeMessage();
                    } else {
                        this.addMessage('bot', "I'm sorry, but the live chat system is currently unavailable. Please try again later or use one of the other contact methods.");
                    }
                }
            }
        }
    </script>
</body>

</html>