<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc((string) ($report['title'] ?? 'Report Export')) ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111827; }
        h1 { margin-bottom: 4px; }
        p { color: #4b5563; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; vertical-align: top; }
        th { background: #e2e8f0; }
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
</body>
</html>
