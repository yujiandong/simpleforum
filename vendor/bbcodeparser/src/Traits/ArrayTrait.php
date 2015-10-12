<?php namespace Golonka\BBCode\Traits;

trait ArrayTrait
{

    /**
     * Filters all parsers that you don´t want
     * @param array $parsers An array of all parsers
     * @param array $only Chosen parsers
     * @return array parsers
     */
    private function arrayOnly(array $parsers, $only)
    {
        return array_intersect_key($parsers, array_flip((array) $only));
    }

    /**
     * Removes the parsers that you don´t want
     * @param array $parsers An array of all parsers
     * @param array $except Parsers to exclude
     * @return array parsers
     */
    private function arrayExcept(array $parsers, $except)
    {
        return array_diff_key($parsers, array_flip((array) $except));
    }
}
