<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Info - PingPong Pro</title>
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
  /* Fonts: readable body (Roboto) + retro headings (Press Start 2P) */
  html,body{height:100%;margin:0;padding:0;font-family:'Roboto',Arial,sans-serif}
  body{display:flex;align-items:center;justify-content:center;min-height:100vh;background:linear-gradient(to top,#000010,#190033,#38005a,#5e00a1);background-size:100% 400%}
  body::before{content:'';position:fixed;inset:0;background-image:linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);background-size:8px 8px;pointer-events:none;mix-blend-mode:overlay}
  .page-wrap{width:100%;max-width:920px;padding:20px}
  .info-modal{width:100%;max-width:780px;margin:0 auto;background:linear-gradient(180deg,rgba(0,0,0,0.85),rgba(0,0,0,0.6));border:3px solid rgba(212,22,165,0.9);padding:20px;border-radius:12px;color:#fff;box-shadow:0 12px 40px rgba(0,0,0,0.6)}
  .modal-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
  .modal-title{color:#fff;font-size:16px;font-family:'Press Start 2P',monospace}
  .close-x{background:transparent;border:none;color:#fff;font-size:20px;cursor:pointer}
  .info-body{display:grid;grid-template-columns:1fr 320px;gap:20px;padding:12px;align-items:start}
  .section{background:rgba(0,0,0,0.6);padding:12px;border-radius:8px;border:1px solid rgba(255,255,255,0.04)}
  .section h3{margin:0 0 8px 0;font-size:15px;font-family:'Press Start 2P',monospace}
  .author-badge{display:flex;align-items:center;gap:10px}
  .author-avatar{width:64px;height:64px;border-radius:8px;background:linear-gradient(90deg,#06d906,#00ff66);display:flex;align-items:center;justify-content:center;color:#002200;font-weight:800}
  /* Power-up rows: emoji + description */
  .powerups{display:flex;flex-direction:column;gap:10px;margin-top:6px}
  .power-item{display:flex;gap:12px;align-items:flex-start;padding:8px;border-radius:8px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.03)}
  .power-emoji{width:48px;min-width:48px;height:48px;display:flex;align-items:center;justify-content:center;font-size:26px;border-radius:8px;background:#111}
  .power-info{font-size:13px;line-height:1.4;color:rgba(255,255,255,0.95)}
  .emotes{display:flex;flex-direction:column;gap:8px}
  .emote-card{display:flex;gap:10px;align-items:center;background:rgba(255,255,255,0.02);padding:8px;border-radius:8px;border:1px solid rgba(255,255,255,0.04)}
  .emote-emoji{font-size:22px;width:44px;text-align:center}
  /* Power-up grid: horizontal, proportional cards */
  .grid-2{display:flex;gap:12px;flex-wrap:wrap;align-items:stretch}
  .card{display:flex;gap:14px;align-items:center;padding:14px;border-radius:10px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.04);flex:1 1 220px;min-width:220px}
  .card .power-emoji{width:64px;height:64px;font-size:34px;border-radius:10px}
  .card .power-info{font-size:15px}
  .btn{padding:10px 14px;border-radius:8px;border:2px solid transparent;font-family:'Roboto',Arial,sans-serif;cursor:pointer;font-size:13px}
  .btn-ghost{background:#222;border:2px solid rgba(255,255,255,0.06);color:#fff}
  @media(max-width:880px){.info-body{grid-template-columns:1fr;}.grid-2{flex-direction:column}.card{min-width:0}}
  /* Additional small-screen tweaks for mobile devices */
  @media (max-width:600px) {
    .page-wrap { padding: 12px; }
    /* Make modal scrollable and fit within viewport on mobile */
    .info-modal { max-width: 96vw; padding: 12px; border-radius: 8px; max-height: calc(100vh - 24px); overflow-y: auto; -webkit-overflow-scrolling: touch; }
    /* hide native scrollbar visuals but keep scrolling functional */
    .info-modal { scrollbar-width: none; -ms-overflow-style: none; }
    .info-modal::-webkit-scrollbar { display: none; width: 0; height: 0; }
    body { align-items: flex-start; padding-top: 12px; }
    .modal-title { font-size: 14px; }
    .info-body { gap: 12px; padding: 8px; grid-template-columns: 1fr; }
    .section { padding: 10px; }
    .section h3 { font-size: 13px; }
    .card { padding: 10px; gap: 10px; }
    .card .power-emoji { width: 56px; height: 56px; font-size:28px }
    .card .power-info { font-size: 14px }
    .power-emoji { width:44px; height:44px; font-size:22px }
    .author-avatar { width:52px; height:52px; font-size:14px }
    .btn, .close-x { padding: 10px 12px; font-size:14px }
    input[type=range] { height: 36px; }
  }
  @media (max-width:420px) {
    body { background-position: center; }
    .info-modal { margin: 6px; max-height: calc(100vh - 12px); }
    .modal-title { font-size: 13px; }
    .info-modal { border-width: 2px; }
    .card { flex-direction: row; align-items: center; }
    .card .power-emoji { width: 48px; height:48px; font-size:26px }
    .card .power-info { font-size: 13px }
    .grid-2 { gap:10px }
    .power-emoji { width:40px; height:40px }
    .author-avatar { width:48px; height:48px }
    .btn-ghost { min-width:48px }
  }
  </style>
</head>
<body>
  <div class="page-wrap">
    <div class="info-modal" role="dialog" aria-modal="true">
      <div class="modal-head">
        <div class="modal-title">INFO</div>
        <div>
          <button id="back-x" class="close-x" title="Kembali">✕</button>
        </div>
      </div>

      <div class="info-body">
        <div>
          <div class="section">
            <h3>About this game</h3>
            <p style="font-size:12px;color:rgba(255,255,255,0.9);line-height:1.4">PingPong Pro dibuat oleh <strong>FebrianVN</strong>. Ini adalah game pong bergaya neon dengan mode PVE, power-ups, dan efek emote untuk menambah keseruan.
            </p>
          </div>

          <div class="section" style="margin-top:12px">
            <h3>How to play</h3>
            <ol style="font-size:12px;line-height:1.5;margin:8px 0;padding-left:18px">
              <li>Gunakan W / S atau panah atas/bawah untuk menggerakkan paddle pemain.</li>
              <li>Pertahankan bola dan capai poin maksimal sebelum waktu habis.</li>
              <li>Setiap power-up akan mempengaruhi kecepatan bola atau paddle; gunakan dengan bijak.</li>
              <li>Anda bisa menyimpan skor jika login. Setelah game selesai, leaderboard akan muncul.</li>
            </ol>
          </div>

          <div class="section" style="margin-top:12px">
            <h3>Power-ups</h3>
            <div class="grid-2" style="margin-top:8px">
              <div class="card"><div class="power-emoji">🔥</div><div class="power-info"><strong>Boost</strong><div style="opacity:0.9">Meningkatkan kecepatan bola selama beberapa detik sehingga sulit dikembalikan lawan.</div></div></div>
              <div class="card"><div class="power-emoji">🛡️</div><div class="power-info"><strong>Shield</strong><div style="opacity:0.9">Memperbesar paddle pemain untuk sementara, memudahkan menangkap bola.</div></div></div>
              <div class="card"><div class="power-emoji">🐢</div><div class="power-info"><strong>Slow</strong><div style="opacity:0.9">Memperlambat bola untuk sementara sehingga lawan lebih mudah kehilangan tempo.</div></div></div>
              <div class="card"><div class="power-emoji">💫</div><div class="power-info"><strong>Confuse</strong><div style="opacity:0.9">Membalik kontrol lawan (atas/bawah) untuk beberapa detik, membingungkan pergerakan mereka.</div></div></div>
            </div>
          </div>
        </div>

        <div>
          <div id="creator-section" class="section">
            <h3>Creator</h3>
            <div class="author-badge">
              <div class="author-avatar">FV</div>
              <div style="font-size:12px">
                <div><strong>FebrianVN</strong></div>
                <div style="opacity:0.9">Creator & Developer</div>
              </div>
            </div>
          </div>

            <!-- Settings Dropdown (added below creator, matches UI/UX) -->
            <div id="settings-section" class="section" style="margin-top:12px">
              <h3>Settings</h3>
              <div style="font-size:13px;color:rgba(255,255,255,0.95);margin-top:6px">
                <label for="info-sfx-volume" style="display:block;margin-bottom:6px">Sound Effect</label>
                <div style="display:flex;gap:8px;align-items:center;margin-bottom:10px">
                  <input type="range" id="info-sfx-volume" min="0" max="100" value="80" style="flex:1;">
                  <button id="info-sfx-mute-toggle" class="btn btn-ghost" style="width:56px;">🔊</button>
                </div>

                <label for="info-music-volume" style="display:block;margin-bottom:6px">Music</label>
                <div style="display:flex;gap:8px;align-items:center">
                  <input type="range" id="info-music-volume" min="0" max="100" value="70" style="flex:1;">
                  <button id="info-mute-toggle" class="btn btn-ghost" style="width:56px;">🔊</button>
                </div>
              </div>
            </div>

            <!-- Ringkasan Power-ups (di samping daftar utama) -->
            <div class="section" style="margin-top:12px">
              <div class="grid-2" style="margin-top:8px">
                <div class="card"><div class="power-emoji">🔻</div><div class="power-info"><strong>Shrink Opponent</strong><div style="opacity:0.9">Mengecilkan paddle lawan ~35% selama 6–8s (tidak bisa ditumpuk).</div></div></div>
                <div class="card"><div class="power-emoji">⏳</div><div class="power-info"><strong>Time Dilation</strong><div style="opacity:0.9">Memperlambat gerakan paddle lawan ~40% selama 5–7s.</div></div></div>
                <div class="card"><div class="power-emoji">2️⃣</div><div class="power-info"><strong>Double Point</strong><div style="opacity:0.9">Selama durasi, poin yang Anda dapat menjadi dua kali lipat saat mencetak skor.</div></div></div>
                <div class="card"><div class="power-emoji">⏱️</div><div class="power-info"><strong>+5s</strong><div style="opacity:0.9">Menambahkan 5 detik ke waktu pertandingan seketika saat diambil,</div></div></div>
              </div>
            </div>

          <!-- Emotes section removed per user request -->

          <!-- Back button removed per request (only top-close remains) -->
        </div>
      </div>
    </div>
  </div>

  <script>
    document.getElementById('back-x').addEventListener('click', ()=>{ window.location.href='index.html'; });
  </script>
  <script>
    // Move Settings section to bottom on small/mobile viewports so audio controls sit at the end
    (function(){
      function adjustLayoutForMobile(){
        try{
          const container = document.querySelector('.info-body');
          const settings = document.getElementById('settings-section');
          const creator = document.getElementById('creator-section');
          if (!container || !settings) return;
          if (window.innerWidth <= 600) {
            // append settings at the end of container if not already last
            if (container.lastElementChild !== settings) container.appendChild(settings);
            // move creator to the top of the container
            if (creator && container.firstElementChild !== creator) container.insertBefore(creator, container.firstElementChild);
          } else {
            // on larger screens, ensure settings is back inside right column (second child)
            const cols = container.querySelectorAll(':scope > div');
            const rightCol = cols[1];
            if (rightCol) {
              if (!rightCol.contains(creator) && creator) {
                // put creator as the first section in right column
                rightCol.insertBefore(creator, rightCol.firstElementChild || null);
              }
              if (!rightCol.contains(settings)) {
                // find a reasonable insertion point: after creator section
                const firstSection = rightCol.querySelector('.section');
                if (firstSection && firstSection.nextSibling) rightCol.insertBefore(settings, firstSection.nextSibling);
                else rightCol.appendChild(settings);
              }
            }
          }
        }catch(e){}
      }
      adjustLayoutForMobile();
      let _mvTimer;
      window.addEventListener('resize', ()=>{ clearTimeout(_mvTimer); _mvTimer = setTimeout(adjustLayoutForMobile, 220); });
    })();
  </script>
  <script>
    // Initialize settings controls in Info modal to match main settings behavior
    // This script persists music & sfx volume + mute toggles to localStorage,
    // updates icons, and plays a short preview when sliders change.
    (function(){
      function safeGet(id) { return document.getElementById(id); }

      const sfxVolEl = safeGet('info-sfx-volume');
      const sfxMuteEl = safeGet('info-sfx-mute-toggle');
      const musicVolEl = safeGet('info-music-volume');
      const musicMuteEl = safeGet('info-mute-toggle');

      // lightweight preview audio elements (do not autoplay)
      const previewMenu = new Audio('muscicmenu.mp3'); previewMenu.preload = 'auto'; previewMenu.loop = true;
      const previewIngame = new Audio('ingame.mp3'); previewIngame.preload = 'auto'; previewIngame.loop = true;
      const previewSfx = {
        hit: new Audio('hit.mp3'),
        point: new Audio('point.mp3'),
        power: new Audio('power.mp3'),
        win: new Audio('win.mp3'),
        lose: new Audio('lose.mp3')
      };

      function updateSfxIcon(){ if (!sfxMuteEl) return; sfxMuteEl.textContent = (localStorage.getItem('sfxMuted') === '1') ? '🔇' : '🔊'; }
      function updateMusicIcon(){ if (!musicMuteEl) return; musicMuteEl.textContent = (localStorage.getItem('menuMuted') === '1') ? '🔇' : '🔊'; }

      // apply volumes/mute to preview elements and attempt to update any globals used by main game
      function applyMusicVolume(normalized){
        try { previewMenu.volume = normalized; } catch(e){}
        try { previewIngame.volume = normalized; } catch(e){}
        try { if (window.audioIngame) window.audioIngame.volume = normalized; } catch(e){}
        try { const mm = document.getElementById('menu-music'); if (mm) mm.volume = normalized; } catch(e){}
      }
      function applySfxVolume(normalized){
        Object.values(previewSfx).forEach(a=>{ try{ a.volume = normalized; }catch(e){} });
        try { if (window.sfxVolume !== undefined) window.sfxVolume = normalized; } catch(e){}
      }

      try{
        // restore saved values into inputs (if present) and update preview audio states
        const savedSfx = localStorage.getItem('sfxVolume');
        const savedSfxMuted = localStorage.getItem('sfxMuted');
        const savedMusic = localStorage.getItem('musicVolume');
        const savedMusicMuted = localStorage.getItem('menuMuted');

        if (sfxVolEl && savedSfx !== null) sfxVolEl.value = savedSfx;
        if (musicVolEl && savedMusic !== null) musicVolEl.value = savedMusic;

        updateSfxIcon(); updateMusicIcon();

        const musicNormalized = ((musicVolEl && parseInt(musicVolEl.value,10))||70)/100;
        const sfxNormalized = ((sfxVolEl && parseInt(sfxVolEl.value,10))||80)/100;
        const musicMuted = (savedMusicMuted === '1');
        const sfxMuted = (savedSfxMuted === '1');

        applyMusicVolume(musicNormalized);
        applySfxVolume(sfxNormalized);
        try { previewMenu.muted = musicMuted; previewIngame.muted = musicMuted; } catch(e){}
        try { Object.values(previewSfx).forEach(a=>{ a.muted = sfxMuted; }); } catch(e){}
      }catch(e){}

      // input handlers: save, apply and play short preview
      if (musicVolEl) musicVolEl.addEventListener('input', (ev)=>{
        const val = parseInt(ev.target.value,10) || 70;
        try { localStorage.setItem('musicVolume', String(val)); } catch(e){}
        const normalized = val/100;
        applyMusicVolume(normalized);
        // quick audible preview: play menu audio for a short time
        try{ previewMenu.play().catch(()=>{}); setTimeout(()=>{ previewMenu.pause(); previewMenu.currentTime = 0; }, 500); } catch(e){}
      });

      if (sfxVolEl) sfxVolEl.addEventListener('input', (ev)=>{
        const val = parseInt(ev.target.value,10) || 80;
        try { localStorage.setItem('sfxVolume', String(val)); } catch(e){}
        const normalized = val/100;
        applySfxVolume(normalized);
        // play a short SFX preview
        try{ const a = previewSfx.hit.cloneNode(); a.volume = normalized; a.play().catch(()=>{}); } catch(e){}
      });

      if (sfxMuteEl) sfxMuteEl.addEventListener('click', ()=>{
        try { const cur = localStorage.getItem('sfxMuted') === '1'; localStorage.setItem('sfxMuted', cur ? '0' : '1'); } catch(e){}
        updateSfxIcon();
        const nowMuted = (localStorage.getItem('sfxMuted') === '1');
        try { Object.values(previewSfx).forEach(a=>{ a.muted = nowMuted; }); } catch(e){}
        try { if (window.sfxMuted !== undefined) window.sfxMuted = nowMuted; } catch(e){}
      });

      if (musicMuteEl) musicMuteEl.addEventListener('click', ()=>{
        try { const cur = localStorage.getItem('menuMuted') === '1'; localStorage.setItem('menuMuted', cur ? '0' : '1'); } catch(e){}
        updateMusicIcon();
        const nowMuted = (localStorage.getItem('menuMuted') === '1');
        try { previewMenu.muted = nowMuted; previewIngame.muted = nowMuted; } catch(e){}
        try { if (window.audioIngame) window.audioIngame.muted = nowMuted; } catch(e){}
        try { const mm = document.getElementById('menu-music'); if (mm) mm.muted = nowMuted; } catch(e){}
      });

      // cleanup preview audio on unload
      window.addEventListener('beforeunload', ()=>{ try{ previewMenu.pause(); previewIngame.pause(); }catch(e){} });

    })();
  </script>
</body>
</html>
