<?php
/**
 *
 * @package    mahara
 * @subpackage artefact-bookset
 * @author     Jean FRUITET - jean.fruitet@univ-nan
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

define('INTERNAL', true);
define('MENUITEM', 'content/bookset');
define('SECTION_PLUGINTYPE', 'artefact');
define('SECTION_PLUGINNAME', 'bookset');
define('SECTION_PAGE', 'index');
define('BOOKSET_SUBPAGE', 'publiclists');

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/init.php');
require_once('pieforms/pieform.php');
require_once('pieforms/pieform/elements/calendar.php');
require_once(get_config('docroot') . 'artefact/lib.php');
safe_require('artefact','bookset');
safe_require('artefact', 'booklet');  // Booklet : Christophe Declercq's Mahara artefact
if (!PluginArtefactBookset::is_active()) {
    throw new AccessDeniedException(get_string('plugindisableduser', 'mahara', get_string('bookset','artefact.bookset')));
}

if (!PluginArtefactbooklet::is_active()) {
    throw new AccessDeniedException(get_string('plugindisableduser', 'mahara', get_string('booklet','artefact.booklet')));
}

define('TITLE', get_string('selectcomponents','artefact.bookset'));

$id = param_integer('id');

$artefact = new ArtefactTypeBookset($id);
/*
//  La modification de ce bookset par son auteur ne d�pend pas de son caractere public...
// Par contre il faudrait peut-�tre verifier son statud ?
if (empty($artefact->get('public'))){
    throw new AccessDeniedException(get_string('accessdenied', 'error'));
}
*/
// selectiond des artefact_booklet_tome disponibles sans condition de statut
$components = ArtefactTypeBookset::get_all_components($artefact->get('id'), false, false);   // on prend tout

$editform = ArtefactTypeBookset::get_form_componentselect($artefact, $components);

$smarty = smarty();
$smarty->assign('iconcheckpath', ArtefactTypeBookset::get_icon_checkpath());
$smarty->assign('editform', $editform);
$smarty->assign('PAGEHEADING', hsc(get_string("selectcomponents", "artefact.bookset")));
$smarty->assign('SUBPAGENAV', PluginArtefactBookset::submenu_items());
$smarty->display('artefact:bookset:select_index.tpl');
