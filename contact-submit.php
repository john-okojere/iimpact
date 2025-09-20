<?php
declare(strict_types=1);

$redirectTarget = 'contact.html';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $redirectTarget);
    exit;
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$errors = [];
$formId = $_POST['form_id'] ?? '';
if ($formId !== 'contact_form') {
    $errors[] = 'Invalid form submission. Please try again.';
}

$honeypot = trim($_POST['company'] ?? '');
if ($honeypot !== '') {
    $errors[] = 'Spam detected.';
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '') {
    $errors[] = 'Please let us know your name.';
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please provide a valid email address.';
}

if ($message === '') {
    $errors[] = 'Please add a message so we know how to help.';
}

$status = 'error';
$mailSent = false;
$recipient = 'info@iimpactconsult.org';
$date = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s \U\T\C');

$subject = 'Website enquiry from ' . ($name !== '' ? $name : 'contact form');
$body = "You have received a new enquiry from the I-Impact Consult website.\n\n" .
        'Name: ' . $name . "\n" .
        'Email: ' . $email . "\n\n" .
        "Message:\n" . $message . "\n\n" .
        "--\nSubmitted on $date";

$headers = [
    'From: I-Impact Consult <no-reply@iimpactconsult.org>',
    'Reply-To: ' . ($email !== '' ? $email : 'info@iimpactconsult.org'),
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=utf-8',
    'X-Mailer: PHP/' . PHP_VERSION,
];

if (!$errors) {
    $mailSent = mail($recipient, $subject, $body, implode("\r\n", $headers));
    if ($mailSent) {
        $status = 'success';
    } else {
        $errors[] = 'We could not deliver your message. Please email info@iimpactconsult.org directly.';
    }
}

http_response_code($status === 'success' ? 200 : 400);
$heading = $status === 'success' ? 'Message sent successfully' : 'Something went wrong';
$lead = $status === 'success'
    ? 'Thanks for reaching out. Our team will get back to you shortly.'
    : 'Your message was not sent. Review the items below and try again.';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?= $status === 'success' ? 'Thank you' : 'Please try again'; ?> - I-Impact Consult</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Cormorant+Garamond:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body{background:#f6f4f1;font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial,sans-serif;color:#141416}
.main{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem}
.card{max-width:540px;margin:auto;border-radius:1rem;border:1px solid rgba(0,0,0,.06);box-shadow:0 18px 40px rgba(0,0,0,.08);background:#fff;padding:2.4rem}
.status-pill{display:inline-flex;align-items:center;gap:.5rem;padding:.4rem .85rem;border-radius:999px;font-weight:600;font-size:.87rem}
.status-pill.success{background:rgba(57,181,74,.12);color:#256029}
.status-pill.error{background:rgba(220,53,69,.12);color:#842029}
pre{white-space:pre-wrap;word-break:break-word}
a.btn-back{display:inline-flex;align-items:center;gap:.4rem;text-decoration:none;font-weight:600;color:#8a5a3a;padding:.55rem 1.05rem;border-radius:.75rem;border:1px solid rgba(138,90,58,.35);transition:all .2s ease}
a.btn-back:hover{background:rgba(138,90,58,.08)}
</style>
</head>
<body>
<main class="main">
  <div class="card">
    <div class="status-pill <?= $status ?>">
      <?php if ($status === 'success'): ?>
        <span class="bi bi-check-circle-fill"></span><span>Message sent</span>
      <?php else: ?>
        <span class="bi bi-exclamation-circle-fill"></span><span>Message not sent</span>
      <?php endif; ?>
    </div>
    <h1 class="mt-3 mb-2" style="font-family:'Cormorant Garamond', serif;font-weight:700;letter-spacing:.2px;line-height:1;">
      <?= h($heading) ?>
    </h1>
    <p class="lead" style="color:#555;"><?= h($lead) ?></p>

    <?php if ($status === 'success'): ?>
      <p class="mb-4">We have logged the details below for your reference.</p>
    <?php else: ?>
      <?php if ($errors): ?>
        <div class="alert alert-danger" role="alert">
          <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
              <li><?= h($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    <?php endif; ?>

    <section class="mt-4" aria-label="Submission details">
      <dl class="row mb-0" style="--bs-gutter-x:1.5rem;">
        <dt class="col-sm-4">Name</dt>
        <dd class="col-sm-8"><?= h($name) ?></dd>

        <dt class="col-sm-4">Email</dt>
        <dd class="col-sm-8"><?= h($email) ?></dd>

        <dt class="col-sm-4">Message</dt>
        <dd class="col-sm-8"><pre class="mb-0"><?= h($message) ?></pre></dd>
      </dl>
    </section>

    <div class="mt-4 d-flex gap-2 flex-wrap">
      <a class="btn-back" href="contact.html">Back to contact page</a>
      <a class="btn-back" href="mailto:info@iimpactconsult.org">Email us directly</a>
    </div>
  </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
