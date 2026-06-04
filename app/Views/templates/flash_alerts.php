<?php
$flashKeys = ['success', 'error', 'failed', 'Sorry', 'Required'];
foreach ($flashKeys as $key):
    $message = session()->getFlashdata($key);
    if ($message === null || $message === '') {
        continue;
    }
    $alertClass = in_array($key, ['error', 'failed', 'Sorry', 'Required'], true) ? 'alert-danger' : 'alert-success';
?>
<div class="alert <?= $alertClass ?> alert-dismissible fade show ops-alert" role="alert">
    <?= esc($message) ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<?php endforeach; ?>

<?php if (!empty(session('errors')) && is_array(session('errors'))): ?>
<div class="alert alert-danger ops-alert">
    <strong>Please fix the following:</strong>
    <ul class="mb-0 mt-2">
        <?php foreach (session('errors') as $fieldError): ?>
            <li><?= esc(is_array($fieldError) ? implode(' ', $fieldError) : $fieldError) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>
