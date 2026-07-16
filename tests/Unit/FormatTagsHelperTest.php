<?php

namespace Tests\Unit;

use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FormatTagsHelperTest extends TestCase
{
    #[Test]
    public function it_formats_collection_of_objects_into_csv_string(): void
    {
        $tags = collect([
            (object) ['name' => 'Laravel'],
            (object) ['name' => 'PHP'],
        ]);

        $this->assertSame('Laravel,PHP', format_tags($tags));
    }

    #[Test]
    public function it_returns_empty_string_for_empty_input(): void
    {
        $this->assertSame('', format_tags(Collection::make()));
    }

    #[Test]
    public function it_formats_array_input_into_csv_string(): void
    {
        $this->assertSame('News,AI', format_tags(['News', 'AI']));
    }
}
