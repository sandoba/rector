<?php

namespace RectorPrefix20211030\TYPO3\CMS\Core\Http;

if (\class_exists('TYPO3\\CMS\\Core\\Http\\RequestFactory')) {
    return;
}
class RequestFactory
{
    /**
     * @return void
     * @param string $uri
     * @param string $method
     * @param mixed[] $options
     */
    public function request($uri, $method = 'GET', $options = [])
    {
    }
}
