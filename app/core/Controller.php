<?php
declare(strict_types=1);

class Controller
{
    protected array $params = [];

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function param(string $name, $default = null)
    {
        return $this->params[$name] ?? $default;
    }

    protected function view(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../views/' . $view . '.php';
    }
}
