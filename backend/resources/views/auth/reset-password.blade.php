<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obnovenie hesla – SISP</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            width: 100%;
            max-width: 420px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            color: #007BFF;
            font-size: 32px;
            font-weight: bold;
        }
        
        .logo p {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        
        h2 {
            color: #333;
            font-size: 24px;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: #666;
            text-align: center;
            font-size: 14px;
            margin-bottom: 30px;
            line-height: 1.5;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            color: #333;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e1e5eb;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #007BFF;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        
        input[type="email"]:read-only {
            background: #f5f5f5;
            color: #666;
        }
        
        .btn {
            width: 100%;
            padding: 14px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .btn-primary {
            background: #007BFF;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.4);
        }
        
        .btn-primary:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
            margin-top: 10px;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .alert {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #ffffff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loading .spinner {
            display: inline-block;
        }
        
        .loading .btn-text {
            display: none;
        }
        
        .icon-success {
            width: 60px;
            height: 60px;
            background: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .icon-success svg {
            width: 30px;
            height: 30px;
            fill: white;
        }
        
        .icon-error {
            width: 60px;
            height: 60px;
            background: #dc3545;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .icon-error svg {
            width: 30px;
            height: 30px;
            fill: white;
        }
        
        .password-requirements {
            font-size: 12px;
            color: #666;
            margin-top: 8px;
        }
        
        .password-requirements ul {
            margin-top: 5px;
            margin-left: 20px;
        }
        
        .password-requirements li {
            margin-bottom: 3px;
        }
        
        .password-match {
            font-size: 12px;
            margin-top: 5px;
        }
        
        .password-match.valid {
            color: #28a745;
        }
        
        .password-match.invalid {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>SISP</h1>

        </div>
        
        @if(isset($success) && $success)
            <div class="icon-success">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                </svg>
            </div>
            <h2>Heslo zmenené</h2>
            <div class="alert alert-success">
                {{ $message ?? 'Vaše heslo bolo úspešne zmenené.' }}
            </div>
        @elseif(isset($showRequestNewLink) && $showRequestNewLink)
            <div class="icon-error">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/>
                </svg>
            </div>
            <h2>Neplatný odkaz</h2>
            <div class="alert alert-error">
                {{ $error ?? 'Odkaz na obnovenie hesla je neplatný alebo expiroval.' }}
            </div>
        @else
            <h2>Obnovenie hesla</h2>
            <p class="subtitle">Zadajte nové heslo pre váš účet.</p>
            
            @if(isset($error))
                <div class="alert alert-error">
                    {{ $error }}
                </div>
            @endif
            
            <form method="POST" action="{{ url('/password/reset') }}" id="resetForm">
                @csrf
                <input type="hidden" name="token" value="{{ $token ?? '' }}">
                
                <div class="form-group">
                    <label for="email">E-mailová adresa</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ $email ?? '' }}"
                        readonly
                        autocomplete="email"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password">Nové heslo</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        minlength="8"
                        placeholder="Zadajte nové heslo"
                        autocomplete="new-password"
                    >
                    <div class="password-requirements">
                        Heslo musí obsahovať:
                        <ul>
                            <li>Minimálne 8 znakov</li>
                        </ul>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password_confirmation">Potvrdiť heslo</label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        required 
                        minlength="8"
                        placeholder="Zopakujte nové heslo"
                        autocomplete="new-password"
                    >
                    <div class="password-match" id="passwordMatch"></div>
                </div>
                
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <span class="btn-text">Zmeniť heslo</span>
                    <span class="spinner"></span>
                </button>
            </form>
        @endif
    </div>
    
    <script>
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');
        const passwordMatch = document.getElementById('passwordMatch');
        const form = document.getElementById('resetForm');
        const submitBtn = document.getElementById('submitBtn');
        
        function checkPasswordMatch() {
            if (!password || !passwordConfirmation || !passwordMatch) return;
            
            if (passwordConfirmation.value === '') {
                passwordMatch.textContent = '';
                passwordMatch.className = 'password-match';
                return;
            }
            
            if (password.value === passwordConfirmation.value) {
                passwordMatch.textContent = '✓ Heslá sa zhodujú';
                passwordMatch.className = 'password-match valid';
            } else {
                passwordMatch.textContent = '✗ Heslá sa nezhodujú';
                passwordMatch.className = 'password-match invalid';
            }
        }
        
        password?.addEventListener('input', checkPasswordMatch);
        passwordConfirmation?.addEventListener('input', checkPasswordMatch);
        
        form?.addEventListener('submit', function(e) {
            if (password.value !== passwordConfirmation.value) {
                e.preventDefault();
                passwordMatch.textContent = '✗ Heslá sa nezhodujú';
                passwordMatch.className = 'password-match invalid';
                return;
            }
            
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>
