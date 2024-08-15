<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MyTestResponse extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function assertRedirect($uri = null)
    {
        $detailMessage = $this->statusMessageWithDetails('201, 301, 302, 303, 307, 308', $actual = $this->getStatusCode());

        if ($actual === 500) {
            PHPUnit::fail($detailMessage);
        }

        PHPUnit::assertTrue(
            $this->isRedirect(), 'Response status code ['.$this->getStatusCode().'] is not a redirect status code.'
        );

        if (is_null($uri)) {
            return $this;
        }

        $expectedUri = app('url')->to($uri);
        $actualUri = app('url')->to($this->headers->get('Location'));

        $returnMessage = <<<EOT
        Expected URL: $expectedUri
        Actual URL  : $actualUri

        $detailMessage
        EOT;

        if ($expectedUri !== $actualUri) {
            PHPUnit::fail($returnMessage);
        }

        return $this;
    }

}
