<?php
// signup.php
session_start();
require_once __DIR__ . '/config.php';

// if JSON posted, behave as API endpoint
$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
if (is_array($input)) {
        $username = trim($input['username'] ?? '');
        $password = $input['password'] ?? '';

        if (!$username || !$password) {
                echo json_encode(['success' => false, 'message' => 'Username dan password wajib diisi']);
                exit;
        }

        if (strlen($password) < 6) {
                echo json_encode(['success' => false, 'message' => 'Password minimal 6 karakter']);
                exit;
        }

        try {
                $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt->execute([$username, $hash]);

                $newId = (int)$pdo->lastInsertId();

                // set PHP session for newly created user
                $_SESSION['user_id'] = $newId;
                $_SESSION['username'] = $username;

                echo json_encode(['success' => true, 'message' => 'Signup berhasil', 'user_id' => $newId]);
        } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                        echo json_encode(['success' => false, 'message' => 'Username sudah dipakai']);
                } else {
                        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
                }
        }
        exit;
}

// Otherwise render a styled signup page
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Sign Up - PingPong Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
    html,body{height:100%;margin:0;padding:0;font-family:'Press Start 2P',monospace}
    body{display:flex;align-items:center;justify-content:center;min-height:100vh;background:linear-gradient(to top,#000010,#190033,#38005a,#5e00a1)}
    body::before{content:'';position:fixed;inset:0;background-image:linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);background-size:8px 8px;pointer-events:none;mix-blend-mode:overlay;opacity:0.9}
    .card{width:100%;max-width:520px;background:linear-gradient(180deg,rgba(0,0,0,0.85),rgba(0,0,0,0.6));border:3px solid rgba(212,22,165,0.9);padding:24px;border-radius:16px;color:#fff}
    .card h1{font-size:14px;margin:0 0 12px}
    .field{display:flex;flex-direction:column;gap:8px;margin-bottom:12px}
    input[type=text],input[type=password]{padding:10px;border-radius:8px;border:2px solid rgba(255,255,255,0.06);background:#111;color:#fff;font-size:12px}
    .actions{display:flex;gap:8px;justify-content:flex-end}
    .btn{padding:10px 16px;border-radius:8px;border:1px solid transparent;cursor:pointer;font-family:'Press Start 2P',monospace}
    .btn-green{background:linear-gradient(90deg,#06d906,#00ff66);color:#002200}
    .btn-ghost{background:#222;color:#fff}
    </style>
</head>
<body>
    <div class="card">
        <h1>Sign Up</h1>
        <div class="field">
            <label>Username</label>
            <input id="username" type="text" autocomplete="username" />
        </div>
        <div class="field">
            <label>Password</label>
            <input id="password" type="password" autocomplete="new-password" />
        </div>
        <div class="actions">
            <button id="btn-signup" class="btn btn-green">Sign Up</button>
            <button id="btn-back" class="btn btn-ghost">Back</button>
        </div>
        <div style="margin-top:12px;font-size:12px;color:rgba(255,255,255,0.7)">You can also POST JSON to this endpoint for AJAX signup.</div>
    </div>

    <script>
        async function doSignup(){
            const u=document.getElementById('username').value||'';
            const p=document.getElementById('password').value||'';
            if(!u||!p){ alert('Masukkan username dan password'); return; }
            if(p.length < 6){ alert('Password minimal 6 karakter'); return; }
            try{
                const resp=await fetch('signup.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({username:u,password:p})});
                const j=await resp.json();
                if(j && j.success){
                    localStorage.setItem('playerName', u);
                    localStorage.setItem('playerId', j.user_id||0);
                    alert('Signup berhasil');
                    window.location.href='index.html';
                } else {
                    alert('Signup gagal: '+(j.message||'unknown'));
                }
            }catch(e){console.error(e);alert('Error saat signup')}
        }
        document.getElementById('btn-signup').addEventListener('click',doSignup);
        document.getElementById('btn-back').addEventListener('click',()=>window.location.href='index.html');
        document.getElementById('username').focus();
    </script>
</body>
</html>
