<?php
/**
 * Webstackar - Expert Magento & Développement PHP
 *
 * @author Harouna MADI <harouna@webstackar.fr>
 * @link https://webstackar.fr
 * @copyright Copyright (c) 2023 Webstackar Nantes
 */

namespace Webstackar\StrapiClientBundle\Response;

class Entry implements \ArrayAccess
{

    protected array $_data = [];

    /**
     * Setter/Getter underscore transformation cache
     */
    protected static array $_underscoreCache = [];


    public function __construct(private readonly array $data = []){
        $this->_data = $this->data['attributes'] ?? [];
    }

    public function getId(): ?int
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Overwrite data in the object.
     *
     * The $key parameter can be string or array.
     * If $key is string, the attribute value will be overwritten by $value
     * If $key is an array, it will overwrite all the data in the object.e
     */
    public function setData(string|array $key, mixed $value = null): static
    {
        if ($key === (array)$key) {
            $this->_data = $key;
        } else {
            $this->_data[$key] = $value;
        }
        return $this;
    }

    /**
     * Object data getter
     */
    public function getData(?string $key = null): mixed
    {
        if ('' === $key) {
            return $this->_data;
        }
        /* process a/b/c key as ['a']['b']['c'] */
        if ($key !== null && str_contains($key, '/')) {
            $data = $this->getDataByPath($key);
        } elseif ($key !== null) {
            $data = $this->_data[$key] ?? null;
        } else {
            $data = $this->_data;
        }
        return $data;
    }

    /**
     * Get object data by path
     *
     * Method consider the path as chain of keys: a/b/c => ['a']['b']['c']
     */
    public function getDataByPath(string $path): mixed
    {
        $keys = explode('/', (string)$path);
        $data = $this->_data;
        foreach ($keys as $key) {
            if ((array)$data === $data && isset($data[$key])) {
                $data = $data[$key];
            } elseif ($data instanceof Entry) {
                $data = $this->_data[$key] ?? null;
            } else {
                return null;
            }
        }
        return $data;
    }

    /**
     * Unset data from the object.
     */
    public function unsetData(null|string|array $key = null): static
    {
        if ($key === null) {
            $this->setData([]);
        } elseif (is_string($key)) {
            if (isset($this->_data[$key]) || array_key_exists($key, $this->_data)) {
                unset($this->_data[$key]);
            }
        } elseif ($key === (array)$key) {
            foreach ($key as $element) {
                $this->unsetData($element);
            }
        }
        return $this;
    }

    /**
     * Set/Get attribute wrapper
     * @throws \Exception
     */
    public function __call(string $method, array $args)
    {
        switch (substr($method, 0, 3)) {
            case 'get':
                $key = $this->underscore(substr($method, 3));
                return $this->getData($key);
            case 'set':
                $key = $this->underscore(substr($method, 3));
                $value = $args[0] ?? null;
                return $this->setData($key, $value);
            case 'uns':
                $key = $this->underscore(substr($method, 3));
                return $this->unsetData($key);
            case 'has':
                $key = $this->underscore(substr($method, 3));
                return isset($this->data[$key]);
        }
        throw new \Exception('Invalid method');
    }

    /**
     * Converts field names for setters and getters
     *
     * $this->setMyField($value) === $this->setData('my_field', $value)
     * Uses cache to eliminate unnecessary preg_replace
     */
    protected function underscore(string $name): string
    {
        if (isset(self::$_underscoreCache[$name])) {
            return self::$_underscoreCache[$name];
        }
        $result = strtolower(trim(preg_replace('/([A-Z]|[0-9]+)/', "_$1", $name), '_'));
        self::$_underscoreCache[$name] = $result;
        return $result;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->_data[$offset]) || array_key_exists($offset, $this->_data);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->_data[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->_data[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->_data[$offset]);
    }
}