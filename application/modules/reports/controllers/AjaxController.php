<?php

class Reports_AjaxController extends Frp_Controller_AjaxAction {

    private $report_model;
    private $program_model;

    public function init() {
        $this->report_model = new Application_Model_Report();
        $this->program_model = new Application_Model_Program();
    }

    public function indexAction() {
        $this->returnJSON($this->program_model->getAllPrograms($this->getAuthInfo()), self::OPERATION_SUCCESS);
    }

    public function generateAction() {
        $programs = $this->_request->getParam('programs');
        $first = $this->_request->getParam('first-day');
        $start = DateTime::createFromFormat('m/d/Y', $this->_request->getParam('start-date'));
        $first_day = DateTime::createFromFormat('m/d/Y', $first . "/" . $start->format("Y"));
        if ($first_day > $start) {
            $first_day = DateTime::createFromFormat('m/d/Y', $first . "/" . strval(intval($start->format("Y")) - 1));
        }
        $first = $first_day->format("Y-m-d");
        $start = $start->format("Y-m-d");
        $end = DateTime::createFromFormat('m/d/Y', $this->_request->getParam('end-date'))->format('Y-m-d');
        $comp_start = DateTime::createFromFormat('m/d/Y', $this->_request->getParam('compare-start-date'))->format('Y-m-d');
        $comp_end = DateTime::createFromFormat('m/d/Y', $this->_request->getParam('compare-end-date'))->format('Y-m-d');
        $type = $this->_request->getParam('type');

        if ($type === "1") {
            $data = $this->report_model->quarterly($programs, $first, $start, $end, $comp_start, $comp_end);
        } else if ($type === "0") {
            $data = $this->report_model->annual($programs, $start, $end, $comp_start, $comp_end);
        }

        $data['dates'] = array(DateTime::createFromFormat('m/d/Y', $this->_request->getParam('start-date'))->format("M j, Y"),
            DateTime::createFromFormat('m/d/Y', $this->_request->getParam('end-date'))->format("M j, Y"),
            DateTime::createFromFormat('m/d/Y', $this->_request->getParam('compare-start-date'))->format("M j, Y"),
            DateTime::createFromFormat('m/d/Y', $this->_request->getParam('compare-end-date'))->format("M j, Y"),
            $first_day->format("M j"));

        $this->returnJSON($data, self::OPERATION_SUCCESS);
    }

    public function downloadAction() {
        $filename = $this->_request->getParam('filename');
        $data = $this->_request->getParam('content');

        $this->_helper->Layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if (empty($filename) || empty($data)) {
            return;
        }

        $filename = preg_replace('/[^a-z0-9\-\_\.]/i', '', $filename);

        $this->getResponse()->setHeader("Content-type", "text/csv")
                ->setHeader("Content-Disposition", 'attachment; filename="' . $filename . '"')
                ->setBody($data);
    }

}
