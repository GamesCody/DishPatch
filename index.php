
<?php
include 'includes/connect.php';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="icon" href="assets/graphics/images/favicon.png" sizes="48x48" type="image/png" />
  <title>Dish Patch</title>


  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <link rel="stylesheet" href="css/style.css"/>
  <link rel="stylesheet" href="css/map.css"/>
  
  <script src="https://accounts.google.com/gsi/client" async defer></script>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body>

  <header>
    <button id="burger" onclick="openMenu()"><i class="fa-solid fa-burger"></i></button>
    <img id="logo" src="assets/graphics/images/logo.png" alt="logo"> 
  </header>
  <main>
    <!-- Dodane elementy do obsługi mapy/restauracji -->
    <div id="controls">
      <select id="citySelect">
        <option value="">Wybierz miasto</option>
        <option value="warszawa">Warszawa</option>
        <option value="krakow">Kraków</option>
        <option value="czestochowa">Częstochowa</option>
      </select>
    </div>
    <div id="restaurantList"></div>
    <div class="sidebar" id="menu">
      <div class="tytul">Menu</div>
      <button class="closebutton" onclick="closeMenu()"><i class="fa-solid fa-arrow-left"></i></button>
      <button class="button" onclick="openLoginForm()">Zaloguj się</button>
      <button class="button" onclick="openRegisterForm()">Załóż konto</button>
      <button class="button" onclick="openContactInfo()">Kontakt</button>
      <button class="button" onclick="openNewTab()">O nas</button>
    </div>
    <div id="contactCard">
      <form id="contactForm">
        <button class="closebutton" onclick="closeContactInfo()"><i class="fa-solid fa-arrow-left"></i></button>
            <div class="form-group">
                <label for="contactName">Imię i nazwisko <span class="required">*</span></label>
                <input type="text" id="contactName" name="name" required>
            </div>

            <div class="form-group">
                <label for="contactEmail">Email <span class="required">*</span></label>
                <input type="email" id="contactEmail" name="email" required>
            </div>

            <div class="form-group">
                <label for="contactPhone">Telefon</label>
                <input type="tel" id="contactPhone" name="phone" placeholder="+48 123 456 789">
            </div>

            <div class="form-group">
                <label for="subject">Temat <span class="required">*</span></label>
                <select id="subject" name="subject" required>
                    <option value="">Wybierz temat</option>
                    <option value="ogolny">Ogólne pytanie</option>
                    <option value="oferta">Zapytanie o ofertę</option>
                    <option value="wspolpraca">Współpraca</option>
                    <option value="reklamacja">Reklamacja</option>
                    <option value="inne">Inne</option>
                </select>
            </div>

            <div class="form-group">
                <label for="message">Wiadomość <span class="required">*</span></label>
                <textarea id="message" name="message" placeholder="Napisz swoją wiadomość..." required></textarea>
            </div>

            <button type="submit" class="submit-btn">Wyślij wiadomość</button>
        </form>

    </div>
    <div class="registerForm" id="registerForm">
              <form id="registrationForm">
                <!-- Wewnątrz <form id="registrationForm"> ... -->
<div class="g-recaptcha" data-sitekey="6LcewVorAAAAALK96WoMvINMF6sa__WlW5kybbLJ"></div>
                <button class="closebutton" onclick="closeRegisterForm()"><i class="fa-solid fa-arrow-left"></i></button>
            <div class="input-group">
                <div class="form-group">
                    <label for="registerFirstName">Imię <span class="required">*</span></label>
                    <input type="text" id="registerFirstName" name="firstName" required>
                </div>

                <div class="form-group">
                    <label for="registerLastName">Nazwisko <span class="required">*</span></label>
                    <input type="text" id="registerLastName" name="lastName" required>
                </div>
            </div>

            <div class="form-group">
                <label for="registerPhone">Numer telefonu <span class="required">*</span></label>
                <input type="tel" id="registerPhone" name="phone" placeholder="+48 123 456 789" required>
            </div>

            <div class="form-group">
                <label for="registerEmail">Adres email <span class="required">*</span></label>
                <input type="email" id="registerEmail" name="email" placeholder="twoj@email.com" required>
            </div>

            <div class="form-group">
                <label for="registerAddress">Adres zamieszkania <span class="required">*</span></label>
                <input type="text" id="registerAddress" name="address" placeholder="ul. Przykładowa 123, 00-000 Miasto" required>
            </div>

            <div class="form-group">
                <label for="registerPassword">Hasło <span class="required">*</span></label>
                <div class="show-password">
                    <input type="password" id="registerPassword" name="password" placeholder="Minimum 6 znaków" required>
                    <button type="button" class="show-password-btn" onclick="togglePassword('registerPassword', this)">👁️</button>
                </div>
            </div>

            <div class="form-group">
                <label for="registerConfirmPassword">Potwierdź hasło <span class="required">*</span></label>
                <div class="show-password">
                    <input type="password" id="registerConfirmPassword" name="confirmPassword" placeholder="Powtórz hasło" required>
                    <button type="button" class="show-password-btn" onclick="togglePassword('registerConfirmPassword', this)">👁️</button>
                </div>
            </div>

            <button type="submit" class="register-btn">Zarejestruj się</button>
        </form>

        <div class="form-footer">
            Masz już konto? <a href="#">Zaloguj się</a>
        </div>


    </div>
    <div class="loginForm" id="loginForm">
      <form id="loginForm">
        <!-- Wewnątrz <form id="registrationForm"> ... -->
<div class="g-recaptcha" data-sitekey="6LcewVorAAAAALK96WoMvINMF6sa__WlW5kybbLJ"></div>
        <button class="closebutton" onclick="closeLoginForm()"><i class="fa-solid fa-arrow-left"></i></button>
            <div class="form-group">
                <label for="loginEmail">Email</label>
                <input type="email" id="loginEmail" name="email" placeholder="twoj@email.com" required>
            </div>

            <div class="form-group">
                <label for="loginPassword">Hasło</label>
                <div class="show-password">
                    <input type="password" id="loginPassword" name="password" placeholder="Twoje hasło" required>
                    <button type="button" class="show-password-btn" onclick="togglePassword('loginPassword', this)">👁️</button>
                </div>
            </div>

            <div style="text-align:center; margin: 20px 0;">
              <div id="g_id_onload"
                   data-client_id="202816577841-t9pfnqnm4gqmoup5i4h1ler2hagepops.apps.googleusercontent.com"
                   data-context="signin"
                   data-ux_mode="popup"
                   data-callback="handleGoogleSignIn"
                   data-auto_prompt="false">
              </div>
              <div class="g_id_signin"
                   data-type="standard"
                   data-shape="rectangular"
                   data-theme="outline"
                   data-text="sign_in_with"
                   data-size="large"
                   data-logo_alignment="left">
              </div>
            </div>

            <div class="form-options">
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Zapamiętaj mnie</label>
                </div>
                <a href="#" class="forgot-password">Zapomniałeś hasła?</a>
            </div>

            <button type="submit" class="login-btn">Zaloguj się</button>
        </form>

        <div class="form-footer">
            Nie masz jeszcze konta? <a href="#">Zarejestruj się</a>
        </div>
    </div>
  </main>

  <footer>
  <div>&copy; DishPatch 2025</div>
  </footer>

  <div id="map"></div>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="js/script.js"></script>

  <script>
    function handleGoogleSignIn(response) {
      // Przykład: wyślij token do backendu AJAXem
      // fetch('/api/auth.php', { method: 'POST', body: ... })
      // alert('Zalogowano przez Google! (token przesłany do backendu)');
      window.location.href = 'subsites/user.php';
    }
  </script>


</body>
</html>

