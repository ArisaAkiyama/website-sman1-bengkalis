/* ========== CHATBOT JAVASCRIPT with Groq AI ========== */

document.addEventListener('DOMContentLoaded', function () {
    const fabChatbot = document.getElementById('fabChatbot');
    const chatbotContainer = document.getElementById('chatbotContainer');
    const chatbotClose = document.getElementById('chatbotClose');
    const chatbotMessages = document.getElementById('chatbotMessages');
    const chatInput = document.getElementById('chatInput');
    const chatSend = document.getElementById('chatSend');
    const quickReplies = document.querySelectorAll('.quick-reply');

    // Conversation History - in-memory only, cleared on page refresh
    let conversationHistory = [];
    const MAX_HISTORY = 10; // Keep last 10 messages (5 exchanges)

    // Clear any old history on page load
    try {
        sessionStorage.removeItem('chatHistory');
    } catch (e) {
        // Ignore
    }

    // Save history to sessionStorage
    function saveHistory() {
        try {
            sessionStorage.setItem('chatHistory', JSON.stringify(conversationHistory));
        } catch (e) {
            console.warn('Could not save chat history');
        }
    }

    // Add to history
    function addToHistory(role, content) {
        conversationHistory.push({ role: role, content: content });
        // Keep only last MAX_HISTORY messages
        if (conversationHistory.length > MAX_HISTORY) {
            conversationHistory = conversationHistory.slice(-MAX_HISTORY);
        }
        saveHistory();
    }

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

        // Add user message to UI and history
        addMessage(message, 'user');
        addToHistory('user', message);

        // Clear input
        if (chatInput) chatInput.value = '';

        // Show typing indicator
        showTypingIndicator();

        try {
            // Get base path for API
            const basePath = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/') + 1);
            const apiUrl = basePath + 'api/chat.php';

            console.log('Calling API with history:', conversationHistory.length, 'messages');

            // Call AI API with conversation history
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    message: message,
                    history: conversationHistory.slice(0, -1) // Send history without the current message
                })
            });

            const text = await response.text();
            console.log('API Raw Response:', text);

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

            if (data.success && data.response) {
                addMessage(data.response, 'bot');
                // Add bot response to history
                addToHistory('assistant', data.response);
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

    // Typing indicator with text
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
                    <span class="typing-text">AI sedang berpikir</span>
                    <span class="typing-dots">
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </span>
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
