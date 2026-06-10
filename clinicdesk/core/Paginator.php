<?php
class Paginator {
    private int $totalItems;
    private int $perPage;
    private int $currentPage;

    public function __construct(int $totalItems, int $perPage, int $currentPage) {
        $this->totalItems  = $totalItems;
        $this->perPage     = $perPage;
        $this->currentPage = max(1, $currentPage);
    }

    public function offset(): int {
        return ($this->currentPage - 1) * $this->perPage;
    }

    public function totalPages(): int {
        return (int) ceil($this->totalItems / $this->perPage);
    }

    public function hasPrev(): bool {
        return $this->currentPage > 1;
    }

    public function hasNext(): bool {
        return $this->currentPage < $this->totalPages();
    }

    public function currentPage(): int {
        return $this->currentPage;
    }

    public function perPage(): int {
        return $this->perPage;
    }

    public function totalItems(): int {
        return $this->totalItems;
    }

    public function pages(): array {
        $pages = [];
        $total = $this->totalPages();
        $start = max(1, $this->currentPage - 2);
        $end   = min($total, $this->currentPage + 2);

        for ($i = $start; $i <= $end; $i++) {
            $pages[] = $i;
        }
        return $pages;
    }
}