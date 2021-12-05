<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Genesis\Buffer;

use StephenHill\Base58;
use Genesis\Buffer\Exception\WrongResultValueException;
use Genesis\Buffer\Entity\KeyPair;

/**
 * Description of Buffer
 *
 * @author spawn
 */
class Buffer
{

    /**
     * @var String
     */
    private $from = null;


    public function __construct($from = null, $type = null)
    {
        if ($from) {
            $this->from($from, $type);
        }
    }

    public static function isfrom($from, $type = null)
    {
        return new Buffer($from, $type);
    }

    public static function isBuffer($data)
    {
        if ($data instanceof Buffer) {
            return true;
        } else {
            return false;
        }
    }

    public function from($from, $type = null)
    {
        switch ($type) {
            case 'base64':
                $this->from = base64_decode($from);
                break;
            case 'base64s':
                $this->from = base64_decode($from, true);
                break;
            case 'base58':
                $this->from = (new Base58())->decode($from);
                break;
            case 'nulpad':
                $this->from = unpack('a*', $from);
                break;
            case 'spacepad':
                $this->from = unpack('A*', $from);
                break;
            case 'hexlow':
                $this->from = unpack('h*', $from);
                break;
            case 'hex':
                $this->from = unpack('H*', $from);
                break;
            case 'singned-char':
                $this->from = unpack('c*', $from);
                break;
            case 'raw':
            default:
                $this->from = $from;
        }
        return $this;
    }

    public function toString($type = null)
    {
        switch ($type) :
            default:
                return mb_convert_encoding($this->from, 'UTF-8');
        endswitch;
    }

    public function toKeypair($type = null)
    {
        $keys = $this->toEd25519();

        switch ($type) {
            case 'base58_ed25519':
                return (new KeyPair)
                    ->setPublic($this->base58($keys['publicKey']))
                    ->setPrivate($this->base58($keys['secretKey']));
            default:
                return (new KeyPair)
                    ->setPublic($keys['publicKey'])
                    ->setPrivate($keys['secretKey']);
        }
    }

    private function base58($data)
    {
        $base58 = new Base58();
        return $base58->encode($data);
    }

    /**
     * @private
     * Ed25519 keypair in base58 (as BigchainDB expects base58 keys)
     * @type {Object}
     * @param {Buffer} [seed] A seed that will be used as a key derivation function
     * @property {string} publicKey
     * @property {string} privateKey
     */
    private function toEd25519()
    {
        if (! $this->from) {
            throw new \Exception("To value to translate from");
        }

        $keyPair = sodium_crypto_sign_seed_keypair($this->from);

        if (count($keyPair) > 1) {
            throw new WrongResultValueException(
                sprintf(
                    'Return value from Unpack returned wrong value; expected'
                    . ' array of size 1 but got array of size %s',
                    count($keyPair)
                )
            );
        }

        return [
            'publicKey' => substr($keyPair, 0, ((count($keyPair) / 3) * 2)),
            'secretKey' => substr($keyPair, ((count($keyPair) / 3) * 2)),
        ];
    }
}
