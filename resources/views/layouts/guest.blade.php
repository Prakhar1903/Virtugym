<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>VirtuGym - Virtual Personal Trainer</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>
    <script src="/js/virtugym-icons.js" defer></script>
    <style>
        *{font-family:'Inter',sans-serif;box-sizing:border-box;margin:0;padding:0;}
        .vg-inline-icon{width:1em;height:1em;display:inline-block;vertical-align:-0.14em;margin-right:.35em;stroke-width:2.4;}
        html,body{height:100%;}
        body{
            min-height:100vh;
            background:#08081a;
            overflow-x:hidden;
            color:#fff;
            display:flex;
        }
        #stars{position:fixed;inset:0;z-index:0;pointer-events:none;}
        .orb{position:fixed;border-radius:50%;filter:blur(90px);pointer-events:none;z-index:0;}
        .o1{width:380px;height:380px;background:rgba(139,92,246,.13);top:-100px;right:0;animation:od 20s ease-in-out infinite;}
        .o2{width:300px;height:300px;background:rgba(236,72,153,.1);bottom:-60px;right:20%;animation:od 25s ease-in-out infinite reverse;}
        @keyframes od{0%,100%{transform:translate(0,0);}50%{transform:translate(-20px,30px);}}
        
        .img-panel{display:none;position:fixed;top:0;left:0;width:50%;height:100vh;z-index:5;}
        @media(min-width:900px){.img-panel{display:block;}}
        .img-panel img{width:100%;height:100%;object-fit:cover;object-position:center;}
        .img-overlay{position:absolute;inset:0;background:linear-gradient(to right, transparent 60%, #08081a 100%),linear-gradient(to bottom, rgba(8,8,26,.35) 0%, rgba(8,8,26,.2) 50%, rgba(8,8,26,.6) 100%);}
        .img-content{position:absolute;inset:0;display:flex;flex-direction:column;justify-content:flex-end;padding:3rem;z-index:2;}
        .img-quote{font-size:clamp(1.6rem,2.5vw,2.4rem);font-weight:900;line-height:1.15;background:linear-gradient(135deg,#fff 20%,#c4b5fd 60%,#f9a8d4 90%);-webkit-background-clip:text;background-clip:text;color:transparent;filter:drop-shadow(0 2px 20px rgba(139,92,246,.5));margin-bottom:.75rem;}
        .img-tagline{color:rgba(255,255,255,.5);font-size:.9rem;font-weight:400;line-height:1.6;max-width:340px;}
        .img-stats{display:flex;gap:.75rem;margin-top:1.5rem;flex-wrap:wrap;}
        .i-pill{display:flex;align-items:center;gap:8px;background:rgba(255,255,255,.08);border:1px solid rgba(139,92,246,.3);backdrop-filter:blur(12px);border-radius:50px;padding:7px 16px;font-size:.78rem;font-weight:600;color:#c4b5fd;transition:all .3s;}
        .i-pill:hover{background:rgba(139,92,246,.2);transform:translateY(-2px);}
        .i-pill span{font-size:1rem;}
        .img-panel::after{content:'';position:absolute;top:10%;right:0;width:2px;height:80%;background:linear-gradient(to bottom,transparent,rgba(139,92,246,.6) 30%,rgba(236,72,153,.6) 70%,transparent);box-shadow:0 0 12px rgba(139,92,246,.6);}
        
        .form-panel{position:relative;z-index:10;min-height:100vh;width:100%;margin-left:0;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:2.5rem 1.5rem;overflow-y:auto;}
        @media(min-width:900px){.form-panel{margin-left:50%;width:50%;}}

        .auth-logo-wrap{text-align:center;margin-bottom:1.25rem;}
        .auth-logo{
            width:104px;
            height:104px;
            margin:0 auto;
            border-radius:50%;
            background:rgba(255,255,255,.94);
            display:flex;
            align-items:center;
            justify-content:center;
            overflow:hidden;
            box-shadow:0 0 30px rgba(139,92,246,.42),0 14px 32px rgba(0,0,0,.22);
        }
        .auth-logo img{width:100%;height:100%;object-fit:cover;object-position:center;display:block;}
        .auth-logo-fallback{
            background:linear-gradient(135deg,#8b5cf6,#ec4899);
            color:white;
            font-size:2rem;
            font-weight:900;
        }
        .register-page .form-panel{justify-content:flex-start;padding-top:1.35rem;padding-bottom:1.35rem;}
        .register-page .auth-logo-wrap{margin-bottom:.75rem;}
        .register-page .auth-logo{width:88px;height:88px;}
        .register-page .brand-sub{margin-bottom:.15rem;}
        .register-page .card3d{margin-top:.85rem;}
        @media(max-width:520px){
            .auth-logo{width:92px;height:92px;}
            .register-page .auth-logo{width:78px;height:78px;}
        }
        
        @keyframes spin{from{transform:rotate(0)}to{transform:rotate(360deg)}}
        @keyframes lp{0%,100%{box-shadow:0 0 35px rgba(139,92,246,.5);}50%{box-shadow:0 0 55px rgba(236,72,153,.6);}}
        
        .brand-name{font-size:1.3rem;font-weight:900;letter-spacing:.1em;text-align:center;background:linear-gradient(135deg,#c4b5fd,#f9a8d4);-webkit-background-clip:text;background-clip:text;color:transparent;}
        .brand-sub{color:rgba(255,255,255,.25);font-size:.65rem;text-align:center;letter-spacing:.15em;margin-top:2px;}
        
        .card3d{width:100%;max-width:440px;background:rgba(255,255,255,.04);border:1px solid rgba(139,92,246,.22);border-radius:28px;padding:2.2rem 2rem;backdrop-filter:blur(20px);box-shadow:0 30px 60px rgba(0,0,0,.45),0 0 40px rgba(139,92,246,.07);transition:transform .5s cubic-bezier(.23,1,.32,1),box-shadow .5s;transform-style:preserve-3d;animation:cardIn .7s cubic-bezier(.23,1,.32,1);margin-top:1.2rem;}
        @keyframes cardIn{from{opacity:0;transform:translateY(36px) scale(.96);}to{opacity:1;transform:translateY(0) scale(1);}}
        
        .inp{width:100%;padding:11px 15px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;transition:all .25s;outline:none;}
        .inp::placeholder{color:rgba(255,255,255,.22);}
        .inp:focus{border-color:#8b5cf6;background:rgba(139,92,246,.08);box-shadow:0 0 0 3px rgba(139,92,246,.12);transform:translateY(-1px);}
        .inp option{background:#1a1a2e;color:#fff;}
        label.lbl{display:block;font-size:.75rem;font-weight:600;color:rgba(196,181,253,.65);margin-bottom:5px;letter-spacing:.03em;}
        
        .btn-go{width:100%;padding:13px;border:none;border-radius:12px;cursor:pointer;font-size:.95rem;font-weight:700;color:#fff;background:linear-gradient(135deg,#8b5cf6,#ec4899);box-shadow:0 10px 28px rgba(139,92,246,.35);position:relative;overflow:hidden;transition:all .3s;}
        .btn-go::before{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.18),transparent);transition:left .45s;}
        .btn-go:hover::before{left:100%;}
        .btn-go:hover{transform:translateY(-3px);box-shadow:0 16px 38px rgba(139,92,246,.55);}
        
        .form-title{font-size:1.4rem;font-weight:800;color:#fff;text-align:center;margin-bottom:.25rem;}
        .form-sub{font-size:.8rem;color:rgba(255,255,255,.32);text-align:center;margin-bottom:1.5rem;}
        .link-a{color:#a78bfa;font-weight:600;text-decoration:none;transition:color .2s;}
        .link-a:hover{color:#c4b5fd;}
        .err{color:#f87171;font-size:.72rem;margin-top:3px;}
        .divider{display:flex;align-items:center;gap:10px;margin:6px 0;}
        .divider span{flex:1;height:1px;background:rgba(139,92,246,.18);}
        .divider p{color:rgba(255,255,255,.2);font-size:.72rem;}
        input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            background-color: rgba(255, 255, 255, 0.05);
            border: 1.5px solid rgba(139, 92, 246, 0.35);
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
            vertical-align: middle;
        }

        input[type="checkbox"]:hover {
            border-color: rgba(139, 92, 246, 0.7);
            background-color: rgba(139, 92, 246, 0.1);
            transform: scale(1.08);
            box-shadow: 0 0 12px rgba(139, 92, 246, 0.25);
        }

        input[type="checkbox"]:focus {
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2);
        }

        input[type="checkbox"]:checked {
            background-color: #8b5cf6;
            border-color: #8b5cf6;
            box-shadow: 0 0 14px rgba(139, 92, 246, 0.45);
        }

        input[type="checkbox"]::after {
            content: '';
            position: absolute;
            width: 4px;
            height: 8px;
            border: solid #ffffff;
            border-width: 0 2px 2px 0;
            transform: translate(-50%, -50%) rotate(45deg) scale(0);
            transition: transform 0.2s cubic-bezier(0.12, 0.4, 0.29, 1.46);
            top: 42%;
            left: 50%;
        }

        input[type="checkbox"]:checked::after {
            transform: translate(-50%, -50%) rotate(45deg) scale(1);
        }
        ::-webkit-scrollbar{width:8px;height:8px;}
        ::-webkit-scrollbar-track{background:transparent;}
        ::-webkit-scrollbar-thumb{background:linear-gradient(#8b5cf6,#ec4899);border-radius:8px;border:2px solid #08081a;}
        
        .badge{display:inline-flex;align-items:center;gap:6px;background:rgba(139,92,246,.15);border:1px solid rgba(139,92,246,.35);border-radius:50px;padding:5px 13px;font-size:.73rem;font-weight:600;color:#c4b5fd;margin-bottom:1rem;}
        .bdot{width:6px;height:6px;border-radius:50%;background:#8b5cf6;animation:bk 1.5s infinite;}
        @keyframes bk{0%,100%{opacity:1}50%{opacity:.2}}
    </style>
</head>
<body class="{{ request()->routeIs('register') ? 'register-page' : '' }}">
    <canvas id="stars"></canvas>
    <div class="orb o1"></div>
    <div class="orb o2"></div>

    <div class="img-panel">
        <img src="/images/gym-panel.png" alt="VirtuGym Gym">
        <div class="img-overlay"></div>
        <div class="img-content">
            <div class="img-quote">Push Beyond<br>Your Limits.</div>
            <p class="img-tagline">AI-powered training that adapts to you — every rep, every set, every goal.</p>
            <div class="img-stats">
                <div class="i-pill"><span>🎯</span> 50+ Features</div>
                <div class="i-pill"><span>💪</span> 1K+ Exercises</div>
                <div class="i-pill"><span>⭐</span> 98% Satisfaction</div>
            </div>
        </div>
    </div>

    <div class="form-panel">
        <div class="auth-logo-wrap">
            @if(file_exists(public_path('images/logo.png')))
                <div class="auth-logo">
                    <img src="/images/logo.png" alt="VirtuGym Logo">
                </div>
            @else
                <div class="auth-logo auth-logo-fallback">
                    VG
                </div>
            @endif
        </div>
        
        <div class="brand-name">VIRTU GYM</div>
        <div class="brand-sub">VIRTUAL PERSONAL TRAINER</div>

        <div class="card3d" id="formCard">
            @yield('content')
        </div>

        <p style="color:rgba(255,255,255,.12);font-size:.7rem;margin-top:1.2rem;">
            © 2024 VirtuGym. All rights reserved.
        </p>
    </div>

    <script>
    (function(){
        const c=document.getElementById('stars'),ctx=c.getContext('2d');
        let W,H,S=[];
        function resize(){W=c.width=innerWidth;H=c.height=innerHeight;}
        function init(){S=Array.from({length:140},()=>({x:Math.random()*W,y:Math.random()*H,r:Math.random()*1.1+.2,a:Math.random(),da:(Math.random()-.5)*.005}));}
        function draw(){ctx.clearRect(0,0,W,H);S.forEach(s=>{s.a=Math.max(.05,Math.min(1,s.a+s.da));if(s.a<=.05||s.a>=1)s.da*=-1;ctx.beginPath();ctx.arc(s.x,s.y,s.r,0,Math.PI*2);ctx.fillStyle=`rgba(196,181,253,${s.a})`;ctx.fill();});requestAnimationFrame(draw);}
        window.addEventListener('resize',()=>{resize();init();});
        resize();init();draw();
    })();

    const card=document.getElementById('formCard');
    if(card){
        card.addEventListener('mousemove',e=>{
            const r=card.getBoundingClientRect();
            const x=(e.clientX-r.left)/r.width-.5;
            const y=(e.clientY-r.top)/r.height-.5;
            card.style.transform=`rotateY(${x*10}deg) rotateX(${-y*10}deg)`;
        });
        card.addEventListener('mouseleave',()=>{ card.style.transform=''; });
    }
    </script>
</body>
</html>
