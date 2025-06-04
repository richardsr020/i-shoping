(function(){
  function esc(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
  function qs(sel){return document.querySelector(sel);}
  function fmtTime(ts){if(!ts) return ''; const d=new Date(ts.replace(' ','T')); if(isNaN(d.getTime())) return ''; return d.getHours()+':'+String(d.getMinutes()).padStart(2,'0');}

  document.addEventListener('DOMContentLoaded', async () => {
    if(!window.APP_URL || !window.CURRENT_USER_ID) return;

    const listEl = qs('.conversations-list');
    const messagesEl = qs('.messages-container');
    const inputEl = qs('.message-input');
    const sendEl = qs('.send-btn');
    if(!listEl || !messagesEl || !inputEl || !sendEl) return;

    const apiList = `${window.APP_URL}?page=chat&action=list`;
    const apiPoll = `${window.APP_URL}?page=chat&action=poll`;
    const apiSend = `${window.APP_URL}?page=chat&action=send`;

    let activeConversationId = 0;
    let afterId = 0;
    let pollHandle = null;

    function clearActive(){
      listEl.querySelectorAll('.conversation-item').forEach(i=>i.classList.remove('active'));
    }

    function setHeader(title, subtitle){
      const h3 = qs('.chat-partner-info h3');
      const p = qs('.chat-partner-info p');
      if(h3) h3.textContent = title || 'Conversation';
      if(p) p.textContent = subtitle || '';
    }

    function renderMsg(m){
      const mine = Number(m.sender_user_id) === Number(window.CURRENT_USER_ID);
      const wrap = document.createElement('div');
      wrap.className = mine ? 'message sent' : 'message received';
      wrap.innerHTML = `<div class="message-text">${esc(m.body||'')}</div><div class="message-time">${fmtTime(m.created_at)}</div>`;
      messagesEl.appendChild(wrap);
      afterId = Math.max(afterId, Number(m.id||0));
    }

    async function poll(){
      if(!activeConversationId) return;
      try{
        const res = await fetch(apiPoll,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({conversation_id:activeConversationId,after_id:afterId})});
        const data = await res.json().catch(()=>({}));
        if(!res.ok || !data.success) return;
        (data.messages||[]).forEach(renderMsg);
        if((data.messages||[]).length){messagesEl.scrollTop = messagesEl.scrollHeight;}
      }catch(e){}
    }

    function startPolling(){
      if(pollHandle) clearInterval(pollHandle);
      pollHandle = setInterval(poll, 1500);
    }

    async function openConversation(convId, title, subtitle, el){
      activeConversationId = Number(convId||0);
      afterId = 0;
      messagesEl.innerHTML = '';
      clearActive();
      if(el) el.classList.add('active');
      setHeader(title, subtitle);
      await poll();
      messagesEl.scrollTop = messagesEl.scrollHeight;
      startPolling();
    }

    async function loadConversations(){
      const res = await fetch(apiList);
      const data = await res.json().catch(()=>({}));
      if(!res.ok || !data.success) return [];
      return data.conversations||[];
    }

    function renderConversationRow(c){
      const item = document.createElement('div');
      item.className = 'conversation-item';
      item.dataset.conversationId = c.id;
      const name = c.shop_owner_id == window.CURRENT_USER_ID ? (c.buyer_name||c.buyer_email||'Client') : (c.shop_name||'Boutique');
      const subtitle = c.shop_owner_id == window.CURRENT_USER_ID ? (c.shop_name||'') : (c.buyer_name||c.buyer_email||'');
      item.innerHTML = `
        <div class="conversation-avatar"><i class="fas ${c.shop_owner_id == window.CURRENT_USER_ID ? 'fa-user' : 'fa-store'}"></i></div>
        <div class="conversation-details">
          <div class="conversation-header">
            <div class="conversation-name">${esc(name)}</div>
            <div class="conversation-time">${esc(fmtTime(c.last_message_at))}</div>
          </div>
          <div class="conversation-preview">${esc(c.last_message||'')}</div>
        </div>`;
      item.addEventListener('click',()=>openConversation(c.id, name, subtitle, item));
      return item;
    }

    async function send(){
      const body = (inputEl.value||'').trim();
      if(!body || !activeConversationId) return;
      inputEl.value='';
      try{
        await fetch(apiSend,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({conversation_id:activeConversationId,body})});
        await poll();
        messagesEl.scrollTop = messagesEl.scrollHeight;
      }catch(e){}
    }

    inputEl.addEventListener('keydown',(e)=>{if(e.key==='Enter' && !e.shiftKey){e.preventDefault();send();}});
    sendEl.addEventListener('click',send);

    const convs = await loadConversations();
    listEl.innerHTML='';
    convs.forEach(c=>listEl.appendChild(renderConversationRow(c)));
    if(convs[0]){
      const first = listEl.querySelector('.conversation-item');
      first && first.click();
    } else {
      setHeader('Aucune conversation', '');
      messagesEl.innerHTML = '';
    }
  });
})();
