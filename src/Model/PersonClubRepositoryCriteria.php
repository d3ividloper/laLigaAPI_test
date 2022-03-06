<?php

namespace App\Model;

class PersonClubRepositoryCriteria {
    public $playerName;
    public $itemsPerPage;
    public $page;

    public function __construct(
        ?string $payerName,
        ?int $itemsPerPage,
        ?int $page
    ) {
        $this->playerName = $payerName;
        $this->itemsPerPage = $itemsPerPage ?: 10;
        $this->page = $page ?: 1;
    }
}
