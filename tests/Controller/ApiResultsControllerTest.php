<?php

namespace App\Tests\Controller;

use App\Entity\Message;
use App\Entity\Result;
use App\Entity\User;
use Faker\Factory as FakerFactoryAlias;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiResultsControllerTest
 *
 * @package App\Tests\Controller
 * @group   controllers
 *
 * @coversDefaultClass \App\Controller\ApiUsersController
 */
class ApiResultsControllerTest extends BaseTestCase
{
    private const RUTA_API = '/api/v1/results';

    /**
     * Test OPTIONS /results[/resultId] 204 No Content
     *
     * @covers ::__construct
     * @covers ::optionsAction
     * @return void
     */
    public function testOptionsResultAction204NoContent(): void
    {
        // OPTIONS /api/v1/results
        self::$client->request(
            Request::METHOD_OPTIONS,
            self::RUTA_API
        );
        $response = self::$client->getResponse();

        self::assertSame(
            Response::HTTP_NO_CONTENT,
            $response->getStatusCode()
        );
        self::assertNotEmpty($response->headers->get('Allow'));

        // OPTIONS /api/v1/users/{id}
        self::$client->request(
            Request::METHOD_OPTIONS,
            self::RUTA_API . '/' . self::$faker->numberBetween(1, 100)
        );

        self::assertSame(
            Response::HTTP_NO_CONTENT,
            $response->getStatusCode()
        );
        self::assertNotEmpty($response->headers->get('Allow'));
    }

    /**
     * Test GET /users 404 Not Found
     *
     * @return void
     */
//    public function testCGetAction404(): void
//    {
//        $headers = [];
//        self::$client->request(
//            Request::METHOD_GET,
//            self::RUTA_API,
//            [],
//            [],
//            $headers
//        );
//        $response = self::$client->getResponse();
//
//        self::assertEquals(
//            Response::HTTP_NOT_FOUND,
//            $response->getStatusCode()
//        );
//        $r_body = (string) $response->getContent();
//        self::assertContains(Message::CODE_ATTR, $r_body);
//        self::assertContains(Message::MESSAGE_ATTR, $r_body);
//        $r_data = json_decode($r_body, true);
//        self::assertEquals(Response::HTTP_NOT_FOUND, $r_data[Message::MESSAGE_ATTR][Message::CODE_ATTR]);
//        self::assertEquals(Response::$statusTexts[404], $r_data[Message::MESSAGE_ATTR][Message::MESSAGE_ATTR]);
//    }

    /**
     * Test POST /results 201 Created
     *
     * @return array result data
     */
    public function testPostResultAction201Created(): array {
        $role = self::$faker->word;
        $p_data = [
            Result::RESULT_ATTR => 214,
            Result::USER_ATTR => 1,
            Result::TIME_ATTR => new \DateTime('now'),
        ];

        // 201
        $headers = $this->getTokenHeaders();
        self::$client->request(
            Request::METHOD_POST,
            self::RUTA_API,
            [],
            [],
            $headers,
            json_encode($p_data)
        );
        $response = self::$client->getResponse();

        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertTrue($response->isSuccessful());
        self::assertNotNull($response->headers->get('Location'));
        self::assertJson($response->getContent());
        $result = json_decode($response->getContent(), true);

        self::assertNotEmpty($result['result']['id']);
        self::assertSame($p_data[Result::RESULT_ATTR], $result['result'][Result::RESULT_ATTR]);
        self::assertSame($p_data[Result::USER_ATTR], $result['result'][Result::USER_ATTR]['id']);

        return $result['result'];
    }

    /**
     * Test GET /results 200 Ok
     *
     * @return void
     * @depends testPostResultAction201Created
     */
    public function testCGetAction200Ok(): void
    {
        $headers = $this->getTokenHeaders();
        self::$client->request(Request::METHOD_GET, self::RUTA_API, [], [], $headers);
        $response = self::$client->getResponse();
        self::assertTrue($response->isSuccessful());
        self::assertNotNull($response->getEtag());
        self::assertJson($response->getContent());
        $results = json_decode($response->getContent(), true);
        self::assertArrayHasKey('results', $results);
    }

    /**
     * Test GET /results 200 Ok (XML)
     *
     * @param   array $result result returned by testPostResultAction201()
     * @return  void
     * @depends testPostResultAction201Created
     */
    public function testCGetAction200XmlOk(array $result): void
    {
        $headers = $this->getTokenHeaders();
        self::$client->request(
            Request::METHOD_GET,
            self::RUTA_API . '/' . $result['id'] . '.xml',
            [],
            [],
            $headers
        );
        $response = self::$client->getResponse();
        self::assertTrue($response->isSuccessful());
        self::assertNotNull($response->getEtag());
        self::assertTrue($response->headers->contains('content-type', 'application/xml'));
    }

    /**
     * Test GET /results/{resultId} 200 Ok
     *
     * @param   array $result result returned by testPostResultAction201()
     * @return  void
     * @depends testPostResultAction201Created
     */
    public function testGetResultAction200Ok(array $result): void
    {
        $headers = $this->getTokenHeaders();
        self::$client->request(
            Request::METHOD_GET,
            self::RUTA_API . '/' . $result['id'],
            [],
            [],
            $headers
        );
        $response = self::$client->getResponse();

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertNotNull($response->getEtag());
        self::assertJson((string) $response->getContent());
        $result_aux = json_decode((string) $response->getContent(), true);
        self::assertSame($result['id'], $result_aux['result']['id']);
    }

    /**
     * Test POST /results 400 Bad Request
     *
     * @param   array $result result returned by testPostResultAction201()
     * @return  void
     * @depends testPostResultAction201Created
     */
    public function testPostResultAction400BadRequest(array $result): void
    {
        $headers = $this->getTokenHeaders();

        $p_data = [
            Result::RESULT_ATTR => $result[Result::RESULT_ATTR],
            Result::USER_ATTR => 3
        ];
        self::$client->request(
            Request::METHOD_POST,
            self::RUTA_API,
            [],
            [],
            $headers,
            json_encode($p_data)
        );
        $response = self::$client->getResponse();

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $r_body = (string) $response->getContent();
        self::assertJson($r_body);
        self::assertStringContainsString(Message::CODE_ATTR, $r_body);
        self::assertStringContainsString(Message::MESSAGE_ATTR, $r_body);
        $r_data = json_decode($r_body, true);
        self::assertSame(Response::HTTP_BAD_REQUEST, $r_data[Message::CODE_ATTR]);
        self::assertSame(
            Response::$statusTexts[400],
            $r_data[Message::MESSAGE_ATTR]
        );
    }

    /**
     * Test PUT /results/{resultId} 209 Content Returned
     *
     * @param   array $result result returned by testPostResultAction201()
     * @return  array modified result data
     * @depends testPostResultAction201Created
     */
    public function testPutResultAction209ContentReturned(array $result): array
    {
        $headers = $this->getTokenHeaders();
        $role = self::$faker->word;
        $p_data = [
            Result::RESULT_ATTR => 123,
            Result::USER_ATTR => 1,
            Result::TIME_ATTR => new \DateTime('now'),
        ];

        self::$client->request(
            Request::METHOD_PUT,
            self::RUTA_API . '/' . $result['id'],
            [],
            [],
            $headers,
            json_encode($p_data)
        );
        $response = self::$client->getResponse();

        self::assertSame(209, $response->getStatusCode());
        self::assertJson((string) $response->getContent());
        $result_aux = json_decode((string) $response->getContent(), true);
        self::assertSame($result['id'], $result_aux['result']['id']);
        self::assertSame($p_data[Result::RESULT_ATTR], $result_aux['result'][Result::RESULT_ATTR]);
        self::assertSame($p_data[Result::USER_ATTR], $result_aux['result'][Result::USER_ATTR]['id']);
        return $result_aux['result'];
    }

    /**
     * Test DELETE /results/{resultId} 204 No Content
     *
     * @param   array $result result returned by testPostResultAction201()
     * @return  int resultId
     *
     * @depends testPostResultAction201Created
     * @depends testPostResultAction400BadRequest
     * @depends testGetResultAction200Ok
     * @depends testPutResultAction209ContentReturned
     */
    public function testDeleteResultAction204NoContent(array $result): int
    {
        $headers = $this->getTokenHeaders();
        self::$client->request(
            Request::METHOD_DELETE,
            self::RUTA_API . '/' . $result['id'],
            [],
            [],
            $headers
        );
        $response = self::$client->getResponse();

        self::assertSame(
            Response::HTTP_NO_CONTENT,
            $response->getStatusCode()
        );
        self::assertEmpty((string) $response->getContent());

        return $result['id'];
    }

    /**
     * Test POST /results 422 Unprocessable Entity
     *
     * @param null|int $result
     * @param null|User $user
     * @dataProvider resultProvider422
     * @return void
     */
    public function testPostResultAction422UnprocessableEntity(?int $result, ?User $user): void
    {
        $headers = $this->getTokenHeaders();
        $p_data = [
            Result::RESULT_ATTR => $result,
        ];

        self::$client->request(
            Request::METHOD_POST,
            self::RUTA_API,
            [],
            [],
            $headers,
            json_encode($p_data)
        );
        $response = self::$client->getResponse();

        self::assertSame(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $response->getStatusCode()
        );
        $r_body = (string) $response->getContent();
        self::assertJson($r_body);
        self::assertStringContainsString(Message::CODE_ATTR, $r_body);
        self::assertStringContainsString(Message::MESSAGE_ATTR, $r_body);
        $r_data = json_decode($r_body, true);
        self::assertSame(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $r_data[Message::CODE_ATTR]
        );
        self::assertSame(
            Response::$statusTexts[422],
            $r_data[Message::MESSAGE_ATTR]
        );
    }

    /**
     * Test GET    /results 401 UNAUTHORIZED
     * Test POST   /results 401 UNAUTHORIZED
     * Test GET    /results/{resultId} 401 UNAUTHORIZED
     * Test PUT    /results/{resultId} 401 UNAUTHORIZED
     * Test DELETE /results/{resultId} 401 UNAUTHORIZED
     *
     * @param string $method
     * @param string $uri
     * @dataProvider routeProvider401()
     * @return void
     * @uses \App\EventListener\ExceptionListener
     */
    public function testResultStatus401Unauthorized(string $method, string $uri): void
    {
        self::$client->request(
            $method,
            $uri,
            [],
            [],
            [ 'HTTP_ACCEPT' => 'application/json' ]
        );
        $response = self::$client->getResponse();

        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        self::assertJson((string) $response->getContent());
        $r_body = (string) $response->getContent();
        self::assertStringContainsString(Message::CODE_ATTR, $r_body);
        self::assertStringContainsString(Message::MESSAGE_ATTR, $r_body);
        $r_data = json_decode($r_body, true);
        self::assertSame(Response::HTTP_UNAUTHORIZED, $r_data[Message::CODE_ATTR]);
        /*self::assertContains(
            Response::$statusTexts[401],
            $r_data[Message::MESSAGE_ATTR]
        );*/
    }

    /**
     * Test GET    /results/{resultId} 404 NOT FOUND
     * Test PUT    /results/{resultId} 404 NOT FOUND
     * Test DELETE /results/{resultId} 404 NOT FOUND
     *
     * @param string $method
     * @param int $resultId result id. returned by testDeleteResultAction204NoContent()
     * @dataProvider routeProvider404
     * @return void
     * @depends      testDeleteResultAction204NoContent
     *
     */
    public function testResultStatus404NotFound(string $method, int $resultId): void
    {
        $headers = $this->getTokenHeaders(
            self::$role_admin[User::EMAIL_ATTR],
            self::$role_admin[User::PASSWD_ATTR]
        );
        $p_data = [
            Result::RESULT_ATTR => self::$faker->numberBetween(0, 1000),
            Result::USER_ATTR => 2,
        ];
        self::$client->request(
            $method,
            self::RUTA_API . '/' . $resultId,
            [],
            [],
            $headers,
            json_encode($p_data)
        );
        $response = self::$client->getResponse();

        self::assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $r_body = (string) $response->getContent();
        self::assertStringContainsString(Message::CODE_ATTR, $r_body);
        self::assertStringContainsString(Message::MESSAGE_ATTR, $r_body);
        $r_data = json_decode($r_body, true);
        self::assertSame(Response::HTTP_NOT_FOUND, $r_data[Message::CODE_ATTR]);
        self::assertSame(Response::$statusTexts[404], $r_data[Message::MESSAGE_ATTR]);
    }

    /**
     * Test POST   /results 403 FORBIDDEN
     * Test PUT    /results/{resultId} 403 FORBIDDEN
     * Test DELETE /results/{resultId} 403 FORBIDDEN
     *
     * @param string $method
     * @param string $uri
     * @dataProvider routeProvider403()
     * @return void
     * @uses \App\EventListener\ExceptionListener
     *
     * @depends testPostResultAction201Created
     * @depends testPostResultAction400BadRequest
     * @depends testGetResultAction200Ok
     * @depends testPutResultAction209ContentReturned
     */
    public function testResultStatus403Forbidden(string $method, string $uri): void
    {
        $headers = $this->getTokenHeaders(
            self::$role_user[User::EMAIL_ATTR],
            self::$role_user[User::PASSWD_ATTR]
        );
        $p_data = [
            Result::RESULT_ATTR => self::$faker->numberBetween(0, 1000),
            Result::USER_ATTR => 343,
        ];
        self::$client->request($method, $uri, [], [], $headers,
            json_encode($p_data));
        $response = self::$client->getResponse();

        self::assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        self::assertJson((string) $response->getContent());
        $r_body = (string) $response->getContent();
        self::assertStringContainsString(Message::CODE_ATTR, $r_body);
        self::assertStringContainsString(Message::MESSAGE_ATTR, $r_body);
        $r_data = json_decode($r_body, true);
        self::assertSame(Response::HTTP_FORBIDDEN, $r_data[Message::CODE_ATTR]);
        self::assertSame(
            '`Forbidden`: you don\'t have permission to access',
            $r_data[Message::MESSAGE_ATTR]
        );
    }

    /**
     * *********
     * PROVIDERS
     * *********
     */

    /**
     * Result provider (incomplete) -> 422 status code
     *
     * @return array result data
     */
    public function resultProvider422(): array {
        $faker = FakerFactoryAlias::create('es_ES');
        $result = 324;
        $user = new User('email@gmail.com', 'pass', ['ROLE_USER']);

        return [
            'no_result'  => [ null,   $user ],
            'no_user' => [ $result, null     ],
            'nothing'   => [ null,   null    ],
        ];
    }

    /**
     * Route provider (expected status: 401 UNAUTHORIZED)
     *
     * @return array [ method, url ]
     */
    public function routeProvider401(): array
    {
        return [
            'cgetAction401'   => [ Request::METHOD_GET,    self::RUTA_API ],
            'getAction401'    => [ Request::METHOD_GET,    self::RUTA_API . '/1' ],
            'postAction401'   => [ Request::METHOD_POST,   self::RUTA_API ],
            'putAction401'    => [ Request::METHOD_PUT,    self::RUTA_API . '/1' ],
            'deleteAction401' => [ Request::METHOD_DELETE, self::RUTA_API . '/1' ],
        ];
    }

    /**
     * Route provider (expected status 404 NOT FOUND)
     *
     * @return array [ method ]
     */
    public function routeProvider404(): array
    {
        return [
            'getAction404'    => [ Request::METHOD_GET ],
            'putAction404'    => [ Request::METHOD_PUT ],
            'deleteAction404' => [ Request::METHOD_DELETE ],
        ];
    }

    /**
     * Route provider (expected status: 403 FORBIDDEN)
     *
     * @return array [ method, url ]
     */
    public function routeProvider403(): array
    {
        return [
            'postAction403'   => [ Request::METHOD_POST,   self::RUTA_API ],
            'putAction403'    => [ Request::METHOD_PUT,    self::RUTA_API . '/1' ],
            'deleteAction403' => [ Request::METHOD_DELETE, self::RUTA_API . '/1' ],
        ];
    }
}
