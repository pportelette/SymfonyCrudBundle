<?php

namespace Pportelette\CrudBundle\Model;

interface ViewModelInterface {
    public function fromEntity(): void;
    public function toEntity();

    public function getAll();
    public function getList();
    public function getEntity();
}