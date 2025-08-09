<?php
$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
if (PHP_VERSION_ID >= 70300) {
  session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => $secure,
    'httponly' => true,
    'samesite' => 'Lax',
  ]);
} else {
  // Fallback for older PHP versions (no explicit SameSite)
  session_set_cookie_params(0, '/', '', $secure, true);
}
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
