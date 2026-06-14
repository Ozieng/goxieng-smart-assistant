# Goxieng Smart Assistant
### Creative Apps Track — Agents League Hackathon @ AI Skills Fest 2026
### Hosted by Microsoft

## 🎯 Overview

**Goxieng Smart Assistant** is an AI-powered enterprise agent embedded in **Goxieng Bulk SMS** — a live, production bulk SMS and voice messaging platform serving customers across Nigeria (loan companies, churches, event organizers, and businesses).

The agent connects to Goxieng's real MySQL database via a lightweight PHP API layer and provides:

1. **Customer Support** — answers questions about wallet balance, pricing, SMS and Voice messaging
2. **Grounded Template Intelligence** — analyzes a user's actual message history (`sms_logs`) and suggests reusable message templates grouped by category (Church Announcements, Meeting Invites, Event Reminders, OTPs, Birthday Messages)
3. **Real-time Wallet Checks** — users ask "what's my balance?" and get a live answer from the production database
4. **Voice Messaging** — users record real voice messages in the browser which are delivered as phone calls to recipients via Africa's Talking API — replacing the old robotic TTS system

This is not a demo on fake data — it runs against a **real production system with real users and real message history**, demonstrating how agentic AI can be adopted into an existing SME business today.

---

## 💡 Microsoft IQ Integration

This project implements **Foundry IQ** principles throughout:

| Foundry IQ Principle | How Goxieng Smart Assistant Implements It |
|---|---|
| Agentic knowledge retrieval | Agent retrieves live data from MySQL database on every query |
| Connects multiple enterprise sources | Connects to user_balance table, sms_logs table, and Africa's Talking API |
| Enforces permissions | Session-based authentication + API key validation on every endpoint |
| Delivers grounded answers | All balance and template responses are grounded in real enterprise data |
| Reduces hallucination | Agent never guesses balance or templates — always fetches from live database |
| Cited, data-backed responses | Every template suggestion cites message count and category from real history |

---

## 🏗️ Architecture

```
┌─────────────────────────────────────┐
│     Goxieng Smart Assistant          │
│   (Floating AI Chat Widget)          │
│   Powered by Groq AI / Llama 3.3    │
└────────────┬────────────────────────┘
             │ HTTPS (REST calls)
             ▼
┌─────────────────────────────────────┐
│      Goxieng PHP API Layer           │
│  (hosted on goxiengbulksms.com.ng)   │
│                                      │
│  • api_get_balance.php               │
│  • api_get_history.php               │
│  • api_suggest_templates.php         │
│  • chat_proxy.php                    │
└────────────┬─────────────────────────┘
             │ MySQL queries
             ▼
┌─────────────────────────────────────┐
│         MySQL Database               │
│  • user_balance (wallet credits)     │
│  • sms_logs (message history)        │
└─────────────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────┐
│     Africa's Talking API             │
│  • Bulk SMS delivery                 │
│  • Real voice message delivery       │
└─────────────────────────────────────┘
```

### Flow Example — Template Suggestion (Foundry IQ Grounding)
1. User types: *"Suggest message templates based on my history"*
2. Widget calls `api_suggest_templates.php`
3. PHP queries last 300 messages from `sms_logs`
4. Messages classified into categories (Church, Meeting, OTP, etc.)
5. Most recent message in each category generalized into reusable template
6. Agent responds with **grounded, data-backed suggestions** — e.g:
   *"You've sent 300 church messages. Here's your template: 'Hello. Join us for SERVICE. {TIME} this eve @GLORYSPRINGS'"*

---

## 🔌 API Endpoints

All endpoints require an `api_key` parameter for authentication.

| Endpoint | Method | Parameters | Returns |
|---|---|---|---|
| `api_get_balance.php` | GET/POST | `user_id`, `api_key` | Live wallet balance (NGN) |
| `api_get_history.php` | GET/POST | `user_id`, `limit`, `api_key` | Recent sent messages |
| `api_suggest_templates.php` | GET/POST | `user_id`, `api_key` | Categorized template suggestions from real message history |
| `chat_proxy.php` | POST | `messages`, `system`, `widget_token` | AI response via Groq API |

---

## 🧠 How the Template Intelligence Works

The `api_suggest_templates.php` endpoint:

1. Pulls the user's last 300 text messages from `sms_logs`
2. Classifies each message into categories using keyword pattern matching:
   - **Church / Religious** — service, fellowship, prayer, worship, etc.
   - **Meeting / Event** — agenda, venue, schedule, invite, etc.
   - **Promotional / Marketing** — discount, offer, promo, sale, etc.
   - **OTP / Verification** — code, pin, verify, confirm, etc.
   - **Birthday / Greetings** — birthday, congratulations, celebrate, etc.
3. Generalizes the most recent message by replacing variable data with placeholders (`{AMOUNT}`, `{DATE}`, `{TIME}`)
4. Returns ranked suggestions with usage frequency and ready-to-reuse templates

---

## 🎙️ Voice Messaging Upgrade

The platform also includes an upgraded voice messaging feature:
- Users record their **real voice** in the browser using MediaRecorder API
- Audio is uploaded to the server and stored at a public URL
- Africa's Talking API calls the recipient and **plays the recorded voice**
- Replaces the old robotic Text-to-Speech system that customers disliked

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| AI Agent | Groq API (Llama 3.3-70b) |
| Frontend | HTML, CSS, JavaScript (floating widget) |
| Backend | PHP 8 (REST API layer, cPanel hosting) |
| Database | MySQL (user_balance, sms_logs tables) |
| Voice Delivery | Africa's Talking Voice API |
| SMS Delivery | Termii / Africa's Talking SMS API |
| Version Control | GitHub |

---

## 🚀 Setup Instructions

1. Deploy PHP files to `/backend/` on your server
2. Update `$validApiKey` in each file with your secret key
3. Update `$groqApiKey` in `chat_proxy.php` with your Groq API key
4. Add `voice_uploads/` folder with write permissions (755)
5. Include widget on any PHP page:
   ```php
   <?php include('goxieng_chat_widget.php'); ?>
   ```
6. Enable Nigeria geo-permissions on Africa's Talking dashboard

---

## 🌍 Real-World Impact

Goxieng serves Nigerian businesses including:
- **Churches** — weekly service announcements to thousands of members
- **Microfinance companies** — payment reminders and notifications
- **Event organizers** — meeting invites and RSVPs
- **Schools and institutions** — staff and student communications

The AI assistant helps these users work faster by suggesting templates from their own history — reducing the time to compose and send bulk messages from minutes to seconds.

---

## 👥 Team

- **Ozioma Ugorji** — Full Stack Developer & Platform Owner (Goxieng Bulk SMS)
- **Praise Azunna** — Business Analyst
- **Chinwe Pius** — HR and IT Trainee

---

## 📹 Demo Video

# Goxieng Smart Assistant
### Creative Apps Track — Agents League Hackathon @ AI Skills Fest 2026
### Hosted by Microsoft

## 🎯 Overview

**Goxieng Smart Assistant** is an AI-powered enterprise agent embedded in **Goxieng Bulk SMS** — a live, production bulk SMS and voice messaging platform serving customers across Nigeria (loan companies, churches, event organizers, and businesses).

The agent connects to Goxieng's real MySQL database via a lightweight PHP API layer and provides:

1. **Customer Support** — answers questions about wallet balance, pricing, SMS and Voice messaging
2. **Grounded Template Intelligence** — analyzes a user's actual message history (`sms_logs`) and suggests reusable message templates grouped by category (Church Announcements, Meeting Invites, Event Reminders, OTPs, Birthday Messages)
3. **Real-time Wallet Checks** — users ask "what's my balance?" and get a live answer from the production database
4. **Voice Messaging** — users record real voice messages in the browser which are delivered as phone calls to recipients via Africa's Talking API — replacing the old robotic TTS system

This is not a demo on fake data — it runs against a **real production system with real users and real message history**, demonstrating how agentic AI can be adopted into an existing SME business today.

---

## 💡 Microsoft IQ Integration

This project implements **Foundry IQ** principles throughout:

| Foundry IQ Principle | How Goxieng Smart Assistant Implements It |
|---|---|
| Agentic knowledge retrieval | Agent retrieves live data from MySQL database on every query |
| Connects multiple enterprise sources | Connects to user_balance table, sms_logs table, and Africa's Talking API |
| Enforces permissions | Session-based authentication + API key validation on every endpoint |
| Delivers grounded answers | All balance and template responses are grounded in real enterprise data |
| Reduces hallucination | Agent never guesses balance or templates — always fetches from live database |
| Cited, data-backed responses | Every template suggestion cites message count and category from real history |

---

## 🏗️ Architecture

```
┌─────────────────────────────────────┐
│     Goxieng Smart Assistant          │
│   (Floating AI Chat Widget)          │
│   Powered by Groq AI / Llama 3.3    │
└────────────┬────────────────────────┘
             │ HTTPS (REST calls)
             ▼
┌─────────────────────────────────────┐
│      Goxieng PHP API Layer           │
│  (hosted on goxiengbulksms.com.ng)   │
│                                      │
│  • api_get_balance.php               │
│  • api_get_history.php               │
│  • api_suggest_templates.php         │
│  • chat_proxy.php                    │
└────────────┬─────────────────────────┘
             │ MySQL queries
             ▼
┌─────────────────────────────────────┐
│         MySQL Database               │
│  • user_balance (wallet credits)     │
│  • sms_logs (message history)        │
└─────────────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────┐
│     Africa's Talking API             │
│  • Bulk SMS delivery                 │
│  • Real voice message delivery       │
└─────────────────────────────────────┘
```

### Flow Example — Template Suggestion (Foundry IQ Grounding)
1. User types: *"Suggest message templates based on my history"*
2. Widget calls `api_suggest_templates.php`
3. PHP queries last 300 messages from `sms_logs`
4. Messages classified into categories (Church, Meeting, OTP, etc.)
5. Most recent message in each category generalized into reusable template
6. Agent responds with **grounded, data-backed suggestions** — e.g:
   *"You've sent 300 church messages. Here's your template: 'Hello. Join us for SERVICE. {TIME} this eve @GLORYSPRINGS'"*

---

## 🔌 API Endpoints

All endpoints require an `api_key` parameter for authentication.

| Endpoint | Method | Parameters | Returns |
|---|---|---|---|
| `api_get_balance.php` | GET/POST | `user_id`, `api_key` | Live wallet balance (NGN) |
| `api_get_history.php` | GET/POST | `user_id`, `limit`, `api_key` | Recent sent messages |
| `api_suggest_templates.php` | GET/POST | `user_id`, `api_key` | Categorized template suggestions from real message history |
| `chat_proxy.php` | POST | `messages`, `system`, `widget_token` | AI response via Groq API |

---

## 🧠 How the Template Intelligence Works

The `api_suggest_templates.php` endpoint:

1. Pulls the user's last 300 text messages from `sms_logs`
2. Classifies each message into categories using keyword pattern matching:
   - **Church / Religious** — service, fellowship, prayer, worship, etc.
   - **Meeting / Event** — agenda, venue, schedule, invite, etc.
   - **Promotional / Marketing** — discount, offer, promo, sale, etc.
   - **OTP / Verification** — code, pin, verify, confirm, etc.
   - **Birthday / Greetings** — birthday, congratulations, celebrate, etc.
3. Generalizes the most recent message by replacing variable data with placeholders (`{AMOUNT}`, `{DATE}`, `{TIME}`)
4. Returns ranked suggestions with usage frequency and ready-to-reuse templates

---

## 🎙️ Voice Messaging Upgrade

The platform also includes an upgraded voice messaging feature:
- Users record their **real voice** in the browser using MediaRecorder API
- Audio is uploaded to the server and stored at a public URL
- Africa's Talking API calls the recipient and **plays the recorded voice**
- Replaces the old robotic Text-to-Speech system that customers disliked

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| AI Agent | Groq API (Llama 3.3-70b) |
| Frontend | HTML, CSS, JavaScript (floating widget) |
| Backend | PHP 8 (REST API layer, cPanel hosting) |
| Database | MySQL (user_balance, sms_logs tables) |
| Voice Delivery | Africa's Talking Voice API |
| SMS Delivery | Termii / Africa's Talking SMS API |
| Version Control | GitHub |

---

## 🚀 Setup Instructions

1. Deploy PHP files to `/backend/` on your server
2. Update `$validApiKey` in each file with your secret key
3. Update `$groqApiKey` in `chat_proxy.php` with your Groq API key
4. Add `voice_uploads/` folder with write permissions (755)
5. Include widget on any PHP page:
   ```php
   <?php include('goxieng_chat_widget.php'); ?>
   ```
6. Enable Nigeria geo-permissions on Africa's Talking dashboard

---

## 🌍 Real-World Impact

Goxieng serves Nigerian businesses including:
- **Churches** — weekly service announcements to thousands of members
- **Microfinance companies** — payment reminders and notifications
- **Event organizers** — meeting invites and RSVPs
- **Schools and institutions** — staff and student communications

The AI assistant helps these users work faster by suggesting templates from their own history — reducing the time to compose and send bulk messages from minutes to seconds.

---

## 👥 Team

- **Ozioma Ugorji** — Full Stack Developer & Platform Owner (Goxieng Bulk SMS)
- **Praise Azunna** — Business Analyst
- **Chinwe Pius** — HR and IT Trainee

---

## 📹 Demo Video

https://youtu.be/fsHB-nyBzvY

---

## 🔐 Security Notes

- API keys are placeholder values in this repo — replace before production use
- No customer phone numbers or personal data are included in this repository
- Database credentials are not committed
- All endpoints require authentication before returning any data

---

## 🔐 Security Notes

- API keys are placeholder values in this repo — replace before production use
- No customer phone numbers or personal data are included in this repository
- Database credentials are not committed
- All endpoints require authentication before returning any data
