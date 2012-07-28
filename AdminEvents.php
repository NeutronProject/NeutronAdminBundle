<?php
/*
 * This file is part of NeutronDataGridBundle
 * (c) Zender <azazen09@gmail.com>
 *
 * This source file is subject to the MIT license
 * that is bundled with this source code in the file LICENSE.
 */
namespace Neutron\AdminBundle;

/**
 * This class describes all events in NeutronAdminBundle
 *
 * @author Nikolay Georgiev <azazen09@gmail.com>
 * @since 1.0
 */
class AdminEvents
{
    /**
     * Event is dispatched when main menu is configured
     */
    const onMenuConfigure = 'neutron_admin.onMenuConfigure';
    
    const onAcl = 'neutron_admin.onAcl';
    

}
