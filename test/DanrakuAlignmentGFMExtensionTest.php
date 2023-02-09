<?php

/**
 * Copyright 2023 Jan stanray watt

 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at

 *  http://www.apache.org/licenses/LICENSE-2.0

 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace JSW\Test;

use JSW\Danraku\DanrakuExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \JSW\Danraku\Parser\JisageParser
 *
 * @group jisage
 * @group gfm
 */
final class DanrakuAlignmentGFMExtensionTest extends TestCase
{
    private MarkdownConverter $converter;

    protected function setUp(): void
    {
        $rules = [
            'jisage' => [
                'ignore_alphabet' => false,
                'ignore_dash' => true,
            ],
            'yakumono' => [
                'spacing_yakumono' => true,
                'byte_sensitive' => true,
            ],
        ];

        $environment = new Environment($rules);

        $environment->addExtension(new CommonMarkCoreExtension())
                    ->addExtension(new GithubFlavoredMarkdownExtension())
                    ->addExtension(new DanrakuExtension());

        $this->converter = new MarkdownConverter($environment);
    }

    /**
     * @covers ::parse
     */
    public function testAlignmentAutoLink(): void
    {
        $expect = '<p>　<a href="https://example.com">https://example.com</a> にもあるとおり、この拡張機能は素晴らしい</p>'."\n";
        $actual_text = <<<EOL
        https://example.com にもあるとおり、この拡張機能は素晴らしい
        EOL;

        $actual = $this->converter->convert($actual_text)->getContent();

        $this->assertSame($expect, $actual);
    }

    /**
     * @covers ::parse
     */
    public function testAlignmentDisallowHTML(): void
    {
        $body = 'この拡張機能は素晴らしい';

        $expect = '&lt;textarea>'.$body.'&lt;/textarea>'."\n";
        $actual_text = '<textarea>'.$body.'</textarea>';

        $actual = $this->converter->convert($actual_text)->getContent();

        $this->assertSame($expect, $actual);
    }

    /**
     * @covers ::parse
     */
    public function testAlignmentStrikethrough(): void
    {
        $expect = '<p>　<del>なかなか</del>物凄くすぐれた拡張機能だ</p>'."\n";
        $actual_text = '~~なかなか~~物凄くすぐれた拡張機能だ';

        $actual = $this->converter->convert($actual_text)->getContent();

        $this->assertSame($expect, $actual);
    }

    /**
     * @covers ::parse
     */
    public function testAlignmentTable(): void
    {
        $expect = <<<EOL
        <table>
        <thead>
        <tr>
        <th>名称</th>
        <th>説明</th>
        </tr>
        </thead>
        <tbody>
        <tr>
        <td>DanrakuExtension</td>
        <td>とても優れた拡張機能</td>
        </tr>
        </tbody>
        </table>

        EOL;
        $actual_text = <<<EOL
        名称             |説明 
        -----------------|--------------------
        DanrakuExtension |とても優れた拡張機能
        EOL;

        $actual = $this->converter->convert($actual_text)->getContent();

        $this->assertSame($expect, $actual);
    }

    /**
     * @covers ::parse
     */
    public function testAlignmentTasklists(): void
    {
        $expect = <<<EOL
        <ul>
        <li><input checked="" disabled="" type="checkbox">拡張機能の機能を実装する</li>
        <li><input disabled="" type="checkbox">拡張機能のテストを書く</li>
        </ul>

        EOL;
        $actual_text = <<<EOL
        - [x]拡張機能の機能を実装する
        - [ ]拡張機能のテストを書く
        EOL;

        $actual = $this->converter->convert($actual_text)->getContent();

        $this->assertSame($expect, $actual);
    }
}
