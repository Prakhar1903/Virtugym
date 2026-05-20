@extends('layouts.app')

@section('title', 'Messages')

@section('content')
<div style="margin: -24px -1.5rem -2rem; height: calc(100vh - 64px); display: flex; flex-direction: column; overflow: hidden;">
    <div class="fade-in-up" style="flex: 1; background: rgba(0,0,0,0.15); display: grid; grid-template-columns: 230px 1fr; overflow: hidden;">

        {{-- Conversation Sidebar Column --}}
        <div style="border-right:1px solid rgba(139,92,246,.15);display:flex;flex-direction:column;background:rgba(8,8,26,0.35);">
            <div style="padding:1rem;border-bottom:1px solid rgba(139,92,246,.12);">
                <div style="position:relative;">
                    <input type="text" id="convSearch" placeholder="Search chats..." 
                           style="width:100%;background:rgba(255,255,255,0.05);border:1px solid rgba(139,92,246,0.2);border-radius:12px;padding:9px 12px 9px 34px;color:#fff;font-size:.8rem;outline:none;transition:all 0.2s;">
                    <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);opacity:0.4;">🔍</span>
                </div>
            </div>
            <div style="padding:0.8rem 1.2rem 0.4rem;border-bottom:1px solid rgba(139,92,246,.08);">
                <h2 style="font-size:.7rem;font-weight:800;color:rgba(196,181,253,0.5);letter-spacing:.08em;text-transform:uppercase;">Recent Chats</h2>
            </div>
            
            {{-- Conversation Lists --}}
            <div style="overflow-y:auto;flex:1;display:flex;flex-direction:column;">
                @php
                    use App\Models\Message;
                    use App\Models\User;
                    $uniqueUsers = [];
                    $convList = [];
                    if(isset($conversations)) {
                        foreach($conversations as $conv) {
                            $otherId = ($conv->sender_id == Auth::id()) ? $conv->receiver_id : $conv->sender_id;
                            if(!in_array($otherId, $uniqueUsers)) {
                                $uniqueUsers[] = $otherId;
                                $otherUser = User::find($otherId);
                                if($otherUser) {
                                    $unread = Message::where('receiver_id', Auth::id())
                                        ->where('sender_id', $otherId)
                                        ->where('is_read', false)
                                        ->count();
                                    $convList[] = ['user' => $otherUser, 'last_message' => $conv->message, 'unread' => $unread];
                                }
                            }
                        }
                    }
                @endphp

                {{-- Real/Active Database Chats --}}
                @if(count($convList) > 0)
                    @foreach($convList as $item)
                        @php 
                            $isActive = isset($selectedTrainer) && $selectedTrainer && $selectedTrainer->id == $item['user']->id;
                            $lastMsg = $conversations->where(fn($m) => ($m->sender_id == $item['user']->id || $m->receiver_id == $item['user']->id))->first();
                            $lastMsgTime = $lastMsg ? $lastMsg->created_at : now();
                        @endphp
                        <a href="{{ url('/chat/' . $item['user']->id) }}"
                           class="conv-item"
                           data-name="{{ strtolower($item['user']->name) }}"
                           style="display:block;padding:12px 14px;border-bottom:1px solid rgba(139,92,246,.05);text-decoration:none;transition:all .2s;{{ $isActive ? 'background:rgba(139,92,246,.12);border-left:3px solid #8b5cf6;' : 'border-left:3px solid transparent;' }}">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="position:relative;flex-shrink:0;">
                                    <div style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#8b5cf6,#ec4899);display:flex;align-items:center;justify-content:center;font-size:.95rem;font-weight:800;color:#fff;">
                                        {{ strtoupper(substr($item['user']->name, 0, 1)) }}
                                    </div>
                                    <span style="position:absolute;bottom:-2px;right:-2px;width:10px;height:10px;border-radius:50%;background:#10b981;border:2px solid #0a0a1a;"></span>
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2px;">
                                        <p style="font-size:.82rem;font-weight:700;color:#e2d9f3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin:0;">{{ $item['user']->name }}</p>
                                        <span style="font-size:.6rem;color:rgba(255,255,255,0.2);">{{ $lastMsgTime->format('H:i') }}</span>
                                    </div>
                                    <p style="font-size:.72rem;color:rgba(255,255,255,.35);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin:0;">{{ $item['last_message'] }}</p>
                                </div>
                                @if($item['unread'] > 0)
                                    <span style="background:#8b5cf6;color:#fff;font-size:.6rem;padding:2px 6px;border-radius:50px;font-weight:800;flex-shrink:0;box-shadow:0 0 10px rgba(139,92,246,0.3);">{{ $item['unread'] }}</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                @else
                    <div style="text-align:center;padding:1.5rem 1rem;">
                        <p style="color:rgba(255,255,255,.25);font-size:.78rem;margin:0;">No active chats yet</p>
                    </div>
                @endif


            </div>
        </div>

        {{-- Active Chat Workspace --}}
        <div style="display:flex;flex-direction:column;height:100%;overflow:hidden;">
            @if(isset($selectedTrainer) && $selectedTrainer)
                {{-- Chat Header --}}
                <div style="padding:10px 20px;border-bottom:1px solid rgba(139,92,246,.12);background:rgba(139,92,246,.04);display:flex;align-items:center;justify-content:space-between;gap:12px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="position:relative;">
                            <div style="width:42px;height:42px;border-radius:14px;background:linear-gradient(135deg,#8b5cf6,#ec4899);display:flex;align-items:center;justify-content:center;font-size:1.05rem;font-weight:800;color:#fff;">
                                {{ strtoupper(substr($selectedTrainer->name, 0, 1)) }}
                            </div>
                            <span style="position:absolute;bottom:-2px;right:-2px;width:12px;height:12px;border-radius:50%;background:#10b981;border:3px solid #0a0a1a;animation:pulse-online 1.5s infinite alternate;"></span>
                        </div>
                        <div>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <p style="font-size:.95rem;font-weight:800;color:#fff;margin:0;">{{ $selectedTrainer->name }}</p>
                                @if(isset($nextSession))
                                    <span style="font-size:.65rem;background:rgba(139,92,246,0.15);color:#c4b5fd;padding:2px 8px;border-radius:6px;font-weight:700;border:1px solid rgba(139,92,246,0.2);">
                                        Next Session: {{ \Carbon\Carbon::parse($nextSession->session_date)->format('M d') }}
                                    </span>
                                @endif
                            </div>
                            <p style="font-size:.7rem;color:rgba(255,255,255,.35);font-weight:600;margin:2px 0 0 0;">{{ $selectedTrainer->specialization ?? ucfirst($selectedTrainer->role ?? 'Trainer') }}</p>
                        </div>
                    </div>

                </div>

                {{-- Messages Screen --}}
                <div id="chatMessages" class="chat-cyber-grid" style="flex:1;overflow-y:auto;padding:1.2rem 1.5rem;display:flex;flex-direction:column;gap:0.3rem;">
                    <div style="text-align:center;color:rgba(255,255,255,.2);font-size:.8rem;">Loading messages…</div>
                </div>

                {{-- Input Bar & Quick Action Panel --}}
                <div style="padding:12px 16px;border-top:1px solid rgba(139,92,246,.12);background:rgba(8,8,26,.8);display:flex;flex-direction:column;gap:10px;">
                    
                    {{-- Quick Action Buttons --}}
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                        <button onclick="messageInput.value = '📅 I would like to schedule a session this week. Can you share your available slots?'" style="background:rgba(255,255,255,0.02);border:1px solid rgba(139,92,246,0.15);color:rgba(255,255,255,0.7);padding:6px 12px;border-radius:20px;font-size:0.72rem;font-weight:600;display:flex;align-items:center;gap:4px;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background='rgba(139,92,246,0.06)';this.style.borderColor='rgba(139,92,246,0.3)';this.style.color='#fff'" onmouseout="this.style.background='rgba(255,255,255,0.02)';this.style.borderColor='rgba(139,92,246,0.15)';this.style.color='rgba(255,255,255,0.7)'">
                            📅 Schedule Session
                        </button>
                        <button onclick="messageInput.value = '📎 Can you review this workout sheet and share advice?'" style="background:rgba(255,255,255,0.02);border:1px solid rgba(139,92,246,0.15);color:rgba(255,255,255,0.7);padding:6px 12px;border-radius:20px;font-size:0.72rem;font-weight:600;display:flex;align-items:center;gap:4px;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background='rgba(139,92,246,0.06)';this.style.borderColor='rgba(139,92,246,0.3)';this.style.color='#fff'" onmouseout="this.style.background='rgba(255,255,255,0.02)';this.style.borderColor='rgba(139,92,246,0.15)';this.style.color='rgba(255,255,255,0.7)'">
                            📎 Send Workout
                        </button>
                    </div>

                    {{-- Animated Typing Dots Indicator --}}
                    <div id="typingIndicator" style="display:flex;align-items:center;gap:6px;font-size:.7rem;color:rgba(255,255,255,0.4);margin-bottom:4px;padding-left:4px;font-weight:600;">
                        <span style="display:flex;gap:3px;align-items:center;">
                            <span style="display:inline-block;width:4px;height:4px;background:var(--vg-accent);border-radius:50%;animation:typing-bounce 1s infinite 0.1s;"></span>
                            <span style="display:inline-block;width:4px;height:4px;background:var(--vg-accent);border-radius:50%;animation:typing-bounce 1s infinite 0.2s;"></span>
                            <span style="display:inline-block;width:4px;height:4px;background:var(--vg-accent);border-radius:50%;animation:typing-bounce 1s infinite 0.3s;"></span>
                        </span>
                        <span>Trainer is typing...</span>
                    </div>

                    {{-- Attachment Preview Badge --}}
                    <div id="attachmentPreview" style="display:none; align-items:center; gap:8px; background:rgba(139,92,246,0.15); border:1px solid rgba(139,92,246,0.3); padding:6px 12px; border-radius:12px; font-size:0.75rem; color:#fff; width:fit-content; margin-bottom:8px;">
                        <span style="font-size:0.85rem;">📎</span>
                        <span id="attachmentFileName" style="font-weight:600; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"></span>
                        <button type="button" onclick="clearAttachment()" style="background:transparent; border:none; color:rgba(255,255,255,0.6); cursor:pointer; font-size:0.85rem; font-weight:bold; padding:0 4px; transition:color 0.2s;" onmouseover="this.style.color='#f43f5e'" onmouseout="this.style.color='rgba(255,255,255,0.6)'">×</button>
                    </div>

                    <form id="chatForm" style="display:flex;gap:.8rem;align-items:center;position:relative;" onsubmit="return false;">
                        @csrf
                        <input type="file" id="attachmentInput" style="display: none;">
                        <div style="display:flex;gap:6px;">
                            <button type="button" id="attachButton" title="Attach file" style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);color:#c4b5fd;width:38px;height:38px;border-radius:10px;cursor:pointer;font-size:1rem;transition:all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.08)'" onmouseout="this.style.background='rgba(255,255,255,0.03)'">📎</button>
                            <button type="button" title="Emojis" style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);color:#c4b5fd;width:38px;height:38px;border-radius:10px;cursor:pointer;font-size:1rem;transition:all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.08)'" onmouseout="this.style.background='rgba(255,255,255,0.03)'">😊</button>
                        </div>
                        <input type="hidden" name="receiver_id" value="{{ $selectedTrainer->id }}" id="receiverId">
                        <input type="text" name="message" id="messageInput"
                               style="flex:1;background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.2);border-radius:12px;padding:11px 16px;color:#fff;font-size:.88rem;outline:none;transition:all .3s cubic-bezier(0.16, 1, 0.3, 1);"
                               placeholder="Type a message..." autocomplete="off"
                               onfocus="this.style.borderColor='var(--vg-accent)';this.style.background='rgba(255,255,255,0.06)';this.style.boxShadow='0 0 15px rgba(139,92,246,0.25)';"
                               onblur="this.style.borderColor='rgba(139,92,246,0.2)';this.style.background='rgba(255,255,255,0.03)';this.style.boxShadow='none';">
                        <button type="button" id="sendButton" class="send-btn"
                                style="background:linear-gradient(135deg,#8b5cf6,#ec4899);color:#fff;border:none;border-radius:12px;width:38px;height:38px;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 6px 16px rgba(139,92,246,.2);transition:all .3s cubic-bezier(0.16, 1, 0.3, 1);">
                            <span style="transform:rotate(-45deg);margin-left:2px;font-size:0.95rem;transition:transform 0.2s;">🚀</span>
                        </button>
                    </form>
                </div>
            @else
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:1.5rem;background:#060513;">
                    <div style="width:70px;height:70px;border-radius:24px;background:rgba(139,92,246,0.06);border:1px solid rgba(139,92,246,0.15);display:flex;align-items:center;justify-content:center;color:var(--vg-accent);box-shadow:0 8px 30px rgba(0,0,0,0.2);">
                        <i data-lucide="message-square" style="width:32px;height:32px;"></i>
                    </div>
                    <div style="text-align:center;">
                        <h2 style="font-size:1.15rem;font-weight:900;color:#fff;margin:0 0 6px 0;">Coaching Conversations</h2>
                        <p style="color:rgba(255,255,255,.35);font-size:.82rem;margin:0;max-width:300px;line-height:1.45;">Select an active coach or trainer from the sidebar to coordinate your training sessions.</p>
                    </div>
                </div>
            @endif
    </div>
</div>

<style>
    /* Custom scrollbar inside chat */
    #chatMessages::-webkit-scrollbar { width: 4px; }
    #chatMessages::-webkit-scrollbar-track { background: transparent; }
    #chatMessages::-webkit-scrollbar-thumb { background: rgba(139,92,246,.4); border-radius: 4px; }
    
    @keyframes pulse-online {
        0% { opacity: 0.6; transform: scale(0.9); }
        100% { opacity: 1; transform: scale(1.1); }
    }
    @keyframes typing-bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-3px); }
    }
    @keyframes message-delivery {
        0% { opacity: 0; transform: translateY(8px) scale(0.98); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }
    .chat-bubble-wrapper {
        animation: message-delivery 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    .chat-bubble-wrapper:hover {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
    }
    
    .chat-cyber-grid {
        background: radial-gradient(circle at 50% 50%, rgba(15, 10, 36, 0.4) 0%, rgba(3, 2, 12, 0.7) 100%), 
                    url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'%3E%3Cpath d='M0 0h40v2H0V0zm0 0v40h2V0H0z' fill='rgba(139, 92, 246, 0.035)'/%3E%3C/svg%3E");
        background-color: #070613 !important;
    }

    .send-btn:hover {
        transform: scale(1.08) translateY(-1px);
        box-shadow: 0 8px 20px rgba(139,92,246,.35) !important;
    }
    .send-btn:active {
        transform: scale(0.95);
    }
    .send-btn:hover span {
        transform: rotate(-45deg) scale(1.1) translate(1px, -1px) !important;
    }
</style>

@if(isset($selectedTrainer) && $selectedTrainer)
<script>
    const trainerId = '{{ $selectedTrainer->id }}';
    const currentUserId = '{{ Auth::id() }}';
    const chatMessagesDiv = document.getElementById('chatMessages');
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');

    let lastMessageId = null;
    let firstLoad = true;

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    let lastRenderedDate = null;
    let lastSenderId = null;

    function formatMessageDate(date) {
        const today = new Date();
        const yesterday = new Date(today);
        yesterday.setDate(yesterday.getDate() - 1);

        if (date.toDateString() === today.toDateString()) return 'Today';
        if (date.toDateString() === yesterday.toDateString()) return 'Yesterday';
        
        return date.toLocaleDateString([], { month: 'short', day: 'numeric', year: date.getFullYear() !== today.getFullYear() ? 'numeric' : undefined });
    }

    function buildMessageEl(msg) {
        const isOwn = msg.sender_id == currentUserId;
        const date = new Date(msg.created_at);
        const timeStr = date.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
        
        const fragment = document.createDocumentFragment();

        // Date separator spacing reduced
        const dateStr = date.toDateString();
        if (dateStr !== lastRenderedDate) {
            const separator = document.createElement('div');
            separator.style.cssText = 'text-align:center;margin:0.8rem 0 0.4rem;position:relative;clear:both;';
            separator.innerHTML = `
                <div style="position:absolute;top:50%;left:0;right:0;height:1px;background:rgba(139,92,246,0.06);z-index:1;"></div>
                <span style="position:relative;z-index:2;background:#0b0a1d;padding:4px 14px;border-radius:20px;font-size:.65rem;color:rgba(255,255,255,0.3);font-weight:800;text-transform:uppercase;letter-spacing:0.05em;border:1px solid rgba(139,92,246,0.08);">${formatMessageDate(date)}</span>
            `;
            fragment.appendChild(separator);
            lastRenderedDate = dateStr;
            lastSenderId = null; // Reset consecutive stacking on date boundary
        }

        const isConsecutive = lastSenderId == msg.sender_id;
        lastSenderId = msg.sender_id;

        const wrapper = document.createElement('div');
        wrapper.dataset.msgId = msg.id;
        wrapper.className = 'chat-bubble-wrapper';
        wrapper.style.cssText = 'display:flex;clear:both;transition:all 0.2s ease;' + 
                                (isConsecutive ? 'margin-top:2px;' : 'margin-top:10px;') +
                                (isOwn ? 'justify-content:flex-end;' : 'justify-content:flex-start;');

        // Beautiful Dynamic Borders for Stacking
        let borderRadius = '';
        if (isOwn) {
            borderRadius = isConsecutive ? '18px 4px 4px 18px' : '18px 18px 4px 18px';
        } else {
            borderRadius = isConsecutive ? '4px 18px 18px 4px' : '18px 18px 18px 4px';
        }

        let attachmentHtml = '';
        if (msg.attachment_path) {
            const isImage = msg.attachment_type && msg.attachment_type.startsWith('image/');
            const fileUrl = '/storage/' + msg.attachment_path;
            if (isImage) {
                attachmentHtml = `<a href="${fileUrl}" target="_blank" style="display:block;margin-bottom:8px;"><img src="${fileUrl}" style="max-width:260px; max-height:200px; border-radius:10px; border:1px solid rgba(255,255,255,0.1); display:block; object-fit:cover;" /></a>`;
            } else {
                attachmentHtml = `
                    <div style="display:flex;align-items:center;gap:8px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);padding:10px 14px;border-radius:10px;margin-bottom:8px;max-width:280px;">
                        <span style="font-size:1.3rem;">📎</span>
                        <div style="flex:1;min-width:0;">
                            <a href="${fileUrl}" target="_blank" download style="color:#c4b5fd;text-decoration:none;font-size:0.8rem;font-weight:700;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;transition:color 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#c4b5fd'">${escapeHtml(msg.attachment_name || 'Attached File')}</a>
                            <span style="font-size:0.65rem;color:rgba(255,255,255,0.35);font-weight:600;text-transform:uppercase;">Download</span>
                        </div>
                    </div>`;
            }
        }

        if (isOwn) {
            wrapper.innerHTML = `
                <div style="max-width:75%;position:relative;">
                    <div style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);border-radius:${borderRadius};padding:10px 14px;box-shadow:0 4px 12px rgba(139,92,246,.15);border:1px solid rgba(255,255,255,0.05);">
                        ${attachmentHtml}
                        ${msg.message ? `<p style="font-size:.88rem;color:#fff;word-break:break-word;line-height:1.4;margin:0;font-weight:500;">${escapeHtml(msg.message)}</p>` : ''}
                        <div style="display:flex;align-items:center;justify-content:flex-end;gap:4px;margin-top:4px;">
                            <p style="font-size:.6rem;color:rgba(255,255,255,.6);margin:0;">${timeStr}</p>
                            <span style="font-size:.7rem;color:rgba(255,255,255,0.6);line-height:1;">${msg.is_read ? '✓✓' : '✓'}</span>
                        </div>
                    </div>
                </div>`;
        } else {
            wrapper.innerHTML = `
                <div style="max-width:75%;">
                    <div style="background:rgba(255,255,255,0.035);border:1px solid rgba(139,92,246,.15);border-radius:${borderRadius};padding:10px 14px;backdrop-filter:blur(10px);box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                        ${attachmentHtml}
                        ${msg.message ? `<p style="font-size:.88rem;color:#f1ebfa;word-break:break-word;line-height:1.4;margin:0;font-weight:500;">${escapeHtml(msg.message)}</p>` : ''}
                        <p style="font-size:.6rem;color:rgba(255,255,255,.3);margin:4px 0 0 0;">${timeStr}</p>
                    </div>
                </div>`;
        }
        fragment.appendChild(wrapper);
        return fragment;
    }

    // Search filter
    document.getElementById('convSearch')?.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        document.querySelectorAll('.conv-item').forEach(item => {
            const name = item.dataset.name;
            if (name) {
                item.style.display = name.includes(query) ? 'block' : 'none';
            }
        });
    });

    function loadMessages() {
        fetch('/chat/messages/' + trainerId)
            .then(r => r.json())
            .then(messages => {
                if (!chatMessagesDiv) return;

                if (messages.length === 0) {
                    if (firstLoad) {
                        chatMessagesDiv.innerHTML = `
                            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:1.5rem;padding:3rem 2rem;text-align:center;background:rgba(255,255,255,0.01);border:1px dashed rgba(139,92,246,0.15);border-radius:24px;max-width:440px;margin:2rem auto;" data-placeholder="true">
                                <div style="width:54px;height:54px;border-radius:16px;background:rgba(139,92,246,0.08);display:flex;align-items:center;justify-content:center;color:var(--vg-accent);border:1px solid rgba(139,92,246,0.15);">
                                    <span style="font-size:1.8rem;">👋</span>
                                </div>
                                <div>
                                    <h3 style="font-size:1.05rem;font-weight:800;color:#fff;margin:0 0 6px 0;">Start Your Coaching Chat</h3>
                                    <p style="font-size:0.78rem;color:rgba(255,255,255,0.45);line-height:1.45;margin:0;">Ask questions about your workout plans, request posture tips, or schedule live workouts directly with your trainer.</p>
                                </div>
                                <div style="background:rgba(255,255,255,0.01);border:1px solid rgba(255,255,255,0.04);border-radius:16px;padding:1rem;text-align:left;width:100%;">
                                    <p style="font-size:0.7rem;color:rgba(255,255,255,0.3);text-transform:uppercase;font-weight:700;letter-spacing:0.05em;margin-bottom:6px;">Suggested Starters</p>
                                    <ul style="margin:0;padding-left:1.2rem;font-size:0.75rem;color:rgba(255,255,255,0.6);display:flex;flex-direction:column;gap:6px;">
                                        <li>"Can you review my squats from the last session?"</li>
                                        <li>"What equipment do I need for my HIIT class tomorrow?"</li>
                                    </ul>
                                </div>
                            </div>
                        `;
                        firstLoad = false;
                    }
                    return;
                }

                // On first load, render all messages
                if (firstLoad) {
                    chatMessagesDiv.innerHTML = '';
                    lastRenderedDate = null; 
                    lastSenderId = null;
                    messages.forEach(msg => chatMessagesDiv.appendChild(buildMessageEl(msg)));
                    lastMessageId = messages.length > 0 ? messages[messages.length - 1].id : null;
                    chatMessagesDiv.scrollTop = chatMessagesDiv.scrollHeight;
                    firstLoad = false;
                    return;
                }

                // On subsequent polls, append new messages
                const newMessages = messages.filter(msg => msg.id > lastMessageId);
                if (newMessages.length > 0) {
                    const placeholder = chatMessagesDiv.querySelector('[data-placeholder]');
                    if (placeholder) placeholder.remove();

                    newMessages.forEach(msg => chatMessagesDiv.appendChild(buildMessageEl(msg)));
                    lastMessageId = newMessages[newMessages.length - 1].id;
                    chatMessagesDiv.scrollTop = chatMessagesDiv.scrollHeight;
                }
            })
            .catch(e => console.error('Error loading messages:', e));
    }

    const attachButton = document.getElementById('attachButton');
    const attachmentInput = document.getElementById('attachmentInput');
    const attachmentPreview = document.getElementById('attachmentPreview');
    const attachmentFileName = document.getElementById('attachmentFileName');

    attachButton?.addEventListener('click', () => {
        attachmentInput.click();
    });

    attachmentInput?.addEventListener('change', () => {
        if (attachmentInput.files && attachmentInput.files[0]) {
            const file = attachmentInput.files[0];
            attachmentFileName.textContent = file.name;
            attachmentPreview.style.display = 'flex';
        } else {
            clearAttachment();
        }
    });

    function clearAttachment() {
        if (attachmentInput) attachmentInput.value = '';
        if (attachmentPreview) attachmentPreview.style.display = 'none';
    }

    function sendMessage() {
        const message = messageInput.value.trim();
        const hasAttachment = attachmentInput && attachmentInput.files && attachmentInput.files.length > 0;
        
        if (!message && !hasAttachment) return;
        
        const receiverId = document.getElementById('receiverId').value;
        const formData = new FormData();
        formData.append('receiver_id', receiverId);
        formData.append('message', message);
        if (hasAttachment) {
            formData.append('attachment', attachmentInput.files[0]);
        }

        fetch('/chat/send', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) { 
                messageInput.value = ''; 
                clearAttachment();
                loadMessages(); 
            }
        })
        .catch(e => console.error('Error sending message:', e));
    }

    // Simulate trainer typing activity to make the chat feel extremely alive
    const typingIndicator = document.getElementById('typingIndicator');
    if (typingIndicator) {
        typingIndicator.style.display = 'none';
        
        function simulateTyping() {
            const shouldType = Math.random() < 0.35;
            if (shouldType) {
                typingIndicator.style.display = 'flex';
                setTimeout(() => {
                    if (typingIndicator) typingIndicator.style.display = 'none';
                }, 3500);
            }
        }
        setInterval(simulateTyping, 12000);
    }

    sendButton?.addEventListener('click', sendMessage);
    messageInput?.addEventListener('keypress', e => { if(e.key === 'Enter') { e.preventDefault(); sendMessage(); } });

    loadMessages();
    const interval = setInterval(loadMessages, 3000);
    window.addEventListener('beforeunload', () => clearInterval(interval));
</script>
@endif
@endsection