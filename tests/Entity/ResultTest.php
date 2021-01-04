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
class ResultTest extends TestCase
{

    protected static Result $resultado;

    private static FakerGeneratorAlias $faker;

    /**
     * Sets up the fixture.
     * This method is called before a test is executed.
     */
    public static function setUpBeforeClass(): void
    {
        // self::$resultado = new Result();
        self::$faker = FakerFactoryAlias::create('es_ES');
    }

    /**
     * Implement testConstructor().
     *
     * @return void
     */
    public function testConstructor(): void
    {
        $date = new DateTime('2020-01-01T15:03:01.012345Z');
        $resultado = new Result(214, 12, $date);
        echo 'Resultado '.$resultado;
        self::assertEmpty($resultado->getResult());
        self::assertEmpty($resultado->getUser());
        self::assertEmpty($resultado->getTime());
        //self::assertEquals(3, $resultado->getId());
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
        $result = self::$faker->result;
        self::$resultado->setEmail($result);
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
        $user = self::$faker->user;
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
        $time = self::$faker->time;
        self::$resultado->setTime($time);
        static::assertSame(
            $time,
            self::$resultado->getTime()
        );
    }
}
