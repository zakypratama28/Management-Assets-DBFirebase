<?php
// verify-email.php — Halaman untuk memproses link verifikasi email dari Firebase
// Halaman ini TIDAK memerlukan login (public page)

// Redirect jika tidak ada parameter mode
if (empty($_GET['mode']) || empty($_GET['oobCode'])) {
    header('Location: login.php');
    exit;
}

$mode    = $_GET['mode'] ?? '';
$oobCode = $_GET['oobCode'] ?? '';
$apiKey  = $_GET['apiKey'] ?? 'AIzaSyCTD9y48V-OZO5sKqRL4Hq8LLkjoYxHjJA';
?>
<!DOCTYPE html>
<html
  lang="id"
  class="light-style customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="sneat-1.0.0/assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Verifikasi Email — AssetPro</title>
    <meta name="description" content="Verifikasi email akun AssetPro" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="sneat-1.0.0/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="sneat-1.0.0/assets/vendor/fonts/boxicons.css?v=2" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="sneat-1.0.0/assets/vendor/css/core.css?v=2" class="template-customizer-core-css" />
    <link rel="stylesheet" href="sneat-1.0.0/assets/vendor/css/theme-default.css?v=2" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="sneat-1.0.0/assets/css/demo.css?v=2" />
    <link rel="stylesheet" href="sneat-1.0.0/assets/vendor/css/pages/page-auth.css?v=2" />

    <style>
      .authentication-inner { max-width: 460px; }
      .card { border-radius: 16px; box-shadow: 0 8px 40px rgba(105,108,255,.12); }
      .card-body { padding: 2.5rem !important; text-align: center; }

      /* Spinner state */
      .state-loading .icon-wrapper { background: linear-gradient(135deg, #696cff, #8592ff); }
      /* Success state */
      .state-success .icon-wrapper { background: linear-gradient(135deg, #28a745, #20c997); }
      /* Error state */
      .state-error   .icon-wrapper { background: linear-gradient(135deg, #dc3545, #ff6b6b); }

      .icon-wrapper {
        width: 88px; height: 88px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1.5rem;
        transition: background .4s ease;
      }
      .icon-wrapper i { font-size: 2.8rem; color: #fff; }

      @keyframes spin  { to { transform: rotate(360deg); } }
      @keyframes pulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.08)} }
      @keyframes scaleIn { from{transform:scale(.3);opacity:0} to{transform:scale(1);opacity:1} }
      @keyframes slideUp { from{transform:translateY(20px);opacity:0} to{transform:translateY(0);opacity:1} }

      .spin-icon  { animation: spin 1s linear infinite; }
      .pulse-icon { animation: pulse 1.8s ease-in-out infinite; }
      .pop-in     { animation: scaleIn .4s cubic-bezier(.34,1.56,.64,1); }
      .slide-up   { animation: slideUp .4s ease forwards; }

      .btn-primary { border-radius: 8px; padding: .75rem 2rem; font-weight: 600; transition: all .2s; }
      .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(105,108,255,.4); }
      .btn-outline-secondary { border-radius: 8px; padding: .65rem 1.5rem; font-weight: 500; }

      #countdown { font-weight: 700; color: #696cff; }
      .progress-bar-wrapper {
        height: 4px; background: #eff2f7; border-radius: 4px; overflow: hidden; margin: 1rem 0 1.5rem;
      }
      .progress-bar-fill {
        height: 100%; background: linear-gradient(90deg, #696cff, #8592ff);
        border-radius: 4px; transition: width .1s linear;
      }
    </style>
  </head>

  <body>
    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <div class="card">
            <div class="card-body">

              <!-- Logo -->
              <div class="app-brand justify-content-center mb-4">
                <a href="login.php" class="app-brand-link gap-2">
                  <span class="app-brand-logo demo">
                    <svg width="25" viewBox="0 0 25 42" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                      <defs>
                        <path d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z" id="p1"></path>
                        <path d="M5.47320593,6.00457225 C4.05321814,8.216144 4.36334763,10.0722806 6.40359441,11.5729822 C8.61520715,12.571656 10.0999176,13.2171421 10.8577257,13.5094407 L15.5088241,14.433041 L18.6192054,7.984237 C15.5364148,3.11535317 13.9273018,0.573395879 13.7918663,0.358365126 C13.5790555,0.511491653 10.8061687,2.3935607 5.47320593,6.00457225 Z" id="p3"></path>
                        <path d="M7.50063644,21.2294429 L12.3234468,23.3159332 C14.1688022,24.7579751 14.397098,26.4880487 13.008334,28.506154 C11.6195701,30.5242593 10.3099883,31.790241 9.07958868,32.3040991 C5.78142938,33.4346997 4.13234973,34 4.13234973,34 C4.13234973,34 2.75489982,33.0538207 2.37032616e-14,31.1614621 C-0.55822714,27.8186216 -0.55822714,26.0572515 -4.05231404e-15,25.8773518 C0.83734071,25.6075023 2.77988457,22.8248993 3.3049379,22.52991 C3.65497346,22.3332504 5.05353963,21.8997614 7.50063644,21.2294429 Z" id="p4"></path>
                        <path d="M20.6,7.13333333 L25.6,13.8 C26.2627417,14.6836556 26.0836556,15.9372583 25.2,16.6 C24.8538077,16.8596443 24.4327404,17 24,17 L14,17 C12.8954305,17 12,16.1045695 12,15 C12,14.5672596 12.1403557,14.1461923 12.4,13.8 L17.4,7.13333333 C18.0627417,6.24967773 19.3163444,6.07059163 20.2,6.73333333 C20.3516113,6.84704183 20.4862915,6.981722 20.6,7.13333333 Z" id="p5"></path>
                      </defs>
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g transform="translate(-27,-15)"><g transform="translate(27,15)">
                          <g transform="translate(0,8)">
                            <mask fill="white"><use xlink:href="#p1"></use></mask>
                            <use fill="#696cff" xlink:href="#p1"></use>
                            <g mask="url(#mask-2)"><use fill="#696cff" xlink:href="#p3"></use><use fill-opacity=".2" fill="#FFF" xlink:href="#p3"></use></g>
                            <g mask="url(#mask-2)"><use fill="#696cff" xlink:href="#p4"></use><use fill-opacity=".2" fill="#FFF" xlink:href="#p4"></use></g>
                          </g>
                          <g transform="translate(19,11) rotate(-300) translate(-19,-11)">
                            <use fill="#696cff" xlink:href="#p5"></use>
                            <use fill-opacity=".2" fill="#FFF" xlink:href="#p5"></use>
                          </g>
                        </g></g>
                      </g>
                    </svg>
                  </span>
                  <span class="app-brand-text demo text-body fw-bolder">AssetPro</span>
                </a>
              </div>

              <!-- State: Loading -->
              <div id="stateLoading">
                <div class="icon-wrapper state-loading mb-3">
                  <i class="bx bx-loader-alt spin-icon"></i>
                </div>
                <h4 class="mb-2 fw-bold">Memverifikasi Email...</h4>
                <p class="text-muted" style="font-size:.875rem;">Mohon tunggu sebentar, kami sedang mengaktifkan akun Anda.</p>
              </div>

              <!-- State: Success (hidden) -->
              <div id="stateSuccess" style="display:none;">
                <div class="icon-wrapper state-success pop-in mb-3">
                  <i class="bx bx-check-double"></i>
                </div>
                <h4 class="mb-2 fw-bold slide-up">Email Terverifikasi! 🎉</h4>
                <p class="text-muted slide-up" style="font-size:.875rem;">
                  Akun Anda telah berhasil diaktifkan. Anda akan diarahkan ke halaman login dalam <span id="countdown">5</span> detik.
                </p>
                <div class="progress-bar-wrapper">
                  <div class="progress-bar-fill" id="progressBar" style="width:0%"></div>
                </div>
                <a href="login.php?verified=1" class="btn btn-primary w-100" id="btnLogin">
                  <i class="bx bx-log-in me-1"></i> Masuk Sekarang
                </a>
              </div>

              <!-- State: Error (hidden) -->
              <div id="stateError" style="display:none;">
                <div class="icon-wrapper state-error pop-in mb-3">
                  <i class="bx bx-error"></i>
                </div>
                <h4 class="mb-2 fw-bold">Verifikasi Gagal</h4>
                <p class="text-muted mb-3" id="errorMessage" style="font-size:.875rem;">Link verifikasi tidak valid atau sudah kedaluwarsa.</p>
                <div class="d-flex flex-column gap-2">
                  <a href="login.php" class="btn btn-primary">
                    <i class="bx bx-log-in me-1"></i> Pergi ke Halaman Login
                  </a>
                  <button class="btn btn-outline-secondary" onclick="window.location.href='register.php'">
                    <i class="bx bx-user-plus me-1"></i> Daftar Ulang
                  </button>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Firebase JS SDK -->
    <script type="module">
      import { initializeApp }     from 'https://www.gstatic.com/firebasejs/10.12.4/firebase-app.js';
      import { getAuth, applyActionCode, checkActionCode } from 'https://www.gstatic.com/firebasejs/10.12.4/firebase-auth.js';

      const firebaseConfig = {
        apiKey:            "AIzaSyCTD9y48V-OZO5sKqRL4Hq8LLkjoYxHjJA",
        authDomain:        "db-crud-9d395.firebaseapp.com",
        databaseURL:       "https://db-crud-9d395-default-rtdb.asia-southeast1.firebasedatabase.app",
        projectId:         "db-crud-9d395",
        storageBucket:     "db-crud-9d395.firebasestorage.app",
        messagingSenderId: "982989578926",
        appId:             "1:982989578926:web:6b86d1efa2d46e0ee6033f",
      };

      const app  = initializeApp(firebaseConfig);
      const auth = getAuth(app);

      const mode    = "<?= htmlspecialchars($mode) ?>";
      const oobCode = "<?= htmlspecialchars($oobCode) ?>";

      function showSuccess() {
        document.getElementById('stateLoading').style.display = 'none';
        document.getElementById('stateSuccess').style.display = 'block';

        // Countdown + progress bar auto-redirect
        let seconds = 5;
        const countdownEl   = document.getElementById('countdown');
        const progressBar   = document.getElementById('progressBar');
        const totalMs       = seconds * 1000;
        const startTime     = Date.now();

        const tick = setInterval(() => {
          const elapsed  = Date.now() - startTime;
          const remaining = Math.max(0, totalMs - elapsed);
          const secs  = Math.ceil(remaining / 1000);
          const pct   = ((totalMs - remaining) / totalMs * 100).toFixed(1);

          countdownEl.textContent = secs;
          progressBar.style.width = pct + '%';

          if (remaining <= 0) {
            clearInterval(tick);
            window.location.href = 'login.php?verified=1';
          }
        }, 100);
      }

      function showError(msg) {
        document.getElementById('stateLoading').style.display = 'none';
        document.getElementById('stateError').style.display   = 'block';
        if (msg) document.getElementById('errorMessage').textContent = msg;
      }

      // Proses action code dari Firebase
      if (mode === 'verifyEmail' && oobCode) {
        applyActionCode(auth, oobCode)
          .then(() => {
            showSuccess();
          })
          .catch((error) => {
            console.error('Verification error:', error.code, error.message);
            let msg = 'Link verifikasi tidak valid atau sudah kedaluwarsa.';
            if (error.code === 'auth/expired-action-code') {
              msg = 'Link verifikasi sudah kedaluwarsa. Silakan minta link baru.';
            } else if (error.code === 'auth/invalid-action-code') {
              msg = 'Link verifikasi tidak valid atau sudah pernah digunakan.';
            } else if (error.code === 'auth/user-disabled') {
              msg = 'Akun ini telah dinonaktifkan.';
            } else if (error.code === 'auth/user-not-found') {
              msg = 'Akun tidak ditemukan.';
            }
            showError(msg);
          });
      } else {
        showError('Parameter tidak valid. Gunakan link dari email verifikasi Anda.');
      }
    </script>
  </body>
</html>
