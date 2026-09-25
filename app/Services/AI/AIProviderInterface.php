<?php

namespace App\Services\AI;

interface AIProviderInterface
{
    /**
     * Send structured conversation messages to the AI provider.
     *
     * @param array<int, array{role: string, content: string}> $messages
     * @param array<string, mixed> $options
     * @return array{content: string, model: string, tokens_used: int, provider: string, is_fallback: bool}
     */
    public function sendMessage(array $messages, array $options = []): array;

    /**
     * Get the identifier name of the AI provider.
     */
    public function getProviderName(): string;

    /**
     * Determine if provider credentials and network configurations are set.
     */
    public function isConfigured(): bool;
}
