(function () {
    var toggle, panel, messagesEl, inputEl, sendBtn;

    function ensureElements() {
        if (toggle) return;

        toggle = document.createElement('button');
        toggle.className = 'chatbot-toggle';
        toggle.type = 'button';
        toggle.textContent = 'AI';

        panel = document.createElement('div');
        panel.className = 'chatbot-panel';
        panel.style.display = 'none';

        var header = document.createElement('div');
        header.className = 'chatbot-header';
        header.textContent = 'OTSP Assistant';
        panel.appendChild(header);

        messagesEl = document.createElement('div');
        messagesEl.className = 'chatbot-messages';
        panel.appendChild(messagesEl);

        var inputBar = document.createElement('div');
        inputBar.className = 'chatbot-input';

        inputEl = document.createElement('input');
        inputEl.type = 'text';
        inputEl.placeholder = 'Ask about products...';

        sendBtn = document.createElement('button');
        sendBtn.type = 'button';
        sendBtn.textContent = 'Send';

        inputBar.appendChild(inputEl);
        inputBar.appendChild(sendBtn);
        panel.appendChild(inputBar);

        document.body.appendChild(toggle);
        document.body.appendChild(panel);
    }

    function addMessage(text, role) {
        var msg = document.createElement('div');
        msg.className = 'chatbot-message ' + (role === 'user' ? 'user' : 'bot');
        msg.textContent = text;
        messagesEl.appendChild(msg);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function sendMessage() {
        var text = inputEl.value.trim();
        if (!text) return;
        inputEl.value = '';
        addMessage(text, 'user');

        var loadingText = 'Thinking...';
        var loadingMsg = document.createElement('div');
        loadingMsg.className = 'chatbot-message bot';
        loadingMsg.textContent = loadingText;
        messagesEl.appendChild(loadingMsg);
        messagesEl.scrollTop = messagesEl.scrollHeight;

        fetch('/OTSP/backend/chat.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ message: text })
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                messagesEl.removeChild(loadingMsg);
                if (data && data.reply) {
                    addMessage(data.reply, 'bot');
                } else {
                    addMessage('Sorry, something went wrong.', 'bot');
                }
            })
            .catch(function () {
                messagesEl.removeChild(loadingMsg);
                addMessage('Unable to reach the assistant.', 'bot');
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        ensureElements();

        toggle.addEventListener('click', function () {
            panel.style.display = panel.style.display === 'none' ? 'flex' : 'none';
        });

        sendBtn.addEventListener('click', sendMessage);
        inputEl.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                sendMessage();
            }
        });
    });
})();
