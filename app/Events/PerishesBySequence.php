<?php

namespace App\Events;

trait PerishesBySequence {

    protected abstract function getSequence(): int;

    protected abstract function getOriginalSequence(): int;

    public function hasPerished(): bool {
        return parent::hasPerished()
            || $this->getSequence() > $this->getOriginalSequence();
    }

}