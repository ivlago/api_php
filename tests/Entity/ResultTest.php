<?php

/**
 * PHP version 7.4
 *
 * @category TestEntities
 * @package  App\Tests\Entity
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://www.etsisi.upm.es/ E.T.S. de Ingeniería de Sistemas Informáticos
 */

namespace App\Tests\Entity;

use App\Entity\Result;
use App\Entity\User;
use DateTime;
use Exception;
use Faker\Factory as FakerFactoryAlias;
use Faker\Generator as FakerGeneratorAlias;
use PHPUnit\Framework\TestCase;

/**
 * Class ResultTest
 *
 * @package App\Tests\Entity
 *
 * @group   entities
 * @coversDefaultClass \App\Entity\Result
 */
class ResultTest extends TestCase {

    protected static Result $resultado;

    private static FakerGeneratorAlias $faker;

    /**
     * Sets up the fixture.
     * This method is called before a test is executed.
     */
    public static function setUpBeforeClass(): void
    {
        $user = new User('email@dominio.com', 'password', ['ROLE_USER']);
        $date = new DateTime('2020-01-01T15:03:01.012345Z');
        self::$resultado = new Result(214, $user, $date);
        self::$faker = FakerFactoryAlias::create('es_ES');
    }

    /**
     * Implement testConstructor().
     *
     * @return void
     */
    public function testConstructor(): void {
        $user = new User('email@dominio.com', 'password', ['ROLE_USER']);
        $date = new DateTime('2020-01-01T15:03:01.012345Z');
        $resultado = new Result(214, $user, $date);

        self::assertEquals(214, $resultado->getResult());
        self::assertEquals('email@dominio.com',$resultado->getUser()->getEmail());
    }

    /**
     * Implement testGetId().
     *
     * @return void
     */
    public function testGetId(): void
    {
        self::assertEquals(0, self::$resultado->getId());
    }

    /**
     * Implements testGetSetResult().
     *
     * @throws Exception
     * @return void
     */
    public function testGetSetResult(): void
    {
        $result = 36741;
        self::$resultado->setResult($result);
        static::assertSame(
            $result,
            self::$resultado->getResult()
        );
    }

    /**
     * Implements testGetSetUser().
     *
     * @return void
     * @throws Exception
     */
    public function testGetSetUser(): void
    {
        $email = self::$faker->email;
        $password = self::$faker->password;
        $user = new User($email, $password);
        self::$resultado->setUser($user);
        static::assertSame(
                $user,
                self::$resultado->getUser()
        );
    }

    /**
     * Implement testGetSetTime().
     *
     * @return void
     */
    public function testGetSetTime(): void
    {
        $time = new DateTime('now');
        self::$resultado->setTime($time);
        static::assertSame(
            $time,
            self::$resultado->getTime()
        );
    }
}
