<?php

namespace Vidme\Storage;

/**
 * Stores auth info to a file.
 */
class AuthStorage implements AuthStorageInterface
{
    /**
     * @var string
     */
    protected $storageFile;

    public function __construct($storageFile)
    {
        $this->storageFile = $storageFile;
    }

    /**
     * Store authentication data to file.
     *
     * @param array $authData
     * @return bool
     */
    public function store(array $authData)
    {
        return false !== file_put_contents($this->storageFile, '<?php return '. var_export($authData, true) .';');
    }

    /**
     * Read authentication data from file.
     *
     * @return array|bool
     */
    public function read()
    {
        if (!$this->storageFile || !is_file($this->storageFile)) {
            return false;
        }

        $storedAuthData = include $this->storageFile;

        if (!is_array($storedAuthData)) {
            return false;
        }

        return $storedAuthData;
    }

    /**
     * Remove stored authentication data.
     */
    public function clear()
    {
        unlink($this->storageFile);
    }
}