<?php

require_once '../model/comp_model.php';

class CompetitionController {
    private $compModel;

    public function __construct($db) {
        $this->compModel = new Competition($db);
    }

    public function getComp($comp_id) {
        return $this->compModel->getComp($comp_id);
    }

    public function getAllComp() {
        return $this->compModel->getAllComp();
    }

    public function getUserComp($user_id) {
        return $this->compModel->getUserComp($user_id);
    }

    public function manageComp($action, $comp_id, $comp_title, $comp_image, $comp_desc, $comp_prize, $comp_theme, $start_date, $end_date) {
        return $this->compModel->manageComp($action, $comp_id, $comp_title, $comp_image, $comp_desc, $comp_prize, $comp_theme, $start_date, $end_date);
    }

    public function deleteComp($user_id) {
        return $this->compModel->deleteComp($user_id);
    }

    //================ Competition Entries ================

    public function getAllEntries($comp_id) {
        return $this->compModel->getAllEntries($comp_id);
    }
}
?>