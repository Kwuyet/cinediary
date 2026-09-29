<?php
declare(strict_types=1);

class CommentController extends Controller
{
    public function store(): void
    {
        Auth::requireLogin();
        $reviewId = (int) $this->param('id');
        $body = trim($_POST['body'] ?? '');

        if (!Validator::minLength($body, 10) || stripos($body, 'http') !== false) {
            flash('error', 'Comment is invalid.');
            redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

        $commentModel = new Comment();
        $commentModel->create($reviewId, (int) Auth::user()['id'], $body);
        flash('success', 'Comment added.');
        redirect($_SERVER['HTTP_REFERER'] ?? '/movies/' . $reviewId);
    }

    public function reply(): void
    {
        Auth::requireLogin();
        $parentId = (int) $this->param('id');
        $body = trim($_POST['body'] ?? '');

        if (!Validator::minLength($body, 10) || stripos($body, 'http') !== false) {
            flash('error', 'Comment is invalid.');
            redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

        $commentModel = new Comment();
        $parent = $commentModel->findById($parentId);
        if (!$parent) {
            flash('error', 'Parent comment not found.');
            redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

        $commentModel->create((int) $parent['review_id'], (int) Auth::user()['id'], $body, $parentId);
        flash('success', 'Reply added.');
        redirect('/movies/' . (int) $parent['movie_id']);
    }

    public function update(): void
    {
        Auth::requireLogin();
        $commentId = (int) $this->param('id');
        $body = trim($_POST['body'] ?? '');

        if (!Validator::minLength($body, 10) || stripos($body, 'http') !== false) {
            flash('error', 'Comment is invalid.');
            redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

        $commentModel = new Comment();
        $comment = $commentModel->findById($commentId);
        if (!$comment) {
            flash('error', 'Comment not found.');
            redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

        $currentUser = Auth::user() ?? [];
        if ((int) ($comment['user_id'] ?? 0) !== (int) ($currentUser['id'] ?? 0) && (($currentUser['role'] ?? '') !== 'admin')) {
            http_response_code(403);
            echo '403 Forbidden';
            return;
        }

        $commentModel->update($commentId, $body);
        flash('success', 'Comment updated.');
        redirect('/movies/' . (int) $comment['movie_id']);
    }

    public function destroy(): void
    {
        Auth::requireLogin();
        $commentId = (int) $this->param('id');
        $commentModel = new Comment();
        $comment = $commentModel->findById($commentId);
        if (!$comment) {
            flash('error', 'Comment not found.');
            redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

        $currentUser = Auth::user() ?? [];
        if ((int) ($comment['user_id'] ?? 0) !== (int) ($currentUser['id'] ?? 0) && (($currentUser['role'] ?? '') !== 'admin')) {
            http_response_code(403);
            echo '403 Forbidden';
            return;
        }

        $commentModel->delete($commentId);
        flash('success', 'Comment deleted.');
        redirect('/movies/' . (int) $comment['movie_id']);
    }

    public function like(): void
    {
        Auth::requireLogin();
        $commentId = (int) $this->param('id');
        $currentUser = Auth::user() ?? [];
        $userId = (int) ($currentUser['id'] ?? 0);

        $commentModel = new Comment();
        $comment = $commentModel->findById($commentId);
        if (!$comment) {
            flash('error', 'Comment not found.');
            redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

        if ((int) ($comment['user_id'] ?? 0) === $userId) {
            flash('error', 'You cannot like your own comment.');
            redirect('/movies/' . (int) $comment['movie_id']);
        }

        if ($commentModel->hasLike($commentId, $userId)) {
            $commentModel->removeLike($commentId, $userId);
            flash('success', 'Like removed.');
        } else {
            $commentModel->addLike($commentId, $userId);
            flash('success', 'Comment liked.');
        }

        redirect('/movies/' . (int) $comment['movie_id']);
    }
}
