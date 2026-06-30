<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body style="margin:0;padding:0;">


<style>
    @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;900&display=swap');

    .page-403 {
        font-family: 'Nunito', sans-serif;
        min-height: 100vh;
        background: linear-gradient(135deg, #fef9f0 0%, #fff5e6 50%, #fef0f0 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        overflow: hidden;
        position: relative;
    }

    /* Floating blobs background */
    .blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        opacity: 0.25;
        animation: floatBlob 6s ease-in-out infinite;
    }
    .blob-1 { width: 300px; height: 300px; background: #f97316; top: -80px; left: -80px; animation-delay: 0s; }
    .blob-2 { width: 200px; height: 200px; background: #fb923c; bottom: -60px; right: -60px; animation-delay: 2s; }
    .blob-3 { width: 150px; height: 150px; background: #fbbf24; top: 50%; left: 70%; animation-delay: 4s; }

    @keyframes floatBlob {
        0%, 100% { transform: translateY(0px) scale(1); }
        50% { transform: translateY(-20px) scale(1.05); }
    }

    .card-403 {
        background: white;
        border-radius: 32px;
        padding: 3rem 2.5rem;
        max-width: 480px;
        width: 100%;
        text-align: center;
        box-shadow: 0 20px 60px rgba(249, 115, 22, 0.15), 0 4px 20px rgba(0,0,0,0.08);
        position: relative;
        z-index: 10;
        animation: cardPop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) both;
    }

    @keyframes cardPop {
        from { opacity: 0; transform: scale(0.8) translateY(30px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }

    /* ── KARAKTER ANIMASI ── */
    .character-wrap {
        position: relative;
        width: 160px;
        height: 160px;
        margin: 0 auto 1.5rem;
    }

    .character {
        width: 160px;
        height: 160px;
        animation: wobble 0.6s ease-in-out infinite alternate;
        transform-origin: bottom center;
        cursor: pointer;
        user-select: none;
    }

    /* Geleng-geleng default */
    @keyframes wobble {
        0%   { transform: rotate(-6deg) translateY(0); }
        100% { transform: rotate(6deg) translateY(-4px); }
    }

    /* Klik → loncat */
    .character.jumping {
        animation: jumping 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }
    @keyframes jumping {
        0%   { transform: rotate(0deg) translateY(0) scale(1); }
        30%  { transform: rotate(-10deg) translateY(-40px) scale(1.1); }
        60%  { transform: rotate(10deg) translateY(-25px) scale(0.95); }
        100% { transform: rotate(0deg) translateY(0) scale(1); }
    }

    /* Hover → nod cepat */
    .character:hover {
        animation: nodFast 0.15s ease-in-out infinite alternate;
    }
    @keyframes nodFast {
        0%   { transform: rotate(-12deg) scale(1.05); }
        100% { transform: rotate(12deg) scale(1.05); }
    }

    /* Bintang muncul saat klik */
    .stars-wrap {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        pointer-events: none;
    }
    .star {
        position: absolute;
        font-size: 1.2rem;
        opacity: 0;
        animation: none;
    }
    .star.pop {
        animation: starPop 0.8s ease forwards;
    }
    @keyframes starPop {
        0%   { opacity: 1; transform: translate(0, 0) scale(0); }
        50%  { opacity: 1; transform: translate(var(--tx), var(--ty)) scale(1.3); }
        100% { opacity: 0; transform: translate(calc(var(--tx)*1.5), calc(var(--ty)*1.5)) scale(0.5); }
    }

    /* Badge 403 */
    .badge-403 {
        position: absolute;
        top: -8px; right: -8px;
        background: #ef4444;
        color: white;
        font-size: 0.65rem;
        font-weight: 900;
        padding: 4px 8px;
        border-radius: 20px;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
        animation: badgePulse 1.5s ease-in-out infinite;
    }
    @keyframes badgePulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.12); }
    }

    /* Teks */
    .title-403 {
        font-size: 2rem;
        font-weight: 900;
        color: #1e293b;
        margin-bottom: 0.5rem;
        line-height: 1.1;
    }
    .subtitle-403 {
        color: #64748b;
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
        line-height: 1.5;
    }

    /* Info box */
    .info-box {
        background: #fef9f0;
        border: 1px solid #fed7aa;
        border-radius: 16px;
        padding: 1rem;
        margin: 1.25rem 0;
        text-align: left;
        font-size: 0.82rem;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.35rem 0;
        color: #475569;
    }
    .info-row + .info-row {
        border-top: 1px solid #fde8cc;
    }
    .info-row span.val {
        background: #fff;
        border: 1px solid #fde8cc;
        border-radius: 8px;
        padding: 2px 8px;
        font-weight: 700;
        color: #ea580c;
        font-size: 0.78rem;
        max-width: 180px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Buttons */
    .btn-group {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }
    .btn-back {
        flex: 1;
        padding: 0.75rem;
        border-radius: 14px;
        border: 2px solid #e2e8f0;
        background: white;
        color: #475569;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: flex; align-items: center; justify-content: center; gap: 0.4rem;
    }
    .btn-back:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
        transform: translateY(-1px);
    }
    .btn-home {
        flex: 1;
        padding: 0.75rem;
        border-radius: 14px;
        border: none;
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: white;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: flex; align-items: center; justify-content: center; gap: 0.4rem;
        box-shadow: 0 4px 15px rgba(249, 115, 22, 0.35);
    }
    .btn-home:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(249, 115, 22, 0.45);
    }

    /* Click counter bubble */
    .click-tip {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: 0.5rem;
        transition: all 0.3s;
    }
</style>

<div class="page-403">
    <!-- Background blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <div class="card-403">

        <!-- Karakter animasi -->
        <div class="character-wrap">
            <div class="stars-wrap" id="starsWrap"></div>
            <svg class="character" id="character" viewBox="0 0 160 160" xmlns="http://www.w3.org/2000/svg">
                <!-- Shadow -->
                <ellipse cx="80" cy="150" rx="35" ry="8" fill="#f1f5f9" />

                <!-- Body -->
                <rect x="45" y="90" width="70" height="55" rx="20" fill="#64748b"/>

                <!-- Arms (kiri & kanan, ikut geleng) -->
                <g id="armLeft">
                    <rect x="22" y="100" width="26" height="14" rx="7" fill="#475569" transform="rotate(15 35 107)"/>
                </g>
                <g id="armRight">
                    <rect x="112" y="100" width="26" height="14" rx="7" fill="#475569" transform="rotate(-15 125 107)"/>
                </g>

                <!-- Kepala -->
                <circle cx="80" cy="72" r="38" fill="#fbbf24"/>

                <!-- Telinga -->
                <circle cx="43" cy="72" r="10" fill="#f59e0b"/>
                <circle cx="117" cy="72" r="10" fill="#f59e0b"/>

                <!-- Mata kiri -->
                <ellipse id="eyeL" cx="65" cy="68" rx="8" ry="9" fill="white"/>
                <circle id="pupilL" cx="66" cy="69" r="4.5" fill="#1e293b"/>
                <circle cx="68" cy="67" r="1.5" fill="white"/>

                <!-- Mata kanan -->
                <ellipse id="eyeR" cx="95" cy="68" rx="8" ry="9" fill="white"/>
                <circle id="pupilR" cx="96" cy="69" r="4.5" fill="#1e293b"/>
                <circle cx="98" cy="67" r="1.5" fill="white"/>

                <!-- Alis -->
                <path id="browL" d="M57 57 Q65 53 73 57" stroke="#92400e" stroke-width="3" fill="none" stroke-linecap="round"/>
                <path id="browR" d="M87 57 Q95 53 103 57" stroke="#92400e" stroke-width="3" fill="none" stroke-linecap="round"/>

                <!-- Mulut (default: "O" bingung) -->
                <ellipse id="mouth" cx="80" cy="88" rx="9" ry="7" fill="#92400e"/>
                <ellipse cx="80" cy="89" rx="6" ry="4.5" fill="#7f1d1d"/>

                <!-- Topi -->
                <rect x="50" y="38" width="60" height="12" rx="6" fill="#1e293b"/>
                <rect x="60" y="20" width="40" height="24" rx="10" fill="#1e293b"/>

                <!-- Tanda seru di topi -->
                <text x="80" y="36" text-anchor="middle" font-size="14" font-weight="900" fill="#f97316">!</text>

                <!-- Pipi merah -->
                <ellipse cx="57" cy="80" rx="9" ry="6" fill="#fca5a5" opacity="0.5"/>
                <ellipse cx="103" cy="80" rx="9" ry="6" fill="#fca5a5" opacity="0.5"/>
            </svg>
            <div class="badge-403">403</div>
        </div>

        <p class="click-tip" id="clickTip">👆 Klik karakternya!</p>

        <h1 class="title-403">Akses Ditolak</h1>
        <p class="subtitle-403">
            Kamu tidak punya izin masuk ke halaman ini.<br>
            Akun kamu tidak sesuai dengan yang dibutuhkan.
        </p>

        <div class="info-box">
            <div class="info-row">
                <span>Halaman dituju</span>
                <span class="val" title="{{ request()->path() }}">{{ request()->path() }}</span>
            </div>
           <div class="info-row">
    <span>Keterangan</span>
    <span class="val" title="{{ $exception->getMessage() ?: 'Akses tidak diizinkan' }}">
        {{ $exception->getMessage() ?: 'Akses tidak diizinkan' }}
    </span>
</div>
        </div>

        <div class="btn-group">
            <a href="javascript:history.back()" class="btn-back">
                ← Kembali
            </a>
            <a href="{{ route('dashboard') }}" class="btn-home">
                🏠 Ke Dashboard
            </a>
        </div>
    </div>
</div>

<script>
    var char = document.getElementById('character');
    var starsWrap = document.getElementById('starsWrap');
    var clickTip = document.getElementById('clickTip');
    var clickCount = 0;
    var isJumping = false;

    var tips = [
        '👆 Klik lagi!', '🎉 Hore!', '🤸 Akrobat!',
        '😵 Pusing!', '✨ Magic!', '🚀 Terbang!',
        '💥 Boom!', '🌟 Bintang!', '😂 Lucu kan?'
    ];

    var starEmojis = ['⭐','✨','💫','🌟','❤️','🧡','💛','🎉','🎊'];

    function spawnStars() {
        starsWrap.innerHTML = '';
        for (var i = 0; i < 6; i++) {
            var s = document.createElement('span');
            s.className = 'star';
            s.textContent = starEmojis[Math.floor(Math.random() * starEmojis.length)];
            var angle = (i / 6) * 360;
            var dist = 55 + Math.random() * 25;
            var tx = Math.cos(angle * Math.PI / 180) * dist;
            var ty = Math.sin(angle * Math.PI / 180) * dist - 20;
            s.style.setProperty('--tx', tx + 'px');
            s.style.setProperty('--ty', ty + 'px');
            s.style.left = '50%';
            s.style.top = '40%';
            s.style.animationDelay = (i * 0.07) + 's';
            starsWrap.appendChild(s);
            setTimeout(function(el) { el.classList.add('pop'); }, 20, s);
        }
    }

    char.addEventListener('click', function() {
        if (isJumping) return;
        isJumping = true;
        clickCount++;

        char.classList.remove('jumping');
        void char.offsetWidth; // reflow
        char.classList.add('jumping');

        spawnStars();
        clickTip.textContent = tips[clickCount % tips.length];

        setTimeout(function() {
            char.classList.remove('jumping');
            isJumping = false;
        }, 600);
    });
</script>
</body>
</html>
