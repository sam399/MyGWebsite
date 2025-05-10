<div class="col-md-6 mb-3">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <h5 class="card-title"><?= htmlspecialchars($review['username']) ?></h5>
                <div class="text-warning">
                    <?= str_repeat('★', (int)$review['rating']) ?>
                    <?= str_repeat('☆', 5 - (int)$review['rating']) ?>
                </div>
            </div>
            <p class="card-text"><?= nl2br(htmlspecialchars($review['comment_text'])) ?></p>
            <small class="text-muted">
                <?= date('M j, Y', strtotime($review['timestamp'])) ?>
            </small>
        </div>
    </div>
</div>