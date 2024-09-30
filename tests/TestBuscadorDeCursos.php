<?php

use Alura\BuscadorDeCursos\Buscador;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\{StreamInterface, ResponseInterface};
use Symfony\Component\DomCrawler\Crawler;

class TestBuscadorDeCursos extends TestCase
{
  private $httpClientMock;
  private $url = 'url-teste';

  protected function setUp(): void
  {
    $html = <<<FIM
    <html>
      <body>
        <span class="card-curso__nome">Curso test 1</span>
        <span class="card-curso__nome">Curso test 2</span>
        <span class="card-curso__nome">Curso test 3</span>
      </body>
    </html>
    FIM;

    $stream = $this->createMock(originalClassName: StreamInterface::class);
    $stream
      ->expects($this->once())
      ->method(constraint: '__toString')
      ->willReturn($html);

    $response = $this->createMock(originalClassName: ResponseInterface::class);
    $response
      ->expects($this->once())
      ->method(constraint: 'getBody')
      ->willReturn($stream);

    $httpClient = $this->createMock(originalClassName: ClientInterface::class);
    $httpClient
      ->expects($this->once())
      ->method(constraint: 'request')
      ->with('GET', $this->url)
      ->willReturn($response);

    $this->httpClientMock = $httpClient;
  }

  public function testBuscadorDeveRetornarCursos()
  {
    $crawler = new Crawler();
    $buscador = new Buscador($this->httpClientMock, $crawler);
    $cursos = $buscador->buscar($this->url);

    $this->assertCount(3, $cursos);
    $this->assertEquals('Curso test 1', $cursos[0]);
    $this->assertEquals('Curso test 2', $cursos[1]);
    $this->assertEquals('Curso test 3', $cursos[2]);
  }
}
