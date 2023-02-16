<?php

use PHPUnit\Framework\TestCase;

class ProductControllerTest extends TestCase
{
	public function testWorkMainPage(): void
	{
		$client = new GuzzleHttp\Client();
		$result = $client->request('GET', 'http://www.matavest.beget.tech/product/1/');
		$this->assertEquals(200,$result->getStatusCode()) ;
		$this->assertStringContainsString('class="product-detailed__main-title"',$result->getBody());
	}
}
