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

/**
 * Class FileLoggerWriter.
 * Default logger writer, logs to a file.
 * @package GD75\SimpleLogger
 */
class FileWriter implements SimpleLoggerWriterInterface
{

    private string $logsFilesLocation;
    private string $contextsFilesLocation;
    private string $logFilePrefix;
    private string $logFileSuffix;
    private string $logFileDateFormat;
    private string $logFileExtension;
    private string $logEntryDateTimeFormat;

    /**
     * FileLoggerWriter constructor.
     * @param string $logsFilesLocation Where to write the log files, without trailing slash, relative to the working directory.
     * @param string $contextsFilesLocation Where to write the context files, without trailing slash, relative to the working directory.
     * @param string $logFilePrefix The prefix to prepend to log files names.
     * @param string $logFileSuffix The suffix to append to log files names.
     * @param string $logFileDateFormat The format for the date in the log files names.
     * @param string $logFileExtension The extension for the log files names, must include the leading dot.
     * @param string $logEntryDateTimeFormat The date/time format for the logs entries.
     */
    public function __construct(
        string $logsFilesLocation = "",
        string $contextsFilesLocation = "contexts",
        string $logFilePrefix = "logs_",
        string $logFileSuffix = "",
        string $logFileDateFormat = "Y_m_d",
        string $logFileExtension = ".log",
        string $logEntryDateTimeFormat = "H:i:s"
    ) {
        $this->logsFilesLocation = $logsFilesLocation;
        $this->contextsFilesLocation = $contextsFilesLocation;
        $this->logFilePrefix = $logFilePrefix;
        $this->logFileSuffix = $logFileSuffix;
        $this->logFileDateFormat = $logFileDateFormat;
        $this->logFileExtension = $logFileExtension;
        $this->logEntryDateTimeFormat = $logEntryDateTimeFormat;
    }


    public function write(string $levelName, string $message, array $context): void
    {
        // Writing the context to a file if it is required
        if (!empty($context)) {
            $associatedContextID = date('Y_m_d') . "_" . rand(100000, 999999);
            $associatedContextLogEntry = "[{$associatedContextID}]";
            $associatedContextContent = json_encode($context);
            $associatedContextFileHandle = fopen("{$this->contextsFilesLocation}/{$associatedContextID}.json", "w");
            fwrite($associatedContextFileHandle, $associatedContextContent);
            fclose($associatedContextFileHandle);
        } else {
            $associatedContextLogEntry = "";
        }

        // Logging the entry, including the associated context ID if it exists
        $logFileName =
            $this->logFilePrefix
            . date($this->logFileDateFormat)
            . $this->logFileSuffix
            . $this->logFileExtension;
        $logFileHandle = fopen("{$this->logsFilesLocation}/{$logFileName}", "a");

        $logEntry = "[" . date(
                $this->logEntryDateTimeFormat
            ) . "][{$levelName}]{$associatedContextLogEntry} {$message}\n";
        fwrite($logFileHandle, $logEntry);
        fclose($logFileHandle);
    }
}