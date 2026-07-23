<?php

/**
 * Quick manual test script for Phase 1 auth & role redirect.
 */

$baseUrl = 'http://localhost:8000';

// Helper: follow-redirect-free GET/POST
function httpRequest(string $url, string $method = 'GET', array $postData = [], string $cookie = ''): array {
    $opts = [
        'http' => [
            'method' => $method,
            'follow_location' => 0,
            'ignore_errors' => true,
            'header' => "Accept: text/html\r\n",
        ],
    ];
    if ($cookie) {
        $opts['http']['header'] .= "Cookie: $cookie\r\n";
    }
    if ($method === 'POST') {
        $opts['http']['header'] .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $opts['http']['content'] = http_build_query($postData);
    }
    $ctx = stream_context_create($opts);
    $body = @file_get_contents($url, false, $ctx);
    $status = 0;
    $location = '';
    $setCookie = '';
    foreach ($http_response_header ?? [] as $h) {
        if (preg_match('/^HTTP\/\S+ (\d+)/', $h, $m)) $status = (int)$m[1];
        if (stripos($h, 'Location:') === 0) $location = trim(substr($h, 9));
        if (stripos($h, 'Set-Cookie:') === 0) $setCookie .= trim(explode(';', substr($h, 11))[0]) . '; ';
    }
    return ['status' => $status, 'location' => $location, 'cookie' => $setCookie, 'body' => $body];
}

function extractCsrfToken(string $html): string {
    if (preg_match('/name="csrf-token"\s+content="([^"]+)"/', $html, $m)) return $m[1];
    if (preg_match('/name="_token".*?value="([^"]+)"/', $html, $m)) return $m[1];
    return '';
}

$pass = 0;
$fail = 0;

function test(string $name, bool $result): void {
    global $pass, $fail;
    if ($result) {
        echo "  ✅ PASS: $name\n";
        $pass++;
    } else {
        echo "  ❌ FAIL: $name\n";
        $fail++;
    }
}

echo "=== Phase 1: Auth & Role Tests ===\n\n";

// Test 1: Root redirects to /login
echo "1. GET / → should redirect to /login\n";
$r = httpRequest("$baseUrl/");
test("Status 302", $r['status'] === 302);
test("Redirects to /login", str_contains($r['location'], '/login'));

// Test 2: /login page loads
echo "\n2. GET /login → should return 200\n";
$r = httpRequest("$baseUrl/login");
test("Status 200", $r['status'] === 200);
test("Contains login form", str_contains($r['body'], 'username'));
$sessionCookie = $r['cookie'];

// Get CSRF token from login page
$token = extractCsrfToken($r['body']);
test("CSRF token found", !empty($token));

// Test 3: Login as admin
echo "\n3. POST /login as admin → should redirect to /admin/dashboard\n";
$r = httpRequest("$baseUrl/login", 'POST', [
    '_token' => $token,
    'username' => 'admin',
    'password' => 'password123',
], $sessionCookie);
test("Status 302", $r['status'] === 302);
test("Redirects to /admin/dashboard", str_contains($r['location'], '/admin/dashboard'));

// Test 4: Login as marketing
echo "\n4. POST /login as marketing1 → should redirect to /marketing/dashboard\n";
$r2 = httpRequest("$baseUrl/login");
$sessionCookie2 = $r2['cookie'];
$token2 = extractCsrfToken($r2['body']);
$r = httpRequest("$baseUrl/login", 'POST', [
    '_token' => $token2,
    'username' => 'marketing1',
    'password' => 'password123',
], $sessionCookie2);
test("Status 302", $r['status'] === 302);
test("Redirects to /marketing/dashboard", str_contains($r['location'], '/marketing/dashboard'));

// Test 5: Login as manajemen
echo "\n5. POST /login as manajemen → should redirect to /manajemen/dashboard\n";
$r3 = httpRequest("$baseUrl/login");
$sessionCookie3 = $r3['cookie'];
$token3 = extractCsrfToken($r3['body']);
$r = httpRequest("$baseUrl/login", 'POST', [
    '_token' => $token3,
    'username' => 'manajemen',
    'password' => 'password123',
], $sessionCookie3);
test("Status 302", $r['status'] === 302);
test("Redirects to /manajemen/dashboard", str_contains($r['location'], '/manajemen/dashboard'));

// Test 6: Wrong password
echo "\n6. POST /login with wrong password → should stay on login\n";
$r4 = httpRequest("$baseUrl/login");
$sessionCookie4 = $r4['cookie'];
$token4 = extractCsrfToken($r4['body']);
$r = httpRequest("$baseUrl/login", 'POST', [
    '_token' => $token4,
    'username' => 'admin',
    'password' => 'wrongpassword',
], $sessionCookie4);
test("Status 302 back to login", $r['status'] === 302);
test("Does NOT redirect to dashboard", !str_contains($r['location'], '/dashboard'));

echo "\n=== Results: $pass passed, $fail failed ===\n";
exit($fail > 0 ? 1 : 0);
