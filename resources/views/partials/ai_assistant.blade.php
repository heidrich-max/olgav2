<!-- AI Assistant Global Partial -->
<style>
    /* AI FAB Styles */
    .ai-fab {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary-accent), #6366f1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        cursor: pointer;
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        z-index: 1000;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: none;
    }
    .ai-fab:hover {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 15px 35px rgba(0,0,0,0.4);
    }
    .ai-fab.active {
        transform: scale(0) rotate(90deg);
        opacity: 0;
    }

    /* AI Chat Window Styles */
    .ai-chat-window {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 380px;
        height: 550px;
        background: rgba(30, 41, 59, 0.8);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        z-index: 1001;
        opacity: 0;
        transform: translateY(20px) scale(0.95);
        pointer-events: none;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        overflow: hidden;
    }
    .ai-chat-window.active {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: all;
    }
    .ai-chat-header {
        padding: 20px;
        background: rgba(255,255,255,0.05);
        border-bottom: 1px solid var(--glass-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .ai-chat-header h3 { font-size: 1.1rem; margin: 0; display: flex; align-items: center; gap: 10px; color: #fff; }
    .close-ai { background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; transition: color 0.3s; }
    .close-ai:hover { color: #ef4444; }

    .ai-chat-messages {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 15px;
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,0.1) transparent;
    }
    .ai-msg {
        max-width: 85%;
        padding: 12px 16px;
        border-radius: 15px;
        font-size: 0.95rem;
        line-height: 1.5;
        position: relative;
    }
    .ai-msg.bot {
        align-self: flex-start;
        background: rgba(255,255,255,0.08);
        color: #e2e8f0;
        border-bottom-left-radius: 2px;
    }
    .ai-msg.user {
        align-self: flex-end;
        background: var(--primary-accent);
        color: white;
        border-bottom-right-radius: 2px;
    }
    .ai-typing {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 10px;
        display: none;
    }

    .ai-chat-input-area {
        padding: 20px;
        background: rgba(255,255,255,0.03);
        border-top: 1px solid var(--glass-border);
        display: flex;
        gap: 10px;
    }
    .ai-chat-input-area input {
        flex: 1;
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid var(--glass-border);
        border-radius: 10px;
        padding: 10px 15px;
        color: #fff;
        outline: none;
        transition: border-color 0.3s;
    }
    .ai-chat-input-area input:focus { border-color: var(--primary-accent); }
    .ai-send-btn {
        background: var(--primary-accent);
        border: none;
        width: 42px;
        height: 42px;
        border-radius: 10px;
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }
    .ai-send-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
</style>

<button class="ai-fab" id="aiFabGlobal">
    <i class="fas fa-robot"></i>
</button>

<div class="ai-chat-window" id="aiChatWindowGlobal">
    <div class="ai-chat-header">
        <h3><i class="fas fa-magic"></i> OLGA Assistent</h3>
        <button class="close-ai" id="closeAiGlobal">&times;</button>
    </div>
    <div class="ai-chat-messages" id="aiMessagesGlobal">
        <div class="ai-msg bot">👋 Hallo! Ich bin dein OLGA-Assistent. Wie kann ich dir auf dieser Seite helfen?</div>
    </div>
    <div class="ai-typing" id="aiTypingGlobal" style="padding: 0 20px;">
        <i class="fas fa-spinner fa-spin"></i> GPT-4 schreibt...
    </div>
    <div class="ai-chat-input-area">
        <input type="text" id="aiInputGlobal" placeholder="Frage etwas...">
        <button class="ai-send-btn" id="aiSendBtnGlobal">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const aiFab = document.getElementById('aiFabGlobal');
        const aiChatWindow = document.getElementById('aiChatWindowGlobal');
        const closeAi = document.getElementById('closeAiGlobal');
        const aiInput = document.getElementById('aiInputGlobal');
        const aiSendBtn = document.getElementById('aiSendBtnGlobal');
        const aiMessages = document.getElementById('aiMessagesGlobal');
        const aiTyping = document.getElementById('aiTypingGlobal');

        if (!aiFab) return;

        aiFab.addEventListener('click', () => {
            aiChatWindow.classList.add('active');
            aiFab.classList.add('active');
            aiInput.focus();
        });

        closeAi.addEventListener('click', () => {
            aiChatWindow.classList.remove('active');
            aiFab.classList.remove('active');
        });

        function appendMessage(role, text) {
            const div = document.createElement('div');
            div.className = `ai-msg ${role}`;
            div.innerText = text;
            aiMessages.appendChild(div);
            aiMessages.scrollTop = aiMessages.scrollHeight;
        }

        async function askAi() {
            const prompt = aiInput.value.trim();
            if (!prompt) return;

            appendMessage('user', prompt);
            aiInput.value = '';
            aiInput.disabled = true;
            aiSendBtn.disabled = true;
            aiTyping.style.display = 'block';

            try {
                const response = await fetch('{{ route("manufacturers.ai") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ 
                        prompt: prompt,
                        url: window.location.href,
                        page_title: document.title
                    })
                });

                const data = await response.json();
                if (data.answer) {
                    appendMessage('bot', data.answer);
                } else if (data.error) {
                    appendMessage('bot', 'Fehler: ' + data.error);
                }
            } catch (error) {
                appendMessage('bot', 'Ein technischer Fehler ist aufgetreten.');
            } finally {
                aiInput.disabled = false;
                aiSendBtn.disabled = false;
                aiTyping.style.display = 'none';
                aiInput.focus();
            }
        }

        aiSendBtn.addEventListener('click', askAi);
        aiInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') askAi();
        });
    });
</script>
