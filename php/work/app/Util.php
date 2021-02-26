<?php
declare(strict_types=1);

function safeEcho($text): void
{
    echo htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}