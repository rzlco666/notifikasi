<?php

declare(strict_types=1);

namespace Rzlco\Notifikasi;

use Rzlco\Notifikasi\Enums\NotificationLevel;

class Notification
{
    private string $id;
    private NotificationLevel $level;
    private string $title;
    private string $message;
    private array $options;
    private int $timestamp;

    public function __construct(
        NotificationLevel $level,
        string $title,
        string $message = '',
        array $options = []
    ) {
        $this->id = $this->generateId();
        $this->level = $level;
        $this->title = $title;
        $this->message = $message;
        $this->options = $options;
        $this->timestamp = time();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLevel(): NotificationLevel
    {
        return $this->level;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getOption(string $key, mixed $default = null): mixed
    {
        return $this->options[$key] ?? $default;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function setOption(string $key, mixed $value): self
    {
        $this->options[$key] = $value;
        return $this;
    }

    public function hasOption(string $key): bool
    {
        return array_key_exists($key, $this->options);
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'level' => $this->level->value,
            'title' => $this->title,
            'message' => $this->message,
            'options' => $this->options,
            'timestamp' => $this->timestamp,
        ];
    }

    public static function fromArray(array $data): self
    {
        $notification = new self(
            NotificationLevel::from($data['level']),
            $data['title'],
            $data['message'] ?? '',
            $data['options'] ?? []
        );

        $notification->id = $data['id'];
        $notification->timestamp = $data['timestamp'];

        return $notification;
    }

    private function generateId(): string
    {
        return 'rzlco-notifikasi-' . uniqid() . '-' . random_int(1000, 9999);
    }
}
