<?php

namespace Francerz\Http;

use ArrayAccess;

class UrlEncodedParams implements ArrayAccess
{
    private $params;

    public function __construct($params = null)
    {
        if (is_array($params)) {
            $this->params = $params;
        } elseif (is_string($params)) {
            parse_str($params, $this->params);
        } else {
            $this->params = array();
        }
    }

    #region ArrayAccess methods
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->params);
    }

    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            return null;
        }
        return $this->params[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->params[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->params[$offset]);
    }
    #endregion

    public function getStringStream() : StringStream
    {
        return new StringStream(http_build_query($this->params));
    }
}