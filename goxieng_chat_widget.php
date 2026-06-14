<?php
// goxieng_chat_widget.php
// Include this file at the bottom of ANY page (before </body>)
// to add the floating AI assistant to every page.
// Example: <?php include('goxieng_chat_widget.php'); ?>
//
// It reads the session user_id automatically — no extra config needed.

$widget_user_id = $_SESSION['user_id'] ?? 0;
$widget_api_key = "GOXIENG_AGENT_KEY_CHANGE_ME"; // same key as your 3 API files
$site_base_url  = "https://goxiengbulksms.com.ng";
?>

<style>
/* ── Floating Button ── */
#gx-chat-toggle {
  position: fixed;
  bottom: 28px;
  right: 28px;
  width: 58px;
  height: 58px;
  background: linear-gradient(135deg, #1b5e20, #43a047);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 4px 18px rgba(0,0,0,0.3);
  z-index: 9999;
  transition: transform 0.2s;
  border: none;
}
#gx-chat-toggle:hover { transform: scale(1.08); }
#gx-chat-toggle svg { width: 28px; height: 28px; fill: #fff; }

/* Notification dot */
#gx-chat-dot {
  position: absolute;
  top: 4px; right: 4px;
  width: 12px; height: 12px;
  background: #ff5722;
  border-radius: 50%;
  border: 2px solid #fff;
  display: block;
}

/* ── Chat Window ── */
#gx-chat-window {
  position: fixed;
  bottom: 100px;
  right: 28px;
  width: 360px;
  max-height: 520px;
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 8px 40px rgba(0,0,0,0.22);
  display: none;
  flex-direction: column;
  z-index: 9998;
  overflow: hidden;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
#gx-chat-window.open { display: flex; }

/* Header */
#gx-chat-header {
  background: linear-gradient(135deg, #1b5e20, #43a047);
  padding: 14px 18px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: #fff;
}
#gx-chat-header .gx-title {
  font-weight: 700;
  font-size: 15px;
  display: flex;
  align-items: center;
  gap: 8px;
}
#gx-chat-header .gx-subtitle {
  font-size: 11px;
  opacity: 0.85;
  margin-top: 2px;
}
#gx-chat-close {
  background: none;
  border: none;
  color: #fff;
  font-size: 20px;
  cursor: pointer;
  line-height: 1;
  padding: 0;
}

/* Messages area */
#gx-chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  background: #f9fafb;
}

.gx-msg {
  max-width: 82%;
  padding: 10px 14px;
  border-radius: 14px;
  font-size: 13.5px;
  line-height: 1.5;
  word-wrap: break-word;
}
.gx-msg.bot {
  background: #fff;
  border: 1px solid #e0e0e0;
  align-self: flex-start;
  border-bottom-left-radius: 4px;
  color: #222;
}
.gx-msg.user {
  background: linear-gradient(135deg, #1b5e20, #43a047);
  color: #fff;
  align-self: flex-end;
  border-bottom-right-radius: 4px;
}
.gx-msg.typing {
  background: #fff;
  border: 1px solid #e0e0e0;
  align-self: flex-start;
  color: #888;
  font-style: italic;
}

/* Quick action chips */
#gx-quick-actions {
  padding: 8px 12px;
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  background: #f9fafb;
  border-top: 1px solid #f0f0f0;
}
.gx-chip {
  background: #e8f5e9;
  border: 1px solid #a5d6a7;
  color: #1b5e20;
  border-radius: 20px;
  padding: 5px 12px;
  font-size: 12px;
  cursor: pointer;
  transition: background 0.2s;
  white-space: nowrap;
}
.gx-chip:hover { background: #c8e6c9; }

/* Input area */
#gx-chat-input-area {
  display: flex;
  padding: 10px 12px;
  gap: 8px;
  border-top: 1px solid #eee;
  background: #fff;
}
#gx-chat-input {
  flex: 1;
  border: 1px solid #ddd;
  border-radius: 22px;
  padding: 9px 14px;
  font-size: 13px;
  outline: none;
  resize: none;
  font-family: inherit;
}
#gx-chat-input:focus { border-color: #43a047; }
#gx-chat-send {
  background: linear-gradient(135deg, #1b5e20, #43a047);
  border: none;
  border-radius: 50%;
  width: 38px;
  height: 38px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: transform 0.15s;
}
#gx-chat-send:hover { transform: scale(1.08); }
#gx-chat-send svg { width: 18px; height: 18px; fill: #fff; }

/* Mobile responsive */
@media (max-width: 420px) {
  #gx-chat-window { width: calc(100vw - 24px); right: 12px; bottom: 90px; }
  #gx-chat-toggle { right: 16px; bottom: 16px; }
}
</style>

<!-- Floating Toggle Button -->
<button id="gx-chat-toggle" title="Chat with Goxieng Assistant" aria-label="Open chat">
  <span id="gx-chat-dot"></span>
  <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
    <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
  </svg>
</button>

<!-- Chat Window -->
<div id="gx-chat-window" role="dialog" aria-label="Goxieng Smart Assistant">
  <div id="gx-chat-header">
    <div>
      <div class="gx-title">🤖 Goxieng Assistant</div>
      <div class="gx-subtitle">Powered by AI · Always here to help</div>
    </div>
    <button id="gx-chat-close" aria-label="Close chat">×</button>
  </div>

  <div id="gx-chat-messages">
    <!-- Messages injected by JS -->
  </div>

  <div id="gx-quick-actions">
    <span class="gx-chip" data-msg="What is my wallet balance?">💰 My Balance</span>
    <span class="gx-chip" data-msg="Suggest message templates based on my history">💡 Templates</span>
    <span class="gx-chip" data-msg="How do I send a bulk SMS?">📱 Send SMS</span>
    <span class="gx-chip" data-msg="How does voice messaging work?">🎙️ Voice</span>
    <span class="gx-chip" data-msg="What are the pricing rates?">💵 Pricing</span>
  </div>

  <div id="gx-chat-input-area">
    <textarea id="gx-chat-input" rows="1" placeholder="Ask me anything..." aria-label="Type your message"></textarea>
    <button id="gx-chat-send" aria-label="Send message">
      <svg viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
    </button>
  </div>
</div>

<script>
(function() {
  // ── Config (injected from PHP) ──
  const USER_ID    = <?= (int) $widget_user_id ?>;
  const API_KEY    = <?= json_encode($widget_api_key) ?>;
  const SITE_URL   = <?= json_encode($site_base_url) ?>;

  // ── State ──
  const conversationHistory = [];
  let isTyping = false;

  // ── Elements ──
  const toggle    = document.getElementById('gx-chat-toggle');
  const window_   = document.getElementById('gx-chat-window');
  const closeBtn  = document.getElementById('gx-chat-close');
  const messages  = document.getElementById('gx-chat-messages');
  const input     = document.getElementById('gx-chat-input');
  const sendBtn   = document.getElementById('gx-chat-send');
  const dot       = document.getElementById('gx-chat-dot');
  const chips     = document.querySelectorAll('.gx-chip');

  // ── Toggle chat ──
  toggle.addEventListener('click', () => {
    window_.classList.toggle('open');
    dot.style.display = 'none';
    if (window_.classList.contains('open') && conversationHistory.length === 0) {
      addBotMessage("👋 Hi! I'm your Goxieng Smart Assistant. I can check your wallet balance, suggest message templates from your history, and answer questions about SMS and Voice messaging.\n\nHow can I help you today?");
    }
    if (window_.classList.contains('open')) input.focus();
  });

  closeBtn.addEventListener('click', () => window_.classList.remove('open'));

  // ── Quick action chips ──
  chips.forEach(chip => {
    chip.addEventListener('click', () => {
      const msg = chip.getAttribute('data-msg');
      sendMessage(msg);
    });
  });

  // ── Send on Enter (Shift+Enter for newline) ──
  input.addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      sendMessage(input.value.trim());
    }
  });
  sendBtn.addEventListener('click', () => sendMessage(input.value.trim()));

  // ── Add message to UI ──
  function addBotMessage(text) {
    const div = document.createElement('div');
    div.className = 'gx-msg bot';
    div.textContent = text;
    messages.appendChild(div);
    messages.scrollTop = messages.scrollHeight;
    return div;
  }

  function addUserMessage(text) {
    const div = document.createElement('div');
    div.className = 'gx-msg user';
    div.textContent = text;
    messages.appendChild(div);
    messages.scrollTop = messages.scrollHeight;
  }

  function showTyping() {
    const div = document.createElement('div');
    div.className = 'gx-msg typing';
    div.id = 'gx-typing';
    div.textContent = 'Goxieng Assistant is typing...';
    messages.appendChild(div);
    messages.scrollTop = messages.scrollHeight;
  }

  function removeTyping() {
    const el = document.getElementById('gx-typing');
    if (el) el.remove();
  }

  // ── Fetch live data from your PHP APIs ──
  async function fetchBalance() {
    try {
      const res  = await fetch(`${SITE_URL}/backend/api_get_balance.php?user_id=${USER_ID}&api_key=${encodeURIComponent(API_KEY)}`);
      const data = await res.json();
      if (data.success) return `Your current wallet balance is ₦${data.wallet_balance.toLocaleString('en-NG', {minimumFractionDigits: 2})}.`;
      return "I couldn't retrieve your balance right now. Please try again.";
    } catch { return "Network error fetching balance. Please check your connection."; }
  }

  async function fetchTemplates() {
    try {
      const res  = await fetch(`${SITE_URL}/backend/api_suggest_templates.php?user_id=${USER_ID}&api_key=${encodeURIComponent(API_KEY)}`);
      const data = await res.json();
      if (data.success && data.suggestions.length > 0) {
        let summary = `Based on your last ${data.total_messages_analyzed} messages, here are your top template suggestions:\n\n`;
        data.suggestions.forEach((s, i) => {
          summary += `${i + 1}. 📂 ${s.category} (${s.message_count} messages sent)\n`;
          summary += `   Template: "${s.suggested_template}"\n\n`;
        });
        summary += "Would you like to use any of these as a starting point for your next message?";
        return summary;
      }
      return "No message history found yet to suggest templates from. Send some messages first and I'll learn your patterns!";
    } catch { return "Network error fetching templates. Please try again."; }
  }

  // ── Main send function ──
  async function sendMessage(text) {
    if (!text || isTyping) return;
    input.value = '';
    isTyping = true;

    addUserMessage(text);
    showTyping();

    // Check for balance/template requests before calling Claude API
    // to inject live data into the conversation context
    let liveDataContext = '';
    const lowerText = text.toLowerCase();

    if (lowerText.includes('balance') || lowerText.includes('wallet') || lowerText.includes('credit')) {
      liveDataContext = await fetchBalance();
    } else if (lowerText.includes('template') || lowerText.includes('suggest') || lowerText.includes('history')) {
      liveDataContext = await fetchTemplates();
    }

    // Build messages for Claude API
    conversationHistory.push({ role: 'user', content: text });

    const systemPrompt = `You are the Goxieng Smart Assistant, a helpful AI agent embedded in Goxieng Bulk SMS — a Nigerian bulk messaging platform (goxiengbulksms.com.ng) that helps businesses send bulk SMS and voice messages.

Your job is to:
1. Answer questions about the platform — how to send SMS, voice messages, wallet top-up, pricing
2. Help users compose professional message templates (church announcements, event invites, meeting reminders, OTPs, birthday messages)
3. Provide wallet balance and template suggestions when live data is available
4. Be friendly, concise, and helpful — speak naturally to Nigerian business users

Platform facts you know:
- SMS costs ₦6 per message (160 characters)
- Voice messaging is available using real recorded voices (not robotic)
- Users can paste phone numbers from Excel or Word
- Wallet top-up is done from the dashboard
- Supported message types: Church/Religious, Meeting/Event, OTP, Promotions, Birthday/Greetings

${liveDataContext ? `LIVE DATA FROM USER'S ACCOUNT:\n${liveDataContext}\n\nUse this live data directly in your response — do not say you cannot access it, because it has already been fetched for you.` : ''}

Keep responses under 120 words unless showing templates. Be warm and professional. Use ₦ for Naira.`;

    try {
      const response = await fetch('https://api.anthropic.com/v1/messages', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          model: 'claude-sonnet-4-6',
          max_tokens: 1000,
          system: systemPrompt,
          messages: conversationHistory
        })
      });

      const data = await response.json();
      const reply = data.content?.[0]?.text || "Sorry, I couldn't process that. Please try again.";

      conversationHistory.push({ role: 'assistant', content: reply });

      removeTyping();
      addBotMessage(reply);

    } catch (err) {
      removeTyping();
      addBotMessage("Sorry, I'm having trouble connecting right now. Please try again in a moment.");
      conversationHistory.pop(); // remove failed user message
    }

    isTyping = false;
  }
})();
</script>
