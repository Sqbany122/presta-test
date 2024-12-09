<?php
/**
 * 2007-2017 Hybridauth
 *
 *  @author Hybridauth <https://hybridauth.github.io>
 *  @copyright  2009-2017 Hybridauth
 *  @license    https://hybridauth.github.io/license.html
 *  International Registered Trademark & Property of Hybridauth
 */

namespace Hybridauth\Thirdparty\OAuth;

class OAuthSignatureMethodHMACSHA1 extends OAuthSignatureMethod
{
    public function get_name()
    {
        return "HMAC-SHA1";
    }

    public function build_signature($request, $consumer, $token)
    {
        $base_string = $request->get_signature_base_string();
        $request->base_string = $base_string;

        $key_parts = array( $consumer->secret, $token ? $token->secret : '' );

        $key_parts = OAuthUtil::urlencode_rfc3986($key_parts);
        $key = implode('&', $key_parts);
        
        return call_user_func('base64_encode', call_user_func_array('hash_hmac', array('sha1', $base_string, $key, true)));
    }
}
