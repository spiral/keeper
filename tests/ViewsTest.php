<?php

declare(strict_types=1);

namespace Spiral\Tests\Keeper;

class ViewsTest extends TestCase
{
    use HttpTrait;

    public function testTabsWithConditionTrue(): void
    {
        $content = $this->getContent('/default/view/tabs/true');

        $contains = ['first', '1st', 'First tab', 'second', '2nd', 'Second tab'];
        foreach ($contains as $string) {
            $this->assertStringContainsString($string, $content);
        }
    }

    public function testTabsWithConditionFalse(): void
    {
        $content = $this->getContent('/default/view/tabs/false');

        $contains = ['first', '1st', 'First tab'];
        foreach ($contains as $string) {
            $this->assertStringContainsString($string, $content);
        }

        $notContains = ['second', '2nd', 'Second tab'];
        foreach ($notContains as $string) {
            $this->assertStringNotContainsString($string, $content);
        }
    }

    private function getContent(string $url): string
    {
        return trim($this->get($url)->getBody()->__toString());
    }
}
