<?php
/**
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Blocks module
 * @link http://xaraya.com/index.php/release/13.html
 */
/**
 * view block instances
 * @author Jim McDonald, Paul Rosania
 */
function blocks_admin_view_instances()
{
    if (!xarVarFetch('filter', 'str', $filter, "", XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('startat', 'int', $startat,   1,      XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('currenttab', 'str', $data['currenttab'],   "",      XARVAR_NOT_REQUIRED)) {return;}

// Security Check
    if (!xarSecurityCheck('EditBlock', 0, 'Instance')) {return;}
    $authid = xarSecGenAuthKey();

    // Get current style.
    $data['selstyle'] = xarModUserVars::get('blocks', 'selstyle');

    if ($data['selstyle'] == 'bytype') {
    } 
    elseif ($data['selstyle'] == 'bygroup') {
    }
    // Get all block instances (whether they have group membership or not.
    // CHECKME: & removed below for php 4.4.
    $rowstodo = (int)xarModVars::get('blocks','items_per_page');
    // Need to find a better way to do this without breaking the API
    $instances = xarModAPIfunc('blocks', 'user', 'getall', array('filter' => $filter,
                                                                 'order' => 'name'));
    $total = count($instances);
    $instances = xarModAPIfunc('blocks', 'user', 'getall', array('filter' => $filter,
                                                                 'order' => 'name',
                                                                 'rowstodo' => $rowstodo,
                                                                 'startat' => $startat));
    // Create extra links and confirmation text.
    foreach ($instances as $index => $instance) {
        $instances[$index]['deleteurl'] = xarModUrl(
            'blocks', 'admin', 'delete_instance',
            array('bid' => $instance['bid'], 'authid' => $authid)
        );
        $instances[$index]['typeurl'] = xarModUrl(
            'blocks', 'admin', 'view_types',
            array('tid' => $instance['tid'])
        );
        $instances[$index]['deleteconfirm'] = xarML('Delete instance "#(1)"', addslashes($instance['name']));
    }

    // Set default style if none selected.
    if (empty($data['selstyle'])){
        $data['selstyle'] = 'plain';
    }

    $data['authid'] = $authid;
    // Item filter and pager
    $data['filter'] = $filter;
    sys::import('xaraya.pager');
    $data['pager'] = xarTplGetPager($startat,
                            $total,
                            xarModURL('blocks', 'admin', 'view_instances',array('startat' => '%%')),
                            $rowstodo);

    if ($data['selstyle'] == 'bytype') {
        $tabs = xarModAPIfunc('blocks', 'user', 'getallblocktypes');
        $data['tabs'] = array();
        foreach ($tabs as $tab) {
            $tab['name'] = $tab['info']['text_type'];
            $data['tabs'][] = $tab;
        }
        if (empty($data['currenttab'])) $data['currenttab'] = 'Login';
    } 
    elseif ($data['selstyle'] == 'bygroup') {
        $data['tabs'] = xarModAPIfunc('blocks', 'user', 'getallgroups');
        if (empty($data['currenttab'])) $data['currenttab'] = 'left';
    }

    // State descriptions.
    $data['state_desc'][0] = xarML('Hidden');
    $data['state_desc'][1] = xarML('Minimized');
    $data['state_desc'][2] = xarML('Maximized');

    $data['blocks'] = $instances;

    return $data;
}

?>