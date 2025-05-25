# Dish Patch
Projekt na zaliczenie przedmiotu "Projektowanie serwisów internetowych".

## Do zrobienia:
- [x] Zacząć - DONE
### Design:
- [ ] Wybór kolorystyki
- [ ] Aplikowanie ikon
- [ ] Stylizacja buttonów
- [ ] Stylizacja kart z restauracjami
- [ ] Pinezka na mapie ma wyświetlać godziny otwarcia i adres restauracji
### Features:
- [ ] about.html - opis firmy, przesłanie, misja, kontaktowe info (html-adress), formularz kontaktowy(html-form)
- [ ] user.html - panel z zamówieniami, panel z rezerwacjami, historia zamówień i rezerwacji, ustawienia
- [ ] index.html - logowanie
- [ ] admin.html - panel zarządzania informacjami kontaktowymi, adresem,  wyświetlenie danych kontaktowych usera i informacji o rezerwacji
- [ ] planner.html - panel zarządzania rezerwacjami, dodawanie godzin rezerwacji, dezaktywacja godzin rezerwacji, kalendarzowy look na rezerwacje
- [ ] Baza danych
- [ ] Dodanie linków do glovo/pyszne danych restauracji
- [ ] Sesje w PHP
---
# Budowanie projektu

## 1.Instalacja XAMPP

1. Wejdź na:
```
 https://www.apachefriends.org/index.html.
```
2. Pobierz wersję dla swojego systemu (Windows, Linux, macOS).  
3. Zainstaluj z domyślnymi ustawieniami.  
4. W przypadku konieczności wyłączenia UAC (Kontrola konta użytkownika) w systemie Windows 10:  
- Windows + R  
```
control
```
-W panelu sterowania:  
`Konta użytkowników → Zmień ustawienia funkcji Kontrola konta użytkownika`

## 2.Uruchom Apache and MySQL
1. Otwórz XAMPP Control Panel.  
2. Uruchom `Apache` oraz `MySQL`.  
3. Jeśli uruchomią się poprawnie tło ukaże się w kolorze zielonym.  

## 3.Dodawanie plików projektu
1. W folderze gdzie zainstalowany jest XAMPP odszukaj `htdocs`
2. Sklonuj repozytorium w to miejsce
3. Wejdź na:
```
http://localhost/DishPatch/
```
Zwróć uwagę na to, że możliwe, że trzeba będzie dopasować ścieżkę.
