/* ========== CHATBOT JAVASCRIPT with Groq AI ========== */

document.addEventListener('DOMContentLoaded', function () {
    const fabChatbot = document.getElementById('fabChatbot');
    const chatbotContainer = document.getElementById('chatbotContainer');
    const chatbotClose = document.getElementById('chatbotClose');
    const chatbotMessages = document.getElementById('chatbotMessages');
    const chatInput = document.getElementById('chatInput');
    const chatSend = document.getElementById('chatSend');
    const quickReplies = document.querySelectorAll('.quick-reply');

    // Toggle Chatbot from FAB option
    if (fabChatbot) {
        fabChatbot.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            chatbotContainer.classList.add('active');

            // Focus input
            setTimeout(() => {
                if (chatInput) chatInput.focus();
            }, 300);
        });
    }

    // Close Chatbot
    if (chatbotClose) {
        chatbotClose.addEventListener('click', function () {
            chatbotContainer.classList.remove('active');
        });
    }

    // Close chatbot when clicking outside
    document.addEventListener('click', function (e) {
        if (chatbotContainer && chatbotContainer.classList.contains('active')) {
            if (!chatbotContainer.contains(e.target) && !fabChatbot.contains(e.target)) {
                chatbotContainer.classList.remove('active');
            }
        }
    });

    // Send Message
    async function sendMessage(message) {
        if (!message.trim()) return;

        // Add user message
        addMessage(message, 'user');

        // Clear input
        if (chatInput) chatInput.value = '';

        // Show typing indicator
        showTypingIndicator();

        try {
            // Get base path for API
            const basePath = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/') + 1);
            const apiUrl = basePath + 'api/chat.php';

            console.log('Calling API:', apiUrl); // Debug

            // Call AI API
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ message: message })
            });

            const text = await response.text();
            console.log('API Raw Response:', text); // Debug

            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('JSON Parse Error:', e, 'Response:', text);
                hideTypingIndicator();
                addMessage('Error: Server response tidak valid. Silakan coba lagi.', 'bot');
                return;
            }

            hideTypingIndicator();
            console.log('API Data:', data); // Debug

            if (data.success && data.response) {
                addMessage(data.response, 'bot');
            } else if (data.error) {
                console.error('API Error:', data);
                addMessage('⚠️ ' + (data.error || 'Terjadi kesalahan'), 'bot');
            }
        } catch (error) {
            console.error('Fetch Error:', error);
            hideTypingIndicator();

            // Fallback to local FAQ
            const fallbackResponse = getFallbackResponse(message);
            addMessage(fallbackResponse, 'bot');
        }
    }

    // Fallback FAQ responses (when AI is unavailable)
    function getFallbackResponse(message) {
        const lowerMessage = message.toLowerCase();

        if (lowerMessage.includes('jam') || lowerMessage.includes('buka') || lowerMessage.includes('operasional')) {
            return '🕐 <strong>Jam Operasional:</strong><br>Senin - Kamis: 07.00 - 15.00 WIB<br>Jumat: 07.00 - 11.30 WIB<br>Sabtu - Minggu: Libur';
        }
        if (lowerMessage.includes('alamat') || lowerMessage.includes('lokasi')) {
            return '📍 <strong>Alamat:</strong><br>SMAN 1 Bengkalis<br>Jl. Kelapapati Tengah, Bengkalis<br>Riau 28711';
        }
        if (lowerMessage.includes('kontak') || lowerMessage.includes('telepon') || lowerMessage.includes('email')) {
            return '📞 <strong>Kontak:</strong><br>Telepon: (0766) 21234<br>Email: info@sman1bengkalis.sch.id';
        }
        if (lowerMessage.includes('halo') || lowerMessage.includes('hai') || lowerMessage.includes('hi')) {
            return 'Halo juga! 👋 Ada yang bisa saya bantu?';
        }

        return 'Maaf, saat ini saya mengalami gangguan teknis. 🙏<br><br>Silakan hubungi kami langsung:<br>📞 (0766) 21234<br>📧 info@sman1bengkalis.sch.id';
    }

    // Add message to chat
    function addMessage(content, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'chat-message ' + type;

        const avatarIcon = type === 'bot' ? 'fa-robot' : 'fa-user';

        messageDiv.innerHTML = `
            <div class="message-avatar">
                <i class="fas ${avatarIcon}"></i>
            </div>
            <div class="message-content">
                <p>${content}</p>
            </div>
        `;

        chatbotMessages.appendChild(messageDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }

    // Typing indicator
    function showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chat-message bot typing';
        typingDiv.id = 'typingIndicator';
        typingDiv.innerHTML = `
            <div class="message-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="message-content">
                <div class="typing-indicator">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        `;
        chatbotMessages.appendChild(typingDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }

    function hideTypingIndicator() {
        const typingIndicator = document.getElementById('typingIndicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    // Send button click
    if (chatSend) {
        chatSend.addEventListener('click', function () {
            sendMessage(chatInput.value);
        });
    }

    // Enter key to send
    if (chatInput) {
        chatInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                sendMessage(chatInput.value);
            }
        });
    }

    // Quick replies
    quickReplies.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const message = this.getAttribute('data-message');
            sendMessage(message);
        });
    });
});
