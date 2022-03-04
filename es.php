<?php

namespace NumberToWords\Legacy\Numbers\Words\Locale;

use NumberToWords\Exception\NumberToWordsException;
use NumberToWords\Legacy\Numbers\Words;

class Es extends Words
{
    const LOCALE = 'es';
    const LANGUAGE_NAME = 'Spanish';
    const LANGUAGE_NAME_NATIVE = 'Español';

    private $wordSeparator = ' ';

    private $minus = 'menos';

    private static $exponent = [
        0 => ['', ''],
        3 => ['mil', 'mil'],
        6 => ['millón', 'millones'],
        12 => ['billón', 'billones'],
        18 => ['trilón', 'trillones'],
        24 => ['cuatrillón', 'cuatrillones'],
        30 => ['quintillón', 'quintillones'],
        36 => ['sextillón', 'sextillones'],
        42 => ['septillón', 'septillones'],
        48 => ['octallón', 'octallones'],
        54 => ['nonallón', 'nonallones'],
        60 => ['decallón', 'decallones'],
    ];

    private static $units = [
        'cero',
        'uno',
        'dos',
        'tres',
        'cuatro',
        'cinco',
        'seis',
        'siete',
        'ocho',
        'nueve'
    ];

    private static array $teens = [
        'diez',
        'once',
        'doce',
        'trece',
        'catorce',
        'quince',
        'dieciseis',
        'diecisiete',
        'dieciocho',
        'diecinueve'
    ];

    private static array $tens = [
        '',
        'diez',
        'veinte',
        'treinta',
        'cuarenta',
        'cincuenta',
        'sesenta',
        'setenta',
        'ochenta',
        'noventa'
    ];

    private static array $hundreds = [
        '',
        'ciento',
        'doscientos',
        'trescientos',
        'cuatrocientos',
        'quinientos',
        'seiscientos',
        'setecientos',
        'ochocientos',
        'novecientos'
    ];

    private static $currencyNames = [
        'PEN' => [['sol', 'soles'], ['centavo']],
        'ALL' => [['lek'], ['qindarka']],
        'AUD' => [['dólar australiano', 'dólares australianos'], ['centavo']],
        'ARS' => [['peso'], ['centavo']],
        'BAM' => [['convertible marka'], ['fenig']],
        'BGN' => [['lev'], ['stotinka']],
        'BRL' => [['real', 'reales'], ['centavo']],
        'BYR' => [['rublo bielorruso', 'rublos bielorrusos'], ['kopek', 'kopeks']],
        'CAD' => [['dólar canadiense', 'dólares canadienses'], ['centavo']],
        'CHF' => [['swiss franc'], ['rapp']],
        'CYP' => [['cypriot pound'], ['cent']],
        'CZK' => [['czech koruna'], ['halerz']],
        'CRC' => [['colón', 'colones'], ['centavo']],
        'DZD' => [['dinar', 'dinares'], ['céntimo']],
        'DKK' => [['danish krone'], ['ore']],
        'DOP' => [['peso dominicano', 'pesos dominicanos'], ['centavo', 'centavos']],
        'EEK' => [['kroon'], ['senti']],
        'EUR' => [['euro'], ['centavo']],
        'GBP' => [['libra'], ['peñique']],
        'HKD' => [['dólar de hong kong', 'dólares de hong kong'], ['centavo']],
        'HRK' => [['croatian kuna'], ['lipa']],
        'HUF' => [['forint'], ['filler']],
        'ILS' => [['new sheqel', 'new sheqels'], ['agora', 'agorot']],
        'ISK' => [['icelandic króna'], ['aurar']],
        'JPY' => [['yen', 'yenes'], ['sen']],
        'LTL' => [['litas'], ['cent']],
        'LVL' => [['lat'], ['sentim']],
        'LYD' => [['dinar', 'dinares'], ['céntimo']],
        'MAD' => [['dírham'], ['céntimo']],
        'MKD' => [['denar macedonio', 'denares macedonios'], ['deni']],
        'MRO' => [['ouguiya'], ['khoums']],
        'MTL' => [['lira maltesa'], ['céntimo']],
        'MXN' => [['peso'], ['centavo']],
        'NOK' => [['norwegian krone'], ['oere']],
        'PLN' => [['zloty', 'zlotys'], ['grosz']],
        'ROL' => [['romanian leu'], ['bani']],
        'RUB' => [['rublo ruso', 'rublos rusos'], ['kopek']],
        'SEK' => [['Swedish krona'], ['oere']],
        'SIT' => [['tolar'], ['stotinia']],
        'SKK' => [['slovak koruna'], []],
        'TND' => [['dinar', 'dinares'], ['céntimo']],
        'TRL' => [['lira'], ['kuruþ']],
        'UAH' => [['hryvna'], ['cent']],
        'USD' => [['dólar', 'dólares'], ['centavo']],
        'UYU' => [['peso uruguayo', 'pesos uruguayos'], ['centavo']],
        'VEB' => [['bolívar', 'bolívares'], ['céntimo']],
        'XAF' => [['franco CFA', 'francos CFA'], ['céntimo']],
        'XOF' => [['franco CFA', 'francos CFA'], ['céntimo']],
        'YUM' => [['dinar', 'dinares'], ['para']],
        'ZAR' => [['rand'], ['cent']]
    ];

    /**
     * @param int $number
     * @param int $power
     *
     * @return string
     */
    protected function toWords($number, $power = 0)
    {
        $words = [];

        if ($number === 0) {
            return self::$units[0];
        }

        if ($number < 0) {
            $words[] = $this->minus;
            $number *= -1;
        }

        list($number, $words[]) = $this->needToUsedPower($power, $number);

        $units = $number % 10;
        $tens = (int) ($number / 10) % 10;
        $hundreds = (int) ($number / 100) % 10;
        
        $words[] = $this->getThousands($number);
        $words[] = $this->getHundred($hundreds, $tens, $units);
        $words[] = $this->getTens($tens, $units, $power);
        $words[] = $this->getDigitsOnlyForMultipleOfTen($tens, $units, $power);
        $words[] = $this->getExponent($hundreds, $tens, $units, $power, $number);

        return implode('', array_filter($words, fn ($word) => strlen(trim($word))));
    }

    private function needToUsedPower($power, $number)
    {
        if (! $this->numberIsAboveSixUnits($number)) {
            return [$number, null];
        }

        return [substr($number, -6), $this->highestPower($power, $this->numberWithCorrespondingPower($number))];
    }

    private function numberIsAboveSixUnits($number)
    {
        return (strlen($number) > 6);
    }

    private function numberWithCorrespondingPower($number)
    {
        return preg_replace('/^0+/', '', substr($number, 0, -6));
    }

    private function highestPower($power, $snum)
    {
        return $this->checkHighestPower($power, $snum)
            ? $this->toWords($snum, $power + 6)
            : null;
    }

    private function checkHighestPower($power, $snum)
    {
        return isset(self::$exponent[$power]) && $snum !== '';
    }

    private function getThousands($number)
    {
        $thousands = floor($number / 1000);

        if ($thousands == 1) {
            return $this->wordSeparator . 'mil';
        }

        if ($thousands > 1) {
            return $this->toWords($thousands, 3);
        }
    }

    private function getHundred($hundreds, $tens, $units)
    {   
        if ($hundreds == 1 && $units == 0 && $tens == 0) {
            return $this->wordSeparator . 'cien';
        }
        
        return $this->wordSeparator . self::$hundreds[$hundreds];
    }

    private function getTens($tens, $units, $power)
    {
        if ($tens == 1) {
            return $this->getTeens($units);
        }

        if ($tens == 2 && $units <> 0) {
            return ($power > 0 && $units == 1) 
                ? $this->wordSeparator . 'veintiún' 
                : $this->wordSeparator . 'veinti' . self::$units[$units];
        }

        return $this->wordSeparator . self::$tens[$tens];
    }

    private function getTeens($units)
    {
        return $this->wordSeparator . self::$teens[$units];
    }

    private function getDigitsOnlyForMultipleOfTen($tens, $units, $power)
    {
        if (! ($tens <> 1 && $tens <> 2 && $units > 0)) {
           return;
        }

        if ($tens <> 0) {
            return $this->wordSeparator . 'y ' . self::$units[$units];
        }

        if ($power > 0 && $units == 1) {
            return $this->wordSeparator . 'un';
        }  
        
        return $this->wordSeparator . self::$units[$units];
    }

    private function getExponent($hundreds, $tens, $units, $power, $number)
    {
        if ($this->hasNotExponentKey($power) || !($number <> 0)) {
            return;
        }

        return $this->wordSeparator . $this->exponentName($hundreds, $tens, $units, self::$exponent[$power]);
    }

    private function hasNotExponentKey($power)
    {
        return !($power > 0) 
            && !array_key_exists($power, self::$exponent[$power]) 
            && !is_array(self::$exponent[$power]);
    }

    private function exponentName($hundreds, $tens, $units, $exponent)
    {
        return ($units == 1 && $tens == 0 && $hundreds == 0) ? $exponent[0] : $exponent[1];
    }

    /**
     * @param int $currency
     * @param int $decimal
     * @param null $fraction
     *
     * @return string
     */
    public function toCurrencyWords($currency, $decimal, $fraction = null)
    {
        $currency = strtoupper($currency);

        $this->checkCurrencySupport($currency);

        //change digit "one" to the short version see: https://www.rae.es/dpd/una
        self::$units[1] = 'un';

        $currencyNames = static::$currencyNames[$currency];

        $words = [];

        array_push($words, $this->wordSeparator . trim($this->toWords($decimal)));
        array_push($words, $this->wordSeparator . $this->correspondingCurrency($currencyNames, $this->level($decimal)));
        array_push($words, $this->correspondingfraction($currencyNames, $fraction));

        //Go back digit "one"
        self::$units[1] = 'uno';

        return implode('', $words);
    }

    private function checkCurrencySupport($currency)
    {
        if (!array_key_exists($currency, static::$currencyNames)) {
            throw new NumberToWordsException(
                sprintf('Currency "%s" is not available for "%s" language', $currency, get_class($this))
            );
        }
    }

    private function correspondingCurrency($currencyNames, $level)
    {   
        if (! $level) {
            return $currencyNames[0][0];
        }

        return count($currencyNames[0]) > 1 
            ? $currencyNames[0][$level]
            : $currencyNames[0][0] . 's';
    }

    private function correspondingfraction($currencyNames, $fraction)
    {
        if (is_null($fraction)) {
            return;
        }

        $words = [];

        array_push($words, $this->wordSeparator . 'con');
        array_push($words, $this->wordSeparator . trim($this->toWords($fraction)));

        if (! $this->level($fraction)) {
            array_push($words, $this->wordSeparator . $currencyNames[1][0]);
            return implode('', $words);
        }

        count($currencyNames[1]) > 1 
            ? array_push($words, $this->wordSeparator . $currencyNames[1][$this->level($fraction)])
            : array_push($words, $this->wordSeparator . $currencyNames[1][0] . 's');
   
        return implode('', $words);
    }

    private function level($value)
    {
        return $value == 1 ? 0 : 1;
    }
}
