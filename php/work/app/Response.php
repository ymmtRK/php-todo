<?php
declare(strict_types=1);

/**
 * Response class
 */
class Response {

    /**
     * HTTPレスポンスステータスコード
     *
     * @var int
     */
    public int $statusCode;
    
    /**
     * HTTPメッセージ
     *
     * @var string
     */
    public string $statusText;
    
    /**
     * HTTPヘッダー
     *
     * @var array
     */
    public array $httpHeaders;
    
    /**
     * HTTPリクエストのボディ
     *
     * @var string
     */
    public string $content;
    
    /**
     * constructor function
     *
     * @param integer $statusCode
     * @param string $statusText
     * @param array $httpHeaders
     * @param string $content
     */
    function __construct(int $statusCode, string $statusText, array $httpHeaders, string $content)
    {
        $this->statusCode  = $statusCode;
        $this->statusText  = $statusText;
        $this->httpHeaders = $httpHeaders;
        $this->content     = $content;
    }

    /**
     * リクエストのボディを出力する
     *
     * @return void
     */
    function send(): void
    {
        header("HTTP/1.1 {$this->statusCode} {$this->statusText}");
        foreach ($this->httpHeaders as $name => $value) {
            header("{$name}: {$value}");
        }

        echo $this->content;
    }

    /**
     * 成功時のレスポンス
     *
     * @param string $content
     * @return Response
     */
    public static function statusOk(string $content): Response
    {
        return new Response(200, 'OK', [], $content);
    }
    
    /**
     * リダイレクト時のレスポンス
     *
     * @param string $pathInfo
     * @return Response
     */
    public static function redirect(string $pathInfo): Response
    {
        $protocol = ($_SERVER['HTTPS'] ?? '') === 'on' ? 'https://' : 'http://';
        $url = $protocol . $_SERVER['HTTP_HOST'] . $pathInfo;
        return new Response(302, 'Found', [ 'Location' => $url ], '');
    }

    /**
     * リクエストが無効の場合のレスポンス
     *
     * @param string $content
     * @return Response
     */
    public static function badRequest(string $content): Response
    {
        return new Response(400, 'Bad Request', [], $content);
    }

    /**
     * 認証に失敗した場合のレスポンス
     *
     * @param string $content
     * @return Response
     */
    public static function forbidden(string $content): Response
    {
        return new Response(403, 'Forbidden', [], $content);
    }

    /**
     * リソースがない場合のレスポンス
     *
     * @param string $content
     * @return Response
     */
    public static function notFound(string $content): Response
    {
        return new Response(404, 'Not Found', [], $content);
    }

    /**
     * サーバーでエラーが発生した場合のレスポンス
     *
     * @param string $content
     * @return Response
     */
    public static function internalServerError(string $content): Response
    {
        return new Response(500, 'Internal Server Error', [], $content);
    }
}