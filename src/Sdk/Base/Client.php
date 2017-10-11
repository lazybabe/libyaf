<?php
namespace Libyaf\Sdk\Base;

use Libyaf\Helper\Arr;

class Client
{
    const WARNING_COST_TIME = 0.5;

    protected $conf;

    protected $client;

    protected $resource = '';

    protected $operate  = '';

    protected $headers  = [];

    protected $cookies  = [];

    protected $caller   = 'unknown';

    public function __construct(Conf $conf, $caller = null)
    {
        //SDK配置
        $this->conf     = $conf;

        //HTTP Client
        $this->client   = new \GuzzleHttp\Client();

        //caller
        $this->caller   = $caller ? : $this->caller;
    }

    /**
     * @brief 对资源发起GET请求
     *
     * @param string $resource   资源名
     * @param stirng $operate    操作
     * @param array  $params     请求参数
     *
     * @return string or null    响应内容
     */
    public function get($resource, $operate, array $params = [])
    {
        return $this->request('GET', $resource.'/'.$operate, $params);
    }

    /**
        * @brief 对资源发起POST请求
        *
        * @param string $resource   资源名
        * @param stirng $operate    操作
        * @param array  $params     请求参数
        *
        * @return string or null    响应内容
     */
    public function post($resource, $operate, array $params = [])
    {
        return $this->request('POST', $resource.'/'.$operate, $params);
    }

    /**
        * @brief 对资源发起POST请求
        *
        * @param string $method     请求方法
        * @param stirng $requestUri 请求URI
        * @param array  $params     请求参数
        *
        * @return string or null    响应内容
     */
    public function request($method, $requestUri, array $params = [])
    {
        $options = [
            'base_uri'  => $this->conf->baseUri,
            'timeout'   => $this->conf->timeout,
        ];

        //毫秒超时
        if ($this->conf->timeout < 1) {
            //需要cURL版本大于7.16.2
            $support = version_compare(curl_version()['version'], '7.16.2', '>=');

            if ($support) {
                //此设置会让DNS无超时,建议libcurl使用c-ares做异步DNS
                $options['curl'] = [
                    CURLOPT_NOSIGNAL => 1,
                ];
            } else {
                $this->conf->logger->warning('TIMEOUT_MS added in cURL 7.16.2 .');

                $this->conf->timeout = 1;
            }
        }

        //设置代理
        if ($this->conf->proxy) {
            $options['proxy'] = $this->conf->proxy;
        }

        //设置认证信息
        if ($this->conf->auth['user']) {
            $options['auth'] = array_values($this->conf->auth);
        }

        //追加caller
        $params['caller'] = $this->caller;

        //配置请求参数
        if ($params) {
            switch ($method) {
                case 'GET':
                    $options['query'] = $params;
                    break;
                case 'POST':
                    $options['form_params'] = $params;
                    break;
                case 'PUT':
                    $options['body'] = http_build_query($params);
                    break;
                default:
            }
        }

        //设置头信息
        if ($this->headers) {
            $options['headers'] = $this->headers;
        }

        //设置cookie
        if ($this->cookies) {
            $options['cookies'] = $this->cookies;
        }

        //传输细节
        $options['on_stats'] = function ($stats) use ($method, $params) {
            //记录请求日志
            $requestInfo  = 'Request';
            $requestInfo .= ' [method] '.$method;
            $requestInfo .= ' [uri] '.$stats->getEffectiveUri();
            $requestInfo .= ' [usetime] '.round($stats->getTransferTime(), 2).'(s)';

            $this->conf->logger->info($requestInfo, $params);

            $statsInfo = $stats->getHandlerStats();

            //记录请求慢日志
            if ($statsInfo['total_time'] > self::WARNING_COST_TIME) {
                $this->conf->logger->warning(
                    'Request time is too long.',
                    Arr::extract($statsInfo, [
                        'total_time',
                        'namelookup_time',
                        'connect_time',
                        'pretransfer_time',
                        'starttransfer_time',
                    ])
                );
            }
        };

        try {
            //开始请求
            $response = $this->client->request($method, $requestUri, $options);

            if ($response->getStatusCode() === 200) {
                //获取响应内容
                $data = $response->getBody()->getContents();

                //响应内容记录DEBUG日志
                $this->conf->logger->info('Response [content] '.$data);

                return $data;
            } else {
                return null;
            }
        } catch (\Exception $e) {
            //请求异常记录ERROR日志
            $this->conf->logger->error($e->getMessage());
            return null;
        }
    }

    /**
     * @brief 设置请求头信息
     *
     * @param array  $headers    请求头信息数组
     *
     * @return Client
     */
    public function setHeaders(array $headers = [])
    {
        $this->headers += $headers;

        return $this;
    }

    /**
     * @brief 设置请求Cookie
     *
     * @param array  $cookies    请求Cookie数组
     * @param string $domain     Cookie域
     *
     * @return Client
     */
    public function setCookies(array $cookies = [], $domain = null)
    {
        if (! $cookies) {
            $this->cookies = [];
        } else {
            //没传默认当前base_uri的域
            if (! $domain) {
                $domain = parse_url($this->conf->baseUri, PHP_URL_HOST);
            }

            $this->cookies = \GuzzleHttp\Cookie\CookieJar::fromArray($cookies, $domain);
        }

        return $this;
    }

}

