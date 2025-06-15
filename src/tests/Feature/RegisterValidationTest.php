<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_requires_name()
    {
        $response = $this->from('/register')->post('/register', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/register');
        $response = $this->get('/register');
        $response->assertSee('お名前を入力してください');
    }

    public function test_register_requires_email()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'KIE',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/register');
        $response = $this->get('/register');
        $response->assertSee('メールアドレスを入力してください');
    }

    public function test_register_requires_password()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'KIE',
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect('/register');
        $response = $this->get('/register');
        $response->assertSee('パスワードを入力してください');
    }

    public function test_register_requires_password_min_length()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'KIE',
            'email' => 'test@example.com',
            'password' => '12345',
            'password_confirmation' => '12345',
        ]);

        $response->assertRedirect('/register');
        $response = $this->get('/register');
        $response->assertSee('パスワードは8文字以上で入力してください');
    }

    public function test_password_confirmation_must_match()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'KIE',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'wrongpass',
        ]);

        $response->assertRedirect('/register');
        $response = $this->get('/register');
        $response->assertSee('パスワードと一致しません');
    }

    public function test_successful_registration_redirects_to_email_verify()
    {
        $response = $this->post('/register', [
            'name' => 'KIE',
            'email' => 'kie@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/email/verify');
    }
}
