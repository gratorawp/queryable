<?php

declare(strict_types=1);

namespace Queryable\Tests;

use PHPUnit\Framework\TestCase;
use Queryable\Model;
use Queryable\Schema\Table;
use Queryable\Tests\Support\WpdbDouble;

class DeclaredCollationModel extends Model
{
    protected string $table = 'declared_collation';
    protected array $collation = ['utf8mb4', 'utf8mb4_unicode_520_ci'];
}

class WpdbCollationModel extends Model
{
    protected string $table = 'wpdb_collation';
}

class ModelCollationTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . '/stubs/wp-functions.php';
    }

    protected function setUp(): void
    {
        $GLOBALS['wpdb'] = new WpdbDouble();
        $GLOBALS['queryable_options'] = [];
        $GLOBALS['queryable_dbdelta'] = [];
    }

    public function test_the_model_collation_beats_the_wpdb_one(): void
    {
        DeclaredCollationModel::schema(fn (Table $t) => $t->id());
        DeclaredCollationModel::migrate();

        self::assertStringContainsString(
            'DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci',
            $GLOBALS['queryable_dbdelta'][0]
        );
    }

    public function test_a_model_declaring_no_collation_takes_the_wpdb_one(): void
    {
        WpdbCollationModel::schema(fn (Table $t) => $t->id());
        WpdbCollationModel::migrate();

        self::assertStringContainsString(
            'DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci',
            $GLOBALS['queryable_dbdelta'][0]
        );
    }
}
