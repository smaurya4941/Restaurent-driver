Error: <?= $type ?? 'Exception' . PHP_EOL ?>
Message: <?= $message ?? 'An unexpected error occurred.' . PHP_EOL ?>
File: <?= $file ?? 'Unknown' ?>:<?= $line ?? 'Unknown' . PHP_EOL ?>

<?php if (! empty($trace)) : ?>
Stack trace:
<?php foreach ($trace as $index => $step) : ?>
#<?= $index ?> <?= ($step['file'] ?? '[internal]') . ':' . ($step['line'] ?? 0) ?> <?= ($step['class'] ?? '') . ($step['type'] ?? '') . ($step['function'] ?? '') ?>(...)
<?php endforeach; ?>
<?php endif; ?>
