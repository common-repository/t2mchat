<?php
namespace T2mchat\Providers\Proxies;

use Proxy\Proxy;
use Proxy\Exception\UnexpectedValueException;
use T2mchat\Providers\Filters\Oauth2Request;

class Oauth2Proxy extends Proxy
{
    /**
     * Forward the request to the target url and return the response.
     *
     * @param  string $target
     * @throws UnexpectedValueException
     * @return Response
     */
    
    public function to($target)
    {
        if (is_null($this->request))
        {
            throw new UnexpectedValueException('Missing request instance.');
        }
        $copyRequest = $this->applyRequestFilter($this->request);
        $response = $this->adapter->send($copyRequest, $target);
        $data = json_decode($response->getContent(), true);
        if ($response->getStatusCode() == 401 || ($data['error'] && ($data['error']['statusCode'] == 503 || $data['error']['statusCode'] == 401 ))) {
            foreach ($this->requestFilters as $filter)
            {
                if ($filter instanceof Oauth2Request){
                    Oauth2Request::clearAcessToken();
                }
            }
            $copyRequest = $this->applyRequestFilter($this->request);
            $response = $this->adapter->send($copyRequest, $target);
        }
        $this->request = $copyRequest;
        $response = $this->applyResponseFilter($response);
        return $response;
    }
}