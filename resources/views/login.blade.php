<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Access | UKK MODELS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-deep: #030014;
            --primary: #6366f1;
            --cyan: #06b6d4;
            --glass-bg: rgba(15, 23, 42, 0.8);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            margin: 0; padding: 0;
            width: 100vw; height: 100vh;
            overflow: hidden;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-deep);
            display: flex; align-items: center; justify-content: center;
            color: white;
        }

        /* --- BACKGROUND ENGINE (STATIC) --- */
        .scifi-stage {
            position: absolute; inset: 0; z-index: -1;
            background: radial-gradient(circle at 50% 50%, #1e1b4b 0%, #030014 100%);
        }

        /* Grid statis tanpa animasi bergerak */
        .grid-floor {
            position: absolute; inset: 0;
            background-image: linear-gradient(rgba(99, 102, 241, 0.1) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(99, 102, 241, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            mask-image: radial-gradient(circle, black, transparent 80%);
            -webkit-mask-image: radial-gradient(circle, black, transparent 80%);
        }

        /* Ambient Glow statis */
        .ambient-glow {
            position: absolute; inset: 0;
            background: radial-gradient(circle at 10% 10%, rgba(99, 102, 241, 0.15) 0%, transparent 40%),
                        radial-gradient(circle at 90% 90%, rgba(6, 182, 212, 0.15) 0%, transparent 40%);
        }

        /* --- LOGIN CARD --- */
        .login-glass {
            width: 90%; max-width: 400px; padding: 2.5rem;
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.8);
            position: relative; z-index: 10;
        }

        .tech-logo { 
            font-size: 3.5rem; 
            margin-bottom: 10px; 
            color: var(--cyan); 
            filter: drop-shadow(0 0 10px rgba(6, 182, 212, 0.4)); 
            text-align: center;
        }

        .title-text { 
            font-family: 'Rajdhani', sans-serif; 
            font-weight: 700; font-size: 2.2rem; 
            letter-spacing: 3px; 
            text-align: center;
            background: linear-gradient(to bottom, #ffffff, #94a3b8); 
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent; 
        }

        .error-box { 
            background: rgba(239, 68, 68, 0.1); 
            border: 1px solid rgba(239, 68, 68, 0.4); 
            color: #fca5a5; 
            font-size: 0.9rem; 
            padding: 12px; 
            border-radius: 12px; 
            margin-bottom: 20px; 
            display: flex; 
            align-items: center; 
            gap: 12px; 
        }

        .form-group { position: relative; margin-bottom: 1.5rem; }
        
        .form-input { 
            width: 100%; 
            background: rgba(0, 0, 0, 0.3); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            color: white; 
            padding: 14px 14px 14px 50px; 
            border-radius: 14px; 
            font-size: 0.95rem; 
            transition: 0.3s; 
        }
        
        .form-input:focus { 
            background: rgba(0, 0, 0, 0.5); 
            border-color: var(--primary); 
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2); 
            outline: none; 
        }

        .input-icon { 
            position: absolute; 
            left: 18px; 
            top: 50%; 
            transform: translateY(-50%); 
            color: rgba(255, 255, 255, 0.4); 
            pointer-events: none; 
        }

        .btn-glow { 
            width: 100%; 
            padding: 16px; 
            background: linear-gradient(135deg, var(--primary), #8b5cf6); 
            color: white; 
            font-weight: 700; 
            border: none; 
            border-radius: 14px; 
            letter-spacing: 1px; 
            text-transform: uppercase; 
            font-size: 0.9rem; 
            cursor: pointer; 
            transition: 0.3s; 
            box-shadow: 0 5px 20px rgba(99, 102, 241, 0.3); 
        }

        .btn-glow:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.5); 
        }

        .footer { 
            margin-top: 2rem; 
            font-size: 0.75rem; 
            color: rgba(255, 255, 255, 0.3); 
            text-align: center; 
            border-top: 1px solid rgba(255,255,255,0.05); 
            padding-top: 1.5rem; 
        }
    </style>
</head>
<body>

    <div class="scifi-stage">
        <div class="ambient-glow"></div>
        <div class="grid-floor"></div>
    </div>

    <div class="login-glass">
        <div class="tech-logo"><i class="fas fa-boxes-stacked"></i></div>
        <h1 class="title-text">UKK MODELS</h1>
        <p style="color: #94a3b8; font-size: 0.85rem; letter-spacing: 1px; text-align: center; margin-bottom: 2.5rem; text-transform: uppercase;">Inventory Access Portal</p>

        @if(session('error'))
            <div class="error-box">
                <i class="fas fa-triangle-exclamation"></i>
                <div>
                    <strong style="display:block; color:#f87171;">ERROR_CODE_401</strong>
                    <span style="opacity:0.8;">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <form action="{{ route('login.proses') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <input type="text" name="username" class="form-input" placeholder="Operator ID" value="{{ old('username') }}" required autocomplete="off">
                <i class="fas fa-id-badge input-icon"></i>
            </div>

            <div class="form-group">
                <input type="password" name="password" class="form-input" placeholder="Security Key" required>
                <i class="fas fa-shield-halved input-icon"></i>
            </div>

            <button type="submit" class="btn-glow mt-2">
                INITIATE PROTOCOL <i class="fas fa-right-to-bracket ms-2"></i>
            </button>
        </form>

        <div class="footer">
            AUTHORIZED PERSONNEL ONLY • V.2.0.5 <br>
            © 2026 SMK MUHAMMADIYAH 8 SILIRAGUNG
        </div>
    </div>

</body>
</html>