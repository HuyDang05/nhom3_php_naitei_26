<?php

namespace Tests\Unit;

use Database\Seeders\DatabaseSeeder;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class DatabaseSeederDefinitionTest extends TestCase
{
    public function test_service_definitions_are_complete_unique_and_balanced_by_category(): void
    {
        $seeder = new DatabaseSeeder;
        $method = new ReflectionMethod($seeder, 'serviceDefinitions');
        $definitions = $method->invoke($seeder);

        $this->assertCount(30, $definitions);
        $this->assertCount(30, array_unique(array_column($definitions, 'code')));

        $categoryCounts = array_count_values(array_column($definitions, 'category_code'));
        $this->assertCount(15, $categoryCounts);
        $this->assertSame([2], array_values(array_unique($categoryCounts)));

        $this->assertEqualsCanonicalizing(
            ['HTCT', 'LDAS', 'GDDT', 'YTE', 'QLXD', 'TNMT', 'TCKH', 'VHTT'],
            array_values(array_unique(array_column($definitions, 'department_code'))),
        );

        foreach ($definitions as $definition) {
            $this->assertNotEmpty($definition['name']);
            $this->assertNotEmpty($definition['description']);
            $this->assertNotEmpty($definition['requirements']);
            $this->assertNotEmpty($definition['form_schema']);
            $this->assertNotEmpty($definition['document_requirements']);
            $this->assertGreaterThan(0, $definition['processing_time_days']);
            $this->assertGreaterThanOrEqual(0, $definition['fee']);

            foreach ($definition['form_schema'] as $field) {
                $this->assertArrayHasKey('name', $field);
                $this->assertArrayHasKey('label', $field);
                $this->assertContains($field['type'], ['text', 'number', 'date']);
                $this->assertIsBool($field['required']);
            }

            foreach ($definition['document_requirements'] as $document) {
                $this->assertArrayHasKey('code', $document);
                $this->assertArrayHasKey('label', $document);
                $this->assertContains($document['type'], ['pdf', 'image', 'mixed']);
                $this->assertIsBool($document['required']);
            }
        }
    }
}
