<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Application Error') ?></title>
    <style>
        body {
            margin: 0;
            padding: 24px;
            font: 14px/1.5 Arial, sans-serif;
            background: #f5f5f5;
            color: #1f2933;
        }
        .wrap {
            max-width: 960px;
            margin: 0 auto;
        }
        .card {
            background: #fff;
            border: 1px solid #d9e2ec;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.08);
        }
        h1, h2 {
            margin-top: 0;
        }
        .meta {
            margin: 16px 0;
            padding: 12px 16px;
            background: #f8fafc;
            border-left: 4px solid #dc2626;
        }
        code, pre {
            font-family: Consolas, "Courier New", monospace;
        }
        pre {
            overflow: auto;
            padding: 16px;
            background: #0f172a;
            color: #e2e8f0;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <h1><?= esc($title ?? 'Application Error') ?></h1>
            <p><?= esc($message ?? 'An unexpected error occurred.') ?></p>

            <div class="meta">
                <strong>Type:</strong> <?= esc($type ?? 'Unknown') ?><br>
                <strong>File:</strong> <?= esc($file ?? 'Unknown') ?><br>
                <strong>Line:</strong> <?= esc((string) ($line ?? 'Unknown')) ?><br>
                <strong>Status:</strong> <?= esc((string) ($code ?? 500)) ?>
            </div>

            <?php if (! empty($file) && ! empty($line)) : ?>
                <h2>Code Snippet</h2>
                <?= CodeIgniter\Debug\BaseExceptionHandler::highlightFile($file, (int) $line) ?: '<p>Source preview unavailable.</p>' ?>
            <?php endif; ?>

            <?php if (! empty($trace)) : ?>
                <h2>Stack Trace</h2>
                <pre><?php foreach ($trace as $index => $step) : ?>#<?= $index ?> <?= esc(($step['file'] ?? '[internal]') . ':' . ($step['line'] ?? 0)) ?> <?= esc($step['class'] ?? '') ?><?= esc($step['type'] ?? '') ?><?= esc($step['function'] ?? '') ?>(...)
<?php endforeach; ?></pre>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
