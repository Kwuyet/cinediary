<?php
declare(strict_types=1);

class MovieController extends Controller
{
    public function index(): void
    {
        $filters = [
            'q' => trim($_GET['q'] ?? ''),
            'genre' => trim($_GET['genre'] ?? ''),
            'year' => trim($_GET['year'] ?? ''),
        ];

        $movieModel = new Movie();
        $movies = $movieModel->search($filters);

        $this->view('movies/index', [
            'movies' => $movies,
            'filters' => $filters,
        ]);
    }

    public function show(): void
    {
        $movieId = (int) $this->param('id');
        $movieModel = new Movie();
        $movie = $movieModel->find($movieId);

        if (!$movie) {
            http_response_code(404);
            echo 'Movie not found';
            return;
        }

        $reviewModel = new Review();
        $reviews = $reviewModel->getByMovieId($movieId);
        $commentModel = new Comment();
        $currentUser = Auth::user();
        $viewerId = (int) ($currentUser['id'] ?? 0);

        foreach ($reviews as &$review) {
            $comments = $commentModel->getByReviewId((int) $review['id'], $viewerId);
            $review['comments'] = $this->buildCommentTree($comments);
        }
        unset($review);

        $this->view('movies/show', [
            'movie' => $movie,
            'reviews' => $reviews,
        ]);
    }

    private function buildCommentTree(array $comments, ?int $parentId = null): array
    {
        $branch = [];
        foreach ($comments as $comment) {
            $currentParentId = isset($comment['parent_id']) ? (int) $comment['parent_id'] : null;
            if ($currentParentId === 0) {
                $currentParentId = null;
            }
            if ($currentParentId !== $parentId) {
                continue;
            }
            $children = $this->buildCommentTree($comments, (int) $comment['id']);
            $comment['children'] = $children;
            $branch[] = $comment;
        }

        return $branch;
    }
}
