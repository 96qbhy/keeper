<?php
/**
 * Response.php
 *
 * @copyright Chongyi <xpz3847878@163.com>
 * @link      https://insp.top
 */

namespace Dybasedev\Keeper\Http;

use Swoole\Http\Response as SwooleResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Class Response
 *
 * @package Dybasedev\Keeper\Http
 */
class Response extends SymfonyResponse
{
    /**
     * @var SwooleResponse
     */
    protected $swooleResponse;
    
    /**
     * @param SwooleResponse $swooleResponse
     *
     * @return Response
     */
    public function setSwooleResponse(SwooleResponse $swooleResponse)
    {
        $this->swooleResponse = $swooleResponse;
        
        return $this;
    }
    
    /**
     * @return SwooleResponse
     */
    public function getSwooleResponse(): SwooleResponse
    {
        return $this->swooleResponse;
    }
    
    /**
     * @inheritDoc
     */
    public function sendHeaders(): Response
    {
        /* RFC2616 - 14.18 says all Responses need to have a Date */
        if (!$this->headers->has('Date')) {
            $this->setDate(\DateTime::createFromFormat('U', time()));
        }
        
        // headers
        foreach ($this->headers->allPreserveCaseWithoutCookies() as $name => $values) {
            foreach ($values as $value) {
                $this->getSwooleResponse()->header($name, $value);
            }
        }
        
        // status
        $this->getSwooleResponse()->status($this->statusCode);
        
        // cookies
        /** @var Cookie $cookie */
        foreach ($this->headers->getCookies() as $cookie) {
            $this->swooleResponse->cookie($cookie->getName(), $cookie->getValue(), $cookie->getExpiresTime(),
                $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
        }
        
        return $this;
    }
    
    /**
     * @param string $key
     * @param string $value
     * @return Response
     */
    public function addHeader(string $key, string $value): Response
    {
        $this->headers->set($key, $value);
        
        return $this;
    }
    
    /**
     * @param array $headers
     * @return $this
     */
    public function addHeaders(array $headers): Response
    {
        foreach ($headers as $key => $header) {
            $this->headers->set($key, $header);
        }
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function sendContent(): Response
    {
        $this->getSwooleResponse()->end($this->content);
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function send(): Response
    {
        $this->sendHeaders();
        $this->sendContent();
        
        return $this;
    }
}