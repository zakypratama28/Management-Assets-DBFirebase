<?php
// login.php
session_start();
require_once 'firebase_config.php';

$error = '';
$errorType = ''; // 'not_found' | 'wrong_password' | 'unverified' | 'general'
$success = '';

// Tangkap pesan sukses dari redirect register/verify
if (isset($_GET['verified'])) {
    $success = "Email berhasil diverifikasi! Silakan login dengan akun Anda.";
} elseif (isset($_GET['registered'])) {
    $success = "Pendaftaran berhasil! Cek email Anda untuk verifikasi akun sebelum login.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Silakan isi email dan password.";
        $errorType = 'general';
    } else {
        try {
            // Login via Firebase REST API
            $signInResult = $auth->signInWithEmailAndPassword($email, $password);
            $uid = $signInResult->firebaseUserId();

            // Ambil data user untuk cek status email verification
            $user = $auth->getUser($uid);

            if (!$user->emailVerified) {
                $errorType = 'unverified';
                $error = "Email Anda belum diverifikasi. Cek inbox Anda dan klik link verifikasi.";
            } else {
                // Set session
                $_SESSION['user_id'] = $uid;
                $_SESSION['email']   = $email;
                $_SESSION['name']    = $user->displayName ?: explode('@', $email)[0];

                header('Location: index.php');
                exit;
            }

        } catch (\Kreait\Firebase\Exception\Auth\InvalidPassword $e) {
            $errorType = 'wrong_password';
            $error = "Password salah. Periksa kembali password Anda.";
        } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
            $errorType = 'not_found';
            $error = "Akun dengan email <strong>" . htmlspecialchars($email) . "</strong> tidak ditemukan.";
        } catch (\Kreait\Firebase\Exception\Auth\UserDisabled $e) {
            $errorType = 'general';
            $error = "Akun Anda telah dinonaktifkan. Hubungi administrator.";
        } catch (\Exception $e) {
            $errorType = 'general';
            $errMsg = $e->getMessage();
            // Firebase REST error parsing
            if (str_contains($errMsg, 'INVALID_PASSWORD') || str_contains($errMsg, 'INVALID_LOGIN_CREDENTIALS')) {
                $errorType = 'wrong_password';
                $error = "Email atau password salah. Periksa kembali kredensial Anda.";
            } elseif (str_contains($errMsg, 'EMAIL_NOT_FOUND') || str_contains($errMsg, 'USER_NOT_FOUND')) {
                $errorType = 'not_found';
                $error = "Akun dengan email <strong>" . htmlspecialchars($email) . "</strong> tidak ditemukan.";
            } elseif (str_contains($errMsg, 'TOO_MANY_ATTEMPTS')) {
                $errorType = 'general';
                $error = "Terlalu banyak percobaan login. Coba lagi beberapa saat.";
            } else {
                $error = "Login gagal. Silakan coba lagi.";
            }
        }
    }
}

// Helper: kirim ulang email verifikasi
$resendSuccess = '';
$resendError   = '';
if (isset($_POST['resend_email']) && !empty($_POST['resend_email_addr'])) {
    try {
        $auth->sendEmailVerificationLink(trim($_POST['resend_email_addr']));
        $resendSuccess = "Link verifikasi baru telah dikirim ke " . htmlspecialchars(trim($_POST['resend_email_addr'])) . ".";
    } catch (\Exception $e) {
        $resendError = "Gagal mengirim ulang. Pastikan email sudah terdaftar.";
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
    <title>Login — AssetPro</title>
    <meta name="description" content="Login ke AssetPro — Sistem Manajemen Aset Digital." />

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
      .authentication-inner { max-width: 430px; }
      .card { border-radius: 16px; box-shadow: 0 8px 40px rgba(105,108,255,.12); }
      .card-body { padding: 2.5rem !important; }
      .form-label { font-weight: 500; font-size: .875rem; }
      .btn-primary { border-radius: 8px; padding: .75rem; font-weight: 600; letter-spacing: .3px; transition: all .2s; }
      .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(105,108,255,.4); }
      .alert { border-radius: 10px; font-size: .875rem; }
      .form-control:focus { border-color: #696cff; box-shadow: 0 0 0 3px rgba(105,108,255,.15); }
      .input-group-text { background: transparent; }
      .error-hint { font-size: .8rem; margin-top: .35rem; }
      /* Shake animation for wrong password */
      @keyframes shake {
        0%,100%{transform:translateX(0)}
        20%{transform:translateX(-6px)}
        40%{transform:translateX(6px)}
        60%{transform:translateX(-4px)}
        80%{transform:translateX(4px)}
      }
      .shake { animation: shake .4s ease; }
      .alert-unverified { background: #fff3cd; border: 1px solid #ffca2c; color: #664d03; border-radius: 10px; }
      .alert-not-found  { background: #f8d7da; border: 1px solid #f5c2c7; }
      .resend-form { background: #f8f9fa; border-radius: 10px; padding: 1rem; margin-top: .75rem; }
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

              <h4 class="mb-1 fw-bold">Selamat Datang 👋</h4>
              <p class="mb-4 text-muted" style="font-size:.875rem;">Masukkan akun Anda untuk melanjutkan.</p>

              <!-- Alert Sukses -->
              <?php if (!empty($success)): ?>
                <div class="alert alert-success d-flex align-items-start gap-2 mb-3" role="alert">
                  <i class="bx bx-check-circle mt-1 flex-shrink-0 fs-5"></i>
                  <div><?= htmlspecialchars($success) ?></div>
                </div>
              <?php endif; ?>

              <!-- Resend verification success -->
              <?php if (!empty($resendSuccess)): ?>
                <div class="alert alert-success d-flex align-items-start gap-2 mb-3">
                  <i class="bx bx-mail-send mt-1 flex-shrink-0 fs-5"></i>
                  <div><?= htmlspecialchars($resendSuccess) ?></div>
                </div>
              <?php endif; ?>

              <!-- Error: akun tidak ditemukan -->
              <?php if ($errorType === 'not_found'): ?>
                <div class="alert alert-not-found d-flex align-items-start gap-2 mb-3" role="alert">
                  <i class="bx bx-user-x mt-1 flex-shrink-0 fs-5 text-danger"></i>
                  <div>
                    <?= $error ?>
                    <div class="error-hint">
                      Belum punya akun? <a href="register.php" class="fw-semibold">Daftar sekarang</a>.
                    </div>
                  </div>
                </div>

              <!-- Error: password salah -->
              <?php elseif ($errorType === 'wrong_password'): ?>
                <div class="alert alert-danger d-flex align-items-start gap-2 mb-3 shake" role="alert">
                  <i class="bx bx-lock-open-alt mt-1 flex-shrink-0 fs-5"></i>
                  <div>
                    <?= htmlspecialchars($error) ?>
                    <div class="error-hint text-muted">Periksa huruf besar/kecil pada password Anda.</div>
                  </div>
                </div>

              <!-- Error: belum verifikasi -->
              <?php elseif ($errorType === 'unverified'): ?>
                <div class="alert-unverified p-3 mb-3">
                  <div class="d-flex align-items-start gap-2">
                    <i class="bx bx-envelope-open mt-1 flex-shrink-0 fs-5"></i>
                    <div>
                      <strong>Email belum diverifikasi</strong>
                      <p class="mb-2 mt-1" style="font-size:.84rem;">Cek inbox <strong><?= htmlspecialchars($email ?? '') ?></strong> dan klik link verifikasi. Cek juga folder Spam.</p>
                      <button class="btn btn-sm btn-warning fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#resendForm">
                        <i class="bx bx-mail-send me-1"></i> Kirim Ulang Email
                      </button>
                    </div>
                  </div>
                  <div class="collapse mt-2" id="resendForm">
                    <form method="POST" action="login.php" class="resend-form">
                      <label class="form-label mb-1" style="font-size:.82rem;font-weight:600;">Email akun Anda:</label>
                      <div class="input-group input-group-sm">
                        <input type="email" class="form-control" name="resend_email_addr"
                          value="<?= htmlspecialchars($email ?? '') ?>" required placeholder="Email Anda" />
                        <button type="submit" name="resend_email" value="1" class="btn btn-warning">Kirim</button>
                      </div>
                      <?php if (!empty($resendError)): ?>
                        <small class="text-danger mt-1 d-block"><?= htmlspecialchars($resendError) ?></small>
                      <?php endif; ?>
                    </form>
                  </div>
                </div>

              <!-- Error umum -->
              <?php elseif (!empty($error)): ?>
                <div class="alert alert-danger d-flex align-items-start gap-2 mb-3" role="alert">
                  <i class="bx bx-error-circle mt-1 flex-shrink-0 fs-5"></i>
                  <div><?= htmlspecialchars($error) ?></div>
                </div>
              <?php endif; ?>

              <!-- Login Form -->
              <form id="formLogin" action="login.php" method="POST" novalidate>
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                    <input type="email" class="form-control <?= ($errorType === 'not_found') ? 'is-invalid' : '' ?>"
                      id="email" name="email"
                      placeholder="contoh@email.com"
                      value="<?= htmlspecialchars($email ?? '') ?>"
                      autofocus required />
                  </div>
                </div>

                <div class="mb-4 form-password-toggle">
                  <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label mb-0" for="password">Password</label>
                  </div>
                  <div class="input-group input-group-merge mt-1">
                    <span class="input-group-text"><i class="bx bx-lock-alt"></i></span>
                    <input type="password" id="password" class="form-control <?= ($errorType === 'wrong_password') ? 'is-invalid' : '' ?>"
                      name="password"
                      placeholder="············"
                      required />
                    <span class="input-group-text cursor-pointer" id="toggleLogin">
                      <i class="bx bx-hide" id="toggleLoginIcon"></i>
                    </span>
                  </div>
                </div>

                <button class="btn btn-primary d-grid w-100 mb-3" type="submit" id="btnLogin">
                  <span class="d-flex align-items-center justify-content-center gap-2">
                    <i class="bx bx-log-in"></i> Masuk
                  </span>
                </button>
              </form>

              <p class="text-center mb-0" style="font-size:.875rem;">
                <span class="text-muted">Belum punya akun?</span>
                <a href="register.php" class="fw-semibold ms-1">Daftar sekarang</a>
              </p>

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
      document.getElementById('toggleLogin')?.addEventListener('click', function() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('toggleLoginIcon');
        input.type  = input.type === 'password' ? 'text' : 'password';
        icon.classList.toggle('bx-show');
        icon.classList.toggle('bx-hide');
      });

      // Loading state
      document.getElementById('formLogin')?.addEventListener('submit', function() {
        const btn = document.getElementById('btnLogin');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memverifikasi...';
        btn.disabled = true;
      });
    </script>
  </body>
</html>
