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
 * Content Box block
 *
 * @package    block_tb_faq
 * @copyright  2020 Leeloo LXP (https://leeloolxp.com)
 * @author     Leeloo LXP <info@leeloolxp.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * This block simply outputs the FAQ.
 *
 * @copyright  2020 Leeloo LXP (https://leeloolxp.com)
 * @author     Leeloo LXP <info@leeloolxp.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_tb_faq extends block_base {

    /**
     * Initialize.
     *
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_tb_faq');
    }

    /**
     * Return contents of tb_faq block
     *
     * @return stdClass contents of block
     */
    public function get_content() {

        global $CFG;

        if ($this->content !== null) {
            return $this->content;
        }

        $leeloolxplicense = get_config('block_tb_faq')->license;

        $url = 'https://leeloolxp.com/api_moodle.php/?action=page_info';
        $postdata = '&license_key=' . $leeloolxplicense;

        $curl = new curl;

        $options = array(
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_HEADER' => false,
            'CURLOPT_POST' => 1,
        );

        if (!$output = $curl->post($url, $postdata, $options)) {
            $this->content->text = get_string('nolicense', 'block_tb_faq');
            return $this->content;
        }

        $infoleeloolxp = json_decode($output);

        if ($infoleeloolxp->status != 'false') {
            $leeloolxpurl = $infoleeloolxp->data->install_url;
        } else {
            $this->content->text = get_string('nolicense', 'block_tb_faq');
            return $this->content;
        }

        $url = $leeloolxpurl . '/admin/Theme_setup/get_faq_settings';

        $postdata = '&license_key=' . $leeloolxplicense;

        $curl = new curl;

        $options = array(
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_HEADER' => false,
            'CURLOPT_POST' => 1,
        );

        if (!$output = $curl->post($url, $postdata, $options)) {
            $this->content->text = get_string('nolicense', 'block_tb_faq');
            return $this->content;
        }

        $resposedata = json_decode($output);
        $mdata = $resposedata->data->faq_settings;

        if (empty($resposedata->data->block_title)) {
            $resposedata->data->block_title = get_string('displayname', 'block_tb_faq');
        }
        $this->title = $resposedata->data->block_title;

        $this->page->requires->js(new moodle_url($CFG->wwwroot . '/blocks/tb_faq/js/jquery.min.js'));
        $this->page->requires->js(new moodle_url($CFG->wwwroot . '/blocks/tb_faq/js/faq.js'));

        $this->content = new stdClass();
        $this->content->text = '<div class="tb_faq">';

        foreach($mdata as $mdatasing){
            $this->content->text .= '<div id="faq_box" class="faq_box">';

            $this->content->text .= '<div class="faq_title">';
            $this->content->text .= $mdatasing->faq_title;
            $this->content->text .= '</div>';
    
            $this->content->text .= '<div class="faq_des">';
            $this->content->text .= $mdatasing->faq_answer;
            $this->content->text .= '</div>';
    
            $this->content->text .= '</div>';
        }

        $this->content->text .= '</div>';

        $this->content->text .= '<div class="faq_more"><a href="'.$resposedata->data->button_link.'">'.$resposedata->data->buttons_text.'</a></div>';

        $this->content->footer = '';

        return $this->content;
    }

    /**
     * Allow the block to have a configuration page
     *
     * @return boolean
     */
    public function has_config() {
        return true;
    }

    /**
     * Locations where block can be displayed
     *
     * @return array
     */
    public function applicable_formats() {
        return array('all' => true);
    }
}
