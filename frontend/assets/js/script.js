document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
  
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    const errorMsg = document.getElementById('errorMsg');
  
    if (username === 'admin' && password === 'admin123') {
      window.location.href = 'dashboard.html';
    } else {
      errorMsg.textContent = 'Invalid username or password.';
    }
  });
  