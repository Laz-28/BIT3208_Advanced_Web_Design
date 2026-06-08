<?php

session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: landing.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechStore - Authentication</title>
    <style>
        
        * { 
            box-sizing: border-box; 
            margin: 0; 
            padding: 0; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        }

        
        body { 
            background-color: #f3f4f6; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
        }

        .container { 
            background-color: #ffffff; 
            padding: 40px; 
            border-radius: 12px; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); 
            width: 100%; 
            max-width: 400px; 
        }

        
        .hidden { 
            display: none !important; 
        }

        
        header { 
            text-align: center; 
            margin-bottom: 24px; 
        }

        header h2 { 
            font-size: 24px; 
            color: #111827; 
        }

        
        form { 
            display: flex; 
            flex-direction: column; 
            gap: 20px; 
        }

        .secta { 
            display: flex; 
            flex-direction: column; 
            gap: 6px; 
        }

        label { 
            font-size: 14px; 
            font-weight: 600; 
            color: #374151; 
        }

        input { 
            border-radius: 8px; 
            width: 100%; 
            padding: 12px 16px; 
            border: 1px solid #d1d5db; 
            font-size: 15px; 
            outline: none; 
            transition: all 0.2s ease; 
        }

        input:focus { 
            border-color: #2563eb; 
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); 
        }

        
        button.primary-btn { 
            width: 100%; 
            padding: 14px; 
            background-color: #2563eb; 
            color: white; 
            border: none; 
            border-radius: 8px; 
            font-size: 16px; 
            font-weight: 600; 
            cursor: pointer; 
            transition: background-color 0.2s ease; 
            margin-top: 10px; 
        }

        button.primary-btn:hover { 
            background-color: #1d4ed8; 
        }

        
        .divider { 
            display: flex; 
            align-items: center; 
            text-align: center; 
            margin: 24px 0; 
            color: #9ca3af; 
            font-size: 12px; 
            font-weight: 600; 
        }

        .divider::before, 
        .divider::after { 
            content: ''; 
            flex: 1; 
            border-bottom: 1px solid #e5e7eb; 
        }

        .divider:not(:empty)::before { 
            margin-right: .8em; 
        }

        .divider:not(:empty)::after { 
            margin-left: .8em; 
        }

        .social-btn { 
            width: 100%; 
            padding: 12px; 
            background-color: white; 
            color: #374151; 
            border: 1px solid #d1d5db; 
            border-radius: 8px; 
            font-size: 14px; 
            font-weight: 600; 
            cursor: pointer; 
            margin-bottom: 12px; 
            transition: background-color 0.2s ease; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            gap: 10px; 
        }

        .social-btn:hover { 
            background-color: #f9fafb; 
        }

        .social-btn svg { 
            width: 20px; 
            height: 20px; 
        }

        .toggle-text { 
            text-align: center; 
            margin-top: 20px; 
            font-size: 14px; 
        }

        .toggle-text a { 
            color: #2563eb; 
            text-decoration: none; 
            font-weight: 600; 
        }

        .social-btn img {
            width: 20px;
            height: 20px;
            object-fit: contain;
        }
    </style>
</head>
<body>
   <section class="container">
    
    <div id="login-view">
        <header>
            <h2>Welcome Back</h2>
        </header>

        <form method="POST" action="auth.php">
            <input type="hidden" name="action" value="login">

            <div class="secta">
                <label for="login-email">Email</label>
                <input type="email" name="email" id="login-email" required placeholder="Enter Email">
            </div>

            <div class="secta">
                <label for="login-password">Password</label>
                <input type="password" name="password" id="login-password" required placeholder="Enter Password">
            </div>

            <button type="submit" class="primary-btn">Log in</button>
        </form>

        <div class="divider">OR CONTINUE WITH</div>

        <div>
            <button type="button" class="social-btn">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Google
            </button>
           <button type="button" class="social-btn" onclick="alert('Social logins coming soon!')">
          <img class="apple" src="../Images/apple-logo.png">
    Apple
</button>
        </div>

        <p class="toggle-text">Don't have an account? <a href="#" id="go-to-signup">Sign Up</a></p>
    </div>

    <div id="signup-view" class="hidden">
        <header>
            <h2>Create Account</h2>
        </header>

        <form method="POST" action="auth.php">
            <input type="hidden" name="action" value="register">

            <div class="secta">
                <label for="signup-username">Username</label>
                <input type="text" name="username" id="signup-username" required placeholder="Choose a username">
            </div>

            <div class="secta">
                <label for="signup-email">Email</label>
                <input type="email" name="email" id="signup-email" required placeholder="Enter Email">
            </div>

            <div class="secta">
                <label for="signup-password">Password</label>
                <input type="password" name="password" id="signup-password" required placeholder="Create Password">
                <span id="strength-message" style="font-size: 12px; font-weight: 600; margin-top: -2px;"></span>
</div>

            <button type="submit" class="primary-btn">Sign Up</button>
        </form>

        <div class="divider">OR CONTINUE WITH</div>

        <div>
            <button type="button" class="social-btn">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Google
            </button>
            <button type="button" class="social-btn" onclick="alert('Social logins coming soon!')">
              <img class="apple" src="../Images/apple-logo.png">
    Apple
</button>
        </div>

        <p class="toggle-text">Already have an account? <a href="#" id="go-to-login">Log In</a></p>
    </div>
   </section> 

   <script>
       const loginView = document.getElementById('login-view');
       const signupView = document.getElementById('signup-view');
       const goToSignup = document.getElementById('go-to-signup');
       const goToLogin = document.getElementById('go-to-login');

       goToSignup.addEventListener('click', function(e){
           e.preventDefault();
           loginView.classList.add('hidden');
           signupView.classList.remove('hidden');
       });

       goToLogin.addEventListener('click', function(e){
           e.preventDefault();
           signupView.classList.add('hidden');
           loginView.classList.remove('hidden');
       });

      

const signupPassword = document.getElementById('signup-password');
const strengthMessage = document.getElementById('strength-message');
const signupForm = document.querySelector('#signup-view form');


signupPassword.addEventListener('input', function() {
    const val = signupPassword.value;
    let strength = 0;

    
    if (val.length > 5) strength += 1;
    if (val.length > 7) strength += 1;
    if (/[A-Z]/.test(val)) strength += 1;
    if (/[0-9]/.test(val)) strength += 1;

    
    if (val.length === 0) {
        strengthMessage.textContent = "";
    } else if (strength < 2) {
        strengthMessage.textContent = "Weak Password";
        strengthMessage.style.color = "#ef4444"; // Red
    } else if (strength === 2 || strength === 3) {
        strengthMessage.textContent = "Medium Password";
        strengthMessage.style.color = "#f59e0b"; // Orange
    } else {
        strengthMessage.textContent = "Strong Password";
        strengthMessage.style.color = "#10b981"; // Green
    }
});


signupForm.addEventListener('submit', function(e) {
    if (signupPassword.value.length < 6) {
        e.preventDefault(); 
        alert('Please enter a password of at least 6 characters.');
    }
});
   </script>
</body>
</html>
