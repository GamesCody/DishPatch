-- Najpierw dodaj przykładowych restauratorów
INSERT INTO restaurants (email, password) VALUES
('czest1@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C'),
('czest2@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C'),
('czest3@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C'),
('krakow1@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C'),
('krakow2@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C'),
('krakow3@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C'),
('warszawa1@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C'),
('warszawa2@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C'),
('warszawa3@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C'),
('wroclaw1@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C'),
('wroclaw2@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C'),
('wroclaw3@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C'),
('gdansk1@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C'),
('gdansk2@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C'),
('gdansk3@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C'),
('arczi@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C'),
('thanglong@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C'),
('mcdonalds@foodapp.pl', '$2y$10$iFugm8CYpb9zUKRxtllZTe0v1.GauQy5Pe71FRbtHBq9qaAdTEA/C');

-- Następnie dodaj lokalizacje restauracji (restaurant_id zgodnie z kolejnością powyżej)
INSERT INTO locations (restaurant_id, restaurant_name, city, address, opening_hours, contact_email, phone, lat, lng)
VALUES
(1, 'Pierogarnia Stary Młyn', 'Częstochowa', 'ul. NMP 24', '10:00-22:00', 'kontakt@starymlyn.pl', '+48343211234', 50.81182, 19.12031),
(2, 'Restauracja Jurajska', 'Częstochowa', 'ul. Jurajska 5', '11:00-23:00', 'info@jurajska.pl', '+48343211235', 50.81300, 19.12090),
(3, 'Bar Mleczny Częstochowa', 'Częstochowa', 'ul. Piłsudskiego 10', '08:00-20:00', 'bar@czestochowa.pl', '+48343211236', 50.81000, 19.12200),
(4, 'Restauracja Wawel', 'Kraków', 'ul. Wawelska 1', '09:00-23:00', 'kontakt@wawel.pl', '+48123456789', 50.06143, 19.93658),
(5, 'Pierogarnia Krakowska', 'Kraków', 'ul. Grodzka 15', '10:00-22:00', 'info@krakowska.pl', '+48123456780', 50.06200, 19.93700),
(6, 'Bar Mleczny Kraków', 'Kraków', 'ul. Dietla 50', '07:00-19:00', 'bar@krakow.pl', '+48123456781', 50.06000, 19.94000),
(7, 'Restauracja Panorama', 'Warszawa', 'ul. Marszałkowska 100', '11:00-23:00', 'kontakt@panorama.pl', '+48221234567', 52.22977, 21.01178),
(8, 'Pierogarnia Warszawska', 'Warszawa', 'ul. Nowy Świat 20', '10:00-22:00', 'info@warszawska.pl', '+48221234568', 52.23000, 21.01200),
(9, 'Bar Mleczny Warszawa', 'Warszawa', 'ul. Świętokrzyska 30', '07:00-19:00', 'bar@warszawa.pl', '+48221234569', 52.22800, 21.01000),
(10, 'Karczma Polska', 'Wrocław', 'ul. Rynek 1', '10:00-22:00', 'kontakt@karczma.pl', '+48713456789', 51.10789, 17.03854),
(11, 'Restauracja Odra', 'Wrocław', 'ul. Odrzańska 2', '11:00-23:00', 'info@odra.pl', '+48713456780', 51.10850, 17.03900),
(12, 'Bar Mleczny Wrocław', 'Wrocław', 'ul. Piłsudskiego 20', '07:00-19:00', 'bar@wroclaw.pl', '+48713456781', 51.10700, 17.04000),
(13, 'Bar Mleczny Neptun', 'Gdańsk', 'ul. Długa 50', '08:00-20:00', 'kontakt@neptun.pl', '+48583456789', 54.35205, 18.64637),
(14, 'Restauracja Motława', 'Gdańsk', 'ul. Motławska 10', '11:00-23:00', 'info@motlawa.pl', '+48583456780', 54.35300, 18.64700),
(15, 'Pierogarnia Gdańska', 'Gdańsk', 'ul. Piwna 5', '10:00-22:00', 'pierogi@gdansk.pl', '+48583456781', 54.35100, 18.64800),
(16, 'Pizzeria Restauracja Arczi', 'Częstochowa', 'ul. Arczi 1', '10:00-22:00', 'kontakt@arczi.pl', '+48343211237', 50.81250, 19.12100),
(17, 'Thang Long', 'Częstochowa', 'ul. Wietnamska 2', '11:00-23:00', 'info@thanglong.pl', '+48343211238', 50.81350, 19.12250),
(18, 'McDonald’s', 'Częstochowa', 'al. Wojska Polskiego 207', '07:00-23:00', 'kontakt@mcdonalds.pl', '+48343211239', 50.81400, 19.12300);

-- Przykładowe restauracje z linkiem do Glovo
UPDATE locations SET order_url = 'https://glovoapp.com/pl/czestochowa/pierogarnia-stary-mlyn-cze/' WHERE restaurant_name = 'Pierogarnia Stary Młyn' AND city = 'Częstochowa';
UPDATE locations SET order_url = 'https://glovoapp.com/pl/krakow/restauracja-wawel-krk/' WHERE restaurant_name = 'Restauracja Wawel' AND city = 'Kraków';
UPDATE locations SET order_url = 'https://glovoapp.com/pl/warszawa/restauracja-panorama-waw/' WHERE restaurant_name = 'Restauracja Panorama' AND city = 'Warszawa';
UPDATE locations SET order_url = 'https://glovoapp.com/pl/wroclaw/karczma-polska-wro/' WHERE restaurant_name = 'Karczma Polska' AND city = 'Wrocław';
UPDATE locations SET order_url = 'https://glovoapp.com/pl/gdansk/bar-mleczny-neptun-gdn/' WHERE restaurant_name = 'Bar Mleczny Neptun' AND city = 'Gdańsk';
UPDATE locations SET order_url = 'https://glovoapp.com/pl/en/czestochowa/pizzeria-restauracja-arczi-czw/' WHERE restaurant_name = 'Pizzeria Restauracja Arczi' AND city = 'Częstochowa';
UPDATE locations SET order_url = 'https://glovoapp.com/pl/en/czestochowa/thang-long-czw/' WHERE restaurant_name = 'Thang Long' AND city = 'Częstochowa';
UPDATE locations SET order_url = 'https://glovoapp.com/pl/en/czestochowa/mcdonald-s-czw/' WHERE restaurant_name = 'McDonald’s' AND city = 'Częstochowa';
