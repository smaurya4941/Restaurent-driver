<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc((string) ($report['title'] ?? 'PDF Export')) ?></title>
    <style>
        body { font-family: Arial, sans-serif; color: #111827; margin: 24px; }
        h1 { margin-bottom: 4px; }
        p { color: #4b5563; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #d1d5db; padding: 8px; font-size: 12px; text-align: left; vertical-align: top; }
        th { background: #f3f4f6; }
        .note { margin-top: 12px; font-size: 12px; }
        @media print {
            .note { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <h1><?= esc((string) ($report['title'] ?? 'Report')) ?></h1>
    <p><?= esc((string) ($report['description'] ?? '')) ?></p>

    <table>
        <thead>
            <tr>
                <?php foreach (($report['columns'] ?? []) as $column): ?>
                    <th><?= esc((string) $column['label']) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach (($report['rows'] ?? []) as $row): ?>
                <tr>
                    <?php foreach (($report['columns'] ?? []) as $column): ?>
                        <?php $value = $row[$column['key']] ?? ''; ?>
                        <td>
                            <?php if (($column['format'] ?? '') === 'currency'): ?>
                                <?= esc(number_format((float) $value, 2)) ?>
                            <?php else: ?>
                                <?= esc((string) $value) ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p class="note">Use your browser's Print dialog and choose "Save as PDF" to download this report as a PDF.</p>

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>
</html>
