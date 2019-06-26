<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Course Category Finder Block
 *
 * Course Category Finder is an extended version of the Course Finder Block. An Option is added to
 * enable the user to search for categories as well as the courses.
 *
 * @package    block_search_course_category
 * @copyright  Copyrights © 2016 - 2017 | BTU Cottbus-Senftenberg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 defined('MOODLE_INTERNAL') || die();

/**
 * Searches courses by categoryname if they match the form
 * Changed to case sensitive=false
 */
function search_courses_by_category($fromform) {

    // Search for categories with given name.
    global $DB;
    $table   = 'course_categories';
    $select  = $DB->sql_like('name', ':search', $casesensitive = false);
    $param   = array('search' => '%'.$fromform->search.'%');
    $records = $DB->get_records_select($table, $select, $param);

    // If categories were found...
    if (!empty($records)) {
        // Search for courses with matching categories.
        $sql = "SELECT id
                FROM {course}
                WHERE";
        foreach ($records as $record) {
            $sql .= " category = " . $record->id . " OR";
        }
        $sql = substr($sql, 0, -3);
        // Apply filters.
        $sql .= search_filter($fromform);
        // Get courses from database.
        return $DB->get_records_sql($sql, null);
    }

    return null;
}

/**
 * Searches courses by coursename if they match the form
 */
function search_courses_by_name($fromform) {
    global $DB;

    // Search all courses except the frontpage (where category = 0).
    $searchquery = "SELECT id
                    FROM {course}
                    WHERE fullname LIKE '%" . $fromform->search . "%' AND category != 0";
    // Apply filters.
    $searchquery .= search_filter($fromform);
    // Get courses from database.
    return $DB->get_records_sql($searchquery, null);
}

function search_courses_by_summary($fromform) {
    global $DB;

    // Search all courses except the frontpage (where category = 0).
    $searchquery = "SELECT id
                    FROM {course}
                    WHERE summary LIKE '%" . $fromform->search . "%' AND category != 0";
    // Apply filters.
    $searchquery .= search_filter($fromform);
    // Get courses from database.
    return $DB->get_records_sql($searchquery, null);
}

/**
 * Translates form filters into sql
 */
function search_filter($fromform) {

    $searchquery = '';

    if (isset($fromform->description)) {
        $searchquery .= " OR summary LIKE '%" . $fromform->search . "%'";
    }
    if (isset($fromform->coursestartdate) && $fromform->coursestartdate != 0) {
        $searchquery .= " AND startdate >= " . $fromform->coursestartdate . "";
    }
    if (isset($fromform->courseenddate) && $fromform->courseenddate != 0) {
        $searchquery .= " AND enddate  <= " . $fromform->courseenddate . "";
    }
    if (!empty($fromform->courseformat)) {
        $searchquery .= " AND format  = '" . $fromform->courseformat . "'";
    }
    if (!empty($fromform->completioncriteria)) {
        $searchquery .= " AND enablecompletion  = " . $fromform->completioncriteria . "";
    }
    if (!empty($fromform->sortmenu)) {
        $searchquery .= " ORDER BY " . $fromform->sortmenu;
    }

    return $searchquery;
}

