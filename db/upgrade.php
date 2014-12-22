<?php

defined('INTERNAL') || die();

function xmldb_artefact_bookset_upgrade($oldversion=0) {
    $status = true;
    if ($oldversion < 2014122201) {
        $table = new XMLDBTable('artefact_bookset');
        $field = new XMLDBField('select');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'public');
        $status = $status && add_field($table, $field);
    }

    return $status;
}

