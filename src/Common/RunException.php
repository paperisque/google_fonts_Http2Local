<?php

namespace Application\Common;

class RunException extends \Exception {

    public function __construct($e){

        if ( is_string( $e ) )
        return parent::__construct($e);

        parent::__construct(
            $e->getMessage(),
            $e->getCode(), $e
        );

        $this->line = $e->getLine();
        $this->file = $e->getFile();
    }

}
?>
