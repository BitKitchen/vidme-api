<?php

namespace Vidme\Storage;


interface AuthStorageInterface
{
    /**
     * @param array $authData
     * @return bool
     */
    public function store(array $authData);

    /**
     * @return array|bool
     */
    public function read();

    public function clear();
} 