<?php
/**
 * 2007-2017 Hybridauth
 *
 *  @author Hybridauth <https://hybridauth.github.io>
 *  @copyright  2009-2017 Hybridauth
 *  @license    https://hybridauth.github.io/license.html
 *  International Registered Trademark & Property of Hybridauth
 */

namespace Hybridauth\Exception;

/**
 * UnexpectedValueException
 *
 * Exception thrown if a value does not match with a set of values. Typically this happens when a function calls
 * another function and expects the return value to be of a certain type or value not including arithmetic or
 * buffer related errors.
 */
class UnexpectedValueException extends RuntimeException implements ExceptionInterface
{
}
