<?php

use Consolidation\Log\Logger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;

class MergeJUnitReportsTest extends TestCase
{
    use Codeception\Task\MergeReports;

    public function testMergeReports()
    {
        $task = new Codeception\Task\MergeXmlReportsTask;
        $task->setLogger(new Logger(new NullOutput()));
        $task->from('tests/fixtures/result1.xml')
            ->from('tests/fixtures/result2.xml')
            ->into('tests/result/merged.xml')
            ->run();

        self::assertFileExists('tests/result/merged.xml');
        $xml = file_get_contents('tests/result/merged.xml');
        self::assertStringContainsString('<testsuite name="cli" tests="53" assertions="209" failures="0" errors="0"', $xml);
        self::assertStringContainsString('<testsuite name="unit" tests="22" assertions="52"', $xml);
        self::assertStringContainsString('<testcase file="/home/davert/Codeception/tests/cli/BootstrapCest.php"', $xml, 'from first file');
        self::assertStringContainsString('<testcase name="testBasic" class="GenerateCestTest"', $xml, 'from second file');
    }

    public function testMergeRewriteReports()
    {
        $task = new Codeception\Task\MergeXmlReportsTask;
        $task->setLogger(new Logger(new NullOutput()));
        $task->from('tests/fixtures/result1.xml')
            ->from('tests/fixtures/result2.xml')
            ->into('tests/result/merged.xml')
            ->mergeRewrite()
            ->run();

        $task->mergeRewrite()->run();
        self::assertFileExists('tests/result/merged.xml');
        $xml = file_get_contents('tests/result/merged.xml');
        self::assertStringContainsString('<testsuite name="cli" tests="51" assertions="204" failures="0" errors="0"', $xml);
        self::assertStringContainsString('<testsuite name="unit" tests="22" assertions="52"', $xml);
        self::assertStringContainsString('<testcase file="/home/davert/Codeception/tests/cli/BootstrapCest.php"', $xml, 'from first file');
        self::assertStringContainsString('<testcase name="testBasic" class="GenerateCestTest"', $xml, 'from second file');
    }

    protected function setUp(): void
    {
        @mkdir('tests/result');
        @unlink('tests/result/merged.xml');
    }
}
