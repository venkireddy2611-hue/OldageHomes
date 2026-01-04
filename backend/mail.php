<?php
/**
 * Render a simple template file and substitute {{key}} placeholders.
 */
function render_template($templateName, $vars = []) {
    $tpl = __DIR__ . '/templates/' . $templateName;
    if (!file_exists($tpl)) return false;
    $content = file_get_contents($tpl);
    foreach ($vars as $k => $v) {
        // Escape for safe HTML output
        $safe = htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
        $content = str_replace('{{'.$k.'}}', $safe, $content);
    }
    // Replace any remaining placeholders with empty string
    $content = preg_replace('/{{\s*[^}]+\s*}}/', '', $content);
    return $content;
}

/**
 * Send an email with optional HTML body (sends multipart/alternative).
 * If $html is provided, $text should be the plain-text fallback.
 */
function send_mail($to, $subject, $text, $from = null, $html = null) {
    $charset = 'UTF-8';
    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    if ($from) $headers[] = 'From: ' . $from;

    if ($html) {
        $boundary = '=_'.md5(uniqid(time()));
        $headers[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';

        $body = "--$boundary\r\n";
        $body .= 'Content-Type: text/plain; charset=' . $charset . "\r\n\r\n";
        $body .= $text . "\r\n\r\n";
        $body .= "--$boundary\r\n";
        $body .= 'Content-Type: text/html; charset=' . $charset . "\r\n\r\n";
        $body .= $html . "\r\n\r\n";
        $body .= "--$boundary--";
        $msg = $body;
        $hdr = implode("\r\n", $headers);
    } else {
        $headers[] = 'Content-type: text/plain; charset=' . $charset;
        $msg = $text;
        $hdr = implode("\r\n", $headers);
    }

    $ok = @mail($to, $subject, $msg, $hdr);
    if (!$ok) {
        $log = __DIR__ . '/data/mail.log';
        $entry = date('c') . " | To: $to | Subject: $subject\n";
        $entry .= "From: " . ($from ?? '') . "\n";
        $entry .= "Headers:\n" . $hdr . "\n";
        $entry .= "Text:\n" . $text . "\n";
        if ($html) $entry .= "HTML:\n" . $html . "\n";
        $entry .= "---------------------\n";
        file_put_contents($log, $entry, FILE_APPEND | LOCK_EX);
    }

    return $ok;
}
?>
