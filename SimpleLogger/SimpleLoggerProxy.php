<?php
/**
 * MIT License
 * Copyright (c) 2021 Noah Boegli
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

declare(strict_types=1);

namespace GD75\SimpleLogger;

use Psr\Log\LoggerInterface;

/**
 * Class SimpleLoggerProxy.
 * Provides an easy way to prefix your log messages (e.g.: Add the classname in front of the message, etc.)
 * @package GD75\SimpleLogger
 */
class SimpleLoggerProxy implements LoggerInterface
{
    private LoggerInterface $logger;
    private string $prefix;

    /**
     * LoggerProxy constructor.
     * @param \Psr\Log\LoggerInterface $logger An object implementing the LoggerInterface interface (can be a Logger or another LoggerProxy).
     * @param string $prefix The prefix to prepend to messages.
     */
    public function __construct(LoggerInterface $logger, string $prefix)
    {
        $this->logger = $logger;
        $this->prefix = $prefix;
    }

    /**
     * Retrieve a new LoggerProxy using the current LoggerProxy as Logger.
     * @param string $prefix The prefix to prepend to messages.
     * @return \VoltaCrew\SimpleLogger\SimpleLoggerProxy A new LoggerProxy.
     */
    public function getProxy(string $prefix): SimpleLoggerProxy
    {
        return new SimpleLoggerProxy($this, $prefix);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param string $level The log level, Use the class constants prefixed with `LEVEL_`.
     * @param string $message The content of the log entry.
     * @param array $context An optional context to associate to the log entry.
     * @return void
     */
    public function log($level, $message, $context = array())
    {
        $this->logger->log($level, "{$this->prefix} {$message}", $context);
    }

    /**
     * System is unusable.
     *
     * @param string $message The content of the log entry.
     * @param array $context An optional context to associate to the log entry.
     * @return void
     */
    public function emergency($message, $context = array())
    {
        $this->logger->emergency("{$this->prefix} {$message}", $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message The content of the log entry.
     * @param array $context An optional context to associate to the log entry.
     * @return void
     */
    public function alert($message, $context = array())
    {
        $this->logger->alert("{$this->prefix} {$message}", $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message The content of the log entry.
     * @param array $context An optional context to associate to the log entry.
     * @return void
     */
    public function critical($message, $context = array())
    {
        $this->logger->critical("{$this->prefix} {$message}", $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message The content of the log entry.
     * @param array $context An optional context to associate to the log entry.
     * @return void
     */
    public function error($message, $context = array())
    {
        $this->logger->error("{$this->prefix} {$message}", $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message The content of the log entry.
     * @param array $context An optional context to associate to the log entry.
     * @return void
     */
    public function warning($message, $context = array())
    {
        $this->logger->warning("{$this->prefix} {$message}", $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message The content of the log entry.
     * @param array $context An optional context to associate to the log entry.
     * @return void
     */
    public function notice($message, $context = array())
    {
        $this->logger->notice("{$this->prefix} {$message}", $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message The content of the log entry.
     * @param array $context An optional context to associate to the log entry.
     * @return void
     */
    public function info($message, $context = array())
    {
        $this->logger->info("{$this->prefix} {$message}", $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message The content of the log entry.
     * @param array $context An optional context to associate to the log entry.
     * @return void
     */
    public function debug($message, $context = array())
    {
        $this->logger->debug("{$this->prefix} {$message}", $context);
    }
}