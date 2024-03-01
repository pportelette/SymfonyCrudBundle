<?php

namespace Pportelette\CrudBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Pierre Portelette <pierre@cloudnite.net>
 */
class PporteletteCrudBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
