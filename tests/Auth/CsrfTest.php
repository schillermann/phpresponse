<?php

declare(strict_types=1);

namespace PhpResponse\Tests\Auth;

use PHPUnit\Framework\TestCase;
use PhpResponse\Auth\Csrf\SameToken;
use PhpResponse\Auth\Csrf\RandomToken;
use PhpResponse\Auth\Csrf\CsrfCandidate;
use PhpResponse\Auth\Csrf\RequestCandidate;
use PhpResponse\Auth\Csrf\GuardedResponse;
use PhpResponse\Text\LiteralText;
use PhpResponse\Response\Body;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\StatusLine\Forbidden;
use PhpResponse\Response\Media\Fake;

final class CsrfTest extends TestCase
{
    public function testSameTokenValidatesMatchingCandidate(): void
    {
        $token = new SameToken("secret-token");
        $this->assertEquals("secret-token", $token->string());
        $this->assertTrue($token->valid(new LiteralText("secret-token")));
        $this->assertFalse($token->valid(new LiteralText("wrong-token")));
        $this->assertFalse($token->valid(new LiteralText("")));
    }

    public function testRandomTokenGeneratesValidToken(): void
    {
        $token = new RandomToken(16);
        $this->assertEquals(32, strlen($token->string())); // 16 bytes = 32 hex chars
        $this->assertTrue($token->valid(new LiteralText($token->string())));
        $this->assertFalse($token->valid(new LiteralText("invalid")));
    }

    public function testCsrfCandidateFallbackWhenEmpty(): void
    {
        $candidate = new CsrfCandidate();
        $this->assertEquals("", $candidate->string());

        $customCandidate = new CsrfCandidate(new LiteralText("header-token"));
        $this->assertEquals("header-token", $customCandidate->string());
    }

    public function testRequestCandidateReadsFromEnvironment(): void
    {
        $_SERVER['HTTP_X_CSRF_TOKEN'] = 'header-csrf-value';
        $candidate = new RequestCandidate();
        $this->assertEquals('header-csrf-value', $candidate->string());
        unset($_SERVER['HTTP_X_CSRF_TOKEN']);
    }

    public function testGuardedResponseAllowsSafeHttpMethodsWithoutToken(): void
    {
        $target = new Ok(new Body("target"));
        $fallback = new Forbidden(new Body("fallback"));
        $token = new SameToken("valid-token");

        $guarded = new GuardedResponse(
            $target,
            $token,
            $fallback,
            new LiteralText("wrong-token"),
            new LiteralText("GET")
        );

        $media = $guarded->media(new Fake());
        /** @var Fake $media */
        $this->assertEquals(
            [
                "status: 200 OK",
                "body: target"
            ],
            $media->array()
        );
    }

    public function testGuardedResponseAllowsPostWhenTokenMatches(): void
    {
        $target = new Ok(new Body("target"));
        $fallback = new Forbidden(new Body("fallback"));
        $token = new SameToken("valid-token");

        $guarded = new GuardedResponse(
            $target,
            $token,
            $fallback,
            new LiteralText("valid-token"),
            new LiteralText("POST")
        );

        $media = $guarded->media(new Fake());
        /** @var Fake $media */
        $this->assertEquals(
            [
                "status: 200 OK",
                "body: target"
            ],
            $media->array()
        );
    }

    public function testGuardedResponseRejectsPostWhenTokenIsInvalid(): void
    {
        $target = new Ok(new Body("target"));
        $fallback = new Forbidden(new Body("fallback"));
        $token = new SameToken("valid-token");

        $guarded = new GuardedResponse(
            $target,
            $token,
            $fallback,
            new LiteralText("invalid-token"),
            new LiteralText("POST")
        );

        $media = $guarded->media(new Fake());
        /** @var Fake $media */
        $this->assertEquals(
            [
                "status: 403 Forbidden",
                "body: fallback"
            ],
            $media->array()
        );
    }
}
