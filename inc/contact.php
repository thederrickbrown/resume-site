<?php
// Contact form handler for resume.dmbweb.com. Replaces the old sendEmail.php, which put visitor input
// straight into mail headers. Visitor input only ever goes in the body and a validated Reply-To.
header('Content-Type: application/json');

$to = 'thederrickbrown@gmail.com';

function reply(int $code, string $msg) {
  http_response_code($code);
  echo json_encode(['ok' => $code === 200, 'message' => $msg]);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') reply(405, 'Use the form to send a message.');
if (!empty($_POST['website'])) reply(200, 'Thanks, your message was sent.'); // honeypot field, hidden from people

$clean = fn($v, $max) => mb_substr(trim(str_replace(["\r", "\n", "\0"], ' ', (string)($v ?? ''))), 0, $max);
$name    = $clean($_POST['name'] ?? '', 100);
$email   = $clean($_POST['email'] ?? '', 200);
$company = $clean($_POST['company'] ?? '', 150);
$message = mb_substr(trim(str_replace("\0", '', (string)($_POST['message'] ?? ''))), 0, 5000);

if (mb_strlen($name) < 2) reply(422, 'Please enter your name.');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) reply(422, 'Please enter a valid email address.');
if (mb_strlen($message) < 15) reply(422, 'Please write a little more, at least 15 characters.');

$body = "Name: $name\nEmail: $email\n" . ($company !== '' ? "Company: $company\n" : '') . "\n$message\n\n-- Sent from the contact form on resume.dmbweb.com\n";
$headers = implode("\r\n", [
  'From: resume.dmbweb.com <noreply@resume.dmbweb.com>',
  'Reply-To: ' . $email,
  'MIME-Version: 1.0',
  'Content-Type: text/plain; charset=UTF-8',
]);
$subject = '=?UTF-8?B?' . base64_encode("Resume site: message from $name") . '?=';

if (!mail($to, $subject, $body, $headers, '-fnoreply@resume.dmbweb.com')) {
  reply(500, "That didn't go through. Please email thederrickbrown@gmail.com directly.");
}
reply(200, "Thanks, your message was sent. I'll reply within a day.");
