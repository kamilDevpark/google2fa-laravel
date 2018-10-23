<?php

namespace PragmaRX\Google2FALaravel\Tests\Components;

use PragmaRX\Google2FALaravel\Support\Constants as SupportConstants;
use PragmaRX\Google2FALaravel\Tests\Support\BootstrapEnvironment;
use PragmaRX\Google2FALaravel\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

class StatelessTest extends TestCase
{
    use BootstrapEnvironment;

    public function setUp()
    {
        parent::setup();

        $this->loginUser();
        $this->session = app('session.store');

    }
    /**
     * @test
     */
    public function session_keeps_OTP_after_login()
    {
        $auth_flag = 'google2fa.' . SupportConstants::SESSION_AUTH_PASSED;
        $this->assertNull($this->session->get($auth_flag));
        $this->assertLogin($this->getOTP());
        $this->assertTrue($this->session->get($auth_flag));
    }


    /**
     * @test
     */
    public function session_flush_OTP_after_logout()
    {
        $auth_key = 'google2fa.' . SupportConstants::SESSION_AUTH_PASSED;
        $this->session->put($auth_key, true);
        $this->json('POST', 'logout');
        $this->assertNull(session($auth_key));
    }


    /**
     * @test
     */
    public function Google2FAPostPassesForStatelessMode()
    {
        $this->assertLogin($this->getOTP());

        $this->assertContains(
            'we are home',
            $this->json('GET', 'home')->getContent()
        );
    }

    /**
     * @test
     */
    public function Google2FAFailedForStatelessMode()
    {
        $response = $this->json('GET', 'home')->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertArraySubset([
            config('google2fa.error_messages.wrong_otp')
        ], $response->decodeResponseJson()['message']);
    }




}
