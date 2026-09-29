<?php
declare(strict_types=1);

class DiscoverController extends Controller
{
    public function index(): void
    {
        $reviewModel = new Review();
        $sort = trim((string) ($_GET['sort'] ?? 'new'));
        $tab = trim((string) ($_GET['tab'] ?? 'all'));
        $page = (int) ($_GET['page'] ?? 1);
        $filters = [
            'q' => trim((string) ($_GET['q'] ?? '')),
            'director' => trim((string) ($_GET['director'] ?? '')),
            'genre' => trim((string) ($_GET['genre'] ?? '')),
            'rating' => trim((string) ($_GET['rating'] ?? '')),
        ];

        $allowedSorts = ['new', 'top', 'liked'];
        $allowedTabs = ['all', 'viet', 'hollywood', 'hot'];

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'new';
        }
        if (!in_array($tab, $allowedTabs, true)) {
            $tab = 'all';
        }

        $perPage = 9;
        $page = max(1, $page);
        $total = $reviewModel->countFeedFiltered($tab, $filters);
        $totalPages = max(1, (int) ceil($total / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $reviews = $reviewModel->getFeedFiltered($page, $perPage, $sort, $tab, $filters);

        $this->view('discover/index', [
            'reviews' => $reviews,
            'sort' => $sort,
            'tab' => $tab,
            'filters' => $filters,
            'page' => $page,
            'total_pages' => $totalPages,
        ]);
    }
}
