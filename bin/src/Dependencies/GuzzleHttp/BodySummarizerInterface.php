<?php

namespace MergeInc\Sort\Dependencies\GuzzleHttp;

use MergeInc\Sort\Dependencies\Psr\Http\Message\MessageInterface;

interface BodySummarizerInterface
{
    /**
     * Returns a summarized message body.
     */
    public function summarize(MessageInterface $message): ?string;
}
