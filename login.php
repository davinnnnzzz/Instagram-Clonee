<?php

include 'config/koneksi.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Login</title>
    <link rel="stylesheet" href="public/css/login.css">
</head>
<body>
    <header>
        <h2 class = "Judul1">Instagram Clone</h2>
         <nav class = "Menu">
            <ul>
                <li><a href="Registasi.php">Registasi</a></li>
            </ul>
         </nav>
    </header>
    
    
    <main>
        <section class="login">
            <div class="container">
                                <form id="loginForm">
                                <h2 class="Judul2">Login</h2>
                                <div class="form-group">
                                        <label for="username">Username:</label>
                                        <input type="text" id="username" name="username" required>
                                </div>
                                <div class="form-group">
                                        <label for="password">Password:</label>
                                        <input type="password" id="password" name="password" required>
                                </div>
                                <button type="submit">Login</button>
                        </form>
                        <script>
                            document.getElementById('loginForm').addEventListener('submit', function(e){
                                e.preventDefault();
                                const form = e.target;
                                const data = new URLSearchParams();
                                data.append('username', form.username.value);
                                data.append('password', form.password.value);
                                fetch('api.php?action=login', { method: 'POST', body: data }).then(r=>r.json()).then(res=>{
                                    if (res.ok) {
                                        // redirect to index
                                        window.location.href = 'index.php';
                                    } else alert('Login gagal: '+(res.msg||'error'));
                                }).catch(err=>alert('Gagal terhubung: '+err.message));
                            });
                        </script>
            </div>
        </section>
    </main>
</body>
</html>