<?php

require_once '../model/comp_model.php';

class CompetitionController {
    private $compModel;

    public function __construct($db) {
        $this->compModel = new Comp($db);
    }

}
?>