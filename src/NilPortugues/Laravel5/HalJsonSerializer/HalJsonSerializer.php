<?php

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 8/16/15
 * Time: 4:43 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NilPortugues\Laravel5\HalJsonSerializer;

use NilPortugues\Api\HalJson\HalJsonTransformer;
use NilPortugues\Serializer\DeepCopySerializer;

/**
 * Class HalJsonSerializer.
 */
class HalJsonSerializer extends DeepCopySerializer
{
    /**
     * @param HalJsonTransformer $halJsonTransformer
     */
    public function __construct(HalJsonTransformer $halJsonTransformer)
    {
        parent::__construct($halJsonTransformer);
    }
}
