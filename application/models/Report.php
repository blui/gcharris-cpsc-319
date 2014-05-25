<?php

/**
 * Model for table Report
 * @package    Frp
 * @author     Brian Lui and Mark Obad(mainly)
 * @version    1.0
 */
class Application_Model_Report extends Frp_Model_Abstract {

    function __construct() {
        
    }

    /**
     * Combines multiple same lengths columns in to one array
     * @param array of rowsets
     * @return array combined rowsets
     */
    private function mergeCols($colsets) {
        $width = count($colsets);
        $length = count($colsets[0]);

        $data = array();

        for ($j = 0; $j < $length; $j++) {
            $columns = array();
            for ($i = 0; $i < $width; $i++) {
                $columns += $colsets[$i][$j];
            }
            $data[] = $columns;
        }

        return $data;
    }

    /**
     * Make index to help plot
     * @param array rows
     * @param string column
     * @return array
     */
    private function makeIndex($rows, $column) {
        $index = array();
        foreach ($rows as $row) {
            $index[] = $row[$column];
        }

        $index = array_unique($index);
        sort($index);
        $rows['index'] = $index;

        return $rows;
    }

    /**
     * Add week_range field to array
     * @param array with week num and year
     * @return array with week ranges
     */
    private function weekRange($rows) {
        for ($i = 0; $i < count($rows); $i++) {
            if (is_null($rows[$i]['week'])) {
                continue;
            }
            
            $year = $rows[$i]['year'];
            $week = $rows[$i]['week'];
            
            if ($week < 10) {
                $week = "0$week";
            }

            $tmp = DateTime::createFromFormat("Y-m-d", date("Y-m-d", strtotime($year."W".$week)));
            $start = $tmp->format("M j");
            $w = new DateInterval("P6D");
            $tmp->add($w);
            $end = $tmp->format("M j");

            $rows[$i]['week_range'] = str_replace(" ", "&nbsp;", "$start - $end");
        }

        return $rows;
    }

    /**
     * Gets a count of how many child vists during range
     * @param array $programs Array of program ids
     * @param string $start Start of range
     * @param string $end End of range
     * @param int $week If 1 divide in to weeks
     * @return array 
     */
    private function getAllChildCount($programs, $start, $end, $week = 0) {
        $sql = 'SELECT P.id AS program_id, P.name AS program_name,
            IFNULL(SUM(count_child),0) AS all_child_count';
        if ($week === 1) {
            $sql .= ', YEAR(PS.date) as year, WEEK(PS.date) AS week';
        }
        $sql .= ' FROM program as P
             LEFT JOIN program_session PS ON PS.program_id = P.id AND
             PS.date BETWEEN ? AND ?
             WHERE P.id IN (?)
             GROUP BY P.id';
        if ($week === 1) {
            $sql .= ", year, week";
            $sql .= ' ORDER BY program_name, year, week';
        } else {
            $sql .= ' ORDER BY program_name';
        }

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->quoteInto($sql, $start, 'DATE', 1);
        $sql = $db->quoteInto($sql, $end, 'DATE', 1);
        $sql = $db->quoteInto($sql, $programs, 'INTEGER', 1);

        return $db->fetchAll($sql);
    }

    /**
     * Gets a count of how many children visited a new program for the first time
     * @param array $programs Array of program ids
     * @param string $start Start of range
     * @param string $end End of range
     * @param int $week If 1 divide in to weeks
     * @return array 
     */
    private function getNewChildCount($programs, $start, $end, $week = 0) {
        $sql = 'SELECT P.id AS program_id, P.name AS program_name,
            COUNT(*) AS new_child_count';
        if ($week === 1) {
            $sql .= ', YEAR(PS.date) AS year, WEEK(PS.date) AS week';
        }

        $sql .= ' FROM program as P
      LEFT JOIN program_session PS ON PS.program_id = P.id AND
      PS.date BETWEEN ? AND ?
      LEFT JOIN sign_in_child_first_visit FV ON FV.program_session_id = PS.id
      WHERE P.id IN (?)
      GROUP BY P.id';
        if ($week === 1) {
            $sql .= ", year, week";
            $sql .= ' ORDER BY program_name, year, week';
        } else {
            $sql .= ' ORDER BY program_name';
        }

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->quoteInto($sql, $start, 'DATE', 1);
        $sql = $db->quoteInto($sql, $end, 'DATE', 1);
        $sql = $db->quoteInto($sql, $programs, 'INTEGER', 1);

        return $db->fetchAll($sql);
    }

    /**
     * Sums all adult vists during range
     * @param array $programs Array of program ids
     * @param string $start Start of range
     * @param string $end End of range
     * @param int $week If 1 divide in to weeks
     * @return array 
     */
    private function getAllAdultSum($programs, $start, $end, $week = 0) {
        $sql = 'SELECT P.id AS program_id, P.name AS program_name,
            IFNULL(SUM(PS.count_adult),0) AS all_adult_sum';
        if ($week === 1) {
            $sql .= ', YEAR(PS.date) AS year, WEEK(PS.date) AS week';
        }
        $sql .= ' FROM program AS P
      LEFT JOIN program_session PS ON PS.program_id = P.id AND PS.date BETWEEN ? AND ?
      WHERE P.id IN (?)
      GROUP BY P.id';
        if ($week === 1) {
            $sql .= ", year, week";
            $sql .= ' ORDER BY program_name, year, week';
        } else {
            $sql .= ' ORDER BY program_name';
        }

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->quoteInto($sql, $start, 'DATE', 1);
        $sql = $db->quoteInto($sql, $end, 'DATE', 1);
        $sql = $db->quoteInto($sql, $programs, 'INTEGER', 1);

        return $db->fetchAll($sql);
    }

    /*
      private function getNewAdultCount($programs, $start, $end, $week = 0) {
      $sql = 'SELECT P.id AS program_id, P.name AS program_name,
      COUNT(DISTINCT SF.family_id) AS new_adult_sum';
      if ($week === 1) {
      $sql .= ', YEAR(PS.date) AS year, WEEK(PS.date) AS week';
      }
      $sql .= ' FROM program AS P
      LEFT JOIN program_session PS ON PS.program_id = P.id AND PS.date BETWEEN start AND end
      LEFT JOIN sign_in_family SF ON SF.program_session_id = PS.id
      LEFT JOIN family F ON F.id = SF.family_id
      WHERE program_id IN (?) AND F.registration_date BETWEEN start AND end
      GROUP BY P.id
      ORDER BY P.name
     */

    /**
     * Gets a count of how many families visited a new program for the first time
     * @param array $programs Array of program ids
     * @param string $start Start of range
     * @param string $end End of range
     * @param int $week If 1 divide in to weeks
     * @return array 
     */
    private function getNewFamilyCount($programs, $start, $end, $week = 0) {
        $sql = 'SELECT P.id AS program_id, P.name AS program_name,
          COUNT(*) AS new_family_count';
        if ($week === 1) {
            $sql .= ', YEAR(PS.date) AS year, WEEK(PS.date) AS week';
        }
        $sql .= ' FROM program AS P
      LEFT JOIN program_session PS ON PS.program_id = P.id AND PS.date BETWEEN ? AND ?
      LEFT JOIN sign_in_family_first_visit FV ON FV.program_session_id = PS.id 
      WHERE P.id IN (?)
      GROUP BY P.id';
        if ($week === 1) {
            $sql .= ", year, week";
            $sql .= ' ORDER BY program_name, year, week';
        } else {
            $sql .= ' ORDER BY program_name';
        }

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->quoteInto($sql, $start, 'DATE', 1);
        $sql = $db->quoteInto($sql, $end, 'DATE', 1);
        $sql = $db->quoteInto($sql, $programs, 'INTEGER', 1);

        return $db->fetchAll($sql);
    }

    /**
     * Gets a count of how sessions occured in range
     * @param array $programs Array of program ids
     * @param string $start Start of range
     * @param string $end End of range
     * @param int $week If 1 divide in to weeks
     * @return array 
     */
    private function getSessionCount($programs, $start, $end, $week = 0) {

        $sql = 'SELECT P.id AS program_id, P.name AS program_name,
          COUNT(DISTINCT PS.id) AS session_count';
        if ($week === 1) {
            $sql .= ', YEAR(PS.date) AS year, WEEK(PS.date) AS week';
        }
        $sql .= ' FROM program AS P
      LEFT JOIN program_session PS ON PS.program_id = P.id AND PS.date BETWEEN ? AND ?
      WHERE P.id IN (?)
      GROUP BY P.id';
        if ($week === 1) {
            $sql .= ", year, week";
            $sql .= ' ORDER BY program_name, year, week';
        } else {
            $sql .= ' ORDER BY program_name';
        }

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->quoteInto($sql, $start, 'DATE', 1);
        $sql = $db->quoteInto($sql, $end, 'DATE', 1);
        $sql = $db->quoteInto($sql, $programs, 'INTEGER', 1);

        return $db->fetchAll($sql);
    }

    /**
     * Sums the length of all sessions occuring in range
     * @param array $programs Array of program ids
     * @param string $start Start of range
     * @param string $end End of range
     * @param int $week If 1 divide in to weeks
     * @return array 
     */
    private function getSessionHourSum($programs, $start, $end, $week = 0) {
        $sql = 'SELECT P.id AS program_id, P.name AS program_name,
          IFNULL(SUM(PS.hours),0) AS session_hour_sum';
        if ($week === 1) {
            $sql .= ', YEAR(PS.date) AS year, WEEK(PS.date) AS week';
        }
        $sql .= ' FROM program AS P
      LEFT JOIN program_session PS ON PS.program_id = P.id AND PS.date BETWEEN ? AND ?
      WHERE P.id IN (?)
      GROUP BY P.id';
        if ($week === 1) {
            $sql .= ", year, week";
            $sql .= ' ORDER BY program_name, year, week';
        } else {
            $sql .= ' ORDER BY program_name';
        }

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->quoteInto($sql, $start, 'DATE', 1);
        $sql = $db->quoteInto($sql, $end, 'DATE', 1);
        $sql = $db->quoteInto($sql, $programs, 'INTEGER', 1);

        return $db->fetchAll($sql);
    }

    /**
     * Gets a count session field trips in range
     * @param array $programs Array of program ids
     * @param string $start Start of range
     * @param string $end End of range
     * @param int $week If 1 divide in to weeks
     * @return array 
     */
    private function getFieldTripCount($programs, $start, $end, $week = 0) {
        $sql = 'SELECT P.id AS program_id, P.name AS program_name,
            COUNT(NULLIF(PS.field_trip,"")) AS field_trip_count';
        if ($week === 1) {
            $sql .= ', YEAR(PS.date) AS year, WEEK(PS.date) AS week';
        }
        $sql .= ' FROM program AS P
          LEFT JOIN program_session PS ON PS.program_id = P.id AND PS.date BETWEEN ? AND ?
          WHERE P.id IN (?)
          GROUP BY P.id';
        if ($week === 1) {
            $sql .= ", year, week";
            $sql .= ' ORDER BY program_name, year, week';
        } else {
            $sql .= ' ORDER BY program_name';
        }

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->quoteInto($sql, $start, 'DATE', 1);
        $sql = $db->quoteInto($sql, $end, 'DATE', 1);
        $sql = $db->quoteInto($sql, $programs, 'INTEGER', 1);

        return $db->fetchAll($sql);
    }

    /**
     * Gets a count of how many session guest speakers in range
     * @param array $programs Array of program ids
     * @param string $start Start of range
     * @param string $end End of range
     * @param int $week If 1 divide in to weeks
     * @return array 
     */
    private function getGuestSpeakerCount($programs, $start, $end, $week = 0) {
        $sql = 'SELECT P.id AS program_id, P.name AS program_name,
          COUNT(GS.id) AS guest_speaker_count';
        if ($week === 1) {
            $sql .= ', YEAR(PS.date) AS year, WEEK(PS.date) AS week';
        }
        $sql .= ' FROM program AS P
      LEFT JOIN program_session PS ON PS.program_id = P.id AND PS.date BETWEEN ? AND ?
      LEFT JOIN guest_speaker GS ON GS.program_session_id = PS.id
      WHERE P.id IN (?)
      GROUP BY P.id';
        if ($week === 1) {
            $sql .= ", year, week";
            $sql .= ' ORDER BY program_name, year, week';
        } else {
            $sql .= ' ORDER BY program_name';
        }
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->quoteInto($sql, $start, 'DATE', 1);
        $sql = $db->quoteInto($sql, $end, 'DATE', 1);
        $sql = $db->quoteInto($sql, $programs, 'INTEGER', 1);

        return $db->fetchAll($sql);
    }

    /**
     * Sums how many resources were handed out in range
     * @param array $programs Array of program ids
     * @param string $start Start of range
     * @param string $end End of range
     * @param int $week If 1 divide in to weeks
     * @return array 
     */
    private function getResourceSum($programs, $start, $end, $week = 0) {

        $sql = 'SELECT P.id AS program_id, P.name AS program_name,
                  IFNULL(SUM(SR.count),0) AS resource_sum';
        if ($week === 1) {
            $sql .= ', YEAR(PS.date) AS year, WEEK(PS.date) AS week';
        }
        $sql .= ' FROM program AS P
          LEFT JOIN program_session PS ON PS.program_id = P.id AND PS.date BETWEEN ? AND ?
          LEFT JOIN session_resource SR ON SR.program_session_id = PS.id
          WHERE P.id IN (?)
          GROUP BY P.id';
        if ($week === 1) {
            $sql .= ", year, week";
            $sql .= ' ORDER BY program_name, year, week';
        } else {
            $sql .= ' ORDER BY program_name';
        }

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->quoteInto($sql, $start, 'DATE', 1);
        $sql = $db->quoteInto($sql, $end, 'DATE', 1);
        $sql = $db->quoteInto($sql, $programs, 'INTEGER', 1);

        return $db->fetchAll($sql);
    }

    /**
     * Sums how many referrals were given in range
     * @param array $programs Array of program ids
     * @param string $start Start of range
     * @param string $end End of range
     * @param int $week If 1 divide in to weeks
     * @return array 
     */
    private function getReferralSum($programs, $start, $end, $week = 0) {
        $sql = 'SELECT P.id AS program_id, P.name AS program_name,
                  IFNULL(SUM(SR.count),0) AS referral_sum';
        if ($week === 1) {
            $sql .= ', YEAR(PS.date) AS year, WEEK(PS.date) AS week';
        }
        $sql .= ' FROM program AS P
          LEFT JOIN program_session PS ON PS.program_id = P.id AND PS.date BETWEEN ? AND ?
          LEFT JOIN session_referral SR ON SR.program_session_id = PS.id
          WHERE P.id IN (?)
          GROUP BY P.id';
        if ($week === 1) {
            $sql .= ", year, week";
            $sql .= ' ORDER BY program_name, year, week';
        } else {
            $sql .= ' ORDER BY program_name';
        }

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->quoteInto($sql, $start, 'DATE', 1);
        $sql = $db->quoteInto($sql, $end, 'DATE', 1);
        $sql = $db->quoteInto($sql, $programs, 'INTEGER', 1);

        return $db->fetchAll($sql);
    }

    /**
     * Counts first languages of families that visited a program in range
     * @param array $programs Array of program ids
     * @param string $start Start of range
     * @param string $end End of range
     * @return array 
     */
    private function getFamilyLanguageCount($programs, $start, $end) {
        $sql = 'SELECT P.id AS program_id, P.name AS program_name,
                  FM.guardian_first_lang_name AS lang_name_english, 
                  COUNT(DISTINCT FM.id) AS family_language_count
          FROM program AS P
          LEFT JOIN program_session PS ON PS.program_id = P.id AND PS.date BETWEEN ? AND ?
          LEFT JOIN sign_in_family SF ON SF.program_session_id = PS.id
          LEFT JOIN family FM ON SF.family_id = FM.id
          WHERE P.id IN (?)
          GROUP BY FM.guardian_first_lang, P.id
          ORDER BY ISNULL(FM.guardian_first_lang_name), FM.guardian_first_lang_name, P.name';

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->quoteInto($sql, $start, 'DATE', 1);
        $sql = $db->quoteInto($sql, $end, 'DATE', 1);
        $sql = $db->quoteInto($sql, $programs, 'INTEGER', 1);

        return $this->makeIndex($db->fetchAll($sql), 'program_name');
    }

    /**
     * Counts origin country of families that visited a program in range
     * @param array $programs Array of program ids
     * @param string $start Start of range
     * @param string $end End of range
     * @return array 
     */
    private function getCulturalCount($programs, $start, $end) {
        $sql = 'SELECT P.id AS program_id, P.name AS program_name,
                 FM.guardian_origin_country_name AS country_name,
                 COUNT(DISTINCT FM.id) AS cutural_count
          FROM program AS P
          LEFT JOIN program_session PS ON PS.program_id = P.id AND PS.date BETWEEN ? AND ?
          LEFT JOIN sign_in_family SF ON SF.program_session_id = PS.id
          LEFT JOIN family FM ON SF.family_id = FM.id
          WHERE P.id IN (?)
          GROUP BY FM.guardian_origin_country, P.id
          ORDER BY ISNULL(FM.guardian_origin_country_name), FM.guardian_origin_country_name, P.name';

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = $db->quoteInto($sql, $start, 'DATE', 1);
        $sql = $db->quoteInto($sql, $end, 'DATE', 1);
        $sql = $db->quoteInto($sql, $programs, 'INTEGER', 1);

        return $this->makeIndex($db->fetchAll($sql), 'program_name');
    }

    /**
     * Sums volunteer hours in range
     * @param array $programs Array of program ids
     * @param string $start Start of range
     * @param string $end End of range
     * @return array 
     */
    private function getVolunteerHourSum($programs, $start, $end) {
        $sql = 'SELECT P.id AS program_id, P.name AS program_name, S.job_type,
              COUNT(DISTINCT S.id) AS volunteer_count,
              IFNULL(SUM(SH.hours),0) AS volunteer_hours
      FROM program AS P
      LEFT JOIN staff_hours SH ON SH.program_id = P.id AND SH.date BETWEEN ? AND ?
      LEFT JOIN staff S ON S.id = SH.staff_id AND S.job_type IN (2,3)
      WHERE P.id IN (?)
      GROUP BY P.id, S.job_type
      ORDER BY P.name, S.job_type';

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $sql = $db->quoteInto($sql, $start, 'DATE', 1);
        $sql = $db->quoteInto($sql, $end, 'DATE', 1);
        $sql = $db->quoteInto($sql, $programs, 'INTEGER', 1);

        return $db->fetchAll($sql);
    }

    /**
     * Generates a quarterly report based on various data in the database
     * @param array $programs Array of program ids
     * @param string $start Start of range
     * @param string $end End of range
     * @param string $comp_start Start of comparison range
     * @param string $comp_end End of comparison range
     * @return array 
     */
    function quarterly($programs, $first, $start, $end, $comp_start, $comp_end) {
        $data = array();

        $data['all'] = $this->mergeCols(
                array($this->getAllAdultSum($programs, $start, $end),
                    $this->getNewFamilyCount($programs, $first, $end),
                    $this->getAllChildCount($programs, $start, $end),
                    $this->getNewChildCount($programs, $first, $end),
                    $this->getNewFamilyCount($programs, $first, $end),
                    $this->getSessionCount($programs, $start, $end),
                    $this->getSessionHourSum($programs, $start, $end),
                    $this->getFieldTripCount($programs, $start, $end),
                    $this->getGuestSpeakerCount($programs, $start, $end),
                    $this->getResourceSum($programs, $start, $end),
                    $this->getReferralSum($programs, $start, $end))
        );

        $data['comp'] = $this->mergeCols(
                array($this->getAllAdultSum($programs, $comp_start, $comp_end),
                    $this->getAllChildCount($programs, $comp_start, $comp_end),
                    $this->getSessionHourSum($programs, $comp_start, $comp_end))
        );

        $data['week'] = $this->weekRange($this->mergeCols(
                        array($this->getAllAdultSum($programs, $start, $end, 1),
                            $this->getNewFamilyCount($programs, $start, $end, 1),
                            $this->getAllChildCount($programs, $start, $end, 1),
                            $this->getNewChildCount($programs, $start, $end, 1),
                            $this->getNewFamilyCount($programs, $start, $end, 1),
                            $this->getSessionCount($programs, $start, $end, 1),
                            $this->getSessionHourSum($programs, $start, $end, 1),
                            $this->getFieldTripCount($programs, $start, $end, 1),
                            $this->getGuestSpeakerCount($programs, $start, $end, 1),
                            $this->getResourceSum($programs, $start, $end, 1),
                            $this->getReferralSum($programs, $start, $end, 1)))
        );

        $data['lang'] = $this->getFamilyLanguageCount($programs, $start, $end);
        $data['country'] = $this->getCulturalCount($programs, $start, $end);
        $data['volun'] = $this->getVolunteerHourSum($programs, $start, $end);

        return $data;
    }

    /**
     * Generates an annual report based on various data in the database
     * @param array $programs Array of program ids
     * @param string $start Start of range
     * @param string $end End of range
     * @param string $comp_start Start of comparison range
     * @param string $comp_end End of comparison range
     * @return array 
     */
    function annual($programs, $start, $end, $comp_start, $comp_end) {
        $data = array();

        $data['all'] = $this->mergeCols(
                array($this->getAllAdultSum($programs, $start, $end),
                    $this->getNewFamilyCount($programs, $start, $end),
                    $this->getAllChildCount($programs, $start, $end),
                    $this->getNewChildCount($programs, $start, $end),
                    $this->getNewFamilyCount($programs, $start, $end),
                    $this->getSessionCount($programs, $start, $end),
                    $this->getSessionHourSum($programs, $start, $end),
                    $this->getFieldTripCount($programs, $start, $end),
                    $this->getGuestSpeakerCount($programs, $start, $end))
        );

        $data['comp'] = $this->mergeCols(
                array($this->getAllAdultSum($programs, $comp_start, $comp_end),
                    $this->getNewFamilyCount($programs, $comp_start, $comp_end),
                    $this->getAllChildCount($programs, $comp_start, $comp_end),
                    $this->getNewChildCount($programs, $comp_start, $comp_end),
                    $this->getNewFamilyCount($programs, $comp_start, $comp_end),
                    $this->getSessionCount($programs, $comp_start, $comp_end),
                    $this->getSessionHourSum($programs, $comp_start, $comp_end),
                    $this->getFieldTripCount($programs, $comp_start, $comp_end),
                    $this->getGuestSpeakerCount($programs, $comp_start, $comp_end))
        );


        $data['lang'] = $this->getFamilyLanguageCount($programs, $start, $end);
        $data['country'] = $this->getCulturalCount($programs, $start, $end);
        $data['volun'] = $this->getVolunteerHourSum($programs, $start, $end);

        return $data;
    }

}

?>
