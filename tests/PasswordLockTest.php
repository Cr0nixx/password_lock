<?php

declare(strict_types=1);
use \Defuse\Crypto\Key;
use \ParagonIE\PasswordLock\PasswordLock;

/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class PasswordLockTest extends PHPUnit_Framework_TestCase
{
    public function testHash()
    {
        $key = Key::createNewRandomKey();
        $options = [];
        $password = PasswordLock::hashAndEncrypt('YELLOW SUBMARINE', $key, $options);

        $this->assertTrue(
            PasswordLock::decryptAndVerify('YELLOW SUBMARINE', $password, $key, $options)
        );

        $this->assertFalse(
            PasswordLock::decryptAndVerify('YELLOW SUBMARINF', $password, $key, $options)
        );

        $options = ['cost' => 5];
        $new_password = PasswordLock::checkRehash('YELLOW SUBMARINE', $password, $key, $options);
        $this->assertTrue(
            PasswordLock::decryptAndVerify('YELLOW SUBMARINE', $new_password, $key, $options)
    );
    }

    /**
     * @expectedException \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
     */
    public function testBitflip()
    {
        $key = Key::createNewRandomKey();
        $password = PasswordLock::hashAndEncrypt('YELLOW SUBMARINE', $key);
        $password[0] = (\ord($password[0]) === 0 ? 255 : 0);

        PasswordLock::decryptAndVerify('YELLOW SUBMARINE', $password, $key);
    }
}
