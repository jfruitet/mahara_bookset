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

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/init.php');
require_once('pieforms/pieform.php');
safe_require('artefact','bookset');

define('TITLE', get_string('deletebookset','artefact.bookset'));

$id = param_integer('id');
$todelete = new ArtefactTypeBookset($id);
if (!$USER->can_edit_artefact($todelete)) {
    throw new AccessDeniedException(get_string('accessdenied', 'error'));
}

$deleteform = array(
    'name' => 'deletebooksetform',
    'plugintype' => 'artefact',
    'pluginname' => 'bookset',
    'renderer' => 'div',
    'elements' => array(
        'submit' => array(
            'type' => 'submitcancel',
            'value' => array(get_string('deletebookset','artefact.bookset'), get_string('cancel')),
            'goto' => get_config('wwwroot') . '/artefact/bookset/index.php',
        ),
    )
);
$form = pieform($deleteform);

$smarty = smarty();
$smarty->assign('form', $form);
$smarty->assign('PAGEHEADING', $todelete->get('title'));
$smarty->assign('subheading', get_string('deletethisbookset','artefact.bookset',$todelete->get('title')));
$smarty->assign('message', get_string('deletebooksetconfirm','artefact.bookset'));
$smarty->display('artefact:bookset:delete_index.tpl');

// calls this function first so that we can get the artefact and call delete on it
function deletebooksetform_submit(Pieform $form, $values) {
    global $SESSION, $todelete;

    $todelete->delete();
    $SESSION->add_ok_msg(get_string('booksetdeletedsuccessfully', 'artefact.bookset'));

    redirect(get_config('wwwroot').'/artefact/bookset/index.php');
}
