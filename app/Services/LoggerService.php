<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;

class LoggerService
{
    private static $instance;
    private $logChannel;
    private $logFile;
    // Private constructor to prevent direct instantiation
    private function __construct() 
    {
        $this->logFile = 'logs/custom.log'; // Default path
        $this->initializeLogFile();
    }
    // Prevent cloning
    private function __clone() {}
    // Prevent unserialization
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
    public static function getInstance():self
    {
        if(!isset(self::$instance)){
            return self::$instance = new self();
        }
        return self::$instance;
    }

    public function log(string $message, string $level = 'info'): void
    {
        $logEntry = sprintf(
            "[%s] %s: %s\n",
            now()->toDateTimeString(),
            strtoupper($level),
            $message
        );

        Storage::append($this->logFile, $logEntry);
    }

    public function getLogs(): array
    {
        if(Storage::exists($this->logFile)){
            $content = Storage::get($this->logFile);
            return explode("\n", trim($content));
        }
        return [];
    }
    public function setLogFile(string $path): self
    {
        $this->logFile = $path;
        return $this;
    }
    private function initializeLogFile()
    {
        $logPath = storage_path($this->logFile);
        if (!file_exists(dirname($logPath))) {
            mkdir(dirname($logPath), 0755, true);
        }
    }
    public function clearLogs(): void
    {
        Storage::put($this->logFile, '');
    }

}