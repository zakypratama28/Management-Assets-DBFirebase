<?php
// register.php
session_start();
require_once 'firebase_config.php';

$error = '';
$success = '';
$verificationSent = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $name = trim($_POST['name'] ?? '');

    if (empty($email) || empty($password) || empty($password_confirm)) {
        $error = "Silakan lengkapi semua field yang wajib diisi.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    } elseif ($password !== $password_confirm) {
        $error = "Konfirmasi password tidak cocok.";
    } else {
        try {
            // Buat user baru di Firebase
            $userProps = [
                'email'       => $email,
                'password'    => $password,
                'displayName' => $name ?: explode('@', $email)[0],
            ];

            $user = $auth->createUser($userProps);

            // Kirim email verifikasi via Firebase Action Code URL
            // Action URL redirect ke login.php dengan query ?verified=1
            $actionCodeSettings = \Kreait\Firebase\Request\CreateActionLink::forEmailVerification(
                $email,
                []
            );

            // Kirim verification link
            $verificationLink = $auth->getEmailVerificationLink($email);

            // Kirim email menggunakan fungsi built-in Firebase (Admin SDK)
            $auth->sendEmailVerificationLink($email);

            $verificationSent = true;
            $success = "Akun berhasil dibuat! Email verifikasi telah dikirim ke <strong>" . htmlspecialchars($email) . "</strong>. Silakan cek inbox (dan folder spam) Anda, lalu klik link verifikasi untuk mengaktifkan akun.";

        } catch (\Kreait\Firebase\Exception\Auth\EmailExists $e) {
            $error = "Email <strong>" . htmlspecialchars($email) . "</strong> sudah terdaftar. Silakan <a href='login.php'>login</a> atau gunakan email lain.";
        } catch (\Kreait\Firebase\Exception\Auth\InvalidEmail $e) {
            $error = "Format email tidak valid. Periksa kembali alamat email Anda.";
        } catch (\Kreait\Firebase\Exception\Auth\WeakPassword $e) {
            $error = "Password terlalu lemah. Gunakan kombinasi huruf, angka, dan simbol.";
        } catch (\Exception $e) {
            $error = "Registrasi gagal: " . $e->getMessage();
        }
    }
}
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
    <title>Daftar Akun — AssetPro</title>
    <meta name="description" content="Daftar akun baru di AssetPro — Sistem Manajemen Aset Digital." />

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

    <!-- Page CSS -->
    <link rel="stylesheet" href="sneat-1.0.0/assets/vendor/css/pages/page-auth.css?v=2" />

    <style>
      .authentication-inner { max-width: 450px; }
      .card { border-radius: 16px; box-shadow: 0 8px 40px rgba(105,108,255,.12); }
      .card-body { padding: 2.5rem !important; }
      .app-brand { margin-bottom: 1.5rem; }
      .divider-text { font-size: 0.78rem; color: #8592a3; }
      .form-label { font-weight: 500; font-size: 0.875rem; }
      .btn-primary { border-radius: 8px; padding: .75rem; font-weight: 600; letter-spacing: .3px; transition: all .2s; }
      .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(105,108,255,.4); }
      .alert { border-radius: 10px; font-size: .875rem; }
      .input-group-text { background: transparent; }
      .form-control:focus { border-color: #696cff; box-shadow: 0 0 0 3px rgba(105,108,255,.15); }
      .verification-success { text-align: center; padding: 1rem 0; }
      .verification-success .icon-wrapper {
        width: 80px; height: 80px; border-radius: 50%;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1.25rem;
        animation: scaleIn .4s ease;
      }
      .verification-success .icon-wrapper i { font-size: 2.5rem; color: #fff; }
      @keyframes scaleIn { from { transform: scale(0.5); opacity: 0; } to { transform: scale(1); opacity: 1; } }
      .password-strength { height: 4px; border-radius: 4px; margin-top: 6px; transition: all .3s; }
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
                <a href="index.php" class="app-brand-link gap-2">
                  <span class="app-brand-logo demo">
                    <svg width="25" viewBox="0 0 25 42" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                      <defs>
                        <path d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z" id="path-1"></path>
                        <path d="M5.47320593,6.00457225 C4.05321814,8.216144 4.36334763,10.0722806 6.40359441,11.5729822 C8.61520715,12.571656 10.0999176,13.2171421 10.8577257,13.5094407 L15.5088241,14.433041 L18.6192054,7.984237 C15.5364148,3.11535317 13.9273018,0.573395879 13.7918663,0.358365126 C13.5790555,0.511491653 10.8061687,2.3935607 5.47320593,6.00457225 Z" id="path-3"></path>
                        <path d="M7.50063644,21.2294429 L12.3234468,23.3159332 C14.1688022,24.7579751 14.397098,26.4880487 13.008334,28.506154 C11.6195701,30.5242593 10.3099883,31.790241 9.07958868,32.3040991 C5.78142938,33.4346997 4.13234973,34 4.13234973,34 C4.13234973,34 2.75489982,33.0538207 2.37032616e-14,31.1614621 C-0.55822714,27.8186216 -0.55822714,26.0572515 -4.05231404e-15,25.8773518 C0.83734071,25.6075023 2.77988457,22.8248993 3.3049379,22.52991 C3.65497346,22.3332504 5.05353963,21.8997614 7.50063644,21.2294429 Z" id="path-4"></path>
                        <path d="M20.6,7.13333333 L25.6,13.8 C26.2627417,14.6836556 26.0836556,15.9372583 25.2,16.6 C24.8538077,16.8596443 24.4327404,17 24,17 L14,17 C12.8954305,17 12,16.1045695 12,15 C12,14.5672596 12.1403557,14.1461923 12.4,13.8 L17.4,7.13333333 C18.0627417,6.24967773 19.3163444,6.07059163 20.2,6.73333333 C20.3516113,6.84704183 20.4862915,6.981722 20.6,7.13333333 Z" id="path-5"></path>
                      </defs>
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g transform="translate(-27.000000, -15.000000)">
                          <g transform="translate(27.000000, 15.000000)">
                            <g transform="translate(0.000000, 8.000000)">
                              <mask fill="white"><use xlink:href="#path-1"></use></mask>
                              <use fill="#696cff" xlink:href="#path-1"></use>
                              <g mask="url(#mask-2)">
                                <use fill="#696cff" xlink:href="#path-3"></use>
                                <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3"></use>
                              </g>
                              <g mask="url(#mask-2)">
                                <use fill="#696cff" xlink:href="#path-4"></use>
                                <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4"></use>
                              </g>
                            </g>
                            <g transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000)">
                              <use fill="#696cff" xlink:href="#path-5"></use>
                              <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5"></use>
                            </g>
                          </g>
                        </g>
                      </g>
                    </svg>
                  </span>
                  <span class="app-brand-text demo text-body fw-bolder">AssetPro</span>
                </a>
              </div>

              <?php if ($verificationSent): ?>
                <!-- State: Email Verifikasi Terkirim -->
                <div class="verification-success">
                  <div class="icon-wrapper">
                    <i class="bx bx-mail-send"></i>
                  </div>
                  <h4 class="mb-2 fw-bold">Cek Email Anda! 📬</h4>
                  <p class="text-muted mb-3" style="font-size:.9rem;">
                    Link verifikasi telah dikirim ke:<br>
                    <strong class="text-dark"><?= htmlspecialchars($email) ?></strong>
                  </p>
                  <div class="alert alert-info text-start" style="font-size:.82rem;">
                    <i class="bx bx-info-circle me-1"></i>
                    Klik link di email tersebut untuk mengaktifkan akun, kemudian login. Jika tidak muncul, cek folder <strong>Spam/Junk</strong>.
                  </div>
                  <a href="login.php" class="btn btn-primary w-100 mt-2">
                    <i class="bx bx-log-in me-1"></i> Pergi ke Halaman Login
                  </a>
                </div>

              <?php else: ?>
                <h4 class="mb-1 fw-bold">Buat Akun Baru 🚀</h4>
                <p class="mb-4 text-muted" style="font-size:.875rem;">Daftarkan diri untuk mulai mengelola aset perusahaan Anda.</p>

                <?php if (!empty($error)): ?>
                  <div class="alert alert-danger d-flex align-items-start gap-2" role="alert">
                    <i class="bx bx-error-circle mt-1 flex-shrink-0"></i>
                    <div><?= $error ?></div>
                  </div>
                <?php endif; ?>

                <form id="formRegister" action="register.php" method="POST" novalidate>
                  <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap <span class="text-muted fw-normal">(Opsional)</span></label>
                    <div class="input-group input-group-merge">
                      <span class="input-group-text"><i class="bx bx-user"></i></span>
                      <input type="text" class="form-control" id="name" name="name"
                        placeholder="Nama Anda"
                        value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" />
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <div class="input-group input-group-merge">
                      <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                      <input type="email" class="form-control" id="email" name="email"
                        placeholder="contoh@email.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        autofocus required />
                    </div>
                  </div>

                  <div class="mb-3 form-password-toggle">
                    <label class="form-label" for="password">Password <span class="text-danger">*</span></label>
                    <div class="input-group input-group-merge">
                      <span class="input-group-text"><i class="bx bx-lock-alt"></i></span>
                      <input type="password" id="password" class="form-control" name="password"
                        placeholder="Min. 6 karakter" required minlength="6" />
                      <span class="input-group-text cursor-pointer" id="togglePassword">
                        <i class="bx bx-hide" id="togglePasswordIcon"></i>
                      </span>
                    </div>
                    <div class="password-strength bg-secondary mt-2" id="strengthBar" style="display:none;"></div>
                    <small class="text-muted" id="strengthText"></small>
                  </div>

                  <div class="mb-4 form-password-toggle">
                    <label class="form-label" for="password_confirm">Konfirmasi Password <span class="text-danger">*</span></label>
                    <div class="input-group input-group-merge">
                      <span class="input-group-text"><i class="bx bx-lock-alt"></i></span>
                      <input type="password" id="password_confirm" class="form-control" name="password_confirm"
                        placeholder="Ulangi password" required />
                      <span class="input-group-text cursor-pointer" id="toggleConfirm">
                        <i class="bx bx-hide" id="toggleConfirmIcon"></i>
                      </span>
                    </div>
                    <small class="text-danger d-none" id="matchError"><i class="bx bx-x-circle me-1"></i>Password tidak cocok</small>
                    <small class="text-success d-none" id="matchOk"><i class="bx bx-check-circle me-1"></i>Password cocok</small>
                  </div>

                  <button class="btn btn-primary d-grid w-100 mb-3" type="submit" id="btnRegister">
                    <span class="d-flex align-items-center justify-content-center gap-2">
                      <i class="bx bx-user-plus"></i> Daftar Sekarang
                    </span>
                  </button>
                </form>

                <p class="text-center mb-0" style="font-size:.875rem;">
                  <span class="text-muted">Sudah punya akun?</span>
                  <a href="login.php" class="fw-semibold ms-1">Masuk disini</a>
                </p>
              <?php endif; ?>

            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Core JS -->
    <script src="sneat-1.0.0/assets/vendor/libs/jquery/jquery.js?v=2"></script>
    <script src="sneat-1.0.0/assets/vendor/libs/popper/popper.js?v=2"></script>
    <script src="sneat-1.0.0/assets/vendor/js/bootstrap.js?v=2"></script>
    <script src="sneat-1.0.0/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js?v=2"></script>
    <script src="sneat-1.0.0/assets/vendor/js/menu.js?v=2"></script>
    <script src="sneat-1.0.0/assets/js/main.js?v=2"></script>

    <script>
      // Toggle password visibility
      document.getElementById('togglePassword').addEventListener('click', function() {
        const input = document.getElementById('password');
        const icon = document.getElementById('togglePasswordIcon');
        input.type = input.type === 'password' ? 'text' : 'password';
        icon.classList.toggle('bx-show');
        icon.classList.toggle('bx-hide');
      });
      document.getElementById('toggleConfirm').addEventListener('click', function() {
        const input = document.getElementById('password_confirm');
        const icon = document.getElementById('toggleConfirmIcon');
        input.type = input.type === 'password' ? 'text' : 'password';
        icon.classList.toggle('bx-show');
        icon.classList.toggle('bx-hide');
      });

      // Password strength indicator
      const passwordInput = document.getElementById('password');
      const strengthBar = document.getElementById('strengthBar');
      const strengthText = document.getElementById('strengthText');
      passwordInput.addEventListener('input', function() {
        const pw = this.value;
        if (!pw) { strengthBar.style.display='none'; strengthText.textContent=''; return; }
        strengthBar.style.display = 'block';
        let strength = 0;
        if (pw.length >= 6) strength++;
        if (pw.length >= 10) strength++;
        if (/[A-Z]/.test(pw)) strength++;
        if (/[0-9]/.test(pw)) strength++;
        if (/[^a-zA-Z0-9]/.test(pw)) strength++;
        const levels = [
          { color: '#ff4d4f', label: 'Sangat Lemah', width: '20%' },
          { color: '#ff7043', label: 'Lemah', width: '40%' },
          { color: '#ffa726', label: 'Cukup', width: '60%' },
          { color: '#66bb6a', label: 'Kuat', width: '80%' },
          { color: '#43a047', label: 'Sangat Kuat', width: '100%' },
        ];
        const lv = levels[Math.min(strength - 1, 4)];
        strengthBar.style.background = lv.color;
        strengthBar.style.width = lv.width;
        strengthText.textContent = 'Kekuatan: ' + lv.label;
        strengthText.style.color = lv.color;
      });

      // Password match check
      const confirmInput = document.getElementById('password_confirm');
      confirmInput.addEventListener('input', function() {
        const matchError = document.getElementById('matchError');
        const matchOk = document.getElementById('matchOk');
        if (!this.value) { matchError.classList.add('d-none'); matchOk.classList.add('d-none'); return; }
        if (this.value === passwordInput.value) {
          matchError.classList.add('d-none'); matchOk.classList.remove('d-none');
        } else {
          matchOk.classList.add('d-none'); matchError.classList.remove('d-none');
        }
      });

      // Loading state on submit
      document.getElementById('formRegister')?.addEventListener('submit', function() {
        const btn = document.getElementById('btnRegister');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
        btn.disabled = true;
      });
    </script>
  </body>
</html>
