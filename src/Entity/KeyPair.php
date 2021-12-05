<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Genesis\Buffer\Entity;

/**
 * Description of KeyPair
 *
 * @author spawn
 */
class KeyPair
{

    private $private = null;

    private $public = null;

    public function getPrivate()
    {
        return $this->private;
    }

    public function getPublic()
    {
        return $this->public;
    }

    public function setPrivate($private)
    {
        $this->private = $private;
        return $this;
    }

    public function setPublic($public)
    {
        $this->public = $public;
        return $this;
    }
}
