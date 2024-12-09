﻿<?php

// Klucz autoryzacyjny dla polaczen z API
// znaki alfanumeryczne (a-z, A-Z, 0-9)
// zalecana dlugosc 16-32 znaki
define('_FL_AUTH_KEY','M55GLAWJF6MFX5C2V6F39YLKX85SUVA1');

// Id zamowienia, od ktorego zaczac pobieranie
define('_FL_PS_ORDER_START_ID',1);

// Opoznienie dla pobieranych zamowien (w minutach)
// przydatne przy obsłudze płatnosci elektronicznych
define('_FL_PS_ORDER_DELAY',0);

// Prefiks dodawany do numeru zamowienia (przed numerem)
define('_FL_ORDER_NUMBER_PREFIX','');

// Sufiks dodawany do numeru zamowienia (za numerem)
define('_FL_ORDER_NUMBER_SUFFIX','');

// format numeru zamowienia, dostęne od 1.5
// 0 - format domyslny pełny numer zamowienia i id w nawiasie, np. IPXCTKQYA (43)
// 1 - tylko id zamowienia
// 2 - tylko pełny nymer zamowienia
define('_FL_ORDER_NUMBER_FORMAT',1);

// czy dodawac do numeru zamowienia ID sklepu
// 0 - nie, 1 - tak
define('_FL_ADD_SHOP_ID_TO_ORDER_NUMBER',0);

// nazwa pola w bazie, w ktorym jest zapisywany NIP klienta (zaleznie od posiadanej modyfikacji sklepu)
// obsługiwane tabele customer, address, order, nalezy podac nazwe tabeli bez prefixu i nazwe kolumny po kropce
// np. order.nip , customer.nip lub address.nip
define('_FL_CUSTOMER_TAX_NUMBER_FIELD','address.dni');

// kontrola unikalnosci statusu zamowienia
// 0 - wyłaczona
// dla 1 - nie ustawi statusu, jesli wystapił on przynajmniej raz w historii 
define('_FL_ORDER_STATUS_CONTROL',0);

// lista id statusow (oddzielana przecinkami) dla ktorych program nie zmieni statusu zamowienia 
define('_FL_ORDER_STATUS_SKIP_UPDATE_LIST','');

// metoda pobierania zamowien
// 0 - domyslnie, wszystkie zlozone zamowienia, z uwzglednieniem parametru _FL_ORDER_DELAY
// 1 - tylko ze statusami podanymi w _FL_ORDER_RETRIEVE_STATUS_LIST
define('_FL_ORDER_GET_METHOD',0);

// lista ID statusow zamowien, z ktorymi program moze pobrac zamowienia
// przy ustawieniu _FL_ORDER_GET_METHOD = 1
// lista ID oddzielanych przecinkami np. '1, 2, 3'
// jesli pusta, pobrane zostana wszystkie statusy
define('_FL_ORDER_GET_STATUS_LIST','');

// Opoznienie dla pobieranych zamowien (w minutach)
// przy ustawieniu _FL_ORDER_GET_METHOD = 1
// przydatne przy obsłudze płatnosci elektronicznych
define('_FL_ORDER_GET_DELAY',0);

// umozliwia wprowadzenie korekty daty (timestamp) przy pobieraniu listy zamowien
// definicja jako liczba calkowita dodatnia, 0 - mechanizm nieaktywny
// przydatne np. przy modulach allegro, ktore zapisuja zamowienia z data transakcji a nie z data utworzenia zamowienia w sklepie
// modyfikuje filtr daty zamowienia o "x" minut wstecz
// nie uzywac rownoczesnie z _FL_ORDER_GET_DELAY
define('_FL_ORDER_GET_TIMESTAMP_OFFSET',0);

// okresla czy program bedzie pobieral takze produkty nieaktywne przy imporcie do subiekta lub uzgadnianiu symboli
// 0 - wylaczone, pobiera tylko aktywne
// 1 - wlaczone
define('_FL_GET_INACTIVE_PRODUCTS',0);

// okresla czy import produktow bedzie przeprowadzony tylko w kontekscie domyslnego sklepu
// 0 - wylaczone
// 1 - wlaczone
define('_FL_IMPORT_PRODUCT_FROM_DEFAULT_SHOP',0);

// okresla, czy program importujac produkty ze sklepu do systemu bedzie pobieral cene regularna czy uwzglednial takze promocje
// 0 - wylaczone, pobiera cene regularna
// 1 - wlaczone, sprawdza czy istnieje cena promocyjna (zdefiniowana jako (wszystkie sklepy), wszystkie kraje, wszystkie waluty, wszystkie grupy, bez ograniczen czasowych)
define('_FL_IMPORT_PRODUCT_WITH_SPECIFIC_PRICE',0);

// okresla sposob obliczania cen na dokumencie w programie magazynowym (od netto lub od brutto)
// 0 - sposob obliczania cen bedzie zgodny z rodzajem wyswietlanych cen dla klienta (netto/brutto)
// 1 - zamowienia beda obliczane od netto
// 2 - zamowienia beda obliczane od brutto
define('_FL_ORDER_PRICE_CALCULATION_METHOD',0);

// automatyczne deaktywowanie produktow, ktorych stan magazynowy jest aktualizowany na 0
// 0 - wylaczone
// 1 - wlaczone
define('_FL_HIDE_OUT_OF_STOCK_PRODUCTS',0);

// automatyczne aktywowanie produktow, ktorych stan magazynowy jest aktualizowany > 0
// 0 - wylaczone
// 1 - wlaczone
define('_FL_SHOW_ON_STOCK_PRODUCTS',0);


// sposob zapisywania cen na pozycjach zamowieniach przy stosowaniu rabatow dla grup klientow
// 0 - zapisuj cene po rabacie grupowym
// 1 - zapisuj cene przed rabatem + rabat (rabaty sumowane)
define('_FL_CUSTOMER_ORDER_PRICE_WITH_DISCOUNT',0);

// sposob obliczania rabatow z grup klientow na zamowieniu
// 0 - (domyslnie) rabat pomniejsza cene wyjsciowa pozycji
// 1 - rabat pomniejsza cene koncowa (wystepuje w niektorych wersjach PrestaShop)
define('_FL_ORDER_CUSTOMER_GROUP_REDUCTION_CALCULATION',0);

// sposob traktowania zniżek (kuponów) na zamówieniach
// 0 - informacja o znizkach zapisana w uwagach zamówenia, bez przeliczania cen
// 1 - ceny zamówionych produktów są przeliczane wg. zniżek, wartość zamówienia uwzględnia zniżki
define('_FL_ORDER_SUBSTRACT_DISCOUNTS',1);

//okresla sposob zapisywania cen na pozycjiach zamowien
// 0 - cena jest cena po rabatach
// 1 - cena jest cena wyjsciową + procentowy rabat
define('_FL_ORDER_SHOW_ITEM_DISCOUNT',0);


// Id pracownika (personelu) powiazanego z operacjami wykonywanymi przez API
define('_FL_PS_EMPLOYEE_ID',1);



// ustawiaj kategorie strona główna (home) jako domyslna, jeśli produkt sie w niej znajduje
// 0 - opcja wylaczona
// 1 - opcja wlaczona
define('_FL_PRODUCT_CATEGORY_SET_HOME_AS_DEFAULT',1);

// sposob ustawiania domyslnej kategorii produktu
// 0 - ustawiaj pierwsza kategorię w jakiej znajdzie się produkt
// 1- ustawiaj pierwsza kategorie najnizszego poziomu, w jakiej znajdzie sie produkt
define('_FL_PRODUCT_CATEGORY_DEFAULT_SET_METHOD',1);

// zachowuje kategorie domyslna produktu, jesli po aktualizacji produkt nadal jest do niej przypisany
define('_FL_PRODUCT_CATEGORY_PRESERVE_DEFAULT',0);


// Szablon pola uwagi do zamowienia
// lista dostepnych elemntów
//    {ORDER-PAYMENT-METHOD} - wybrany sposob płatnosci oraz dodatkowe informacje dotyczace platnosci
//    {ORDER-PAYMENT-METHOD-NAME} - wybrany sposob płatnosci (tylko nazwa)
//    {ORDER-DISCOUNT-INFO}  - informacje o ew. znizkach
//    {ORDER-DELIVERY-FORM}  - wybrany sposób dostawy zamowienia
//    {ORDER-DELIVERY-ADDRESS} - adres dostawy (uzupelniane zawsze)
//    {ORDER-DELIVERY-ADDRESS-IF-DIFFERENT} - adres dostawy (uzupelniane jesli adres dostawy jest inny niz dane platnika))
//    {ORDER-DELIVERY-PHONE} - telefon dla adresu dostawy (z danych platnika, jesli jest tylko jeden adres)
//    {ORDER-DELIVERY-EMAIL} - adres email 
//    {ORDER-CUSTOMER-COMMENTS} - komentarz do zamowienia przeslany przez klienta
//    {ORDER-SYSTEM-NOTES} - ew komentarze do zamowienia wygenerowane przez program
//    {ORDER-SHOP-NAME} - nazwa sklepu w ktorym zlozono zamowienie
//    {ORDER-DELIVERY-PERSON} - imię i nazwisko osoby wg adresu dostawy
//    {ORDER-ID} - ID zamowienia
//    {ORDER-NUMBER} - numer zamowienia
//    {ORDER-CART-ID} - id koszyka zakupow

define('_FL_ORDER_COMMENTS_TEMPLATE',
'{ORDER-PAYMENT-METHOD}
{ORDER-DISCOUNT-INFO}
{ORDER-DELIVERY-FORM}
{ORDER-CUSTOMER-COMMENTS}
');

// format adresów klienta
// 0 - domyslnie, adres laczony z pol adres i adrescd
// 1 - adres tylko z pola adres(1)
// 2 - adres tylko z pola adres cd(2)
define('ORDER_ADDRESS_FORMAT',0);

// USTAWIENIA DLA PRESTASHOP 1.5.x, 1.6.x i 1.7.x
// obsluga multistore

// ID domyslnego sklepu
define('_FL_DEFAULT_SHOP_ID',1);

// czy dodawac produktu z przypisaniem do wszystkich sklepow
// 0 - tylko do sklepu domyslnego
define('_FL_ADD_PRODUCTS_TO_EVERY_SHOP',1);

// czy aktualizowac dane produktow i ceny dla wszystkich sklepow
// 0 - tylko do sklepu domyslnego
define('_FL_UPDATE_PRODUCTS_IN_EVERY_SHOP',1);

// czy aktualizowac stan magazynowy (dostepnosc) dla wszystkich sklepow
// 0 - tylko do sklepu domyslnego
define('_FL_UPDATE_STOCK_IN_EVERY_SHOP',1);


// laczenie zdjec wariantow produktow
// 0 - opcja wylaczona, kazdy wariant ma zdjecia na podstawie zdjec przypisanych do towaru w programie magazynowym
// 1 - program laczy zdjecia wariantow na podstawie sumy kontrolnej pliku (to samo zdjecie przypisane do wielu towarow - wariantow w programie magazynowym zostanie dodane raz i powiazane z wariantami)
define('_FL_PRODUCT_IMAGE_ALLOW_LINKING',1);

//
//  USTAWIENIA SPECYFICZNE DLA PrestaShop 1.4.x
//
// Historia rotacji stanu magazynowego
// Czy zapisywac zmiany stanu magazynowego w historii rotacji magazynu
// 1 - zapisuj, 0 - nie zapisuj
// uwaga, stosowac od wersji 1.4
define('_FL_PS_STOCK_TRACK',0);

// Definicja powodow rotacji magazynu
// Zakładka Katalog -> Magazyn -> Powod rotacji magazynu
// mozna utworzyc nowe lub wykorzystac istniejace
// Id powodu rotacji magazynu dla zmian na plus
define('_FL_PS_STOCK_TRACK_PLUS_ID',1);
// Id powodu rotacji magazynu dla zmian na minus
define('_FL_PS_STOCK_TRACK_MINUS_ID',2);


//
//  USTAWIENIA SPECYFICZNE DLA PrestaShop 1.3.x i PrestaShop 1.2.x
//
// domyslna stawka VAT dla kuriera
define('_FL_PS_DEFAULT_TAX_RATE',23);

