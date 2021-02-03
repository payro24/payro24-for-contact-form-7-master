<?php
/**
 * @file Contains Init class.
 */

namespace payro24\CF7;

use payro24\CF7\Admin\AdditionalSettingsForm;
use payro24\CF7\Admin\Menu;
use payro24\CF7\Payment\Payment;
use payro24\CF7\Payment\Result;

/**
 * Class Init
 * This class registers all services and instantiate them.
 *
 * @see     \payro24\CF7\ServiceInterface
 *
 * @package payro24\CF7.
 */
class Init {

    public static function call_services() {
        foreach ( self::discover() as $class ) {
            /** @var \payro24\CF7\ServiceInterface $service */
            $service = self::instantiate( $class );
            $service->register();
        }
    }

    /**
     * Lists all services.
     *
     * @return array
     */
    private static function discover() {
        return array(
            AdditionalSettingsForm::class,
            Result::class,
            Menu::class,
            Payment::class,
        );
    }

    /**
     * Instantiate a class.
     *
     * @param $class
     *   the class must be instantiated.
     *
     * @return \payro24\CF7\ServiceInterface
     */

    private static function instantiate( $class ) {
        /** @var \payro24\CF7\ServiceInterface $service */
        $service = new $class();
        return $service;
    }
}
