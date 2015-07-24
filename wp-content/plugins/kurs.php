<?php
/*
Plugin Name: kursgood
Description: Wtyczka wyświetla sredni kurs walut z ostatniego dnia. Źródło: NBP.pl .
Version: 0.001.
Author: Paweł Łabędzki.
*/
add_action('get_footer', 'test');
function test()
{
    print '<meta type="test" content="Test"/>';


    $ch = curl_init("http://www.nbp.pl/kursy/xml/dir.txt"); //inicjuje sesję
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //zwraca zawartość URL(plik 'dir.txt')...
    $text = curl_exec($ch); //...przekazanie jej do zmiennej text

    $textArray = explode("\n", $text); //'rozbija' zawartosc pliku 'dir.txt'(string) na tablicę

    $textArrayCount = count($textArray); //liczy elementy tablicy

    for ($i = $textArrayCount; $i > 0; $i--) { //'czytanie' tablicy od tyłu
        if (strpos($textArray[$i], 'a') !== false) { //szuka miejsca w którym jest 'a'

            $result = $textArray[$i]; //jeśli jest 'a' w tablicy...
                                      // ..przekazuje miejsce występowania do elementu 'i' tablicy textArray i zapisuje w zmiennej $result
            break; //przerwanie pętli, przejscie do kodu ponizej
        }
    }

    $result = preg_replace('/\s+/', '', $result);//usuwa białe spacje
    //echo "Średni kurs walut z NBP.pl , nazwa pliku: " . $result . ";";
    $xml = simplexml_load_file("http://www.nbp.pl/kursy/xml/" . "$result" . ".xml");//ładuje plik .xml o nazwie rozpoczynającej się na 'a' z katalogu 'kursy/xml/'

    $title = $xml->numer_tabeli;//przesyła numer tabeli do zmiennej title

    $kodyPozycji = array();//tworzy pustą tablice o nazwie 'kodyPozycji'

    $i = 0;//zeruje zmienną 'i'
    foreach ($xml as $key => $element) {
        if ($key == 'pozycja') {
            if ($element->kod_waluty == 'USD' || $element->kod_waluty == 'EUR') {//jeśli wartosc 'kod waluty' jest rowna EUR lub USD...
                array_push($kodyPozycji, $i);//...zapisuje do tablicy kodyPozycji pod indeksem 'i'
            }
            $i++;//zwiększenie(inkrementacja) zmiennej i, pętla wykonuje się dopóki będą znajdowane kolejne pozycje z kodem waluty EUR lub USD
        }
    }

    foreach ($kodyPozycji as $kod) {//czyta z tablicy kodyPozycji
        echo " Waluta: " . $xml->pozycja[$kod]->kod_waluty;//'wyrzucenie' na ekran danych
        echo " Kurs: " . $xml->pozycja[$kod]->kurs_sredni;
    }
}

?>
