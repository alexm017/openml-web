<?php
$chat_lang = isset($_COOKIE['site_lang']) ? $_COOKIE['site_lang'] : 'en';

if ($chat_lang === 'ro') {
    $chat_bubble_title = 'Ai nevoie de ajutor cu OpenML?';
    $chat_bubble_subtitle = 'Intreaba asistentul AlphaBit';
    $chat_title = 'Asistent AI AlphaBit';
    $chat_welcome = 'Salut! Te pot ajuta cu setup-ul modelului, date de antrenament si intrebari despre workflow-ul ML in robotica.';
    $chat_placeholder = 'Scrie intrebarea ta...';
    $chat_send = 'Trimite';
    $chat_error = 'A aparut o eroare. Te rog incearca din nou.';
    $chat_offline = 'Nu am putut contacta asistentul acum. Verifica conexiunea.';
} else {
    $chat_bubble_title = 'Need help with OpenML?';
    $chat_bubble_subtitle = 'Ask the AlphaBit assistant';
    $chat_title = 'AlphaBit AI Assistant';
    $chat_welcome = 'Hi! I can help with model setup, training data, and robotics ML workflow questions.';
    $chat_placeholder = 'Type your question...';
    $chat_send = 'Send';
    $chat_error = 'Sorry, something went wrong. Please try again.';
    $chat_offline = 'Could not reach the assistant right now. Please check your connection.';
}
?>
<link rel="stylesheet" href="/assets/css/chat.css?v=20260304">

<div id="chat-bubble" class="chat-bubble">
    <span class="chat-bubble-title"><?php echo htmlspecialchars($chat_bubble_title, ENT_QUOTES, 'UTF-8'); ?></span>
    <span class="chat-bubble-subtitle"><?php echo htmlspecialchars($chat_bubble_subtitle, ENT_QUOTES, 'UTF-8'); ?></span>
</div>
<button id="chat-toggle-btn" class="chat-toggle-btn"
    aria-label="<?php echo htmlspecialchars($chat_title, ENT_QUOTES, 'UTF-8'); ?>">
    <i class="fas fa-comment-dots" aria-hidden="true"></i>
</button>

<div id="chat-window" class="chat-window">
    <div class="chat-header">
        <h3><?php echo htmlspecialchars($chat_title, ENT_QUOTES, 'UTF-8'); ?></h3>
        <button id="chat-close-btn" class="chat-close-btn" aria-label="Close">
            <i class="fas fa-times" aria-hidden="true"></i>
        </button>
    </div>
    <div id="chat-messages" class="chat-messages">
        <div class="message ai"><?php echo htmlspecialchars($chat_welcome, ENT_QUOTES, 'UTF-8'); ?></div>
        <div id="typing-indicator" class="typing-indicator">
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
        </div>
    </div>
    <div class="chat-input-area">
        <input type="text" id="chat-input"
            placeholder="<?php echo htmlspecialchars($chat_placeholder, ENT_QUOTES, 'UTF-8'); ?>">
        <button id="chat-send-btn" class="chat-send-btn"
            aria-label="<?php echo htmlspecialchars($chat_send, ENT_QUOTES, 'UTF-8'); ?>">
            <i class="fas fa-paper-plane" aria-hidden="true"></i>
        </button>
    </div>
</div>

<script>
window.AlphaBitChatConfig = {
    endpoint: '/api/chat.php',
    apiErrorMessage: <?php echo json_encode($chat_error); ?>,
    networkErrorMessage: <?php echo json_encode($chat_offline); ?>
};
</script>
<script src="/assets/js/chat.js?v=20260306"></script>
