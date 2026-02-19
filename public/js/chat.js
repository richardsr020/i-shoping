(function(){
  function esc(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
  function escAttr(s){return String(s).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;');}
  function qs(sel){return document.querySelector(sel);}
  function fmtTime(ts){if(!ts) return ''; const d=new Date(ts.replace(' ','T')); if(isNaN(d.getTime())) return ''; return d.getHours()+':'+String(d.getMinutes()).padStart(2,'0');}
  function getIntParam(name){
    try{
      const u = new URL(window.location.href);
      const v = u.searchParams.get(name);
      const n = parseInt(v || '0', 10);
      return Number.isFinite(n) ? n : 0;
    } catch(e){
      return 0;
    }
  }

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
    const apiStart = `${window.APP_URL}?page=chat&action=start`;
    const requestedShopId = getIntParam('shop_id');

    let activeConversationId = 0;
    let afterId = 0;
    let pollHandle = null;
    let pollInFlight = false;
    let renderedMessageIds = new Set();
    let pendingProductId = requestedShopId > 0 ? getIntParam('product_id') : 0;

    function setStatus(msg){
      const el = qs('.chat-status');
      if(!el) return;
      el.textContent = msg || '';
      el.style.display = msg ? 'block' : 'none';
    }

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
      const mid = Number(m.id||0);
      if(mid && renderedMessageIds.has(mid)) return;
      const mine = Number(m.sender_user_id) === Number(window.CURRENT_USER_ID);
      const product = (m && m.meta && m.meta.product && typeof m.meta.product === 'object') ? m.meta.product : null;
      const productImage = product && product.image ? String(product.image).trim() : '';
      const productName = product && product.name ? String(product.name).trim() : 'Produit';
      const productHtml = productImage !== ''
        ? `<div class="message-product"><img class="message-product-thumb" src="${escAttr(productImage)}" alt="${escAttr(productName)}"><div class="message-product-name">${esc(productName)}</div></div>`
        : '';
      const wrap = document.createElement('div');
      wrap.className = mine ? 'message sent' : 'message received';
      wrap.innerHTML = `${productHtml}<div class="message-text">${esc(m.body||'')}</div><div class="message-time">${fmtTime(m.created_at)}</div>`;
      messagesEl.appendChild(wrap);
      if(mid) renderedMessageIds.add(mid);
      afterId = Math.max(afterId, mid);
    }

    async function poll(){
      if(!activeConversationId) return;
      if(pollInFlight) return;
      pollInFlight = true;
      try{
        const res = await fetch(apiPoll,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({conversation_id:activeConversationId,after_id:afterId})});
        const data = await res.json().catch(()=>({}));
        if(!res.ok || !data.success){
          setStatus((data && data.error) ? data.error : 'Erreur lors du chargement des messages');
          return;
        }
        setStatus('');
        (data.messages||[]).forEach(renderMsg);
        if((data.messages||[]).length){messagesEl.scrollTop = messagesEl.scrollHeight;}
      }catch(e){
        console.error(e);
        setStatus('Erreur réseau');
      }finally{
        pollInFlight = false;
      }
    }

    function startPolling(){
      if(pollHandle) clearInterval(pollHandle);
      pollHandle = setInterval(poll, 1500);
    }

    async function openConversation(convId, title, subtitle, el){
      activeConversationId = Number(convId||0);
      afterId = 0;
      pollInFlight = false;
      renderedMessageIds = new Set();
      messagesEl.innerHTML = '';
      clearActive();
      if(el) el.classList.add('active');
      setHeader(title, subtitle);
      await poll();
      messagesEl.scrollTop = messagesEl.scrollHeight;
      startPolling();
    }

    async function loadConversations(){
      try{
        const res = await fetch(apiList);
        const data = await res.json().catch(()=>({}));
        if(!res.ok || !data.success){
          setStatus((data && data.error) ? data.error : 'Erreur lors du chargement des conversations');
          return [];
        }
        setStatus('');
        return data.conversations||[];
      } catch(e){
        console.error(e);
        setStatus('Erreur réseau');
        return [];
      }
    }

    async function ensureConversationForShop(shopId){
      if(!shopId) return 0;
      try{
        const res = await fetch(apiStart,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({shop_id:shopId})});
        const data = await res.json().catch(()=>({}));
        if(!res.ok || !data.success){
          setStatus((data && data.error) ? data.error : 'Impossible de démarrer la conversation');
          return 0;
        }
        setStatus('');
        return Number(data.conversation_id||0);
      }catch(e){
        console.error(e);
        setStatus('Erreur réseau');
        return 0;
      }
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
      if(!activeConversationId){
        setStatus('Sélectionne une conversation avant d\'envoyer un message');
        return;
      }
      if(!body){
        setStatus('Message vide');
        return;
      }
      inputEl.value='';
      try{
        const payload = {conversation_id:activeConversationId,body};
        if(pendingProductId > 0){
          payload.product_id = pendingProductId;
        }
        const res = await fetch(apiSend,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)});
        const data = await res.json().catch(()=>({}));
        if(!res.ok || !data.success){
          setStatus((data && data.error) ? data.error : 'Erreur lors de l\'envoi');
          return;
        }
        if(payload.product_id){
          pendingProductId = 0;
        }
        setStatus('');
        await poll();
        messagesEl.scrollTop = messagesEl.scrollHeight;
        try{ document.dispatchEvent(new CustomEvent('chat:message-sent')); }catch(_e){}
      }catch(e){
        console.error(e);
        setStatus('Erreur réseau');
      }
    }

    inputEl.addEventListener('keydown',(e)=>{if(e.key==='Enter' && !e.shiftKey){e.preventDefault();send();}});
    sendEl.addEventListener('click',send);

    const shopId = getIntParam('shop_id');
    const forcedConversationId = await ensureConversationForShop(shopId);

    setStatus('');
    const convs = await loadConversations();
    listEl.innerHTML='';
    convs.forEach(c=>listEl.appendChild(renderConversationRow(c)));

    if(forcedConversationId){
      const el = listEl.querySelector(`.conversation-item[data-conversation-id="${forcedConversationId}"]`);
      el && el.click();
    }

    if(!forcedConversationId){
      if(convs[0]){
        const first = listEl.querySelector('.conversation-item');
        first && first.click();
      } else {
        setHeader('Aucune conversation', '');
        messagesEl.innerHTML = '';
      }
    }
  });
})();
