<?php

namespace App\Reusables;


class JapaneseYearHandler
{
    // All the japanese years
    private static $japanese_years = [
        'M' => [
            30      =>      1897,
            31      =>      1898,
            32      =>      1899,
            33      =>      1900,
            34      =>      1901,
            35      =>      1902,
            36      =>      1903,
            37      =>      1904,
            38      =>      1905,
            39      =>      1906,
            40      =>      1907,
            41      =>      1908,
            42      =>      1909,
            43      =>      1910,
            44      =>      1911,
            45      =>      1912,
        ],
        'T' => [
            1         =>      1912,
            2         =>      1913,
            3         =>      1914,
            4         =>      1915,
            5         =>      1916,
            6         =>      1917,
            7         =>      1918,
            8         =>      1919,
            9         =>      1920,
            10        =>      1921,
            11        =>      1922,
            12        =>      1923,
            13        =>      1924,
            14        =>      1925,
            15        =>      1926,
        ],
        'S' => [
            1         =>      1926,
            2         =>      1927,
            3         =>      1928,
            4         =>      1929,
            5         =>      1930,
            6         =>      1931,
            7         =>      1932,
            8         =>      1933,
            9         =>      1934,
            10        =>      1935,
            11        =>      1936,
            12        =>      1937,
            13        =>      1938,
            14        =>      1939,
            15        =>      1940,
            16        =>      1941,
            17        =>      1942,
            18        =>      1943,
            19        =>      1944,
            20        =>      1945,
            21        =>      1946,
            22        =>      1947,
            23        =>      1948,
            24        =>      1949,
            25        =>      1950,
            26        =>      1951,
            27        =>      1952,
            28        =>      1953,
            29        =>      1954,
            30        =>      1955,
            31        =>      1956,
            32        =>      1957,
            33        =>      1958,
            34        =>      1959,
            35        =>      1960,
            36        =>      1961,
            37        =>      1962,
            38        =>      1963,
            39        =>      1964,
            40        =>      1965,
            41        =>      1966,
            42        =>      1967,
            43        =>      1968,
            44        =>      1969,
            45        =>      1970,
            46        =>      1971,
            47        =>      1972,
            48        =>      1973,
            49        =>      1974,
            50        =>      1975,
            51        =>      1976,
            52        =>      1977,
            53        =>      1978,
            54        =>      1979,
            55        =>      1980,
            56        =>      1981,
            57        =>      1982,
            58        =>      1983,
            59        =>      1984,
            60        =>      1985,
            61        =>      1986,
            62        =>      1987,
            63        =>      1988,
            64        =>      1989,
        ],
        'H' => [
            1         =>      1989,
            2         =>      1990,
            3         =>      1991,
            4         =>      1992,
            5         =>      1993,
            6         =>      1994,
            7         =>      1995,
            8         =>      1996,
            9         =>      1997,
            10        =>      1998,
            11        =>      1999,
            12        =>      2000,
            13        =>      2001,
            14        =>      2002,
            15        =>      2003,
            16        =>      2004,
            17        =>      2005,
            18        =>      2006,
            19        =>      2007,
            20        =>      2008,
            21        =>      2009,
            22        =>      2010,
            23        =>      2011,
            24        =>      2012,
            25        =>      2013,
            26        =>      2014,
            27        =>      2015,
            28        =>      2016,
            29        =>      2017,
            30        =>      2018,
            31        =>      2019,
        ]
    ];

    /**
     * Check if the string is a valid Japanese year expression
     *
     * @param string $string    The Japanese year expression
     * @return boolean
     */
    public static function isValidJapaneseYear($string) {
        return array_key_exists(substr($string, 0, 1), self::$japanese_years) && array_key_exists(intval(substr($string, 1)), self::$japanese_years[substr($string, 0, 1)]);
    }

    /**
     * Return the coresponding year for that japanese year expression
     *
     * @param string $string    The Japanese year expression
     * @return int              The real year
     */
    public static function toNormalYear($string) {
        if (self::isValidJapaneseYear($string)) {
            $string = self::$japanese_years[substr($string, 0, 1)][intval(substr($string, 1))];
        }
        return intval($string);
    }
}