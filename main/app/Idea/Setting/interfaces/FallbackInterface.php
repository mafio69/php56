<?php namespace Idea\Setting\interfaces;

/**
 * Class FallbackInterface
 * @package Idea\Setting\interfaces
 */
interface FallbackInterface {

    /**
     * @param $key
     * @return mixed
     */
    public function fallbackGet($key);

    /**
     * @param $key
     * @return boolean
     */
    public function fallbackHas($key);

}