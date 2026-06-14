# Goxieng Smart Assistant
### Enterprise Agents Track — Agents League Hackathon @ AI Skills Fest 2026

## 🎯 Overview

**Goxieng Smart Assistant** is an AI agent built with **Groq AI API (Llama 3.3)** that integrates with **Goxieng** — a live, production bulk SMS / Voice messaging platform serving customers across Nigeria (loan companies, churches, event organizers).

The agent connects to Goxieng's real MySQL database via a lightweight PHP API layer and provides:

1. **Customer Support** — answers questions about wallet balance, pricing, and how to use the platform (SMS, Voice, WhatsApp).
2. **Grounded Template Intelligence** — analyzes a user's actual message history (`sms_logs`) and suggests reusable message templates grouped by category (Loan Reminders, Church Announcements, Meeting Invites, etc.), helping users save time on repetitive bulk sends.
3. **Real-time Wallet Checks** — users can ask "what's my balance?" and get a live answer from the production database.

This is not a demo on fake data — it runs against a **real production system with real users and real message history**, demonstrating how agentic AI can be adopted into an existing SME business today.

---

## 🏗️ Architecture

```
┌─────────────────────────┐
│   Groq AI Agent / Goxieng Smart Assistant (the "brain") │
└────────────┬─────────────┘
             │ HTTPS (REST calls)
             ▼
┌─────────────────────────────────────┐
│      Goxieng PHP API Layer           │
│  (hosted on goxiengbulksms.com.ng)   │
│                                       │
│  • api_get_balance.php               │
│  • api_get_history.php               │
│  • api_suggest_templates.php         │
└────────────┬──────────────────────────┘
             │ MySQL queries
             ▼
┌─────────────────────────────────────┐
│         MySQL Database               │
│  • user_balance (wallet credits)     │
│  • sms_logs (message history)        │
└─────────────────────────────────────┘
```

### Flow Example — Template Suggestion
1. User (in Groq Ai Studio chat): *"Can you suggest a template based on my message history?"*
2. Agent calls `api_suggest_templates.php?user_id=123&api_key=...`
3. PHP API queries `sms_logs`, classifies messages into categories (Loan Reminder, Church, Meeting, etc.) using keyword-pattern matching, and generalizes the most recent message in each category into a reusable template (replacing amounts, dates, and times with placeholders).
4. Agent responds with grounded, data-backed suggestions — e.g. *"You've sent 14 loan reminder messages. Here's a template you can reuse: 'Dear customer, your loan repayment of ₦{AMOUNT} is due on {DATE}...'"*

---

## 🔌 API Endpoints

All endpoints require an `api_key` parameter for authentication.

| Endpoint | Method | Parameters | Returns |
|---|---|---|---|
| `api_get_balance.php` | GET/POST | `user_id`, `api_key` | Current wallet balance (NGN) |
| `api_get_history.php` | GET/POST | `user_id`, `limit`, `api_key` | Recent sent messages |
| `api_suggest_templates.php` | GET/POST | `user_id`, `api_key` | Categorized template suggestions based on message history |

---

## 🧠 How the Template Intelligence Works

The `api_suggest_templates.php` endpoint:

1. Pulls the user's last 300 text messages from `sms_logs`
2. Classifies each message into categories using keyword pattern matching:
   - **Loan Reminder** — repayment, due, outstanding, etc.
   - **Church / Religious** — service, fellowship, prayer, etc.
   - **Meeting / Event** — agenda, venue, schedule, etc.
   - **Promotional / Marketing** — discount, offer, promo, etc.
   - **OTP / Verification** — code, pin, verify, etc.
   - **Birthday / Greetings** — birthday, congratulations, etc.
3. For the most frequent category, it generalizes the most recent message by replacing variable data (amounts, dates, times) with placeholders (`{AMOUNT}`, `{DATE}`, `{TIME}`)
4. Returns a ranked list of suggestions with usage frequency and a ready-to-reuse template

---

## 🛠️ Tech Stack

- **Groq AI Agent** — agent orchestration and conversational interface
- **PHP 8** — REST API layer (existing Goxieng backend, hosted on cPanel)
- **MySQL** — production database (`user_balance`, `sms_logs` tables)
- **Africa's Talking API** — underlying SMS/Voice delivery (existing integration)

---

## 🚀 Setup Instructions

1. Deploy the three PHP files (`api_get_balance.php`, `api_get_history.php`, `api_suggest_templates.php`) to `/backend/` on the Goxieng server
2. Update the `$validApiKey` constant in each file with a secure key
3. In Copilot Studio:
   - Create a new agent
   - Add a custom connector pointing to the Goxieng API base URL
   - Configure three actions mapped to the three endpoints
   - Add conversational topics: "Check Balance", "View History", "Suggest Template"
4. Test with a real `user_id` from the Goxieng database

---

## 👥 Team

- [Add team member names and roles here]

---

## 📹 Demo Video

[Link to demo video — to be added]

---

## 🔐 Security Notes

- API keys are placeholder values in this repo — replace before production use
- No customer phone numbers or personal data are included in this repository
- Database credentials are not committed (see `.gitignore`)
