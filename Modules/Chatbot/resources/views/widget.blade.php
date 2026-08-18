<!-- Bouton Flottant de l'Assistant IA -->
<div id="ai-assistant-wrapper" style="position:fixed; right:24px; bottom:24px; z-index:9998; display:flex; flex-direction:column; align-items:flex-end; gap:12px;">
    <!-- Bulle de conseil de l'IA (cachée par défaut) -->
    <div id="ai-tip-bubble" style="display:none; background:white; border-radius:12px; padding:12px 16px; box-shadow:0 10px 25px rgba(37,62,135,0.15); border:1px solid #e5eaf8; max-width:280px; position:relative; margin-bottom:10px; animation:slideIn 0.3s ease;">
        <div style="font-size:12px; color:#253e87; font-weight:700; margin-bottom:4px; display:flex; align-items:center; gap:6px;">
            <i class="fas fa-lightbulb" style="color:#f59e0b;"></i> Conseil de l'IA
        </div>
        <div id="ai-tip-content" style="font-size:11.5px; color:#4a5568; line-height:1.4;"></div>
        <!-- Petite flèche en bas -->
        <div style="position:absolute; bottom:-8px; right:24px; width:0; height:0; border-left:8px solid transparent; border-right:8px solid transparent; border-top:8px solid white;"></div>
        <button onclick="hideAiTip()" style="position:absolute; top:4px; right:4px; background:none; border:none; color:#cbd5e0; cursor:pointer; font-size:10px;"><i class="fas fa-times"></i></button>
    </div>

    <!-- Icône de l'Assistant -->
    <div id="chatbot-toggle-btn" onclick="toggleChatbot()" 
         style="width:56px; height:56px; background:linear-gradient(135deg,#253e87 0%,#1a2d64 100%); border-radius:50%; box-shadow:0 8px 24px rgba(37,62,135,0.3); cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); position:relative;">
        <img src="/img/chatbot_avatar.jpg?v={{ time() }}" style="width:100%; height:100%; border-radius:50%; object-fit:cover; border:2px solid rgba(255,255,255,0.2); box-shadow:0 4px 12px rgba(0,0,0,0.15);">
        <!-- Badge de notification -->
        <span id="ai-notif-badge" style="display:none; position:absolute; top:-2px; right:-2px; width:14px; height:14px; background:#ef4444; border:2px solid white; border-radius:50%;"></span>
    </div>
</div>

<div id="chatbot-container" data-url="{{ url('/chatbot-api/chat') }}" style="display:none; position:fixed; right:24px; bottom:90px; width:440px; height:640px; background:white; border-radius:16px; box-shadow:0 20px 60px rgba(37,62,135,0.22); z-index:9999; flex-direction:column; border:1px solid #e5eaf8; overflow:hidden; font-family:'Inter','Segoe UI',sans-serif;">

    <!-- En-tête Premium -->
    <div style="padding:16px 18px; background:linear-gradient(135deg,#253e87 0%,#1a2d64 100%); color:white; display:flex; justify-content:space-between; align-items:center; flex-shrink:0;">
        <div style="display:flex; align-items:center; gap:10px;">
            <img src="/img/chatbot_avatar.jpg?v={{ time() }}" style="width:38px; height:38px; border-radius:10px; object-fit:cover; border:1px solid rgba(255,255,255,0.3);">
            <div>
                <div style="font-weight:700; font-size:14px; letter-spacing:0.3px;">RH Flow AI <span style="font-size:10px; background:rgba(74,222,128,0.2); border:1px solid rgba(74,222,128,0.4); color:#4ade80; padding:2px 6px; border-radius:20px; font-weight:600; margin-left:4px;">AGENT</span></div>
                <div style="font-size:11px; opacity:0.75; display:flex; align-items:center; gap:4px;">
                    <span id="status-dot" style="width:7px; height:7px; background:#4ade80; border-radius:50%; display:inline-block; animation:pulse 2s infinite;"></span>
                    <span id="status-text">Expert RH & Paie • En ligne</span>
                </div>
            </div>
        </div>
        <div style="display:flex; gap:8px; align-items:center;">
            <button onclick="generateReport()" id="report-btn" title="Générer le rapport professionnel"
                style="display:none; background:rgba(255,255,255,0.2); border:1px solid rgba(255,255,255,0.3); color:white; padding:5px 10px; border-radius:8px; cursor:pointer; font-size:11px; font-weight:600; transition:all 0.2s;"
                onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                📄 Rapport
            </button>
            <button onclick="clearChatHistory()" title="Nouvelle conversation"
                style="background:none; border:none; color:rgba(255,255,255,0.7); cursor:pointer; padding:4px; border-radius:6px; transition:all 0.2s; font-size:14px;"
                onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,0.7)'">
                <i class="fas fa-redo-alt"></i>
            </button>
            <button onclick="toggleChatbot()"
                style="background:none; border:none; color:rgba(255,255,255,0.7); cursor:pointer; padding:4px; border-radius:6px; font-size:16px;"
                onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,0.7)'">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Sujets rapides -->
    <div id="quick-topics" style="padding:10px 14px; background:#f0f4ff; border-bottom:1px solid #e5eaf8; overflow-x:auto; white-space:nowrap; flex-shrink:0; scrollbar-width:none;">
        <span style="font-size:10px; color:#7a8bbf; margin-right:6px; font-weight:600;">ACTIONS RAPIDES :</span>
        <button onclick="quickMsg('Lance la paie complète du mois en cours : audit, vérification des variables, calcul des bulletins et livre de paie.')" class="quick-chip" style="background:linear-gradient(135deg,#253e87,#1a2d64); color:white; border-color:#253e87;">🚀 Automatiser la paie</button>
        <button onclick="quickMsg('Audit global de l\'entreprise : contrats, congés, alertes.')" class="quick-chip">🔍 Audit RH</button>
        <button onclick="quickMsg('Génère le livre de paie du dernier mois.')" class="quick-chip">📊 Livre de paie</button>
        <button onclick="quickMsg('Quelles sont les cotisations CNPS en Côte d\'Ivoire ?')" class="quick-chip">📋 CNPS</button>
        <button onclick="quickMsg('Règles des congés annuels ?')" class="quick-chip">🏖️ Congés</button>
    </div>

    <!-- Zone de messages -->
    <div id="chatbot-messages" style="flex:1; padding:16px; overflow-y:auto; background:#f8faff; display:flex; flex-direction:column; gap:12px; scroll-behavior:smooth;"></div>

    <!-- Indicateur de traitement Agent -->
    <div id="agent-thinking" style="display:none; padding:8px 16px; background:#f0f4ff; border-top:1px solid #e5eaf8; flex-shrink:0;">
        <div style="display:flex; align-items:center; gap:8px; font-size:11.5px; color:#253e87; font-weight:600;">
            <div style="display:flex; gap:3px; align-items:center;">
                <span style="width:6px; height:6px; background:#253e87; border-radius:50%; animation:bounce 1s infinite;"></span>
                <span style="width:6px; height:6px; background:#253e87; border-radius:50%; animation:bounce 1s 0.2s infinite;"></span>
                <span style="width:6px; height:6px; background:#253e87; border-radius:50%; animation:bounce 1s 0.4s infinite;"></span>
            </div>
            <span id="agent-step-text">L'agent analyse votre demande...</span>
        </div>
    </div>

    <!-- Zone du Rapport -->
    <div id="report-view" style="display:none; position:absolute; top:0; left:0; right:0; bottom:0; background:white; z-index:10; flex-direction:column; overflow:hidden;">
        <div style="padding:14px 18px; background:linear-gradient(135deg,#253e87 0%,#1a2d64 100%); color:white; display:flex; justify-content:space-between; align-items:center; flex-shrink:0;">
            <div style="display:flex; align-items:center; gap:8px; font-weight:700; font-size:14px;">
                <i class="fas fa-file-alt"></i> Rapport Professionnel RH
            </div>
            <div style="display:flex; gap:8px;">
                <button onclick="printReport()" title="Imprimer" style="background:rgba(255,255,255,0.2); border:1px solid rgba(255,255,255,0.3); color:white; padding:5px 10px; border-radius:8px; cursor:pointer; font-size:11px; font-weight:600;">
                    🖨️ Imprimer
                </button>
                <button onclick="closeReport()" style="background:none; border:none; color:rgba(255,255,255,0.7); cursor:pointer; font-size:16px;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div id="report-content" style="flex:1; overflow-y:auto; padding:20px; font-family:'Inter',sans-serif;"></div>
        <div style="padding:12px 16px; border-top:1px solid #e5eaf8; flex-shrink:0;">
            <button onclick="closeReport()" style="width:100%; padding:10px; background:#253e87; color:white; border:none; border-radius:10px; cursor:pointer; font-weight:600; font-size:13px;">
                ← Retour au chat
            </button>
        </div>
    </div>

    <!-- Zone de saisie -->
    <div style="padding:12px 14px; border-top:1px solid #e5eaf8; display:flex; gap:8px; background:white; flex-shrink:0;">
        <input type="text" id="chatbot-input"
            placeholder="Ex: Lance la paie du mois, recrute Jean Dupont..."
            style="flex:1; border:1.5px solid #e5eaf8; border-radius:10px; padding:9px 14px; font-size:12.5px; outline:none; transition:border 0.2s; color:#2c3e50;"
            onkeypress="if(event.key === 'Enter') sendChatMessage()"
            onfocus="this.style.borderColor='#253e87'" onblur="this.style.borderColor='#e5eaf8'">
        <button onclick="sendChatMessage()" id="send-btn"
            style="background:linear-gradient(135deg,#253e87,#1a2d64); color:white; border:none; border-radius:10px; width:38px; height:38px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:opacity 0.2s; flex-shrink:0;"
            onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
            <i class="fas fa-paper-plane" style="font-size:13px;"></i>
        </button>
    </div>
</div>

<style>
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }
    @keyframes bounce { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-4px)} }
    @keyframes slideIn { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }
    @keyframes stepAppear { from{opacity:0;transform:translateX(-8px)} to{opacity:1;transform:translateX(0)} }
    .quick-chip {
        display:inline-block; background:white; border:1px solid #dee4f7; color:#253e87;
        padding:4px 10px; border-radius:20px; font-size:10.5px; font-weight:600;
        cursor:pointer; margin-right:6px; transition:all 0.2s; white-space:nowrap;
    }
    .quick-chip:hover { background:#253e87; color:white; border-color:#253e87; }
    #chatbot-messages::-webkit-scrollbar { width:4px; }
    #chatbot-messages::-webkit-scrollbar-track { background:transparent; }
    #chatbot-messages::-webkit-scrollbar-thumb { background:#c5cce8; border-radius:4px; }
    #quick-topics::-webkit-scrollbar { display:none; }

    /* Journal d'activité Agent */
    .agent-log-group {
        display:flex; flex-direction:column; gap:4px;
        animation: slideIn 0.3s ease;
    }
    .agent-log-header {
        font-size:10px; font-weight:700; color:#7a8bbf; text-transform:uppercase;
        letter-spacing:0.5px; margin-bottom:2px; padding-left:2px;
    }
    .agent-step {
        display:flex; align-items:flex-start; gap:8px;
        background:white; border:1px solid #e5eaf8; border-radius:8px;
        padding:8px 10px; font-size:11.5px;
        animation: stepAppear 0.25s ease;
        border-left: 3px solid #253e87;
    }
    .agent-step.success { border-left-color:#22c55e; }
    .agent-step.error   { border-left-color:#ef4444; }
    .agent-step-icon { font-size:14px; flex-shrink:0; margin-top:1px; }
    .agent-step-body { flex:1; min-width:0; }
    .agent-step-label {
        font-weight:700; color:#253e87; font-size:11px;
        margin-bottom:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
    }
    .agent-step-summary { color:#5a6a8a; font-size:10.5px; line-height:1.4; }
    .agent-step-badge {
        font-size:9px; font-weight:700; padding:2px 6px; border-radius:20px;
        flex-shrink:0; margin-top:2px;
    }
    .badge-success { background:#dcfce7; color:#166534; }
    .badge-error   { background:#fee2e2; color:#991b1b; }
</style>

<script>
    let chatHistory = JSON.parse(localStorage.getItem('rhflow_chat_history_v3')) || [
        {role: 'assistant', content: '👋 Bonjour ! Je suis **Donalde**, votre assistante RH Flow AI.\n\nJe peux **agir directement** dans votre système :\n• 🚀 **Automatiser la paie complète** (audit → calcul → validation)\n• 👤 Recruter un employé\n• 🏖️ Valider des congés\n• 📊 Générer le livre de paie\n\nComment puis-je vous aider aujourd\'hui ?'}
    ];

    // ===== MIGRATION : corriger les anciens historiques où content est un objet =====
    chatHistory = chatHistory.map(m => {
        if (m && typeof m.content === 'object' && m.content !== null) {
            // Extraire le texte depuis l'objet
            const c = m.content;
            m.content = (typeof c.reply === 'string') ? c.reply
                      : (typeof c.content === 'string') ? c.content
                      : JSON.stringify(c);
        }
        return m;
    }).filter(m => m && m.role && (typeof m.content === 'string'));
    // Si l'historique est vide après migration, remettre le message de bienvenue
    if (chatHistory.length === 0) {
        chatHistory = [{role: 'assistant', content: '👋 Bonjour ! Je suis **Donalde**. Comment puis-je vous aider ?'}];
    }
    // ===== FIN MIGRATION =====

    function quickMsg(text) {
        document.getElementById('chatbot-input').value = text;
        sendChatMessage();
    }

    function toggleChatbot() {
        const c = document.getElementById('chatbot-container');
        c.style.display = (c.style.display === 'none' || c.style.display === '') ? 'flex' : 'none';
        if (c.style.display === 'flex') { renderMessages(); updateReportButton(); }
    }

    function formatMessage(text) {
        if (!text) return '';
        
        // Sécurité : si c'est un objet, on tente d'extraire la réponse
        if (typeof text === 'object') {
            text = text.reply || text.content || JSON.stringify(text);
        }

        try {
            return text
                // Titres
                .replace(/^# (.+)$/gm, '<h4 style="color:#253e87;margin:12px 0 6px;font-weight:800;border-bottom:1px solid #eef1ff;padding-bottom:4px;">$1</h4>')
                .replace(/^## (.+)$/gm, '<h5 style="color:#253e87;margin:10px 0 4px;font-weight:700;">$1</h5>')
                .replace(/^### (.+)$/gm, '<h6 style="color:#253e87;margin:8px 0 4px;font-weight:700;font-size:12px;">$1</h6>')
                // Gras et Italique
                .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.+?)\*/g, '<em>$1</em>')
                // Listes à puces
                .replace(/^[•\-] (.+)$/gm, '<div style="display:flex;gap:8px;margin:4px 0;"><span style="color:#253e87;">•</span><span>$1</span></div>')
                // Listes numérotées
                .replace(/^\d+\. (.+)$/gm, '<div style="display:flex;gap:8px;margin:4px 0;"><span style="font-weight:700;color:#253e87;">$&</span></div>')
                // Séparateurs
                .replace(/^---$/gm, '<hr style="border:0;border-top:1px solid #eef1ff;margin:12px 0;">')
                // Retours à la ligne
                .replace(/\n/g, '<br>');
        } catch (e) {
            return text;
        }
    }

    function renderMessages() {
        const box = document.getElementById('chatbot-messages');
        box.innerHTML = '';

        chatHistory.forEach(msg => {
            if (msg.role === 'system') return;

            const isBot = msg.role === 'assistant';

            // === 1. ACTIONS DE L'AGENT (Si présentes) ===
            if (isBot && msg.activity && msg.activity.length > 0) {
                msg.activity.forEach(step => {
                    const stepEl = document.createElement('div');
                    stepEl.style.cssText = `
                        background: white; border: 1px solid #e5eaf8; border-radius: 10px;
                        padding: 8px 12px; margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between;
                        box-shadow: 0 2px 6px rgba(37,62,135,0.05); border-left: 4px solid ${step.status === 'success' ? '#22c55e' : '#ef4444'};
                        animation: slideIn 0.3s ease;
                    `;
                    stepEl.innerHTML = `
                        <div style="display:flex; align-items:center; gap:10px;">
                            <span style="font-size:16px;">${step.status === 'success' ? '✅' : '❌'}</span>
                            <div>
                                <div style="font-weight:700; color:#253e87; font-size:11.5px;">${step.label || step.tool}</div>
                                <div style="color:#64748b; font-size:10px; margin-bottom: 4px;">${step.summary || ''}</div>
                                ${step.status === 'success' ? `
                                    <button onclick="window.location.reload()" style="background:#253e87; color:white; border:none; border-radius:6px; padding:3px 8px; font-size:9.5px; cursor:pointer; font-weight:700; display:inline-flex; align-items:center; gap:4px; transition:all 0.2s;" onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                                        <i class="fas fa-sync-alt"></i> Actualiser pour voir
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                        <span style="background:${step.status === 'success' ? '#dcfce7' : '#fee2e2'}; color:${step.status === 'success' ? '#166534' : '#991b1b'}; 
                              padding:2px 8px; border-radius:20px; font-size:9px; font-weight:800; align-self:flex-start;">${step.status === 'success' ? 'OK' : 'ERREUR'}</span>
                    `;
                    box.appendChild(stepEl);
                });
            }

            // === 2. BULLE DE TEXTE ===
            const wrapper = document.createElement('div');
            wrapper.style.cssText = `display:flex; flex-direction:column; align-items:${isBot ? 'flex-start' : 'flex-end'}; margin-bottom:12px;`;

            const bubble = document.createElement('div');
            bubble.style.cssText = `
                padding:12px 16px; border-radius:${isBot ? '4px 16px 16px 16px' : '16px 4px 16px 16px'};
                max-width:92%; font-size:13px; line-height:1.6;
                background:${isBot ? 'white' : '#253e87'};
                color:${isBot ? '#2c3e50' : 'white'};
                box-shadow:${isBot ? '0 3px 12px rgba(37,62,135,0.08)' : '0 4px 12px rgba(37,62,135,0.2)'};
                border:${isBot ? '1px solid #eef1ff' : 'none'};
                word-break:break-word;
            `;
            
            // On s'assure que le contenu est bien une chaîne avant le formatage
            let content = msg.content;
            if (typeof content === 'object') {
                content = content.reply || content.content || JSON.stringify(content);
            }

            bubble.innerHTML = isBot ? formatMessage(content) : content.replace(/\n/g,'<br>');
            wrapper.appendChild(bubble);
            box.appendChild(wrapper);
        });

        box.scrollTop = box.scrollHeight;
        updateReportButton();
    }

    function setAgentThinking(show, stepText = "L'agent analyse votre demande...") {
        const el = document.getElementById('agent-thinking');
        const txt = document.getElementById('agent-step-text');
        const dot = document.getElementById('status-dot');
        const statusTxt = document.getElementById('status-text');

        el.style.display = show ? 'block' : 'none';
        txt.textContent = stepText;

        if (show) {
            dot.style.background = '#f59e0b';
            dot.style.animation = 'pulse 0.5s infinite';
            statusTxt.textContent = '⚙️ Agent en cours d\'exécution...';
        } else {
            dot.style.background = '#4ade80';
            dot.style.animation = 'pulse 2s infinite';
            statusTxt.textContent = 'Expert RH & Paie • En ligne';
        }
    }

    async function sendChatMessage() {
        const input = document.getElementById('chatbot-input');
        const btn   = document.getElementById('send-btn');
        const text  = input.value.trim();
        if (!text) return;

        input.value = '';
        input.disabled = true;
        btn.disabled   = true;

        chatHistory.push({role: 'user', content: text});
        saveHistory();
        renderMessages();
        setAgentThinking(true, "L'agent analyse votre demande...");

        // Simuler étapes visuelles progressives pendant l'attente
        const thinkingSteps = [
            "Lecture du contexte de l'entreprise...",
            "Sélection des outils appropriés...",
            "Exécution des actions en cours...",
            "Vérification des résultats..."
        ];
        let stepIdx = 0;
        const stepInterval = setInterval(() => {
            stepIdx = (stepIdx + 1) % thinkingSteps.length;
            document.getElementById('agent-step-text').textContent = thinkingSteps[stepIdx];
        }, 2200);

        try {
            const apiUrl = document.getElementById('chatbot-container').getAttribute('data-url') || '/chatbot-api/chat';
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({
                    history: chatHistory.slice(0, -1),
                    message: text,
                    context: document.title + " (" + window.location.pathname + ")"
                })
            });

            clearInterval(stepInterval);
            setAgentThinking(false);

            const data = await response.json();
            console.log("RH Flow AI: Data received", data);

            // Extraire correctement le texte et l'activité depuis la réponse serveur
            // Le contrôleur retourne : {reply: {reply: "texte", activity: [...]}}
            let finalReply = "Désolée, j'ai rencontré une erreur de format.";
            let finalActivity = [];

            if (data && data.reply) {
                const payload = data.reply;
                if (typeof payload === 'string') {
                    finalReply = payload;
                } else if (typeof payload === 'object') {
                    // Cas normal : {reply: "texte", activity: [...]}
                    if (typeof payload.reply === 'string') {
                        finalReply = payload.reply;
                        finalActivity = payload.activity || [];
                    } else if (typeof payload.content === 'string') {
                        finalReply = payload.content;
                    } else {
                        finalReply = JSON.stringify(payload);
                    }
                }
            } else if (typeof data === 'string') {
                finalReply = data;
            }

            // Stocker dans l'historique (content DOIT être une chaîne)
            chatHistory.push({
                role:     'assistant',
                content:  finalReply,
                activity: finalActivity
            });
            
            saveHistory();
            renderMessages();
            
            // Scroll vers le bas
            const box = document.getElementById('chatbot-messages');
            setTimeout(() => { box.scrollTop = box.scrollHeight; }, 100);
        } catch (e) {
            clearInterval(stepInterval);
            setAgentThinking(false);
            chatHistory.push({role: 'assistant', content: '⚠️ Erreur de connexion. Vérifiez votre réseau.', activity: []});
            renderMessages();
        } finally {
            input.disabled = false;
            btn.disabled   = false;
            input.focus();
        }
    }

    async function generateReport() {
        const reportView    = document.getElementById('report-view');
        const reportContent = document.getElementById('report-content');
        reportView.style.display = 'flex';
        reportContent.innerHTML  = '<div style="text-align:center;padding:40px;"><div style="font-size:32px;margin-bottom:12px;">⏳</div><p style="color:#7a8bbf;font-weight:600;">L\'IA génère votre rapport professionnel...</p></div>';

        try {
            const response = await fetch('/chatbot-api/report', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({history: chatHistory})
            });
            const data    = await response.json();
            const summary = data.summary;

            if (summary && summary.type === 'html') {
                const header = `<div style="text-align:center;padding:15px 0 20px;border-bottom:2px solid #253e87;margin-bottom:20px;">
                    <div style="font-size:13px;color:#7a8bbf;font-weight:600;letter-spacing:1px;">RH FLOW</div>
                    <h2 style="color:#253e87;margin:4px 0;font-size:18px;">Rapport de Clôture de Paie</h2>
                    <div style="color:#9ba8c6;font-size:12px;">${new Date().toLocaleDateString('fr-FR',{weekday:'long',year:'numeric',month:'long',day:'numeric'})}</div>
                </div>`;
                reportContent.innerHTML = header + summary.html;
            } else {
                reportContent.innerHTML = '<p style="color:red;">Erreur lors de la génération du rapport.</p>';
            }
        } catch (e) {
            reportContent.innerHTML = '<p style="color:red;">Erreur de connexion.</p>';
        }
    }

    function printReport() {
        const content = document.getElementById('report-content').innerHTML;
        const win = window.open('', '_blank');
        win.document.write(`<!DOCTYPE html><html><head><title>Rapport RH Flow</title>
        <style>body{font-family:'Segoe UI',sans-serif;padding:30px;color:#333;} h2{color:#253e87;} table{width:100%;border-collapse:collapse;} th{background:#253e87;color:white;padding:8px;} td{border:1px solid #ddd;padding:8px;}</style>
        </head><body>${content}</body></html>`);
        win.document.close();
        win.print();
    }

    function closeReport() { document.getElementById('report-view').style.display = 'none'; }

    function saveHistory() {
        // Ne pas stocker activity dans localStorage (trop lourd) — on garde uniquement role/content
        const toSave = chatHistory.map(m => ({role: m.role, content: m.content}));
        localStorage.setItem('rhflow_chat_history_v3', JSON.stringify(toSave));
    }

    function updateReportButton() {
        document.getElementById('report-btn').style.display = chatHistory.length > 3 ? 'block' : 'none';
    }

    function clearChatHistory() {
        if (confirm('Démarrer une nouvelle conversation ?')) {
            chatHistory = [{role: 'assistant', content: '👋 Nouvelle conversation ! Comment puis-je vous aider ?', activity: []}];
            saveHistory();
            renderMessages();
            closeReport();
        }
    }

    // Fonctions pour le Guide IA Proactif - Exposées globalement
    window.showAiTip = function(message, duration = 8000) {
        const bubble = document.getElementById('ai-tip-bubble');
        const content = document.getElementById('ai-tip-content');
        const badge = document.getElementById('ai-notif-badge');
        
        if (!bubble || !content) return;
        
        content.innerHTML = message;
        bubble.style.display = 'block';
        if(badge) badge.style.display = 'block';
        
        // Vibration légère pour attirer l'attention
        const btn = document.getElementById('chatbot-toggle-btn');
        if(btn) {
            btn.style.transform = 'scale(1.1) rotate(5deg)';
            setTimeout(() => { btn.style.transform = 'scale(1) rotate(0deg)'; }, 300);
        }

        if (duration > 0) {
            setTimeout(() => {
                window.hideAiTip();
            }, duration);
        }
    };

    window.hideAiTip = function() {
        const bubble = document.getElementById('ai-tip-bubble');
        const badge = document.getElementById('ai-notif-badge');
        if(bubble) bubble.style.display = 'none';
        if(badge) badge.style.display = 'none';
    };

    // --- Robot Suiveur de Souris (Version Définitive) ---
    let mouseX = window.innerWidth - 85;
    let mouseY = window.innerHeight - 85;
    let robotX = mouseX;
    let robotY = mouseY;
    let chatbotWidget = null;

    document.addEventListener('mousemove', (e) => {
        if (e.clientX < 320) {
            mouseX = e.clientX + 40; 
            mouseY = e.clientY; 
        } else {
            mouseX = window.innerWidth - 85;
            mouseY = window.innerHeight - 85;
        }
    });

    function animateRobot() {
        if (!chatbotWidget) {
            chatbotWidget = document.getElementById('ai-assistant-wrapper');
            if (chatbotWidget) {
                chatbotWidget.style.bottom = 'auto';
                chatbotWidget.style.right = 'auto';
                chatbotWidget.style.transition = 'none';
                chatbotWidget.style.display = 'flex';
                chatbotWidget.style.flexDirection = 'column';
                chatbotWidget.style.alignItems = 'flex-end';
            }
        }

        const chatWindow = document.getElementById('chatbot-container');
        const isChatOpen = chatWindow && (chatWindow.style.display === 'flex' || chatWindow.style.display === 'block');

        // On ne suit la souris que si le chat est FERMÉ
        if (chatbotWidget && !isChatOpen) {
            robotX += (mouseX - robotX) * 0.12; // Un peu plus rapide (0.12)
            robotY += (mouseY - robotY) * 0.12;
            
            chatbotWidget.style.left = Math.round(robotX - 30) + 'px';
            chatbotWidget.style.top = Math.round(robotY - (chatbotWidget.offsetHeight || 60) + 30) + 'px';
        }
        requestAnimationFrame(animateRobot);
    }
    
    // Forcer le premier démarrage
    animateRobot();

    // --- Dictionnaire d'Expertise RH (Intelligence Navigation) ---
    const appExpertise = {
        'dashboard': "votre tour de contrôle pour surveiller la masse salariale et les effectifs.",
        'listing des exercices': "l'historique de vos périodes de paie pour consulter les anciens bulletins.",
        'éléments du brut': "la saisie des primes, heures supplémentaires et avantages imposables.",
        'avantage en nature': "la gestion des véhicules, logements et autres avantages non-monétaires.",
        'retenues salaire': "le paramétrage des cotisations sociales, impôts et retenues diverses.",
        'prêts': "le suivi des emprunts accordés aux employés et leurs plans de remboursement.",
        'remboursements de frais': "la validation des notes de frais et débours de vos collaborateurs.",
        'calcul salaire': "le moteur de calcul pour générer les bulletins nets du mois.",
        'gestion de paie': "votre espace de travail pour piloter le cycle de paie complet.",
        'gestion des congés': "le suivi des absences, des soldes et des validations de congés.",
        'recrutement': "votre tunnel pour publier des offres et suivre les candidatures.",
        'entreprise': "la configuration de votre structure légale, logo et préférences.",
        'employé' : "la gestion complète des dossiers personnels et des contrats de travail.",
        'configuration': "le paramétrage des départements, postes et barèmes de l'application.",
        'états': "la génération des rapports officiels (DISA, CNPS, Livre de paie).",
        'simulateur': "votre outil de simulation pour tester un calcul de salaire rapidement."
    };

    function getExpertAdvice(text) {
        const cleanText = text.toLowerCase().trim();
        for (let key in appExpertise) {
            if (cleanText.includes(key)) {
                return appExpertise[key];
            }
        }
        return null;
    }

    // --- Narrateur d'Actions Intelligentes (Clics) ---
    function initActionNarrator() {
        document.addEventListener('click', function(e) {
            const target = e.target.closest('a, button, .sidebar-main-item, .submenu-item');
            if (target) {
                const text = (target.innerText || "").split('\n')[0].trim();
                const advice = getExpertAdvice(text);
                
                if (advice) {
                    window.showAiTip(`🎯 <b>Navigation :</b> Ouverture de ${advice}`, 15000);
                }
            }
        }, true);
    }

    // --- Guide IA Interactif (Survol) ---
    function initMenuGuide() {
        const sidebarItems = document.querySelectorAll('.sidebar-main-item, .submenu-item');
        sidebarItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                const text = this.innerText;
                const advice = getExpertAdvice(text);
                if (advice) {
                    window.showAiTip(`🧐 <b>Le saviez-vous ?</b> C'est ici que vous gérez ${advice}`, 15000);
                }
            });
        });
    }

    // Fonctions globalisées
    window.showAiTip = function(message, duration = 15000) {
        const bubble = document.getElementById('ai-tip-bubble');
        const content = document.getElementById('ai-tip-content');
        if (!bubble || !content) return;
        
        content.innerHTML = message;
        bubble.style.display = 'block';
        
        const btn = document.getElementById('chatbot-toggle-btn');
        if(btn) {
            btn.style.transform = 'scale(1.2) rotate(10deg)';
            setTimeout(() => { btn.style.transform = 'scale(1) rotate(0deg)'; }, 400);
        }

        if (window.aiTipTimeout) clearTimeout(window.aiTipTimeout);
        if (duration > 0) {
            window.aiTipTimeout = setTimeout(() => { window.hideAiTip(); }, duration);
        }
    };

    window.hideAiTip = function() {
        const bubble = document.getElementById('ai-tip-bubble');
        if(bubble) bubble.style.display = 'none';
        const badge = document.getElementById('ai-notif-badge');
        if(badge) badge.style.display = 'none';
    };

    const robotStyle = document.createElement('style');
    robotStyle.innerHTML = `
        #ai-assistant-wrapper { 
            pointer-events: none !important; 
            z-index: 1000000 !important; 
        }
        #chatbot-toggle-btn, #ai-tip-bubble { 
            pointer-events: auto !important; 
        }
        @keyframes aiPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); box-shadow: 0 0 30px rgba(105, 108, 255, 0.6); }
            100% { transform: scale(1); }
        }
        #chatbot-toggle-btn { animation: aiPulse 4s infinite ease-in-out; }
    `;
    document.head.appendChild(robotStyle);

    document.addEventListener('DOMContentLoaded', function() {
        renderMessages();
        initMenuGuide();
        initActionNarrator();
        animateRobot();
        
        setTimeout(() => {
            if (chatHistory.length <= 1) {
                window.showAiTip("Je suis là pour vous aider ! 😊", 15000);
            }
        }, 2000);
    });
</script>
