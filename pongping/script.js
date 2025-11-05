// FULL GAME JS (with lastPaddleTouched -> emote targets)
// Pastikan HTML punya elemen dengan id/class yang sesuai:
// ids: arena, ball, player, ai, player-score, ai-score, timer,
// menu-screen, game-container, time-range, point-range, time-val, point-val
// difficulty buttons: class .difficulty-btn, data-level ("easy","medium","hard")
 
// 1. Import / inisialisasi Firebase
// (ini sesuai setup kamu, entah pakai CDN atau modular)

// 2. Ambil instance auth

// 4. Tambahkan event listener untuk tombol login & signup
// Ambil elemen tombol
const loginBtn = document.getElementById('login-btn');
const signupBtn = document.getElementById('signup-btn');

// Event listener untuk navigasi
loginBtn.addEventListener('click', () => {
  window.location.href = 'login.html'; // Ganti dengan path halaman login kamu
});

signupBtn.addEventListener('click', () => {
  window.location.href = 'signup.html'; // Ganti dengan path halaman sign up kamu
});

let currentDifficulty = 'easy'; // Variabel global untuk menyimpan tingkat kesulitan saat ini
const emotes = [
  { emoji: '🔥', type: 'boost', effect: 'speed' },
  { emoji: '🛡️', type: 'boost', effect: 'paddle-up' },
  { emoji: '🐢', type: 'nerf', effect: 'slow' },
  { emoji: '💫', type: 'nerf', effect: 'confuse' }
];

let activeEmotes = [];
let activeEffects = [];

// Game state

let maxTime = 60; // seconds
let maxPoint = 5;
let currentTime = 0;
let timerInterval = null;
let gameRunning = false;

// Fungsi login
async function loginUser(email, password) {
  const { user, error } = await supabase.auth.signInWithPassword({
    email,
    password
  });
  
  if (error) {
    console.error('Login error:', error.message);
    return null;
  }
  
  localStorage.setItem('playerName', user.email.split('@')[0]);
  return user;
}

// Fungsi signup
async function signupUser(email, password) {
  const { user, error } = await supabase.auth.signUp({
    email,
    password
  });
  
  if (error) {
    console.error('Signup error:', error.message);
    return null;
  }
  
  localStorage.setItem('playerName', user.email.split('@')[0]);
  return user;
}

// Event listener untuk tombol login/signup
document.getElementById('login-btn').addEventListener('click', async () => {
  const email = prompt("Email:");
  const password = prompt("Password:");
  
  if (email && password) {
    const user = await loginUser(email, password);
    if (user) {
      alert(`Selamat datang, ${user.email.split('@')[0]}!`);
    } else {
      alert('Login gagal, coba lagi.');
    }
  }
});

document.getElementById('signup-btn').addEventListener('click', async () => {
  const email = prompt("Email untuk pendaftaran:");
  const password = prompt("Password:");
  
  if (email && password) {
    const user = await signupUser(email, password);
    if (user) {
      alert(`Akun berhasil dibuat! Silakan login.`);
    } else {
      alert('Pendaftaran gagal, coba lagi.');
    }
  }
});
document.querySelectorAll('.difficulty-btn').forEach(btn => {
  btn.addEventListener('click', (e) => {
    const level = e.currentTarget.dataset.level;
    if (level && aiSpeedMap[level] !== undefined) {
      currentAiSpeed = aiSpeedMap[level];
      currentDifficulty = level; // Simpan tingkat kesulitan saat ini
    }
    startGameWithDifficulty();
  });
});

const arena = document.getElementById("arena");
const player = document.querySelector(".player");
const ai = document.querySelector(".ai");
const ball = document.getElementById("ball");
const playerScoreEl = document.getElementById("player-score");
const aiScoreEl = document.getElementById("ai-score");

let arenaH = 0, arenaW = 0;
let playerY = 0, aiY = 0, ballX = 0, ballY = 0;
let ballSize = 0;
let playerScore = 0, aiScore = 0;
let ballBaseSpeed = 2;
let ballSpeedX = 3, ballSpeedY = 3;
let ballSpeedMultiplier = 1;

const aiSpeedMap = { easy: 2, medium: 4, hard: 7 };
let currentAiSpeed = aiSpeedMap.easy;

const keyState = {};
let invertControls = false;
let invertAI = false;

// Track who last touched the ball
let lastPaddleTouched = null; // 'player' | 'ai' | null

// Bounce cooldown to avoid multiple bounces in consecutive frames (ms)
const bounceCooldown = 50;
let lastBounceTime = 0;

// ---------- SPAWN EMOTE INSIDE ARENA ----------
function spawnEmote() {
  if (!gameRunning || !arena) return;

  const size = 32; // px
  const margin = 20;
  const aw = arena.clientWidth;
  const ah = arena.clientHeight;

  const base = emotes[Math.floor(Math.random() * emotes.length)];
  const emote = { ...base, size };

  const maxX = Math.max(0, aw - margin * 2 - size);
  const maxY = Math.max(0, ah - margin * 2 - size);
  const x = margin + Math.random() * maxX;
  const y = margin + Math.random() * maxY;

  const el = document.createElement("div");
  el.className = "emote";
  el.textContent = emote.emoji;
  el.style.position = "absolute";
  el.style.left = x + "px";
  el.style.top = y + "px";
  el.style.width = size + "px";
  el.style.height = size + "px";
  el.style.lineHeight = size + "px";
  el.style.textAlign = "center";
  el.style.fontSize = "20px";
  el.style.pointerEvents = "none";
  el.style.userSelect = "none";
  el.style.zIndex = 50;
  el.style.transition = "transform 0.12s ease";

  emote.x = x;
  emote.y = y;
  emote.el = el;

  activeEmotes.push(emote);
  arena.appendChild(el);

  setTimeout(() => {
    if (activeEmotes.includes(emote)) {
      try { emote.el.remove(); } catch (e) {}
      activeEmotes = activeEmotes.filter(e => e !== emote);
    }
  }, 5000);
}

setInterval(() => {
  if (gameRunning) spawnEmote();
}, 7000);

// ---------- EMOTE EFFECTS (target-aware) ----------
function applyEmoteEffect(emote, target) {
  // If no target known, default to 'player' (you can change this behavior)
  target = target || 'player';
  if (!emote || !emote.effect) return;

  const dur = 5000; // ms

  const getPaddleEl = (t) => (t === 'ai' ? ai : player);
  const getPaddleHeight = (t) => getPaddleEl(t).clientHeight;

  switch (emote.effect) {
    case 'speed': {
      // global ball speed boost, and small extra push away from target
      const boostFactor = 1.6;
      ballSpeedMultiplier *= boostFactor;
      if (target === 'player') ballSpeedX = Math.abs(ballSpeedX) + 0.3;
      else if (target === 'ai') ballSpeedX = -Math.abs(ballSpeedX) - 0.3;

      const id = setTimeout(() => {
        ballSpeedMultiplier /= boostFactor;
        activeEffects = activeEffects.filter(a => a.id !== id);
      }, dur);
      activeEffects.push({ id, type: 'speed', target });
      break;
    }

    case 'paddle-up': {
      // enlarge target paddle
      const el = getPaddleEl(target);
      if (!el) break;
      const original = el.clientHeight;
      const extra = Math.round(original * 0.5);
      el.style.height = (original + extra) + "px";

      const id = setTimeout(() => {
        el.style.height = original + "px";
        activeEffects = activeEffects.filter(a => a.id !== id);
      }, dur);
      activeEffects.push({ id, type: 'paddle-up', target });
      break;
    }

    case 'slow': {
      // slow global ball + weaken AI if target is 'ai'
      const slowFactor = 0.6;
      const prevAi = currentAiSpeed;
      ballSpeedMultiplier *= slowFactor;
      if (target === 'ai') currentAiSpeed = Math.max(1, Math.round(currentAiSpeed * 0.5));

      const id = setTimeout(() => {
        ballSpeedMultiplier /= slowFactor;
        if (target === 'ai') currentAiSpeed = prevAi;
        activeEffects = activeEffects.filter(a => a.id !== id);
      }, dur);
      activeEffects.push({ id, type: 'slow', target });
      break;
    }

    case 'confuse': {
      // if target is ai -> invertAI, else invert player controls
      if (target === 'ai') {
        invertAI = true;
        const id = setTimeout(() => {
          invertAI = false;
          activeEffects = activeEffects.filter(a => a.id !== id);
        }, dur);
        activeEffects.push({ id, type: 'confuse', target });
      } else {
        invertControls = true;
        const id = setTimeout(() => {
          invertControls = false;
          activeEffects = activeEffects.filter(a => a.id !== id);
        }, dur);
        activeEffects.push({ id, type: 'confuse', target });
      }
      break;
    }
  }
}

// ---------- UI & START ----------
document.addEventListener('DOMContentLoaded', () => {
  const timeRange = document.getElementById("time-range");
  const pointRange = document.getElementById("point-range");
  const timeVal = document.getElementById("time-val");
  const pointVal = document.getElementById("point-val");

  if (timeRange && timeVal) {
    timeRange.addEventListener("input", () => timeVal.textContent = timeRange.value);
  }
  if (pointRange && pointVal) {
    pointRange.addEventListener("input", () => pointVal.textContent = pointRange.value);
  }

  document.querySelectorAll('.difficulty-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const level = e.currentTarget.dataset.level;
      if (level && aiSpeedMap[level] !== undefined) currentAiSpeed = aiSpeedMap[level];
      startGameWithDifficulty();
    });
  });

  const multiBtn = document.getElementById("multi-btn");
  if (multiBtn) multiBtn.addEventListener("click", () => alert("Fitur multiplayer belum tersedia 😅"));

  const loginBtn = document.getElementById("login-btn");
  if (loginBtn) loginBtn.addEventListener("click", () => {
    const name = prompt("Masukkan nama kamu:");
    if (name) alert(`Selamat datang, ${name}!`);
  });

  const signupBtn = document.getElementById("signup-btn");
  if (signupBtn) signupBtn.addEventListener("click", () => alert("Fitur Sign Up belum tersedia 😅"));
});

function startGameWithDifficulty() {
  const menu = document.getElementById("menu-screen");
  const container = document.getElementById("game-container");
  if (menu) menu.style.display = "none";
  if (container) container.style.display = "block";

  const timeRange = document.getElementById("time-range");
  const pointRange = document.getElementById("point-range");

  maxTime = timeRange ? parseInt(timeRange.value) * 60 : 60;
  maxPoint = pointRange ? parseInt(pointRange.value) : 5;
  currentTime = maxTime;
  gameRunning = true;

  // compute dims and initial positions
  arenaH = arena.clientHeight;
  arenaW = arena.clientWidth;
  ballSize = ball.clientWidth;

  const playerPaddleH = player.clientHeight;
  playerY = arenaH / 2 - playerPaddleH / 2;
  aiY = playerY;
  ballX = arenaW / 2;
  ballY = arenaH / 2;

  ballSpeedMultiplier = 1;
  ballBaseSpeed = 2;
  ballSpeedX = ballBaseSpeed * (Math.random() < 0.5 ? -1 : 1);
  ballSpeedY = ballBaseSpeed * (Math.random() < 0.5 ? -1 : 1);

  lastPaddleTouched = null;
  invertAI = false;
  invertControls = false;
  updateTimerUI();
  startTimer();
  requestAnimationFrame(gameLoop);
}

// ---------- Timer ----------
function startTimer() {
  clearInterval(timerInterval);
  timerInterval = setInterval(() => {
    currentTime--;
    updateTimerUI();
    if (currentTime <= 0) endGame("Waktu habis!");
  }, 1000);
}
function updateTimerUI() {
  const min = String(Math.floor(currentTime / 60)).padStart(2, "0");
  const sec = String(currentTime % 60).padStart(2, "0");
  const el = document.getElementById("timer");
  if (el) el.textContent = `${min}:${sec}`;
}

// ---------- Controls ----------
document.addEventListener("keydown", e => keyState[e.key.toLowerCase()] = true);
document.addEventListener("keyup",   e => keyState[e.key.toLowerCase()] = false);

function updatePlayerControl(playerPaddleH) {
  const upKeys = ["w", "arrowup"];
  const downKeys = ["s", "arrowdown"];
  let up = upKeys.some(k => keyState[k]);
  let down = downKeys.some(k => keyState[k]);

  if (invertControls) {
    const tmp = up;
    up = down;
    down = tmp;
  }

  if (up) playerY -= 6;
  if (down) playerY += 6;
  clampPlayer(playerPaddleH);
}

if (arena) {
  // Support per-paddle touch dragging for multiplayer devices.
  // Logic: when touchstart happens, determine whether the touch is on left half (player)
  // or right half (ai). Track active touches by identifier and move only the targeted paddle
  // for that touch's touchmove events. This allows two simultaneous touches controlling
  // both paddles independently.

  const activeTouchMap = {}; // touchId -> { target: 'player'|'ai' }

  function getTouchTargetByX(clientX) {
    const r = arena.getBoundingClientRect();
    const relativeX = clientX - r.left;
    return relativeX < r.width / 2 ? 'player' : 'ai';
  }

  arena.addEventListener('touchstart', e => {
    if (!gameRunning) return;
    for (let i = 0; i < e.changedTouches.length; i++) {
      const t = e.changedTouches[i];
      const target = getTouchTargetByX(t.clientX);
      activeTouchMap[t.identifier] = { target };
      // Prevent default to avoid page scroll while playing
      // but keep passive: false handled by default here
      e.preventDefault?.();
    }
  }, { passive: false });

  arena.addEventListener('touchmove', e => {
    if (!gameRunning) return;
    const r = arena.getBoundingClientRect();
    for (let i = 0; i < e.touches.length; i++) {
      const t = e.touches[i];
      const info = activeTouchMap[t.identifier];
      if (!info) continue;
      const y = t.clientY - r.top;
      if (info.target === 'player') {
        const playerPaddleH = player.clientHeight;
        playerY = y - playerPaddleH / 2;
        clampPlayer(playerPaddleH);
      } else {
        const aiPaddleH = ai.clientHeight;
        aiY = y - aiPaddleH / 2;
        aiY = Math.max(0, Math.min(arenaH - aiPaddleH, aiY));
      }
    }
    // prevent scrolling while touching the game
    e.preventDefault?.();
  }, { passive: false });

  arena.addEventListener('touchend', e => {
    for (let i = 0; i < e.changedTouches.length; i++) {
      const t = e.changedTouches[i];
      delete activeTouchMap[t.identifier];
    }
  }, { passive: true });

  // Mouse remains for desktop single-player control on the left paddle
  arena.addEventListener("mousemove", e => {
    if (!gameRunning) return;
    if (e.buttons !== 1) return;
    const r = arena.getBoundingClientRect();
    const y = e.clientY - r.top;
    const playerPaddleH = player.clientHeight;
    playerY = y - playerPaddleH / 2;
    clampPlayer(playerPaddleH);
  });
}

function clampPlayer(playerPaddleH) {
  playerY = Math.max(0, Math.min(arenaH - playerPaddleH, playerY));
}

// ---------- Game Loop ----------
function gameLoop() {
  if (!gameRunning) return;

  // refresh sizes each frame (handles paddle-up changes)
  const playerPaddleH = player.clientHeight;
  const aiPaddleH = ai.clientHeight;
  ballSize = ball.clientWidth;
  arenaH = arena.clientHeight;
  arenaW = arena.clientWidth;

  updatePlayerControl(playerPaddleH);

  // speed ramp
  ballSpeedMultiplier += 0.002;

  ballX += ballSpeedX * ballSpeedMultiplier;
  ballY += ballSpeedY * ballSpeedMultiplier;

  // scoring
  if (ballX + ballSize < 0) {
    aiScore++;
    postScore("ai");
    if (aiScore >= maxPoint) return endGame("AI mencapai skor maksimal!");
  }
  if (ballX > arenaW) {
    playerScore++;
    postScore("player");
    if (playerScore >= maxPoint) return endGame("Player mencapai skor maksimal!");
  }

  // top/bottom bounce
  if (ballY <= 0) {
    ballY = 0;
    ballSpeedY *= -1;
  } else if (ballY + ballSize >= arenaH) {
    ballY = arenaH - ballSize;
    ballSpeedY *= -1;
  }

  // AI movement (consider invertAI)
  let aiCenter = aiY + aiPaddleH / 2;
  if (!invertAI) {
    if (aiCenter < ballY) aiY += currentAiSpeed;
    else if (aiCenter > ballY) aiY -= currentAiSpeed;
  } else {
    // inverted behavior: move away from ball
    if (aiCenter < ballY) aiY -= currentAiSpeed;
    else if (aiCenter > ballY) aiY += currentAiSpeed;
  }
  aiY = Math.max(0, Math.min(arenaH - aiPaddleH, aiY));

  // Paddle collision (Player) - only if ball moving left and cooldown expired
  const now = Date.now();
  if (
    ballSpeedX < 0 &&
    (now - lastBounceTime > bounceCooldown) &&
    ballX <= player.clientWidth &&
    ballY + ballSize >= playerY &&
    ballY <= playerY + playerPaddleH
  ) {
    const speedMag = Math.max(1.2, Math.abs(ballSpeedX));
    ballSpeedX = Math.abs(speedMag); // bounce right
    ballX = player.clientWidth + 1;

    const hitPos = (ballY + ballSize / 2) - (playerY + playerPaddleH / 2);
    ballSpeedY += hitPos * 0.03;
    ballSpeedY = Math.max(-8, Math.min(8, ballSpeedY));

    lastBounceTime = now;
    lastPaddleTouched = 'player';
  }

  // Paddle collision (AI) - only if ball moving right and cooldown expired
  const aiLeft = arenaW - ai.clientWidth;
  if (
    ballSpeedX > 0 &&
    (now - lastBounceTime > bounceCooldown) &&
    ballX + ballSize >= aiLeft &&
    ballX <= aiLeft + ai.clientWidth &&
    ballY + ballSize >= aiY &&
    ballY <= aiY + aiPaddleH
  ) {
    const speedMag = Math.max(1.2, Math.abs(ballSpeedX));
    ballSpeedX = -Math.abs(speedMag); // bounce left
    ballX = aiLeft - ballSize - 1;

    const hitPosAI = (ballY + ballSize / 2) - (aiY + aiPaddleH / 2);
    ballSpeedY += hitPosAI * 0.03;
    ballSpeedY = Math.max(-8, Math.min(8, ballSpeedY));

    lastBounceTime = now;
    lastPaddleTouched = 'ai';
  }

  // Update visuals
  player.style.top = playerY + "px";
  ai.style.top     = aiY + "px";
  ball.style.left  = ballX + "px";
  ball.style.top   = ballY + "px";
  playerScoreEl.textContent = playerScore;
  aiScoreEl.textContent     = aiScore;

  // Check collision with emotes (apply to lastPaddleTouched)
  for (let i = activeEmotes.length - 1; i >= 0; i--) {
    const em = activeEmotes[i];
    if (!em || !em.el) continue;
    if (
      ballX + ballSize > em.x &&
      ballX < em.x + em.size &&
      ballY + ballSize > em.y &&
      ballY < em.y + em.size
    ) {
      try { em.el.remove(); } catch (e) {}
      activeEmotes.splice(i, 1);
      applyEmoteEffect(em, lastPaddleTouched); // <-- target the last paddle
    }
  }

  requestAnimationFrame(gameLoop);
}
// Fungsi login
async function loginUser(email, password) {
  const { user, error } = await supabase.auth.signInWithPassword({
    email,
    password
  });
  
  if (error) {
    console.error('Login error:', error.message);
    return null;
  }
  
  localStorage.setItem('playerName', user.email.split('@')[0]);
  return user;
}

// Fungsi signup
async function signupUser(email, password) {
  const { user, error } = await supabase.auth.signUp({
    email,
    password
  });
  
  if (error) {
    console.error('Signup error:', error.message);
    return null;
  }
  
  localStorage.setItem('playerName', user.email.split('@')[0]);
  return user;
}

// Event listener untuk tombol login/signup
loginBtn.addEventListener('click', async () => {
  const email = prompt("Email:");
  const password = prompt("Password:");
  
  if (email && password) {
    const user = await loginUser(email, password);
    if (user) {
      alert(`Selamat datang, ${user.email.split('@')[0]}!`);
    } else {
      alert('Login gagal, coba lagi.');
    }
  }
});

signupBtn.addEventListener('click', async () => {
  const email = prompt("Email untuk pendaftaran:");
  const password = prompt("Password:");
  
  if (email && password) {
    const user = await signupUser(email, password);
    if (user) {
      alert(`Akun berhasil dibuat! Silakan login.`);
    } else {
      alert('Pendaftaran gagal, coba lagi.');
    }
  }
});
// ---------- Score / reset / end ----------
function postScore(by) {
  // center ball and reset speeds
  ballX = arenaW / 2;
  ballY = arenaH / 2;
  ballSpeedMultiplier = 1;
  ballBaseSpeed = 2;
  ballSpeedX = ballBaseSpeed * (Math.random() < 0.5 ? -1 : 1);
  ballSpeedY = ballBaseSpeed * (Math.random() < 0.5 ? -1 : 1);

  // clear emotes to avoid instant-collisions after respawn
  activeEmotes.forEach(e => { try { e.el.remove(); } catch (_) {} });
  activeEmotes = [];

  // reset last touched and control inversions/effects
  lastPaddleTouched = null;
  invertAI = false;
  invertControls = false;

  // clear activeEffects
  activeEffects.forEach(a => clearTimeout(a.id));
  activeEffects = [];

  // visual flash
  arena.classList.remove("player-score", "ai-score");
  arena.classList.add(by === "player" ? "player-score" : "ai-score");
  setTimeout(() => arena.classList.remove("player-score", "ai-score"), 1200);

  lastBounceTime = Date.now();
}

async function endGame(reason) {
  gameRunning = false;
  clearInterval(timerInterval);
  
  let saveSuccess = false;
  try {
    await saveScoreToSupabase(
      playerScore,
      aiScore,
      currentDifficulty
    );
    saveSuccess = true;
  } catch (error) {
    console.error('Gagal menyimpan skor:', error);
  }
  
  setTimeout(() => {
    const message = `${reason}\nSkor: Player ${playerScore} - ${aiScore} AI\n` +
      `${playerScore > aiScore ? "🎉 Player Menang!" :
        aiScore > playerScore ? "🤖 AI Menang!" : "⚖️ Seri!"}`;
    
    if (saveSuccess) {
      alert(message + "\n\nSkor berhasil disimpan ke leaderboard!");
    } else {
      alert(message + "\n\nGagal menyimpan skor, coba lagi nanti.");
    }
    
    location.reload();
  }, 120);
}

async function saveScoreToSupabase(playerScore, aiScore, difficulty) {
  try {
    const playerName = localStorage.getItem('playerName') || 'Guest';
    
    const { data, error } = await supabase
      .from('scores')
      .insert([
        { 
          player_name: playerName,
          player_score: playerScore,
          ai_score: aiScore,
          difficulty: difficulty
        }
      ]);
    
    if (error) throw error;
    return data;
  } catch (error) {
    console.error('Error saving score:', error.message);
    return null;
  }
}