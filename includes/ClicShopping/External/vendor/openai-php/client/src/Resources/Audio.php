<?php

declare(strict_types=1);

namespace OpenAI\Resources;

use OpenAI\Contracts\Resources\AudioContract;
use OpenAI\Responses\Audio\TranscriptionResponse;
use OpenAI\Responses\Audio\TranslationResponse;
use OpenAI\ValueObjects\Transporter\Payload;
use OpenAI\ValueObjects\Transporter\Response;

final class Audio implements AudioContract
{
    use Concerns\Transportable;

    /**
     * Transcribes audio into the input language.
     *
     * @see https://platform.openai.com/docs/api-reference/audio/create
     *
     * @param  array<string, mixed>  $parameters
     */
    public function transcribe(array $parameters): TranscriptionResponse
    {
        $payload = Payload::upload('audio/transcriptions', $parameters);

        /** @var Response<array{task: ?string, language: ?string, duration: ?float, segments: array<int, array{id: int, seek: int, start: float, end: float, text: string, tokens: array<int, int>, temperature: float, avg_logprob: float, compression_ratio: float, no_speech_prob: float, transient: bool}>, text: string}> $response */
        $response = $this->transporter->requestObject($payload);

        return TranscriptionResponse::from($response->data(), $response->meta());
    }

    /**
     * Translates audio into English.
     *
     * @see https://platform.openai.com/docs/api-reference/audio/create
     *
     * @param  array<string, mixed>  $parameters
     */
    public function translate(array $parameters): TranslationResponse
    {
        $payload = Payload::upload('audio/translations', $parameters);

        /** @var Response<array{task: ?string, language: ?string, duration: ?float, segments: array<int, array{id: int, seek: int, start: float, end: float, text: string, tokens: array<int, int>, temperature: float, avg_logprob: float, compression_ratio: float, no_speech_prob: float, transient: bool}>, text: string}> $response */
        $response = $this->transporter->requestObject($payload);

        return TranslationResponse::from($response->data(), $response->meta());
    }
}
