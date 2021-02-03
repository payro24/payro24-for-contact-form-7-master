<?php

/**
 * @file
 * Contains Service interface.
 */

namespace payro24\CF7;

/**
 * Interface ServiceInterface
 *
 * We separated some functions and defined them as a service.
 * Every service must define their related hooks in the register method.
 * for example if a service wants to add some admin menus, it must be hooked
 * into the "admin_menu" in it's register() method.
 *
 * @package payro24\CF7
 */
interface ServiceInterface {

    /**
     * A place for calling hooks.
     */
    public function register();
}
