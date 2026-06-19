@extends('layouts.app')

@section('title', 'Chat dengan Admin - UMKMART')

@section('styles')
<style>
    @media (max-width: 767px) {
        .mobile-chat-input-container {
            position: fixed !important;
            left: 0 !important;
            right: 0 !important;
            bottom: calc(3.5rem + env(safe-area-inset-bottom)) !important;
            width: 100% !important;
            z-index: 40 !important;
            background-color: #ffffff !important;
            box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.05) !important;
            padding: 16px !important;
            border-top: 1px solid #f1f5f9 !important;
        }
        .dark .mobile-chat-input-container {
            background-color: #020617 !important;
            border-top-color: #1e293b !important;
            box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.3) !important;
        }
        #chat-messages-container {
            padding-bottom: 160px !important;
        }
        .chat-contact-header {
            position: sticky !important;
            top: 4rem !important;
            z-index: 30 !important;
            background-color: #ffffff !important;
            border-bottom: 1px solid #f1f5f9 !important;
            padding-top: 12px !important;
            padding-bottom: 12px !important;
        }
        .dark .chat-contact-header {
            background-color: #020617 !important;
            border-bottom-color: #1e293b !important;
        }
    }
    @media (min-width: 768px) {
        .mobile-chat-input-container {
            position: static !important;
            width: auto !important;
            background-color: #ffffff !important;
            box-shadow: none !important;
            padding: 16px !important;
            border-top: 1px solid #f1f5f9 !important;
        }
        .dark .mobile-chat-input-container {
            background-color: #020617 !important;
            border-top-color: #1e293b !important;
        }
        body {
            overflow: hidden !important;
            height: 100vh !important;
        }
        main.flex-grow {
            height: calc(100vh - 80px) !important;
            overflow: hidden !important;
            display: flex;
            flex-direction: column;
            padding-bottom: 0 !important;
        }
        .user-chat-layout {
            flex-grow: 1 !important;
            height: 0 !important;
            min-height: 0 !important;
        }
    }
</style>
@endsection

@section('content')
<h1 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-6 flex items-center gap-2 text-left">
    <span class="material-icons text-emerald-600 dark:text-emerald-400">forum</span>
    Layanan Chat Pelanggan
</h1>

<div class="user-chat-layout bg-white dark:bg-slate-950 border border-slate-100 dark:border-slate-850 rounded-3xl overflow-visible md:overflow-hidden shadow-sm flex flex-col md:flex-row h-[600px] md:h-[600px]">
    
    <!-- Sidebar Contact (Left) -->
    <div class="w-full md:w-80 md:min-w-[320px] md:max-w-[320px] h-[120px] md:h-full border-r border-slate-100 dark:border-slate-850 flex flex-col bg-slate-50/50 dark:bg-slate-950 flex-shrink-0 overflow-hidden">
        <div class="p-4 border-b border-slate-100 dark:border-slate-850 font-bold text-xs uppercase text-slate-400 text-left flex-shrink-0">
            Customer Service
        </div>
        <div class="flex-grow overflow-y-auto overflow-x-hidden">
            <!-- Active Contact Item -->
            <div class="flex items-center gap-3 p-4 bg-emerald-50/30 dark:bg-emerald-950/10 border-l-4 border-emerald-600">
                <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-400 font-bold flex items-center justify-center">
                    CS
                </div>
                <div class="text-left">
                    <h4 class="font-bold text-xs text-slate-850 dark:text-white">Admin UMKMART</h4>
                    <p class="text-[9px] text-emerald-600 dark:text-emerald-400 mt-0.5 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block animate-ping"></span> Online
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Chat Window (Right) -->
    <div class="flex-grow flex flex-col h-[480px] md:h-full overflow-visible md:overflow-hidden bg-white dark:bg-slate-950 relative">
        
        <!-- Window Header -->
        <div class="chat-contact-header px-6 py-4 border-b border-slate-100 dark:border-slate-850 flex items-center gap-3 bg-slate-50/20 dark:bg-slate-900/10">
            <div class="w-9 h-9 rounded-full bg-emerald-600 text-white font-bold text-xs flex items-center justify-center">
                CS
            </div>
            <div class="text-left">
                <h4 class="font-bold text-xs text-slate-850 dark:text-white">Admin UMKMART</h4>
                <p class="text-[9px] text-slate-400">Pertanyaan seputar produk & pemesanan</p>
            </div>
        </div>
        
        <!-- Conversation Area -->
        <div id="chat-messages-container" class="flex-grow p-6 pb-6 overflow-y-auto overflow-x-hidden flex flex-col gap-4 bg-slate-50/30 dark:bg-slate-950/20">
            @forelse($messages as $msg)
                @php
                    $isUser = $msg->sender_id === Auth::user()->id;
                    $editedStr = $msg->updated_at != $msg->created_at ? ' <span class="text-[9px] opacity-60">(diedit)</span>' : '';
                @endphp
                <div class="msg-bubble-container flex {{ $isUser ? 'justify-end' : 'justify-start' }}" data-msg-id="{{ $msg->id }}">
                    <div class="group flex items-center max-w-[70%] {{ $isUser ? 'flex-row-reverse' : 'flex-row' }}" style="{{ $isUser ? 'flex-direction: row-reverse;' : '' }}">
                        <div class="relative msg-bubble-wrapper">
                            <div class="rounded-2xl p-4 shadow-sm text-xs text-left
                                {{ $isUser 
                                    ? 'bg-emerald-600 text-white rounded-tr-none' 
                                    : 'bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-850 text-slate-800 dark:text-slate-200 rounded-tl-none' }}">
                                @if($msg->image)
                                    <div class="mb-2 max-w-sm rounded-xl overflow-hidden">
                                        <a href="/uploads/chat/{{ $msg->image }}" target="_blank">
                                            <img src="/uploads/chat/{{ $msg->image }}" class="max-h-60 object-cover rounded-xl hover:opacity-90 transition-opacity">
                                        </a>
                                    </div>
                                @endif
                                <p class="msg-text @if(!$msg->message_text) hidden @endif">{{ $msg->message_text }}</p>
                                <p class="text-[8px] text-right mt-1.5 leading-none {{ $isUser ? 'text-emerald-200' : 'text-slate-400' }}">
                                    <span class="msg-edited">{!! $editedStr !!}</span> {{ $msg->created_at->format('H:i') }}
                                </p>
                            </div>
                            @if($isUser)
                                <div id="msg-menu-{{ $msg->id }}" class="hidden absolute bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-lg p-1" style="position: absolute; right: 0; bottom: 100%; margin-bottom: 4px; width: 96px; z-index: 9999;">
                                    <button onclick="triggerEdit({{ $msg->id }}, '{{ addslashes($msg->message_text) }}')" class="flex items-center gap-1 w-full p-2 text-left text-[10px] font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg">
                                        <span class="material-icons text-xs">edit</span> Edit
                                    </button>
                                    <button onclick="deleteMessage({{ $msg->id }})" class="flex items-center gap-1 w-full p-2 text-left text-[10px] font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-955/20 rounded-lg">
                                        <span class="material-icons text-xs text-rose-600">delete</span> Hapus
                                    </button>
                                </div>
                            @endif
                        </div>
                        @if($isUser)
                            <div class="msg-menu-container mx-2">
                                <button onclick="toggleMsgMenu(event, {{ $msg->id }})" class="p-1 rounded-full text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-150 dark:hover:bg-slate-800 transition-colors" title="Menu">
                                    <span class="material-icons text-base leading-none">more_vert</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="my-auto text-center py-8">
                    <span class="material-icons text-slate-300 dark:text-slate-700 text-5xl">chat_bubble_outline</span>
                    <h4 class="font-bold text-xs text-slate-700 dark:text-slate-400 mt-2">Mulai Obrolan</h4>
                    <p class="text-[10px] text-slate-400 max-w-xs mx-auto mt-1">Kirim pesan pertama Anda ke Admin UMKMART untuk menanyakan seputar produk atau pemesanan.</p>
                </div>
            @endforelse
        </div>
        
        <!-- Input Form -->
        <div class="mobile-chat-input-container md:static md:p-4 md:border-t md:bg-white md:dark:bg-slate-950 md:shadow-none flex-shrink-0">
            <!-- Selected Image Preview Area -->
            <div id="chat-image-preview-container" class="hidden px-4 py-2 border-b border-slate-100 dark:border-slate-850 flex items-center justify-between bg-slate-50 dark:bg-slate-900 rounded-t-2xl">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-lg overflow-hidden bg-slate-200 dark:bg-slate-800 flex-shrink-0">
                        <img id="chat-image-preview" class="w-full h-full object-cover">
                    </div>
                    <span id="chat-image-preview-filename" class="text-xs text-slate-500 truncate max-w-[150px] sm:max-w-xs"></span>
                </div>
                <button type="button" id="chat-image-clear-btn" class="p-1 rounded-full hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-400 hover:text-rose-600 transition-colors">
                    <span class="material-icons text-lg">close</span>
                </button>
            </div>
            
            <form id="chat-send-form" class="flex items-center gap-2 md:gap-3 p-1" enctype="multipart/form-data">
                <input type="file" id="chat-image-input" accept="image/png, image/jpeg, image/jpg, image/webp" class="hidden">
                <label for="chat-image-input" class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-500 hover:text-emerald-600 flex items-center justify-center transition-colors cursor-pointer flex-shrink-0" title="Unggah Gambar">
                    <span class="material-icons">attach_file</span>
                </label>
                <input type="text" id="chat-message-input" autocomplete="off" placeholder="Ketik pesan Anda disini..." class="flex-grow bg-slate-100 dark:bg-slate-900 border-none rounded-xl py-3 px-4 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 text-slate-800 dark:text-white">
                <button type="submit" class="w-11 h-11 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center transition-colors flex-shrink-0 shadow-lg shadow-emerald-600/10">
                    <span class="material-icons">send</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const adminId = {{ $admin->id }};
    const userId = {{ Auth::user()->id }};
    const container = document.getElementById('chat-messages-container');
    const form = document.getElementById('chat-send-form');
    const input = document.getElementById('chat-message-input');
    
    const imageInput = document.getElementById('chat-image-input');
    const previewContainer = document.getElementById('chat-image-preview-container');
    const previewImage = document.getElementById('chat-image-preview');
    const previewFilename = document.getElementById('chat-image-preview-filename');
    const clearPreviewBtn = document.getElementById('chat-image-clear-btn');
    
    // Scroll to bottom
    function scrollBottom() {
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }
    
    scrollBottom();

    // Image Preview Handling
    if (imageInput) {
        imageInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                previewFilename.textContent = file.name;
                const reader = new FileReader();
                reader.onload = (event) => {
                    previewImage.src = event.target.result;
                    previewContainer.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                clearPreview();
            }
        });
    }

    function clearPreview() {
        if (imageInput) imageInput.value = '';
        if (previewContainer) previewContainer.classList.add('hidden');
        if (previewImage) previewImage.src = '';
        if (previewFilename) previewFilename.textContent = '';
    }

    if (clearPreviewBtn) {
        clearPreviewBtn.addEventListener('click', clearPreview);
    }
    
    // Send message via AJAX
    if (form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const text = input.value.trim();
            const file = imageInput ? imageInput.files[0] : null;
            if (!text && !file) return;
            
            // Set sending state
            input.disabled = true;
            const btn = form.querySelector('button[type="submit"]');
            const btnIcon = btn.querySelector('.material-icons');
            const originalIcon = btnIcon.textContent;
            btn.disabled = true;
            btn.style.opacity = '0.7';
            btnIcon.textContent = 'hourglass_empty';

            const formData = new FormData();
            formData.append('receiver_id', adminId);
            if (text) formData.append('message_text', text);
            if (file) formData.append('chat_image', file);
            
            fetch('/api/chat/send', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                // Restore state
                input.disabled = false;
                btn.disabled = false;
                btn.style.opacity = '1';
                btnIcon.textContent = originalIcon;
                
                if (data.success) {
                    input.value = '';
                    clearPreview();
                    pollMessages();
                } else {
                    alert(data.message || 'Gagal mengirim pesan. Silakan coba lagi.');
                }
            })
            .catch(() => {
                // Restore state
                input.disabled = false;
                btn.disabled = false;
                btn.style.opacity = '1';
                btnIcon.textContent = originalIcon;
                alert('Terjadi kesalahan koneksi. Silakan coba lagi.');
            });
        });
    }
    
    function escapeHTML(str) {
        return str.replace(/[&<>'"]/g, 
            tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag)
        );
    }

    function escapeJSString(str) {
        return str.replace(/\\/g, '\\\\')
                  .replace(/'/g, "\\'")
                  .replace(/"/g, '\\"')
                  .replace(/\n/g, '\\n')
                  .replace(/\r/g, '\\r');
    }

    // Toggle menu
    function toggleMsgMenu(event, id) {
        event.stopPropagation();
        document.querySelectorAll('[id^="msg-menu-"]').forEach(menu => {
            if (menu.id !== `msg-menu-${id}`) {
                menu.classList.add('hidden');
                const wrapper = menu.closest('.msg-bubble-wrapper');
                if (wrapper) wrapper.style.zIndex = '';
            }
        });
        const menu = document.getElementById(`msg-menu-${id}`);
        if (menu) {
            const wrapper = menu.closest('.msg-bubble-wrapper');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                if (wrapper) wrapper.style.zIndex = '50';
            } else {
                menu.classList.add('hidden');
                if (wrapper) wrapper.style.zIndex = '';
            }
        }
    }

    // Trigger edit
    function triggerEdit(id, text) {
        const menu = document.getElementById(`msg-menu-${id}`);
        if (menu) {
            menu.classList.add('hidden');
            const wrapper = menu.closest('.msg-bubble-wrapper');
            if (wrapper) wrapper.style.zIndex = '';
        }
        editMessage(id, text);
    }

    // Close menus on outside click
    document.addEventListener('click', () => {
        document.querySelectorAll('[id^="msg-menu-"]').forEach(menu => {
            menu.classList.add('hidden');
            const wrapper = menu.closest('.msg-bubble-wrapper');
            if (wrapper) wrapper.style.zIndex = '';
        });
    });

    // Edit message function
    function editMessage(id, oldText) {
        const bubble = container.querySelector(`[data-msg-id="${id}"] .msg-text`);
        if (!bubble) return;
        
        if (bubble.querySelector('input')) return;
        
        const originalHTML = bubble.innerHTML;
        bubble.innerHTML = `
            <div class="flex flex-col gap-1.5 mt-1 min-w-[200px]">
                <input type="text" class="edit-input w-full bg-slate-50 dark:bg-slate-850 text-slate-800 dark:text-white text-xs border border-slate-300 dark:border-slate-700 rounded-lg py-1.5 px-2.5 focus:outline-none focus:ring-1 focus:ring-emerald-500" value="${escapeHTML(oldText)}">
                <div class="flex justify-end gap-1">
                    <button class="cancel-btn text-[10px] bg-slate-150 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 px-2 py-0.5 rounded-md">Batal</button>
                    <button class="save-btn text-[10px] bg-emerald-600 hover:bg-emerald-700 text-white px-2 py-0.5 rounded-md">Simpan</button>
                </div>
            </div>
        `;
        
        const input = bubble.querySelector('.edit-input');
        input.focus();
        input.select();
        
        const cancelBtn = bubble.querySelector('.cancel-btn');
        const saveBtn = bubble.querySelector('.save-btn');
        
        cancelBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            bubble.innerHTML = originalHTML;
        });
        
        saveBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const newText = input.value.trim();
            if (!newText) return;
            
            fetch(`/api/chat/edit/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    message_text: newText
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    bubble.innerHTML = escapeHTML(newText);
                    pollMessages();
                } else {
                    alert('Gagal mengedit pesan.');
                    bubble.innerHTML = originalHTML;
                }
            })
            .catch(() => {
                alert('Terjadi kesalahan koneksi.');
                bubble.innerHTML = originalHTML;
            });
        });
        
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveBtn.click();
            } else if (e.key === 'Escape') {
                cancelBtn.click();
            }
        });
    }

    // Delete message function
    function deleteMessage(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus pesan ini?')) return;
        
        fetch(`/api/chat/delete/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const el = container.querySelector(`[data-msg-id="${id}"]`);
                if (el) el.remove();
                pollMessages();
            } else {
                alert('Gagal menghapus pesan.');
            }
        })
        .catch(() => alert('Terjadi kesalahan koneksi.'));
    }
    
    // Poll for new, updated and deleted messages (DOM-diffing)
    function pollMessages() {
        fetch(`/api/chat/messages?other_id=${adminId}`)
            .then(res => res.json())
            .then(messages => {
                const empty = container.querySelector('.my-auto');
                if (empty && messages.length > 0) empty.remove();

                const fetchedIds = messages.map(m => m.id);

                // 1. Remove deleted messages from DOM
                const domMessages = container.querySelectorAll('.msg-bubble-container');
                domMessages.forEach(el => {
                    const id = parseInt(el.getAttribute('data-msg-id'));
                    if (!fetchedIds.includes(id)) {
                        el.remove();
                    }
                });

                // 2. Add or update messages
                let hasNewMessage = false;
                messages.forEach(msg => {
                    let el = container.querySelector(`[data-msg-id="${msg.id}"]`);
                    const isUser = msg.sender_id === userId;
                    const dateObj = new Date(msg.created_at || Date.now());
                    const timeStr = dateObj.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':');
                    const editedStr = msg.updated_at !== msg.created_at ? ' <span class="text-[9px] opacity-60">(diedit)</span>' : '';

                    const newText = msg.message_text || '';

                    if (el) {
                        if (el.querySelector('.edit-input')) return;
                        
                        const textEl = el.querySelector('.msg-text');
                        if (textEl) {
                            if (textEl.innerHTML !== escapeHTML(newText)) {
                                textEl.innerHTML = escapeHTML(newText);
                                if (newText) {
                                    textEl.classList.remove('hidden');
                                } else {
                                    textEl.classList.add('hidden');
                                }
                                let editedEl = el.querySelector('.msg-edited');
                                if (editedEl) editedEl.innerHTML = editedStr;
                                
                                const editBtn = el.querySelector('button[onclick^="triggerEdit"]');
                                if (editBtn) {
                                    editBtn.setAttribute('onclick', `triggerEdit(${msg.id}, '${escapeJSString(newText)}')`);
                                }
                            }
                        }
                    } else {
                        // Append new message
                        const div = document.createElement('div');
                        div.className = 'msg-bubble-container flex ' + (isUser ? 'justify-end' : 'justify-start');
                        div.setAttribute('data-msg-id', msg.id);
                        
                        let actionsHtml = '';
                        let menuHtml = '';
                        if (isUser) {
                            actionsHtml = `
                                <div class="msg-menu-container mx-2">
                                    <button onclick="toggleMsgMenu(event, ${msg.id})" class="p-1 rounded-full text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-150 dark:hover:bg-slate-800 transition-colors" title="Menu">
                                        <span class="material-icons text-base leading-none">more_vert</span>
                                    </button>
                                </div>
                            `;
                            menuHtml = `
                                <div id="msg-menu-${msg.id}" class="hidden absolute bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-lg p-1" style="position: absolute; right: 0; bottom: 100%; margin-bottom: 4px; width: 96px; z-index: 9999;">
                                    <button onclick="triggerEdit(${msg.id}, '${escapeJSString(newText)}')" class="flex items-center gap-1 w-full p-2 text-left text-[10px] font-semibold text-slate-700 dark:text-slate-355 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg">
                                        <span class="material-icons text-xs">edit</span> Edit
                                    </button>
                                    <button onclick="deleteMessage(${msg.id})" class="flex items-center gap-1 w-full p-2 text-left text-[10px] font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-955/20 rounded-lg">
                                        <span class="material-icons text-xs text-rose-600">delete</span> Hapus
                                    </button>
                                </div>
                            `;
                        }

                        let imageHtml = '';
                        if (msg.image) {
                            imageHtml = `
                                <div class="mb-2 max-w-sm rounded-xl overflow-hidden">
                                    <a href="/uploads/chat/${msg.image}" target="_blank">
                                        <img src="/uploads/chat/${msg.image}" class="max-h-60 object-cover rounded-xl hover:opacity-90 transition-opacity">
                                    </a>
                                </div>
                            `;
                        }
                        const textHidden = newText ? '' : 'hidden';

                        div.innerHTML = `
                            <div class="group flex items-center max-w-[70%] ${isUser ? 'flex-row-reverse' : 'flex-row'}" style="${isUser ? 'flex-direction: row-reverse;' : ''}">
                                <div class="relative msg-bubble-wrapper">
                                    <div class="rounded-2xl p-4 shadow-sm text-xs text-left
                                        ${isUser 
                                            ? 'bg-emerald-600 text-white rounded-tr-none' 
                                            : 'bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-850 text-slate-800 dark:text-slate-200 rounded-tl-none'}">
                                        ${imageHtml}
                                        <p class="msg-text ${textHidden}">${escapeHTML(newText)}</p>
                                        <p class="text-[8px] text-right mt-1.5 leading-none ${isUser ? 'text-emerald-200' : 'text-slate-400'}">
                                            <span class="msg-edited">${editedStr}</span> ${timeStr}
                                        </p>
                                    </div>
                                    ${menuHtml}
                                </div>
                                ${actionsHtml}
                            </div>
                        `;
                        container.appendChild(div);
                        hasNewMessage = true;
                    }
                });

                if (hasNewMessage) {
                    scrollBottom();
                }
            })
            .catch(err => console.error('Error polling messages:', err));
    }
    
    setInterval(pollMessages, 2000);
</script>
@endsection
