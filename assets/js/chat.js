document.addEventListener('DOMContentLoaded', function () {
  const chatToggleBtn = document.getElementById('chat-toggle-btn');
  const chatWindow = document.getElementById('chat-window');
  const chatCloseBtn = document.getElementById('chat-close-btn');
  const chatInput = document.getElementById('chat-input');
  const chatSendBtn = document.getElementById('chat-send-btn');
  const chatMessages = document.getElementById('chat-messages');
  const typingIndicator = document.getElementById('typing-indicator');
  const chatBubble = document.getElementById('chat-bubble');

  if (!chatToggleBtn || !chatWindow || !chatCloseBtn || !chatInput || !chatSendBtn || !chatMessages || !typingIndicator) {
    return;
  }

  const config = window.AlphaBitChatConfig || {};
  const endpoint = config.endpoint || '/api/chat.php';
  const apiErrorMessage = config.apiErrorMessage || 'Sorry, something went wrong. Please try again.';
  const networkErrorMessage = config.networkErrorMessage || 'Could not reach the assistant right now. Please check your connection.';

  function toggleChat() {
    chatWindow.classList.toggle('open');
    if (chatBubble) {
      chatBubble.style.display = chatWindow.classList.contains('open') ? 'none' : '';
    }
    if (chatWindow.classList.contains('open')) {
      chatInput.focus();
    }
  }

  function scrollToBottom() {
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

  function addMessage(text, sender) {
    const messageDiv = document.createElement('div');
    messageDiv.classList.add('message', sender);
    messageDiv.textContent = text;

    if (typingIndicator.parentNode === chatMessages) {
      chatMessages.insertBefore(messageDiv, typingIndicator);
    } else {
      chatMessages.appendChild(messageDiv);
    }

    scrollToBottom();
  }

  function showTypingIndicator() {
    typingIndicator.style.display = 'flex';
    scrollToBottom();
  }

  function hideTypingIndicator() {
    typingIndicator.style.display = 'none';
  }

  async function sendMessage() {
    const message = chatInput.value.trim();
    if (!message) {
      return;
    }

    addMessage(message, 'user');
    chatInput.value = '';
    chatSendBtn.disabled = true;
    showTypingIndicator();

    try {
      const response = await fetch(endpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ message: message })
      });

      const data = await response.json();
      hideTypingIndicator();

      if (data && data.success && typeof data.reply === 'string' && data.reply.trim() !== '') {
        addMessage(data.reply.trim(), 'ai');
      } else {
        addMessage((data && data.error) ? data.error : apiErrorMessage, 'ai');
      }
    } catch (error) {
      hideTypingIndicator();
      addMessage(networkErrorMessage, 'ai');
      console.error('Chat request failed:', error);
    } finally {
      chatSendBtn.disabled = false;
      chatInput.focus();
    }
  }

  chatToggleBtn.addEventListener('click', toggleChat);
  chatCloseBtn.addEventListener('click', toggleChat);
  chatSendBtn.addEventListener('click', sendMessage);

  chatInput.addEventListener('keypress', function (event) {
    if (event.key === 'Enter') {
      sendMessage();
    }
  });
});
