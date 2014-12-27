<?php
/**
 *
 * @package    mahara
 * @subpackage artefact-resume
 * @author     Catalyst IT Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

define('INTERNAL', true);
defined('INTERNAL') || die();

require_once(dirname(dirname(dirname(__FILE__))) . '/init.php');
safe_require('artefact', 'bookset'); // BookSet : set of Booklet
safe_require('artefact', 'booklet');  // Booklet : Christophe Declercq's Mahara artefact

if (!PluginArtefactBookset::is_active()) {
    throw new AccessDeniedException(get_string('plugindisableduser', 'mahara', get_string('bookset','artefact.bookset')));
}

if (!PluginArtefactbooklet::is_active()) {
    throw new AccessDeniedException(get_string('plugindisableduser', 'mahara', get_string('booklet','artefact.booklet')));
}

$id = param_integer('id', 0); // tomeid to display
if (!empty($id)){
	$tome = get_record('artefact_booklet_tome', 'id', $id);
	if ($tome){
		if (!$selectedTome = get_record('artefact_booklet_selectedtome', 'iduser', $USER->get('id'))) {
			// si pas de tome sélectionné, on force
			$rec = new stdClass();
			$rec->idtome = $id;
			$rec->iduser = $USER->get('id');
			insert_record('artefact_booklet_selectedtome', $rec);
		}		
		else{
			// Forcer le tome sélectionné
			set_field('artefact_booklet_selectedtome', 'idtome', $id, 'iduser', $USER->get('id'));
		}
		redirect(get_config('wwwroot').'/artefact/booklet/');
	}
}
redirect($get_config('wwwroot').'/artefact/bookset/');
