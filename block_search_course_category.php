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

defined('MOODLE_INTERNAL') || die();

/**
 * The global Course Category Finder block class
 *
 * Course Category Finder Block
 * This plugin enable the user to search courses on the LMS by entering the name of the course.
 * It searches the keywords in course names and course descriptions to generate the search list.
 * Course Category Finder is an extended version of the Course Finder Block. An Option is added to
 * enable the user to search for categories as well as the courses.
 *
 * @package    search_course_category
 * @copyright  Copyrights © 2016 - 2017 | BTU Cottbus-Senftenberg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_search_course_category extends block_base {

    /**
     * Set the initial properties for the block
     */
    public function init() {
        $this->title = get_string('search_course_category', 'block_search_course_category');
    }

    /**
     * Gets the content for this block by grabbing it from $this->page
     *
     * @return object $this->content
     */
    public function get_content() {

        global $CFG, $OUTPUT;
        if ($this->content !== null) {
            return $this->content;
        }

        // Read language strings.
        $strsearch = get_string('search', 'block_search_course_category');
        $strcourse = get_string('course', 'block_search_course_category');
        $strcategory = get_string('category', 'block_search_course_category');
        $strsummary = get_string('summary', 'block_search_course_category');

        // Body of Block.
        $this->content         = new stdClass;
        $this->content->text   = '';
        $this->content->text  .= '<form action="'.$CFG->wwwroot.'/blocks/search_course_category/view.php">';
        // The combobox is added to choose the search option.
        $this->content->text  .= '<select id="searchform_combo" name="combo_option">';
        $this->content->text  .= '<option value="course">' . $strcourse . '</option>';
        $this->content->text  .= '<option value="category">' . $strcategory . '</option>';
        $this->content->text  .= '<option value="summary">'. $strsummary .'</option>';
        $this->content->text  .= '</select>';
        $this->content->text  .= '<input id="searchform_search" name="search" type="text" size="20" />';
        $this->content->text  .= '<button id="searchform_button" type="submit" title="Search">' . $strsearch . '</button><br/>';
        $this->content->text  .= '</form>';

        return $this->content;
    }
}
