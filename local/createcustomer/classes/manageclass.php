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
 * MOODLE VERSION INFORMATION
 *
 * This file defines the current version of the local_createcustomer plugin code being used.
 * This is compared against the values stored in the database to determine
 * whether upgrades should be performed (see lib/db/*.php)
 *
 * @package    local_createcustomer
 * @copyright  2024 Alberto Marín Mendoza (http://myhappycoding.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_createcustomer;

class manageclass {
    public $form;
    public $returnurl;
    public $nexturl;

    public function __construct($form){
        $this->form=$form;
        $this->returnurl=new \moodle_url('/local/createcustomer/index.php');
        $this->nexturl=new \moodle_url('/local/createcustomer/index.php');
    }

    public function managedata(){
        if ($this->form->is_cancelled()) {
            // You need this section if you have a cancel button on your form.
            // You use this section to handle what to do if your user presses the cancel button.
            // This is often a redirect back to an older page.
            // NOTE: is_cancelled() should be called before get_data().
            redirect($this->returnurl);
        
        } else if ($fromform = $this->form->get_data()) {
            // This branch is where you process validated data.
        
            // Typically you finish up by redirecting to somewhere where the user
            // can see what they did.
            redirect($this->nexturl);
        }
    }

    public function displayForm(){
        $this->form->display();
    }
}