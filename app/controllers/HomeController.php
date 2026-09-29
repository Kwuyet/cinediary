<?php
declare(strict_types=1);

class HomeController extends Controller
{
    public function index(): void
    {
        $movieModel = new Movie();
        $reviewModel = new Review();
        $movies = $movieModel->getFeatured();

        $movieReviews = [];
        foreach ($movies as $movie) {
            $movieId = (int) ($movie['id'] ?? 0);
            if ($movieId <= 0) {
                continue;
            }

            $movieReviews[$movieId] = array_slice($reviewModel->getByMovieId($movieId), 0, 5);
        }

        $this->view('home/index', [
            'movies' => $movies,
            'movieReviews' => $movieReviews,
        ]);
    }
}
