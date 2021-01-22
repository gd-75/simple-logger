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
 * Class SimpleLogger.
 * Provides a PSR-3 compliant logger.
 * @package GD75\SimpleLogger
 */
class SimpleLogger implements LoggerInterface
{
    private const LEVEL_UNKNOWN = 999;
    public const LEVEL_EMERGENCY = 7;
    public const LEVEL_ALERT = 6;
    public const LEVEL_CRITICAL = 5;
    public const LEVEL_ERROR = 4;
    public const LEVEL_WARNING = 3;
    public const LEVEL_NOTICE = 2;
    public const LEVEL_INFO = 1;
    public const LEVEL_DEBUG = 0;

    private const LEVELS_NAME = [
        self::LEVEL_EMERGENCY => "EMERGENCY",
        self::LEVEL_ALERT => "ALERT",
        self::LEVEL_CRITICAL => "CRITICAL",
        self::LEVEL_ERROR => "ERROR",
        self::LEVEL_WARNING => "WARNING",
        self::LEVEL_NOTICE => "NOTICE",
        self::LEVEL_INFO => "INFO",
        self::LEVEL_DEBUG => "DEBUG",
        self::LEVEL_UNKNOWN => "UNKNOWN"
    ];

    private array $loggerWriters;

    private int $minimalLogLevel;

    /**
     * SimpleLogger constructor.
     * @param int $minimalLogLevel The minimal log level to actually write to the log file (inclusive). Use the class constants prefixed with `LEVEL_`.
     * @param bool $addDefaultWriter Whether to add the default logger writer.
     */
    public function __construct(
        int $minimalLogLevel = self::LEVEL_DEBUG,
        bool $addDefaultWriter = true
    ) {
        $this->minimalLogLevel = $minimalLogLevel;
        $this->loggerWriters = [];

        if ($addDefaultWriter) {
            $this->loggerWriters[] = [$minimalLogLevel, new FileWriter()];
        }
    }


    /**
     * Adds a writer to the logger.
     * @param int $minimalLogLevel The minimal log level to use with this writer, checked after the logger minimal log level. Use the class constants prefixed with `LEVEL_`.
     * @param \VoltaCrew\SimpleLogger\SimpleLoggerWriterInterface $loggerWriter An instance of a class implementing the `LoggerWriterInterface` interface.
     */
    public function addWriter(int $minimalLogLevel, SimpleLoggerWriterInterface $loggerWriter)
    {
        $this->loggerWriters[] = [$minimalLogLevel, $loggerWriter];
    }

    /**
     * Retrieve a new LoggerProxy for the current Logger.
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
        if (!isset(self::LEVELS_NAME[$level])) {
            $level = self::LEVEL_UNKNOWN;
        }
        $levelName = self::LEVELS_NAME[$level];

        if ($level >= $this->minimalLogLevel) {
            foreach ($this->loggerWriters as $loggerWriter) {
                if ($level >= $loggerWriter[0]) {
                    $loggerWriter[1]->write($levelName, $message, $context);
                }
            }
        }
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
        $this->log(self::LEVEL_EMERGENCY, $message, $context);
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
        $this->log(self::LEVEL_ALERT, $message, $context);
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
        $this->log(self::LEVEL_CRITICAL, $message, $context);
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
        $this->log(self::LEVEL_ERROR, $message, $context);
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
        $this->log(self::LEVEL_WARNING, $message, $context);
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
        $this->log(self::LEVEL_NOTICE, $message, $context);
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
        $this->log(self::LEVEL_INFO, $message, $context);
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
        $this->log(self::LEVEL_DEBUG, $message, $context);
    }
}