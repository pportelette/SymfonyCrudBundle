<?php

namespace Pportelette\CrudBundle\Model;

use Symfony\Component\Serializer\Annotation\Ignore;

abstract class ViewModel implements ViewModelInterface {
    abstract public function fromEntity(): void; 
    abstract public function toEntity();

    public function __construct(?array $properties = null) {
        if(!$properties) {
            return;
        }
        
        foreach($properties as $attr => $value) {
            if(!property_exists($this, $attr)) {
                continue;
            }
            $this->{$attr} = $value;
        }
    }

    #[Ignore]
    public function getAll() {
        return $this;
    }

    #[Ignore]
    public function getList() {
        return $this;
    }

    #[Ignore]
    public function getEntity() {
        return $this;
    }
}