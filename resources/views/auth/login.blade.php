<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - CCTV Web</title>

  <!-- Bootstrap CSS (CDN) -->
  <link 
    rel="stylesheet" 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
  />

  <!-- Tailwind CSS (CDN) -->
  <link 
    href="https://cdn.jsdelivr.net/npm/tailwindcss@3.2.7/dist/tailwind.min.css" 
    rel="stylesheet"
  />

  <style>
    /* Ganti URL berikut dengan gambar background Anda */
    .bg-login {
      background-image: url('/images/gambar.jpg');
      background-size: cover;
      background-position: center;
      background-color: rgba(0, 0, 0, 0.4);
      background-blend-mode: multiply;
    }
  </style>
</head>
<body class="bg-login min-h-screen flex items-center justify-center">

  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="bg-white rounded-lg shadow-lg p-5" style="max-width: 400px; width: 100%;">
      <!-- Judul Form -->
      <h2 class="text-center mb-4 font-bold text-2xl">Please Log In dulu Le</h2>

      <!-- FORM MULAI -->
      <form action="{{ route('login') }}" method="POST">
        @csrf

        <!-- Form Group: Email -->
        <div class="mb-3">
          <label for="email" class="form-label text-gray-700">Email</label>
          <input 
            type="email" 
            class="form-control" 
            id="email" 
            name="email"  
            placeholder="Enter your email" 
            required
          />
        </div>

        <!-- Form Group: Password -->
        <div class="mb-3">
          <div class="d-flex justify-content-between align-items-center">
            <label for="password" class="form-label text-gray-700">Password</label>
            <!-- Link Forgot Password -->
            <a href="{{ route('forgot.password') }}" class="text-blue-600 text-sm hover:underline">Forgot?</a>
          </div>
          <input 
            type="password" 
            class="form-control" 
            id="password" 
            name="password"
            placeholder="Enter your password" 
            required
          />
        </div>

        <!-- Tombol Log In -->
        <div class="mb-3">
          <button 
            class="btn btn-primary w-100 py-2 text-lg font-bold"
            type="submit"
          >
            Log In
          </button>
        </div>
      </form>
      <!-- FORM SELESAI -->

      <!-- Tombol Continue with Google -->
      <div class="mb-3">
        <a href="{{ route('auth.google') }}" class="btn btn-outline-secondary w-100 py-2 d-flex align-items-center justify-content-center">
          <img 
            src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/google/google-original.svg" 
            alt="Google" 
            class="me-2" 
            style="width: 20px;"
          />
          Continue with Google
        </a>
      </div>

      <!-- Link Belum Punya Akun? -->
      <div class="text-center">
        <span class="text-gray-600">Don't have an account?</span>
        <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:underline">
          Sign Up
        </a>
      </div>

    </div>
  </div>

  <!-- Bootstrap JS (CDN) -->
  <script 
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
  </script>
</body>
</html>