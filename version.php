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
 * Course Category Finder is an extended version of the Course Finder Block. An Option is added to
 * enable the user to search for categories as well as the courses.
 * @author: Tobias Kutzner, Igor Nesterow
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2017030602;        // The current plugin version (Date: YYYYMMDDXX)
$plugin->requires  = 2010112400;        // Requires this Moodle version.
$plugin->release = '3.0';
$plugin->component = 'block_search_course_category';
$plugin->maturity = MATURITY_ALPHA;
