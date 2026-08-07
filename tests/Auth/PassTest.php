<?php

declare(strict_types=1);

namespace PhpResponse\Tests\Auth;

use PHPUnit\Framework\TestCase;
use PhpResponse\Auth\Pass\AnonymousIdentity;
use PhpResponse\Auth\Pass\UserIdentity;
use PhpResponse\Auth\Pass\Cookie;
use PhpResponse\Auth\Pass\Header;
use PhpResponse\Auth\Pass\Bearer;
use PhpResponse\Auth\Pass\Fake;
use PhpResponse\Auth\Pass\IdentityAuthentication;
use PhpResponse\Text\LiteralText;

final class PassTest extends TestCase
{
    public function testAnonymousIdentity(): void
    {
        $id = new AnonymousIdentity();
        $this->assertFalse($id->authenticated());
        $this->assertEquals("anonymous", $id->name()->string());
    }

    public function testUserIdentity(): void
    {
        $id = new UserIdentity("john_doe");
        $this->assertTrue($id->authenticated());
        $this->assertEquals("john_doe", $id->name()->string());
    }

    public function testCookieReturnsAnonymousWhenMissing(): void
    {
        unset($_COOKIE['ps_identity']);
        $pass = new Cookie('ps_identity');
        $id = $pass->identity();
        $this->assertFalse($id->authenticated());
    }

    public function testCookieReturnsUserIdentityWhenPresent(): void
    {
        $_COOKIE['ps_identity'] = 'alice';
        $pass = new Cookie('ps_identity');
        $id = $pass->identity();
        $this->assertTrue($id->authenticated());
        $this->assertEquals('alice', $id->name()->string());
        unset($_COOKIE['ps_identity']);
    }

    public function testHeaderReturnsUserIdentityWhenPresent(): void
    {
        $_SERVER['HTTP_X_IDENTITY'] = 'bob';
        $pass = new Header('X-Identity');
        $id = $pass->identity();
        $this->assertTrue($id->authenticated());
        $this->assertEquals('bob', $id->name()->string());
        unset($_SERVER['HTTP_X_IDENTITY']);
    }

    public function testBearerExtractsToken(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer secret-jwt-token';
        $pass = new Bearer();
        $id = $pass->identity();
        $this->assertTrue($id->authenticated());
        $this->assertEquals('secret-jwt-token', $id->name()->string());
        unset($_SERVER['HTTP_AUTHORIZATION']);
    }

    public function testFakeReturnsConfiguredIdentity(): void
    {
        $pass = new Fake(new UserIdentity("test-user"));
        $this->assertTrue($pass->identity()->authenticated());
        $this->assertEquals("test-user", $pass->identity()->name()->string());
    }

    public function testIdentityAuthentication(): void
    {
        $authAnon = new IdentityAuthentication(new Fake(new AnonymousIdentity()));
        $this->assertFalse($authAnon->valid());

        $authUser = new IdentityAuthentication(new Fake(new UserIdentity("admin")));
        $this->assertTrue($authUser->valid());
    }
}
