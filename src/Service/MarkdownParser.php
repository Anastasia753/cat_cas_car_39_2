<?php

namespace App\Service;

use Demontpx\ParsedownBundle\Parsedown;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class MarkdownParser
{
    private Parsedown $parsedown;
    private AdapterInterface $cache;
    private LoggerInterface $logger;
    private bool $debug;

    public function __construct(Parsedown $parsedown, AdapterInterface $cache, LoggerInterface $markdownLogger, bool $debug)
    {
        $this->parsedown = $parsedown;
        $this->cache = $cache;
        $this->logger = $markdownLogger;
        $this->debug = $debug;
    }

    public function parse(string $source): string
    {
        if (stripos($source, 'красн') !== false) {
            $this->logger->info('Кажется и эта статья о красной точке');
        }

        if ($this->debug) {
            return $this->parsedown->text($source);
        }

        return $this->cache->get(
            'markdown_'.md5($source),
            function () use ($source) {
                return $this->parsedown->text($source);
            }
        );
    }
}