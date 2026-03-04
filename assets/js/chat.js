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
  const storageHistoryKey = config.storageHistoryKey || 'alphabit_chat_history_v1';
  const storageWindowKey = config.storageWindowKey || 'alphabit_chat_window_open_v1';
  const maxStoredMessages = 48;
  const maxHistoryForApi = 16;
  const maxMessageLengthForApi = 900;

  const defaultWelcomeElement = chatMessages.querySelector('.message.ai');
  const defaultWelcomeMessage = defaultWelcomeElement ? defaultWelcomeElement.textContent.trim() : '';

  let conversationHistory = [];

  function readStorage(key) {
    try {
      return window.localStorage.getItem(key);
    } catch (error) {
      return null;
    }
  }

  function writeStorage(key, value) {
    try {
      window.localStorage.setItem(key, value);
      return true;
    } catch (error) {
      return false;
    }
  }

  function normalizeText(value) {
    return (typeof value === 'string') ? value.trim() : '';
  }

  function parseStoredHistory(rawValue) {
    if (!rawValue) {
      return [];
    }

    try {
      const parsed = JSON.parse(rawValue);
      if (!Array.isArray(parsed)) {
        return [];
      }

      const normalized = [];
      parsed.forEach(function (entry) {
        if (!entry || typeof entry !== 'object') {
          return;
        }

        const sender = entry.sender === 'ai' ? 'ai' : (entry.sender === 'user' ? 'user' : '');
        const text = normalizeText(entry.text);
        if (!sender || !text) {
          return;
        }

        normalized.push({ sender: sender, text: text });
      });

      return normalized.slice(-maxStoredMessages);
    } catch (error) {
      return [];
    }
  }

  function scrollToBottom() {
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

  function sanitizeLinkHref(rawHref) {
    const hrefValue = normalizeText(rawHref).replace(/^['"]|['"]$/g, '');
    if (!hrefValue) {
      return null;
    }

    try {
      const parsed = new URL(hrefValue, window.location.origin);
      const protocol = parsed.protocol.toLowerCase();

      if (protocol === 'http:' || protocol === 'https:') {
        if (parsed.origin === window.location.origin) {
          return {
            href: parsed.pathname + parsed.search + parsed.hash,
            external: false
          };
        }

        return {
          href: parsed.toString(),
          external: true
        };
      }

      if (protocol === 'mailto:' || protocol === 'tel:') {
        return {
          href: parsed.toString(),
          external: false
        };
      }

      return null;
    } catch (error) {
      return null;
    }
  }

  function createChatAnchor(linkInfo, label) {
    const anchor = document.createElement('a');
    anchor.classList.add('chat-inline-link');
    anchor.href = linkInfo.href;
    anchor.textContent = label;
    if (linkInfo.external) {
      anchor.target = '_blank';
      anchor.rel = 'noopener noreferrer';
    }
    return anchor;
  }

  function trimTrailingLinkPunctuation(linkText) {
    let core = linkText;
    let suffix = '';

    while (core.length > 0) {
      const lastChar = core.charAt(core.length - 1);

      if ('.!,?:;'.indexOf(lastChar) !== -1) {
        suffix = lastChar + suffix;
        core = core.slice(0, -1);
        continue;
      }

      if (lastChar === ')') {
        const openingCount = (core.match(/\(/g) || []).length;
        const closingCount = (core.match(/\)/g) || []).length;
        if (closingCount > openingCount) {
          suffix = ')' + suffix;
          core = core.slice(0, -1);
          continue;
        }
      }

      break;
    }

    return {
      core: core,
      suffix: suffix
    };
  }

  function appendAutoLinkedText(container, text) {
    const plainLinkPattern = /(https?:\/\/[^\s<>"']+|\/[A-Za-z0-9#][^\s<>"']*|model\/[A-Za-z0-9][^\s<>"']*)/g;
    let lastIndex = 0;
    let match;

    while ((match = plainLinkPattern.exec(text)) !== null) {
      if (match.index > lastIndex) {
        container.appendChild(document.createTextNode(text.slice(lastIndex, match.index)));
      }

      const rawLink = match[0];
      const trimmedLink = trimTrailingLinkPunctuation(rawLink);
      const linkInfo = sanitizeLinkHref(trimmedLink.core);

      if (linkInfo) {
        container.appendChild(createChatAnchor(linkInfo, trimmedLink.core));
      } else {
        container.appendChild(document.createTextNode(rawLink));
      }

      if (trimmedLink.suffix) {
        container.appendChild(document.createTextNode(trimmedLink.suffix));
      }

      lastIndex = match.index + rawLink.length;
    }

    if (lastIndex < text.length) {
      container.appendChild(document.createTextNode(text.slice(lastIndex)));
    }
  }

  function appendFormattedInline(container, text) {
    const boldPattern = /\*\*([^*]+)\*\*/g;
    let lastIndex = 0;
    let match;

    while ((match = boldPattern.exec(text)) !== null) {
      if (match.index > lastIndex) {
        appendAutoLinkedText(container, text.slice(lastIndex, match.index));
      }

      const strong = document.createElement('strong');
      appendAutoLinkedText(strong, match[1]);
      container.appendChild(strong);

      lastIndex = boldPattern.lastIndex;
    }

    if (lastIndex < text.length) {
      appendAutoLinkedText(container, text.slice(lastIndex));
    }
  }

  function appendMessageContent(container, text) {
    const markdownLinkPattern = /\[([^[\]]+)\]\(([^)]+)\)/g;
    let lastIndex = 0;
    let match;

    while ((match = markdownLinkPattern.exec(text)) !== null) {
      if (match.index > lastIndex) {
        appendFormattedInline(container, text.slice(lastIndex, match.index));
      }

      const linkLabel = normalizeText(match[1]) || 'Link';
      const linkInfo = sanitizeLinkHref(match[2]);

      if (linkInfo) {
        container.appendChild(createChatAnchor(linkInfo, linkLabel));
      } else {
        appendFormattedInline(container, match[0]);
      }

      lastIndex = markdownLinkPattern.lastIndex;
    }

    if (lastIndex < text.length) {
      appendFormattedInline(container, text.slice(lastIndex));
    }
  }

  function createMessageElement(text, sender) {
    const messageDiv = document.createElement('div');
    messageDiv.classList.add('message', sender);
    appendMessageContent(messageDiv, text);
    return messageDiv;
  }

  function renderMessage(text, sender) {
    const messageDiv = createMessageElement(text, sender);
    if (typingIndicator.parentNode === chatMessages) {
      chatMessages.insertBefore(messageDiv, typingIndicator);
    } else {
      chatMessages.appendChild(messageDiv);
    }
    scrollToBottom();
  }

  function saveConversationHistory() {
    if (conversationHistory.length > maxStoredMessages) {
      conversationHistory = conversationHistory.slice(-maxStoredMessages);
    }
    writeStorage(storageHistoryKey, JSON.stringify(conversationHistory));
  }

  function addMessage(text, sender) {
    const normalized = normalizeText(text);
    if (!normalized || (sender !== 'user' && sender !== 'ai')) {
      return;
    }

    renderMessage(normalized, sender);
    conversationHistory.push({ sender: sender, text: normalized });
    saveConversationHistory();
  }

  function clearRenderedMessages() {
    chatMessages.querySelectorAll('.message').forEach(function (node) {
      node.remove();
    });
  }

  function renderConversationHistory() {
    clearRenderedMessages();
    conversationHistory.forEach(function (entry) {
      renderMessage(entry.text, entry.sender);
    });
  }

  function initializeConversationHistory() {
    conversationHistory = parseStoredHistory(readStorage(storageHistoryKey));

    if (conversationHistory.length === 0 && defaultWelcomeMessage !== '') {
      conversationHistory.push({ sender: 'ai', text: defaultWelcomeMessage });
      saveConversationHistory();
    }

    renderConversationHistory();
  }

  function showTypingIndicator() {
    typingIndicator.style.display = 'flex';
    scrollToBottom();
  }

  function hideTypingIndicator() {
    typingIndicator.style.display = 'none';
  }

  function getHistoryForApi() {
    return conversationHistory.slice(-maxHistoryForApi).map(function (entry) {
      let content = entry.text;
      if (content.length > maxMessageLengthForApi) {
        content = content.slice(0, maxMessageLengthForApi) + '...';
      }

      return {
        role: entry.sender === 'ai' ? 'assistant' : 'user',
        content: content
      };
    });
  }

  function setChatOpenState(isOpen) {
    chatWindow.classList.toggle('open', isOpen);
    if (chatBubble) {
      chatBubble.style.display = isOpen ? 'none' : '';
    }
    writeStorage(storageWindowKey, isOpen ? '1' : '0');
    if (isOpen) {
      chatInput.focus();
    }
  }

  function toggleChat() {
    setChatOpenState(!chatWindow.classList.contains('open'));
  }

  async function sendMessage() {
    const message = normalizeText(chatInput.value);
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
        body: JSON.stringify({
          message: message,
          history: getHistoryForApi()
        })
      });

      const data = await response.json();
      hideTypingIndicator();

      if (data && data.success && typeof data.reply === 'string' && data.reply.trim() !== '') {
        addMessage(data.reply.trim(), 'ai');
      } else {
        addMessage((data && typeof data.error === 'string' && data.error.trim() !== '') ? data.error : apiErrorMessage, 'ai');
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

  initializeConversationHistory();
  hideTypingIndicator();
  setChatOpenState(readStorage(storageWindowKey) === '1');

  chatToggleBtn.addEventListener('click', toggleChat);
  chatCloseBtn.addEventListener('click', toggleChat);
  chatSendBtn.addEventListener('click', sendMessage);

  chatInput.addEventListener('keypress', function (event) {
    if (event.key === 'Enter') {
      sendMessage();
    }
  });
});
