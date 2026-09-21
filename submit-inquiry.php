<?php
// Next Horizon Travel Co. - portable inquiry handler
// Sends website inquiries to Bryan only.

declare(strict_types=1);

const TO_EMAIL = 'bryan@nexthorizontravelco.com';
const FROM_EMAIL = 'website@nexthorizontravelco.com';
const FROM_NAME = 'Next Horizon Travel Co. Website';

function redirect_result(string $result): never {
    header('Location: plan-my-vacation.html?submitted=' . rawurlencode($result) . '#form-status-v34', true, 303);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Method Not Allowed');
}

// Honeypot: real visitors never see or fill this field.
if (trim((string)($_POST['Website'] ?? '')) !== '') {
    redirect_result('1');
}

// Basic timing check blocks many instant bot submissions without affecting normal visitors.
$started = (int)($_POST['form_started'] ?? 0);
if ($started > 0 && time() - $started < 2) {
    redirect_result('error');
}

function clean(string $key, int $max = 1000): string {
    $value = trim((string)($_POST[$key] ?? ''));
    $value = str_replace(["\r\0", "\n\0"], '', $value);
    return mb_substr($value, 0, $max);
}

$name = clean('Name', 150);
$email = clean('Email', 254);
$phone = clean('Phone', 80);
$type = clean('Vacation Type', 100);
$destination = clean('Destination', 300);
$departure = clean('Departure', 300);
$dates = clean('Dates', 300);
$travelers = clean('Travelers', 200);
$children = clean('Children Ages', 200);
$accommodation = clean('Accommodation', 300);
$supplier = clean('Supplier', 300);
$budget = clean('Budget', 200);
$notes = clean('Notes', 1500);

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirect_result('error');
}

$allowedTypes = ['Cruise', 'Resort Vacation', 'Guided / Custom Journey', 'Group Travel'];
if (!in_array($type, $allowedTypes, true)) {
    $type = 'Travel';
}

$subject = 'New Website Travel Inquiry - ' . $type;
$lines = [
    'NEW WEBSITE TRAVEL INQUIRY',
    '==========================',
    '',
    'Vacation Type: ' . $type,
    'Name: ' . $name,
    'Email: ' . $email,
    'Phone: ' . ($phone ?: 'Not provided'),
    '',
    'Destination: ' . ($destination ?: 'Not provided'),
    'Departure Port / Airport: ' . ($departure ?: 'Not provided'),
    'Travel Dates: ' . ($dates ?: 'Not provided'),
    'Travelers: ' . ($travelers ?: 'Not provided'),
    "Children's Ages: " . ($children ?: 'Not provided'),
    'Accommodation / Travel Style: ' . ($accommodation ?: 'Not provided'),
    'Cruise Line / Resort / Interests: ' . ($supplier ?: 'Not provided'),
    'Approximate Budget: ' . ($budget ?: 'Not provided'),
    'Notes: ' . ($notes ?: 'Not provided'),
    '',
    'Submitted from: nexthorizontravelco.com',
    'Submitted: ' . date('Y-m-d H:i:s T'),
];
$body = implode("\r\n", $lines);

// Use a domain-based From address for better deliverability; Reply-To goes to the traveler.
$headers = [
    'From: ' . FROM_NAME . ' <' . FROM_EMAIL . '>',
    'Reply-To: ' . $email,
    'Content-Type: text/plain; charset=UTF-8',
    'X-Mailer: PHP/' . phpversion(),
];

$sent = @mail(TO_EMAIL, $subject, $body, implode("\r\n", $headers));
redirect_result($sent ? '1' : 'error');
