<?php

declare(strict_types=1);

namespace App\Helpers\Libraries;

use ReallySimpleJWT\Validate;

class JwtTokenLaxValidate extends Validate
{
    /**
     * Validate the secret used to secure the token signature is strong enough.
     * It should contain a number and a upper and a lowercase letter. It should be at least 12 characters in length.
     *
     * The regex here uses Lookahead Assertions.
     *
     * @param string $secret
     * @return bool
     */
    public function secret(string $secret): bool
    {
		$passLen = false;
		$passLowerCase = false;
		$passUpperCase = false;
		$passDigit = false;
		if (12 <= strlen($secret)) {
			$passLen = true;
			foreach(range('a', 'z') as $char) {
				if(is_int(strpos($secret, $char))) {
					$passLowerCase = true;
					break;
				}
			}
			if ($passLowerCase) {
				foreach(range('A', 'Z') as $char) {
					if(is_int(strpos($secret, $char))) {
						$passUpperCase = true;
						break;
					}
				}
			}
			if ($passUpperCase) {
				foreach(range(0, 9) as $char) {
					if(is_int(strpos($secret, strval($char)))) {
						$passDigit = true;
						break;
					}
				}
			}
		}
		return $passLen && $passLowerCase && $passUpperCase && $passDigit;
    }
}
