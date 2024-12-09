<?php
/**
 * 2007-2017 Hybridauth
 *
 *  @author Hybridauth <https://hybridauth.github.io>
 *  @copyright  2009-2017 Hybridauth
 *  @license    https://hybridauth.github.io/license.html
 *  International Registered Trademark & Property of Hybridauth
 */

namespace Hybridauth\User;

use Hybridauth\Exception\UnexpectedValueException;

/**
 * Hybridauth\User\Activity
 */
final class Activity
{
    /**
    * activity id on the provider side, usually given as integer
    *
    * @var string
    */
    public $id = null;

    /**
    * activity date of creation
    *
    * @var string
    */
    public $date = null;

    /**
    * activity content as a string
    *
    * @var string
    */
    public $text = null;

    /**
    * user who created the activity
    *
    * @var object
    */
    public $user = null;

    /**
    *
    */
    public function __construct()
    {
        $this->user = new \stdClass();

        // typically, we should have a few information about the user who created the event from social apis
        $this->user->identifier  = null;
        $this->user->displayName = null;
        $this->user->profileURL  = null;
        $this->user->photoURL    = null;
    }

    /**
    * Prevent the providers adapters from adding new fields.
    *
    * @var string $name
    * @var mixed  $value
    *
    * @throws Exception\UnexpectedValueException
    */
    public function __set($name, $value)
    {
	    unset($value);
        throw new UnexpectedValueException(sprintf('Adding new property "%s\' to %s is not allowed.', $name, __CLASS__));
    }
}
