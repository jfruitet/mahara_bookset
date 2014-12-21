<?php
/**
 *
 * @package    mahara
 * @subpackage artefact-bookset
 * @author     Jean FRUITET - jean.fruitet@univ-nantes.fr
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

define('INTERNAL', true);
define('MENUITEM', 'content/bookset');

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/init.php');
require_once('pieforms/pieform.php');
safe_require('artefact','bookset');

define('TITLE', get_string('deletecomponent','artefact.bookset'));

$id = param_integer('id');
$componentid = param_integer('componentid');

$bookset = new ArtefactTypeBookset($id);
if (!$USER->can_edit_artefact($bookset)) {
    throw new AccessDeniedException(get_string('accessdenied', 'error'));
}

$redirect=get_config('wwwroot') . '/artefact/bookset/bookset.php?id='.$bookset->get('id');

if (empty($componentid)){
	redirect($redirect);
}
else {
    $component = ArtefactTypeBookset::get_component($bookset->get('id'), $componentid);
	// DEBUG
	// echo "<br />delete/component.php :: 37<br />\n";
	// print_object($component);
	// exit;
	if (!empty($component)){
	    $deleteform = array(
    		'name' => 'deletecomponentform',
		    'plugintype' => 'artefact',
    		'pluginname' => 'bookset',
	    	'renderer' => 'div',
	    	'elements' => array(
    	    	'submit' => array(
        	    	'type' => 'submitcancel',
	        	    'value' => array(get_string('deletecomponent','artefact.bookset'), get_string('cancel')),
    	        	'goto' => get_config('wwwroot') . '/artefact/bookset/bookset.php?id='.$bookset->get('id'),
	        	),
    		)
		);
		$form = pieform($deleteform);

		$smarty = smarty();
		$smarty->assign('form', $form);
		$smarty->assign('PAGEHEADING', $bookset->get('title'));
		$smarty->assign('subheading', get_string('deletethiscomponent','artefact.bookset',$component->title));
		$smarty->assign('message', get_string('deletecomponentconfirm','artefact.bookset'));
		$smarty->display('artefact:bookset:delete_index.tpl');
	}
	else{
        redirect($redirect);
	}
}


// calls this function first so that we can get the artefact and call delete on it
function deletecomponentform_submit(Pieform $form, $values) {
    global $SESSION, $bookset, $componentid;

    $bookset->deletecomponent($componentid);
    $SESSION->add_ok_msg(get_string('componentdeletedsuccessfully', 'artefact.bookset'));

    redirect('/artefact/bookset/bookset.php?id='.$bookset->get('id'));
}
