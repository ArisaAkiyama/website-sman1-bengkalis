<!-- FAB Container with Chatbot (Same design as index.php) -->
<div class="fab-container" id="fabContainer">
    <!-- Chatbot Window (Inside FAB) -->
    <div class="chatbot-container" id="chatbotContainer">
        <div class="chatbot-header">
            <div class="chatbot-header-info">
                <div class="chatbot-avatar">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="chatbot-title">
                    <h4>SMAN 1 Bengkalis</h4>
                    <span class="chatbot-status"><i class="fas fa-circle"></i> Online</span>
                </div>
            </div>
            <button class="chatbot-close" id="chatbotClose">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="chatbot-messages" id="chatbotMessages">
            <!-- Welcome Message -->
            <div class="chat-message bot">
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <p>Halo! Selamat datang di SMAN 1 Bengkalis 👋</p>
                    <p>Ada yang bisa saya bantu?</p>
                </div>
            </div>
        </div>
        
        <div class="chatbot-quick-replies" id="quickReplies">
            <button class="quick-reply" data-message="Jam operasional sekolah">🕐 Jam Operasional</button>
            <button class="quick-reply" data-message="Informasi PPDB">📝 Info PPDB</button>
            <button class="quick-reply" data-message="Alamat sekolah">📍 Alamat</button>
            <button class="quick-reply" data-message="Ekstrakurikuler apa saja">🎯 Ekstrakurikuler</button>
            <button class="quick-reply" data-message="Cara menghubungi sekolah">📞 Kontak</button>
        </div>
        
        <div class="chatbot-input">
            <input type="text" id="chatInput" placeholder="Ketik pesan..." autocomplete="off">
            <button class="chatbot-send" id="chatSend">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <!-- FAB Options -->
    <div class="fab-options">
        <button class="fab-option fab-chatbot" id="fabChatbot" title="Chatbot">
            <i class="fas fa-robot"></i>
        </button>
        <a href="https://mail.google.com/mail/?view=cm&fs=1&to=info@sman1bengkalis.sch.id&su=Pertanyaan%20dari%20Website" target="_blank" class="fab-option fab-email" title="Kirim Email">
            <i class="fas fa-envelope"></i>
        </a>
        <a href="https://wa.me/62766212340?text=Halo%20SMAN%201%20Bengkalis" target="_blank" class="fab-option fab-whatsapp" title="WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div>
    <button class="fab-button" id="fabButton" title="Hubungi Kami">
        <i class="fas fa-comments"></i>
        <i class="fas fa-times"></i>
    </button>
</div>

<!-- Chatbot Scripts -->
<link rel="stylesheet" href="css/fab.css">
<link rel="stylesheet" href="css/chatbot.css?v=5">
<script src="js/fab.js?v=1"></script>
<script src="js/chatbot.js?v=4"></script>
