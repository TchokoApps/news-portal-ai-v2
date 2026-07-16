<?php

namespace Tests\Unit;

use Tests\TestCase;

class TextTruncationHelperTest extends TestCase
{
    public function test_truncate_text_returns_string_and_adds_suffix_when_truncated(): void
    {
        $result = truncate_text('A very long title for homepage slider', 10);

        $this->assertIsString($result);
        $this->assertStringStartsWith('A very', $result);
        $this->assertStringEndsWith('...', $result);
    }

    public function test_truncate_text_keeps_short_text_unchanged(): void
    {
        $result = truncate_text('Short title', 50);

        $this->assertSame('Short title', $result);
    }

    public function test_truncate_text_handles_null_input(): void
    {
        $result = truncate_text(null, 10);

        $this->assertSame('', $result);
    }
}
