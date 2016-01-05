<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 8/18/15
 * Time: 11:19 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Laravel5\HalJson;

use NilPortugues\Api\HalJson\Http\Message\ErrorResponse;
use NilPortugues\Api\HalJson\Http\Message\ResourceCreatedResponse;
use NilPortugues\Api\HalJson\Http\Message\ResourceDeletedResponse;
use NilPortugues\Api\HalJson\Http\Message\ResourceNotFoundResponse;
use NilPortugues\Api\HalJson\Http\Message\ResourcePatchErrorResponse;
use NilPortugues\Api\HalJson\Http\Message\ResourcePostErrorResponse;
use NilPortugues\Api\HalJson\Http\Message\ResourceProcessingResponse;
use NilPortugues\Api\HalJson\Http\Message\ResourceUpdatedResponse;
use NilPortugues\Api\HalJson\Http\Message\Response;
use NilPortugues\Api\HalJson\Http\Message\UnsupportedActionResponse;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

trait HalJsonResponseTrait
{
    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    protected function addHeaders(ResponseInterface $response)
    {
        return $response;
    }

    /**
     * @param string $json
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function errorResponse($json)
    {
        return (new HttpFoundationFactory())
            ->createResponse($this->addHeaders(new ErrorResponse($json)));
    }

    /**
     * @param string $json
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function resourceCreatedResponse($json)
    {
        return (new HttpFoundationFactory())
            ->createResponse(
                $this->addHeaders(new ResourceCreatedResponse($json))
            );
    }

    /**
     * @param string $json
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function resourceDeletedResponse($json)
    {
        return (new HttpFoundationFactory())
            ->createResponse(
                $this->addHeaders(new ResourceDeletedResponse($json))
            );
    }

    /**
     * @param string $json
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function resourceNotFoundResponse($json)
    {
        return (new HttpFoundationFactory())
            ->createResponse(
                $this->addHeaders(new ResourceNotFoundResponse($json))
            );
    }

    /**
     * @param string $json
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function resourcePatchErrorResponse($json)
    {
        return (new HttpFoundationFactory())
            ->createResponse(
                $this->addHeaders(new ResourcePatchErrorResponse($json))
            );
    }

    /**
     * @param string $json
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function resourcePostErrorResponse($json)
    {
        return (new HttpFoundationFactory())
            ->createResponse(
                $this->addHeaders(new ResourcePostErrorResponse($json))
            );
    }

    /**
     * @param string $json
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function resourceProcessingResponse($json)
    {
        return (new HttpFoundationFactory())
            ->createResponse(
                $this->addHeaders(new ResourceProcessingResponse($json))
            );
    }

    /**
     * @param string $json
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function resourceUpdatedResponse($json)
    {
        return (new HttpFoundationFactory())
            ->createResponse(
                $this->addHeaders(new ResourceUpdatedResponse($json))
            );
    }

    /**
     * @param string $json
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function response($json)
    {
        return (new HttpFoundationFactory())
            ->createResponse($this->addHeaders(new Response($json)));
    }

    /**
     * @param string $json
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unsupportedActionResponse($json)
    {
        return (new HttpFoundationFactory())
            ->createResponse(
                $this->addHeaders(new UnsupportedActionResponse($json))
            );
    }
}
