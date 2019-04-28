<?php

namespace Ingenious\LaravelListenNotes\Concerns;

use function GuzzleHttp\Psr7\build_query;
use Illuminate\Support\Facades\Cache;
use Ingenious\LaravelListenNotes\Exceptions\NotFoundException;
use Ingenious\LaravelListenNotes\Repositories\ApiResponse;
use Zttp\Zttp;

trait MakesHttpRequests
{
    protected $endpoint;

    protected $token;

    protected $force_flag;

    /**
     * Cache stuff for 1 hour by default
     *
     * @var int
     */
    protected $ttl = 3600;

    protected $default_ttl = 3600;

    /**
     * Get the endpoint
     * @method endpoint
     *
     * @return   string
     */
    protected function endpoint()
    {
        return $this->endpoint;
    }

    /**
     * Get the formatted url
     * @method url
     *
     * @param  string $url
     * @return   string
     */
    protected function url($url)
    {
        $url = vsprintf("%s/%s", [
            trim($this->endpoint,'/'),
            trim($url,'/')
        ]);

        return $url;
    }

    protected function ttlReset()
    {
        $this->ttl = $this->default_ttl;
    }

    public function ttl($ttl)
    {
        $this->ttl = $ttl;

        return $this;
    }

    public function force()
    {
        $this->force_flag = true;

        return $this;
    }

    /**
     * Get the request url and return json
     * @method getJson
     *
     * @param  string $url
     * @param  null|array|mixed $params
     * @return   ApiResponse
     */
    public function get($url, $params = [])
    {
        $expanded = $this->url($url);

        if ( $this->force_flag )
        {
            Cache::forget( $this->cacheKey($url, $params));
        }

        $this->force_flag = false;

        $ttl = $this->ttl;

        $this->ttlReset();

        return new ApiResponse(
            Cache::remember( $this->cacheKey($url, $params), $ttl, function() use ($expanded, $params) {

                $response = Zttp::withHeaders(['X-ListenAPI-Key' => $this->token])
                               ->get($expanded, $params);

                if ( ! $response->isOk() ) {

                    switch($response->response->getStatusCode())
                    {
                        case 404 :
                            throw new NotFoundException;
                    }

                    throw new \Exception("The server responded with an error {$response->response->getStatusCode()} {$response->response->getReasonPhrase()}");
                }


                return $response->json();
        }));
    }

    public function getRaw($url, $params = [])
    {
        $expanded = $this->url($url);

        return Zttp::withHeaders(['X-ListenAPI-Key' => $this->token])
                   ->get($expanded, $params);
    }

    /**
     * Get the cache key for the request
     *
     * @param $url
     * @param $params
     * @return string
     */
    protected function cacheKey($url, $params)
    {
        return "laravel-listen-notes:" . $this->url($url) . "?" . build_query($params);
    }
}