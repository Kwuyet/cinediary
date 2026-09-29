<?php
declare(strict_types=1);

class LibraryController extends Controller
{
    public function watchlist(): void
    {
        Auth::requireLogin();
        $movies = SampleData::watchlist();
        $this->view('library/watchlist', ['movies' => $movies]);
    }

    public function diary(): void
    {
        Auth::requireLogin();
        $entries = SampleData::diary();
        $this->view('library/diary', ['entries' => $entries]);
    }

    public function lists(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        $userId = (int) ($user['id'] ?? 0);
        $listModel = new ListModel();
        $lists = $listModel->getByUser($userId);
        $this->view('library/lists', ['lists' => $lists]);
    }

    public function storeList(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        $userId = (int) ($user['id'] ?? 0);

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $visibility = trim($_POST['visibility'] ?? 'public');
        $isPublic = $visibility !== 'private';

        if ($title === '') {
            flash('error', 'Tên danh sách là bắt buộc.');
            redirect('/lists');
        }

        $listModel = new ListModel();
        $listModel->create($userId, $title, $description, $isPublic);

        flash('success', 'Đã tạo danh sách mới.');
        redirect('/lists');
    }

    public function storeListItem(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        $userId = (int) ($user['id'] ?? 0);
        $listId = (int) $this->param('id');

        $listModel = new ListModel();
        $list = $listModel->findByIdAndUser($listId, $userId);
        if (!$list) {
            flash('error', 'Danh sách không tồn tại hoặc bạn không có quyền truy cập.');
            redirect('/lists');
        }

        $movieId = (int) ($_POST['movie_id'] ?? 0);
        $note = trim($_POST['note'] ?? '');
        $positionInput = trim((string) ($_POST['position'] ?? ''));
        $position = $positionInput !== '' ? max(1, (int) $positionInput) : null;

        $movie = (new Movie())->find($movieId);
        if (!$movie) {
            flash('error', 'Phim bạn chọn không tồn tại.');
            redirect('/lists/' . $listId);
        }

        $listModel->addMovieToList($listId, $movieId, $note, $position);

        flash('success', 'Đã thêm phim vào danh sách.');
        redirect('/lists/' . $listId);
    }

    public function updateListItem(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        $userId = (int) ($user['id'] ?? 0);
        $listId = (int) $this->param('id');
        $itemId = (int) $this->param('itemId');

        $listModel = new ListModel();
        $list = $listModel->findByIdAndUser($listId, $userId);
        if (!$list) {
            flash('error', 'Danh sách không tồn tại hoặc bạn không có quyền truy cập.');
            redirect('/lists');
        }

        $item = $listModel->findItem($itemId, $listId);
        if (!$item) {
            flash('error', 'Phim trong danh sách không tồn tại.');
            redirect('/lists/' . $listId);
        }

        $movieId = (int) ($_POST['movie_id'] ?? 0);
        $movie = (new Movie())->find($movieId);
        if (!$movie) {
            flash('error', 'Phim bạn chọn không tồn tại.');
            redirect('/lists/' . $listId);
        }

        $duplicate = $listModel->findItemByMovie($listId, $movieId, $itemId);
        if ($duplicate) {
          flash('error', 'Phim này đã có trong danh sách.');
          redirect('/lists/' . $listId);
        }

        $note = trim($_POST['note'] ?? '');
        $positionInput = trim((string) ($_POST['position'] ?? ''));
        $position = $positionInput !== '' ? max(1, (int) $positionInput) : (int) ($item['position'] ?? 1);

        $listModel->updateItem($itemId, $listId, $movieId, $note, $position);

        flash('success', 'Đã cập nhật phim trong danh sách.');
        redirect('/lists/' . $listId);
    }

    public function deleteListItem(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        $userId = (int) ($user['id'] ?? 0);
        $listId = (int) $this->param('id');
        $itemId = (int) $this->param('itemId');

        $listModel = new ListModel();
        $list = $listModel->findByIdAndUser($listId, $userId);
        if (!$list) {
            flash('error', 'Danh sách không tồn tại hoặc bạn không có quyền truy cập.');
            redirect('/lists');
        }

        $item = $listModel->findItem($itemId, $listId);
        if (!$item) {
            flash('error', 'Phim trong danh sách không tồn tại.');
            redirect('/lists/' . $listId);
        }

        $listModel->deleteItem($itemId, $listId);

        flash('success', 'Đã xóa phim khỏi danh sách.');
        redirect('/lists/' . $listId);
    }

    public function listDetail(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        $userId = (int) ($user['id'] ?? 0);
        $listId = (int) $this->param('id');
        $listModel = new ListModel();
        $list = $listModel->findByIdAndUser($listId, $userId);

        if (!$list) {
            flash('error', 'Danh sách không tồn tại hoặc bạn không có quyền truy cập.');
            redirect('/lists');
        }

        $items = $listModel->getItems($listId);
        $movies = (new Movie())->findAll();
        $this->view('library/list-detail', [
            'list' => $list,
            'items' => $items,
            'movies' => $movies,
        ]);
    }
}
