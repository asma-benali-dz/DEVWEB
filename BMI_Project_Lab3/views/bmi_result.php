<?php if (\$result): ?>
    <div class="alert alert-info mt-3">
        <p><strong>BMI:</strong> <?= \$result['bmi'] ?></p>
        <p><strong>Status:</strong> <?= \$result['status'] ?></p>
    </div>
<?php endif; ?>
